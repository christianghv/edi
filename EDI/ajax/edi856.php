<?php
include("../../conect/conect.php");
include_once("SegAjax.php");
$accion = $_REQUEST["accion"];	

function eliminaNull($valor) {
	return str_replace("null","",$valor);
}

function eliminaNull2($valor) {
	return str_replace("NULL","",$valor);
}

if($accion == "cargaCabecera")
{
	$sociedad 	= $_REQUEST["sociedad"];
	$nro_ocs   	= $_REQUEST["nro_ocs"];
	$facturas  	= $_REQUEST["facturas"];
	$inicio   	= $_REQUEST["inicio"];
	$termino  	= $_REQUEST["termino"];
	
	$conexion = conectar_srvdev();
	
	$sql = " SELECT DISTINCT he.segm_id as nro_factura, convert(char(10),he.segm_date,103) as fecha_despacho, he.segm_time as hora, he.ship_measurement as peso_carga, ";
	$sql.= " he.ship_unit as unidad_medida, he.ship_packing as cod_embalaje, he.ship_lading as tot_unid_embarcadas, ";
	$sql.= " he.ship_transport as tipo_transporte, he.ship_transname as descripcion, he.ship_trailernumber as nro_camion, ";
	$sql.= " he.Sociedad as sociedad, he.enviado as enviado, he.ship_cantBultos as cant_bulto ";
	$sql.= " FROM [856HEADER] as he ";
	$sql.= " INNER JOIN [856DETAIL] as de ON he.segm_id = de.segm_Id ";
	//$sql.= " LEFT JOIN [856BULTO] as bu ON he.segm_id = bu.invNumber "; 
	$sql.= " WHERE 1=1 ";
	
	$sql.= " AND he.Sociedad='".$sociedad."' ";
	
	
	if($nro_ocs != '') 
	{
		$NumerosEncontrados=0;
		$sqlNumeros= " AND SUBSTRING(de.it_po, 0, 11) in ( ";
		$arr_oc = explode('|',$nro_ocs);
		$nfacturas = count($arr_oc);
		
		for($i = 0; $i < $nfacturas; $i++)
		{			
			$OC = trim($arr_oc[$i]);
			if($OC != "") 
			{
				$sqlNumeros.= " SUBSTRING('$OC', 0, 11), ";
				$NumerosEncontrados++;
			}
		}
		$aux = trim($arr_oc[0]);
		$sqlNumeros.= " '$aux' ";
		$sqlNumeros.= " ) ";
		if($NumerosEncontrados>0)
		{
			$sql.=$sqlNumeros;
		}
	}
	
	if($facturas != '') 
	{
		$NumerosEncontrados=0;
		$sqlNumeros= " AND de.segm_Id in ( ";
		$arr_facturas = explode('|',$facturas);
		$nfacturas = count($arr_facturas);
		
		for($i = 0; $i < $nfacturas; $i++)
		{			
			$factura = trim($arr_facturas[$i]);
			if($factura != "") 
			{
				$sqlNumeros.= " '$factura', ";
				$NumerosEncontrados++;
			}			
		}
		$aux = trim($arr_facturas[0]);
		$sqlNumeros.= " '$aux' ";
		$sqlNumeros.= " ) ";
		if($NumerosEncontrados>0)
		{
			$sql.=$sqlNumeros;
		}
	}
	
	if($nro_ocs == '' && $facturas == '')
	{
		if($inicio != "" && $inicio != null) {
			$sql.= " AND he.segm_date >= CONVERT(DATETIME, '".$inicio." 00:00:00', 103) ";
		}
		if($termino != "" && $termino != null) {
			$sql.= " AND he.segm_date <= CONVERT(DATETIME, '".$termino." 23:59:59', 103) ";
		}
	}
	
	$result = sqlsrv_query($conexion, $sql);
	
	$file = fopen("sql_856.txt",'w+');
	fwrite($file,$sql);
	fclose($file);
	
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		$row = array_map("eliminaNull",$row);
		//$row["fecha_despacho"] = date("d-m-Y", strtotime($row["fecha_despacho"])); 
		$row["descripcion"] = utf8_decode($row["descripcion"]);
		array_push($data,$row);
	}
	sqlsrv_close($conexion);
	echo json_encode($data);
}

if($accion == "cargaDetalle")
{
	$nro_factura = $_REQUEST["nro_factura"];
	
	$conexion = conectar_srvdev();
	$sql = " SELECT segm_Id as nro_factura, it_prodid as nro_parte, it_unitshiped as cantidad_despachada, "; 
	$sql.= " it_unitmeasurement as unidad_medicion, it_po as orden_compra,it_poPosition as PoPosition, it_refnumber as tracking_number, it_packingsleep as packing_slip ";
	$sql.= " FROM [856DETAIL] WHERE segm_Id = '".$nro_factura."' ";
	
	$result = sqlsrv_query($conexion, $sql);
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		$row = array_map("eliminaNull",$row);
		if($row["tracking_number"] == NULL || $row["tracking_number"] == null || $row["tracking_number"] == "" || $row["tracking_number"] == ' ')
			$row["tracking_number"] = '0';
		array_push($data,$row);
	}
	sqlsrv_close($conexion);
	echo json_encode($data);
}

if($accion == "cargaInfoAdicional")
{
	$factura   = $_REQUEST["factura"];
	$conexion = conectar_srvdev();
	
	$sql = " SELECT DISTINCT ship_entity as identidad, ship_idcodequali as idcalificador, ship_idcode as idcodigo "; 
	$sql.= " FROM [856NAME] WHERE segm_Id = '$factura' ORDER BY ship_entity, ship_idcodequali, ship_idcode ";
	$result = sqlsrv_query($conexion, $sql);
	
	//echo $sql;
	//die();
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		$row = array_map("eliminaNull",$row);
		array_push($data,$row);
	}
	sqlsrv_close($conexion);
	echo json_encode($data);
}
if($accion == "cargaDetalleBulto")
{
	$IdBulto   = $_REQUEST["IdBulto"];
	$factura   = $_REQUEST["factura"];
	$conexion = conectar_srvdev();
	
	$sql = " SELECT [idenBulto]
				  ,[tipoBulto]
				  ,[peso]
				  ,[unidPeso]
				  ,[volumen]
				  ,[unidVolumen]
				  ,[longitud]
				  ,[ancho]
				  ,[alto]
				  ,[unidDimension]
				  ,CONVERT(varchar, [fechaDespacho],105) as [fechaDespacho]
				  ,[instEspeciales]
				  ,[invNumber]
			  FROM [856BULTO]
			  WHERE [idenBulto]='$IdBulto' 
			  AND [invNumber]='$factura';"; 
	$result = sqlsrv_query($conexion, $sql);
	
	//echo $sql;
	//die();
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		$row = array_map("utf8_encode", $row );
		$row = array_map("eliminaNull",$row);
		array_push($data,$row);
	}
	sqlsrv_close($conexion);
	echo json_encode($data);
}

?>
