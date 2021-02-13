	<?php
include("../../conect/conect.php");
include_once("SegAjax.php");
$accion = $_REQUEST["accion"];	

function eliminaNull($valor) {
	try
	{
			return '';//str_replace("null","",$valor);
	}catch (Exception $e)
	{
		return $valor;
	}
	
}

if($accion == "cargaOC")
{
	$sociedad = $_REQUEST["sociedad"];
	$nro_ocs   = $_REQUEST["nro_ocs"];
	$inicio   = $_REQUEST["inicio"];
	$termino  = $_REQUEST["termino"];
	
	$conexion = conectar_srvdev();
	
	 $sql = " SELECT PO_Number as nro_oc,
			convert(char(10),PO_Date,103) as fecha_oc,
			convert(char(10),ACK_Date,103) as fecha_ack,
			PO_Items as nro_item,
			PO_Sociedad as sociedad, 
			PO_ShipTo as embarque,
			Modificada as modificada,
			Actualiza as actualiza,
			Checkid as checkid 
			FROM PO_HEADER WHERE 1=1 ";
	
	if($sociedad != "" && $sociedad != null ) {
		$sql.= " AND PO_Sociedad = '".$sociedad."' ";
	}
	
	if($nro_ocs != '') 
	{
		$NumerosEncontrados=0;
		$sqlNumeros= " AND  SUBSTRING(PO_Number, 0, 11) in ( ";
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
		else
		{
			if($inicio != "" && $inicio != null) {
				$sql.= " AND PO_Date >= CONVERT(DATETIME, '".$inicio." 00:00:00', 103) ";
			}
			if($termino != "" && $termino != null) {
				$sql.= " AND PO_Date <= CONVERT(DATETIME, '".$termino." 23:59:59', 103) ";
			}
		}
	}
	else
	{
		if($inicio != "" && $inicio != null) {
				$sql.= " AND PO_Date >= CONVERT(DATETIME, '".$inicio." 00:00:00', 103) ";
		}
		if($termino != "" && $termino != null) {
			$sql.= " AND PO_Date <= CONVERT(DATETIME, '".$termino." 23:59:59', 103) ";
		}
	}
	//echo $sql;
	$file = fopen("sql.txt",'w+');
	fwrite($file,$sql);
	fclose($file);
	
	$result = sqlsrv_query($conexion, $sql);
		
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
			//print_r($row);
		//$row = array_map("eliminaNull",$row);
		//	$row[5] = str_replace("null","",$row[5]);
		//$row["fecha_oc"] = date("d-m-Y", strtotime($row["fecha_oc"])); 
		//$row["fecha_ack"] = date("d-m-Y", strtotime($row["fecha_ack"]));
		array_push($data,$row);
	}
	//print_r($data);
	sqlsrv_close($conexion);
	echo json_encode($data);
}

if($accion == "cargaDatosOC")
{
	$nro_oc   = $_REQUEST["nro_oc"];
	
	$conexion = conectar_srvdev();
	$sql = " SELECT DISTINCT Po_Item as pos_oc, Po_PartNumber as nro_parte, Po_Description as descripcion, "; 
	$sql.= " Po_Quantity as cantidad, PO_Unit as unidad, PO_PriceOrig as precio, PO_Money as moneda, ";
	$sql.= " ACK_PartNumber as nro_parte_ack, ACK_Quantity as cantidad_ack, ACK_Unit as unidad_ack, ";
	$sql.= " Po_Price as precio_ack, convert(char(10),ACK_Date,103) as fecha_promesa, ACK_Type as tipo_ack ";
	$sql.= " FROM PO_DETAIL WHERE PO_Number = '$nro_oc' ";
	
	$result = sqlsrv_query($conexion, $sql);
	$data = array();
	
	while( $row = sqlsrv_fetch_array($result) )
	{
		//$row = array_map("eliminaNull",$row);	
		//print_r($row);
		$row["precio_ack"]    = str_replace(",",".",$row["precio_ack"]);
		$row["precio_ack"]    = floatval($row["precio_ack"]);		
		$row["precio"]    = str_replace(",",".",$row["precio"]);
		$row["precio"]    = floatval($row["precio"]);		
		$row["dif_precio"]    = calc_dif_precio($row["precio"], $row["precio_ack"]);
		$row["dif_cantidad"]  = calc_dif_cantidad($row["cantidad"], $row["cantidad_ack"]); 
		$row["dif_nro_parte"] = calc_dif_nparte($row["nro_parte"], $row["nro_parte_ack"]); 
		$row["fecha_promesa"] = str_replace('/','-',$row["fecha_promesa"]);//date("d-m-Y", strtotime($row["fecha_promesa"]));
		$row["modificada"] 	  = calc_modificada($row["dif_precio"],$row["dif_cantidad"],$row["dif_nro_parte"]);
		$row["precio_ackNumber"] = str_replace(",",".",$row["precio_ack"]);
		array_push($data,$row);
	}
	//print_r($data);
	sqlsrv_close($conexion);
	echo json_encode($data);
}

