<?php
include("../../conect/conect.php");
include_once("SegAjax.php");
$accion = $_POST["accion"];	

if($accion == "getSociedades")
{
	$link    = conectar_srvdev();
	$retorno = array();
	$sql     = " SELECT id_sociedad as idsociedad, desc_sociedad as descripcion FROM cfg_sociedades_desc ";
	$result  = sqlsrv_query($sql, $link);
	
	while( $row = sqlsrv_fetch_array($result) )
	{
		$row["descripcion"] = utf8_encode($row["descripcion"]);
		array_push($retorno,$row);
	}
	sqlsrv_close($link);
	echo json_encode($retorno);
}

if($accion == "getProveedor")
{
	$link        = conectar_srvdev();
	$retorno     = array();
	$idproveedor = $_POST["idproveedor"];
	
	$sql = " SELECT nombre as proveedor ";
	$sql.= " FROM cfg_Proveedores ";
	$sql.= " WHERE id_proveedor = '".$idproveedor."' ";
	$result = sqlsrv_query($sql, $link);
	
	while( $row = sqlsrv_fetch_array($result) )
	{
		$row["proveedor"] = utf8_encode($row["proveedor"]);
		array_push($retorno,$row);
	}
	sqlsrv_close($link);
	echo json_encode($retorno);
}

if($accion == "getNumProcesos")
{
	$link       = conectar_srvdev();
	$idsociedad = $_POST["idsociedad"];
	$retorno    = 0;
	
	$sql = " SELECT DISTINCT count(id_tipoProceso) as n_procesos ";
	$sql.= " FROM cfg_TipoProcesoxProveedor ";
	$sql.= " INNER JOIN cfg_sociedades ON cfg_TipoProcesoxProveedor.id_proveedor = cfg_sociedades.id_receiver ";
	$sql.= " INNER JOIN cfg_sociedades_desc ON cfg_sociedades.id_sociedad = cfg_sociedades_desc.id_sociedad ";
	$sql.= " INNER JOIN cfg_TipoProceso  ON cfg_TipoProcesoxProveedor.id_tipoProceso = cfg_TipoProceso.id_tipo ";
	$sql.= " WHERE cfg_sociedades.id_sociedad = '".$idsociedad."' ";
	$result = sqlsrv_query($sql, $link);

	while( $row = sqlsrv_fetch_array($result) )
	{
		$retorno = $row["n_procesos"];
	}
	sqlsrv_close($link);
	echo $retorno;
}

if($accion == "getProcesos")
{
	$link       = conectar_srvdev();
	$retorno    = array();
	$idsociedad = $_POST["idsociedad"];
	
	$sql = " SELECT id_tipo as tipoproceso, 
			 Descripcion as descripcion_tipoproceso  
		     FROM [cfg_TipoProceso]
			 WHERE id_tipo<>4 AND id_tipo<>5;";
	
	
	$result = sqlsrv_query($sql, $link);
	
	while( $row = sqlsrv_fetch_array($result) )
	{
		$row["descripcion_tipoproceso"] = utf8_encode($row["descripcion_tipoproceso"]);
		array_push($retorno,$row);
	}
	sqlsrv_close($link);
	echo json_encode($retorno);
}

