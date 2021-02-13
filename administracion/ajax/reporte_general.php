<?php
require_once($_SERVER['DOCUMENT_ROOT']."/conect/conect.php");
require_once($_SERVER['DOCUMENT_ROOT']."/funciones/fx_util.php");
include_once("SegAjax.php");
$link 	 = conectar_srvdev();
$accion 	 = $_POST["accion"];	

ini_set('memory_limit', "128M");
ini_set('max_execution_time', 600); //x saniye
ini_set("max_input_time ", 600);
set_time_limit(600);

if($accion == "getCabecera")
{
	$retorno = array();
	$fecha 	 = $_POST["fecha"];
	
	$sql 	 = "SELECT logpro.correlativo, logpro.proveedor as idproveedor, prov.nombre as proveedor,
				CONVERT(char(8),logpro.fecha,114) as hora, logpro.id_formato,logpro.id_tipo,
				(SELECT ISNULL(COUNT([status]),0) 
				FROM [log_detalleProceso]
				WHERE [status]<>1
				AND correlativo=logpro.correlativo) as erroneas
				FROM log_proceso logpro
				INNER JOIN cfg_Proveedores prov ON logpro.proveedor = prov.id_proveedor 
				WHERE logpro.fecha BETWEEN CONVERT(DATETIME, '".$fecha." 00:00:00', 103)
				AND CONVERT(DATETIME, '".$fecha." 23:59:59', 103); 
				";
	$result  = sqlsrv_query($sql, $link);

	while( $row = sqlsrv_fetch_array($result) )
	{
		$linea 				  = array();
		//$linea 				  = getData($row["correlativo"]);
		$linea["correlativo"] = $row["correlativo"];
		$linea["idproveedor"] = $row["idproveedor"];
		$linea["hora"]		  = $row["hora"];
		$linea["id_formato"]  = $row["id_formato"];
		$linea["id_tipo"]	  = $row["id_tipo"];
		$linea["erroneas"]	  = $row["erroneas"];
		$linea["proveedor"]   = utf8_encode($row["proveedor"]);
		array_push($retorno,$linea);
	}
	echo json_encode($retorno);
}

/*
if($accion == "loadLogGeneral")
{
	$retorno = array();
	$fecha 	 = $_POST["fecha"];
	$sql 	 = " SELECT lo.correlatvo, lo.proveedor as idproveedor, prov.nombre as proveedor, ";
	$sql	.= " sum (case when [de.status] = 1 then 1 else 0 end) as correctas, ";
	$sql	.= " sum (case when [de.status] = 0 then 1 else 0 end) as erroneas, ";
	$sql	.= " FROM log_proceso lo ";
	$sql	.= " INNER JOIN log_detalleProceso de ON lo.correlativo = de.correlativo ";
	$sql	.= " INNER JOIN cfg_Proveedores prov ON lo.proveedor = prov.id_proveedor ";
	$sql	.= " WHERE CONVERT(DATE,lo.fecha) = CONVERT(DATE, '".$fecha."', 103) ";
	$result  = sqlsrv_query($sql, $link);
	
	//$file = fopen("sql.txt",'w+');
	//fwrite($file,$sql);
	//fclose($file);
	
	while( $row = sqlsrv_fetch_array($result) )
	{
		$row["descripcion"] = utf8_encode($row["descripcion"]);
		$row["proveedor"] = utf8_encode($row["proveedor"]);
		
		if( stripos($row["status"],'Correcto') !== false ) {
			$row["status"] = 1;
			$row["error"] = "";
		} else {
			$row["error"] = $row["status"];
			$row["status"] = 0;
			
		}
		array_push($retorno,$row);
	}
	echo json_encode($retorno);
}
*/
function getMoreData($idproveedor)
{
	$sql 	 = " SELECT correlativo  ";
	$sql	.= " FROM log_proceso ";
	$sql	.= " WHERE proveedor = '".$idproveedor."' ";
	$result  = sqlsrv_query($sql);

	$acum_correctas = 0;
	$acum_erroneas  = 0;
	while( $row = sqlsrv_fetch_array($result) )
	{
		$tmp = array();
		$tmp = getData($row["correlativo"]);
		$acum_correctas = $acum_correctas+$tmp["correctas"];
		$acum_erroneas  = $acum_erroneas+$tmp["erroneas"];
	}
	$retorno["correctas"] = $acum_correctas;
	$retorno["erroneas"]  = $acum_erroneas;
	return $retorno;
}

function getData($correlativo)
{
	$retorno = array();
	$sql 	 = " SELECT sum (case when status = 0 then 1 else 0 end) as erroneas  ";
	$sql	.= " FROM log_detalleProceso ";
	$sql	.= " WHERE 1=1 ";
	$sql 	.= " AND correlativo = '".$correlativo."' ";
	$result  = sqlsrv_query($sql);

	while( $row = sqlsrv_fetch_array($result) )
	{
		$retorno["erroneas"]  = $row["erroneas"];
	}
	return $retorno;
}

if($accion == "getDetalle")
{
	$retorno = array();
	$correlativo = $_POST["correlativo"];
	$estatus     = $_POST["estatus"];
	
	$sql 	 = " 
				SELECT descripcion, CONVERT(char(12),fecha,114) as hora, archivo, status 
				FROM log_detalleProceso 
				WHERE correlativo = '".$correlativo."' 
				ORDER BY 2 ";
	
	$result  = sqlsrv_query($sql, $link);

	while( $row = sqlsrv_fetch_array($result) )
	{
		//$arr = split(" ", $row["hora"]);
		//$row["hora"] = $arr[1];
		$row["descripcion"] = utf8_encode($row["descripcion"]);
		array_push($retorno,$row);
	}
	echo json_encode($retorno);
}

?>