if($accion == "cargaDetalleOCModal")
{
	$nro_oc     = $_REQUEST["nro_oc"];
	$Sociedad   = $_REQUEST["Sociedad"];
	
	$conexion = conectar_srvdev();
	
	$sql = " SELECT DISTINCT Po_Item as pos_oc, PO_PriceOrig as precio, Po_Price as precio_ack, 
			 Po_Quantity as cantidad, ACK_Quantity as cantidad_ack, PO_PartNumber as nro_parte, ACK_PartNumber as nro_parte_ack,
			 (SELECT tolerancia FROM tolerancia WHERE bukrs='$Sociedad') as tolerancia  
			 FROM PO_DETAIL WHERE PO_Number = '$nro_oc' 
			 ORDER BY PO_Item, PO_PriceOrig, PO_Quantity, PO_PartNumber ";
	$result = sqlsrv_query($conexion, $sql);
	
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		$row = array_map("eliminaNull",$row);
		$row["precio"] = str_replace(",",".",$row["precio"]);
		$row["precio"] = floatval($row["precio"]);
		$row["precio_ack"] = str_replace(",",".",$row["precio_ack"]);
		$row["precio_ack"] = floatval($row["precio_ack"]);
		$row["cantidad_ack"] = str_replace(",",".",$row["cantidad_ack"]);
		$row["cantidad_ack"] = floatval($row["cantidad_ack"]);
		$row["cantidad"] = str_replace(",",".",$row["cantidad"]);
		$row["cantidad"] = floatval($row["cantidad"]);
		$row["tolerancia"] = str_replace(",",".",$row["tolerancia"]);
		$row["tolerancia"] = floatval($row["tolerancia"]);
		if($row["cantidad"] == "")
			$row["cantidad"] = '0';
		array_push($data,$row);
	}
	sqlsrv_close($conexion);
	echo json_encode($data);
}

function trataPrecio($precio)
{
	$price = "";
	$price = round($precio, 2);
	if( strpos( $price, '.' ) == true ) {
		$price = str_replace(".",",",$price);
	}
	return $price;
}

function calc_dif_precio($precio, $precio_ack)
{
	$dif 		= '0.00';
	$preciorg 	= '0.00';
	$precioack 	= '0.00';
	$result 	= "";

	if($precio != "") {
		$preciorg = $precio;
	}	
	if($precio_ack != "") {
		$precioack = $precio_ack;
	}
	// verifica que no sean 'cero' ///
	if($preciorg == '0' )
		$preciorg = '0.00';
	if($precioack == '0' )
		$precioack = '0.00';
	//////////////////////////////////////////////
	$precioack = replace($precioack,'-','');
	$preciorg  = replace($preciorg,'-','');
	//////////////////////////////////////////////
	$precioack = replace($precioack,',', '.');
	$preciorg  = replace($preciorg,',','.');
	// verifica mayor y menor ////////////////////
	if($preciorg == $precioack) 
	{
		$dif = '0.00';
	}
	else if($preciorg > $precioack)
	{
		$preciorg = $preciorg-$precioack;
		if($precioack == '0.00') {
			$precioack = '1.00';
		}
		$dif = round( ( ($preciorg*'100.00') / $precioack ), 2 );
		$result = "- ";
	} 
	else if($preciorg < $precioack) 
	{
		$precioack = $precioack - $preciorg;
		if($preciorg == '0.00') {
			$preciorg = '1.00';
		}
		$dif = round( ( ($precioack*'100.00') / $preciorg ), 2 );
		$result = "+";
	} else {}
	
	if($dif<'0.00')
		$result='';
		
	$dif = trataPrecio($dif);	
	$dif = $result.$dif.'%';
	
	return $dif;
}

function replace($val,$sim1,$sim2)
{
	$res = $val;
	if( strpos( $val, $sim1 ) == true ) {
		$res = str_replace($sim1,$sim2,$val);
	}
	return $res;
}

function calc_dif_cantidad($cantidad, $cantidad_ack)
{
	$dif 		= 0;
	$cant 		= 0;
	$cantack 	= 0;
	
	if($cantidad != "")
		$cant = (int)$cantidad;
	if($cantidad_ack != "")
		$cantack = (int)$cantidad_ack;

	if($cant > $cantack)
		$dif = '+ '.($cant-$cantack);
	if($cant < $cantack)
		$dif = '- '.($cantack-$cant);
	
	return $dif;
}

function calc_dif_nparte($nparte, $nparteack)
{
	$dif = false;
	if($nparte != $nparteack)
		$dif = true;
	return $dif;
}

function calc_modificada($difprecio, $difcantidad, $difparte)
{
	//Quitar simbolos
	$quitar = array("-", "%","+");
	
	$difprecio = str_replace($quitar, "", $difprecio);
	$difprecio = str_replace(",",".",$difprecio);
	
	$difcantidad = str_replace($quitar, "", $difcantidad);
	$difcantidad = str_replace(",",".",$difcantidad);

	$difprecio=floatval($difprecio);
	$difcantidad=floatval($difcantidad);
	
	$dif=8;

	if($difprecio == 0 && $difcantidad == 0 && $difparte == false)
		{
			$dif = 1;
		}
	if($difprecio != 0 || $difcantidad != 0 || $difparte == true)
		{
			$dif = 2;
		}
	if($difprecio != 0 && $difcantidad == 0 && $difparte == true)
		{
			$dif = 3;
		}
	return $dif;
		
}
?>