if($accion == "getDatos")
{
	$link       = conectar_srvdev();
	$retorno    = array();
	$idsociedad = $_POST["idsociedad"];
	$tipo       = $_POST["tipoproceso"];
	$fecha      = $_POST["fecha"];
	
	//Validar si es XCBL
	if($tipo<>0)
	{
		$sql = "
			SELECT ca.proveedor as idproveedor, ca.correlativo, tp.descripcion as tipoproceso, 
			pro.nombre as proveedor, so.desc_sociedad as sociedad, 
			sum (case when [status] = 1 then 1 else 0 end) as correctas, 
			sum (case when [status] = 0 then 1 else 0 end) as erroneas 
			FROM log_cabecera ca 
			INNER JOIN log_detalle de ON ca.correlativo = de.correlativo 
			INNER JOIN cfg_Proveedores pro ON ca.proveedor = pro.id_proveedor 
			INNER JOIN cfg_sociedades_desc so ON ca.sociedad = so.id_sociedad 
			INNER JOIN cfg_TipoProceso tp ON ca.id_tipo = tp.id_tipo 
			WHERE CONVERT(DATE,ca.fecha) = CONVERT(DATE, '".$fecha."', 103) 
			AND ca.id_tipo = '".$tipo."' 
			AND ca.sociedad = '".$idsociedad."' 
			GROUP BY ca.correlativo, ca.proveedor, pro.nombre, so.desc_sociedad, tp.descripcion;";
	}
	else
	{
		$sql = "
			SELECT ca.proveedor as idproveedor, ca.correlativo, tp.descripcion as tipoproceso, 
			pro.nombre as proveedor, so.desc_sociedad as sociedad, 
			sum (case when [status] = 1 then 1 else 0 end) as correctas, 
			sum (case when [status] = 0 then 1 else 0 end) as erroneas 
			FROM log_cabecera ca 
			INNER JOIN log_detalle de ON ca.correlativo = de.correlativo 
			INNER JOIN cfg_Proveedores pro ON ca.proveedor = pro.id_proveedor 
			INNER JOIN cfg_sociedades_desc so ON ca.sociedad = so.id_sociedad 
			INNER JOIN cfg_TipoProceso tp ON ca.id_tipo = tp.id_tipo 
			WHERE CONVERT(DATE,ca.fecha) = CONVERT(DATE, '".$fecha."', 103) 
			ca.id_formato = '2' 
			AND ca.sociedad = '".$idsociedad."' 
			GROUP BY ca.correlativo, ca.proveedor, pro.nombre, so.desc_sociedad, tp.descripcion;";
	}
	
	$result = sqlsrv_query($sql, $link);

	while( $row = sqlsrv_fetch_array($result) )
	{
		array_push($retorno,$row);
	}
	sqlsrv_close($link);
	echo json_encode($retorno);
}
/**
if($accion == "ObtenerDatosLog")
{
	$link       = conectar_srvdev();
	$retorno    = array();
	$idsociedad = $_POST["idsociedad"];
	$tipoFormato= $_POST["tipoproceso"];
	$fecha      = $_POST["fecha"];
	
	//Obtener provedores
	
	$SqlProvedores="SELECT distinct(log_cab.[proveedor])
				  FROM [log_cabecera] log_cab
				  INNER JOIN log_detalle log_det ON
				  log_det.correlativo=log_cab.correlativo
				  WHERE log_cab.[sociedad]='$idsociedad'
				  AND log_cab.documento=$tipoFormato";
				  
	$result = sqlsrv_query($SqlProvedores, $link);

	while( $row = sqlsrv_fetch_array($result) )
	{
		$Provedor=$row[proveedor];
		//Obteniendo datos de correctas
		$Correctas=0;
		$SqlCorrectas="SELECT count(log_det.[status]) as correctas
					  FROM [log_cabecera] log_cab
					  INNER JOIN log_detalle log_det ON
					  log_det.correlativo=log_cab.correlativo
					  WHERE log_cab.[sociedad]='$idsociedad'
					  AND log_cab.proveedor='$Provedor'
					  AND log_det.[status]=1
					  AND log_cab.documento=$tipoFormato;";
					  
		$resultCorrectas = sqlsrv_query($SqlCorrectas, $link);
		while( $rowCorrectas = sqlsrv_fetch_array($resultCorrectas) )
		{
			$Correctas=$rowCorrectas[correctas];
		}
		
		//Obteniendo Incorrectas
		$Incorrectas=0;
		$SqlIncorrectas="SELECT count(log_det.[status]) as incorrectas
					  FROM [log_cabecera] log_cab
					  INNER JOIN log_detalle log_det ON
					  log_det.correlativo=log_cab.correlativo
					  WHERE log_cab.[sociedad]='$idsociedad'
					  AND log_cab.proveedor='$Provedor'
					  AND log_det.[status]=0
					  AND log_cab.documento=$tipoFormato;";
					  
		$resultIncorrectas = sqlsrv_query($SqlIncorrectas, $link);
		while( $rowIncorrectas = sqlsrv_fetch_array($resultIncorrectas) )
		{
			$Incorrectas=$rowIncorrectas[incorrectas];
		}
		array_push($retorno,$row);
	}
	
}
*/
if($accion == "getDatosDetalle")
{
	$link       = conectar_srvdev();
	$retorno    = array();
	$correlativo = $_POST["correlativo"];
	
	$sql = " SELECT status as estatus, descripcion, NumeroFactura as nro_factura, CONVERT(char(8),fecha,114) as hora ";
	$sql.= " FROM log_detalle ";
	$sql.= " WHERE correlativo = '".$correlativo."' ";
	$result = sqlsrv_query($sql, $link);

	while( $row = sqlsrv_fetch_array($result) )
	{
		array_push($retorno,$row);
	}
	sqlsrv_close($link);
	echo json_encode($retorno);
}

?>
