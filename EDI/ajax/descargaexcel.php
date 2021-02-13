<?
error_reporting(0);
$sociedad = $_REQUEST['sociedad'];
$nro_oc = $_REQUEST['nro_oc'];
$inicio= $_REQUEST['inicio'];
$termino = $_REQUEST['termino'];
include("../../conect/conect.php");
include_once("SegAjax.php");
require_once('Writer.php');
	
	//////////// F O R M A T O //////////////////
	$nombrearchivo = "EDI855_DETALLE";
	$fecha = date("Ymd"); 
	//$arc = 'C:\inetpub\wwwroot\EDI20\EDI\ajax\'.;
	$arc = $nombrearchivo.'_'.$fecha.'.xls';
	$workbook = new Spreadsheet_Excel_Writer($arc);
	$num_format = $workbook->addFormat();
	$num_format->setNumFormat('###,##0.00');
	$num_format1 = $workbook->addFormat();	
	$num_format1->setNumFormat('###,##0');
	$format_bold = $workbook->addFormat();
	$format_bold->setBold();
	$format_bold->setBorder(2);
	$format_bold->setFgColor(23);
	$format_bold->setTextWrap();
	///////////////////////////////////////////
	$verde = $workbook->addFormat();
	$verde->setFgColor('green');
	$verde->setBorder(1);
	$rojo = $workbook->addFormat();
	$rojo->setFgColor('red');
	$rojo->setBorder(1);
	$amarillo = $workbook->addFormat();
	$amarillo->setFgColor('yellow');
	$amarillo->setBorder(1);
	$celda = $workbook->addFormat();
	//$celda->setFgColor(22);
	$celda->setBorder(1);
	//$celda->setTextWrap();
	///////////////////////////////////////////
	
	//$workbook->send($nombrearchivo.'_'.$fecha.'.xls');
	//////////////////////////////////////////	
	$worksheet = $workbook->addWorksheet('DETALLE OC');
	//$worksheet->write(0, 0, 'INFORME CONFIG. LIMITES X ELEMENTO' ,$format_bold);
	
	//////////////////// CABECERAS /////////////////////////////////////////////
	$worksheet->write(0, 0, 'Nro OC', $format_bold);
	$worksheet->write(0, 1, 'Fecha OC', $format_bold);
	$worksheet->write(0, 2, 'Fecha ACK', $format_bold);
	$worksheet->write(0, 3, 'Pos OC', $format_bold);
	$worksheet->write(0, 4, 'Nro Parte', $format_bold);
	$worksheet->write(0, 5, utf8_decode('Descripción'),$format_bold );
	$worksheet->write(0, 6, 'Cantidad',$format_bold );
	$worksheet->write(0, 7, 'Unidad', $format_bold);
	$worksheet->write(0, 8, 'Precio', $format_bold);
	$worksheet->write(0, 9, 'Moneda', $format_bold);
	$worksheet->write(0, 10, 'Nro Parte ACK', $format_bold);
	$worksheet->write(0, 11, 'Cantidad ACK', $format_bold);		
	$worksheet->write(0, 12, 'Unidad ACK', $format_bold);
	$worksheet->write(0, 13, 'Precio ACK', $format_bold);		
	$worksheet->write(0, 14, 'Fecha Promesa', $format_bold);
	$worksheet->write(0, 15, 'Tipo ACK', $format_bold);		
	$worksheet->write(0, 16, 'Dif Precio', $format_bold);
	$worksheet->write(0, 17, 'Dif Cantidad', $format_bold);		
	$worksheet->write(0, 18, 'Dif Nro Parte', $format_bold);
	$worksheet->write(0, 19, 'Modificada', $format_bold);		
	$data = array();
	$data = getDataExcel($sociedad, $nro_oc, $inicio, $termino);
	$fil = 1;
	foreach($data as $registro)
	{	
		//echo "hola";
		//////////// VALORES CABECERA //////////////////////////////////////////////
		$worksheet->write($fil, 0, $registro["nro_oc"], $celda);
		$worksheet->write($fil, 1, $registro["fecha_oc"], $celda);
		$worksheet->write($fil, 2, $registro["fecha_ack"], $celda);
		$worksheet->write($fil, 3, $registro["pos_oc"], $celda);
		$worksheet->write($fil, 4, $registro["nro_parte"], $celda);
		$worksheet->write($fil, 5, $registro["descripcion"], $celda);
		$worksheet->write($fil, 6, $registro["cantidad"], $celda);
		$worksheet->write($fil, 7, $registro["unidad"], $celda);
		$worksheet->write($fil, 8, $registro["precio"], $celda);
		$worksheet->write($fil, 9, $registro["moneda"], $celda);
		$worksheet->write($fil, 10, $registro["nro_parte_ack"], $celda);
		$worksheet->write($fil, 11, $registro["cantidad_ack"], $celda);
		$worksheet->write($fil, 12, $registro["unidad_ack"], $celda);
		$worksheet->write($fil, 13, $registro["precio_ack"], $celda);
		$worksheet->write($fil, 14, $registro["fecha_promesa"], $celda);		
		$worksheet->write($fil, 15, $registro["tipo_ack"], $celda);
		$worksheet->write($fil, 16, $registro["dif_precio"], $celda);		
		$worksheet->write($fil, 17, $registro["dif_cantidad"], $celda);
		if($registro["dif_nro_parte"] == 'SI') {
			$worksheet->write($fil, 18, '', $rojo);	
		}
		if($registro["dif_nro_parte"] == 'NO') {			
			$worksheet->write($fil, 18, '', $verde);
		}
		if($registro["modificada"] == 1)
			$worksheet->write($fil, 19, '', $verde);	
		if($registro["modificada"] == 2)
			$worksheet->write($fil, 19, '', $amarillo);	
		if($registro["modificada"] == 3)
			$worksheet->write($fil, 19, '', $rojo);	
		$fil++;	
	}

		//$worksheet->write(1,0,'prueba'); 
		
	$workbook->close();
	
	header("location: $arc");
	
////////////////// FUNCIONES //////////////////////
function getDataExcel($sociedad, $nro_oc, $inicio, $termino)
{
		/*echo '<table width="100%%" border="1">
  <tr>
    <td>1</td>
    <td>2</td>
    <td>3</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>';*/
	$sql = " SELECT de.PO_Number as nro_oc,
	CONVERT(char(10),he.PO_Date, 103) as fecha_oc,
	CONVERT(char(10),he.ACK_Date, 103) as fecha_ack,
	de.PO_Item as pos_oc, de.PO_PartNumber as nro_parte, de.PO_Description as descripcion,
	de.PO_Quantity as cantidad, de.PO_Unit as unidad, de.PO_PriceOrig as precio, de.PO_Money as moneda, 
	ACK_PartNumber as nro_parte_ack, ACK_Quantity as cantidad_ack, ACK_Unit as unidad_ack, 
	de.ACK_PartNumber as nro_parte_ack, de.ACK_Quantity as cantidad_ack, de.ACK_Unit as unidad_ack, 
	de.Po_Price as precio_ack, 
	CONVERT(char(10),de.ACK_Date, 103) as fecha_promesa, de.ACK_Type as tipo_ack 
	FROM PO_HEADER he 
	INNER JOIN PO_DETAIL de ON he.PO_Number = de.PO_Number WHERE 1=1 ";
	
	if($sociedad != "")  
		$sql.= " AND he.PO_Sociedad = '".$sociedad."' ";
	
	if($nro_oc != '') 
	{
		$NumerosEncontrados=0;
		$sqlNumeros= " AND  SUBSTRING(he.PO_Number, 0, 11) in ( ";
		$arr_oc = explode('|',$nro_oc);
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
				$sql.= " AND he.PO_Date >= CONVERT(DATETIME, '".$inicio." 00:00:00', 103) ";
			}
			if($termino != "" && $termino != null) {
				$sql.= " AND he.PO_Date <= CONVERT(DATETIME, '".$termino." 23:59:59', 103) ";
			}
		}
	}
	else
	{
		if($inicio != "" && $inicio != null) {
				$sql.= " AND he.PO_Date >= CONVERT(DATETIME, '".$inicio." 00:00:00', 103) ";
		}
		if($termino != "" && $termino != null) {
			$sql.= " AND he.PO_Date <= CONVERT(DATETIME, '".$termino." 23:59:59', 103) ";
		}
	}
	
	$sql.= " ORDER BY de.PO_Number DESC ";
	
		$file = fopen("sql855.txt",'w+');
	fwrite($file,$sql);
	fclose($file);
	//echo $sql;
	$conexion = conectar_srvdev();
	$result = sqlsrv_query($conexion, $sql);
	$retorno = array();	
    while ($row = sqlsrv_fetch_array($result))
	{
		$registro 					= array();	
		$row["precio_ack"]   		= trataPrecio($row["precio_ack"]);	
		$registro["nro_oc"] 		= $row["nro_oc"];
		$registro["fecha_oc"] 		= $row["fecha_oc"];
		$registro["fecha_ack"] 		= $row["fecha_ack"];
		$registro["pos_oc"] 		= $row["pos_oc"];
		$registro["nro_parte"] 		= $row["nro_parte"];
		$registro["descripcion"] 	= $row["descripcion"];
		$registro["cantidad"] 		= $row["cantidad"];
		$registro["unidad"] 		= $row["unidad"];
		$registro["precio"] 		= $row["precio"];
		$registro["moneda"] 		= $row["moneda"];
		$registro["precio_ack"] 	= $row["precio_ack"];
		$registro["nro_parte_ack"] 	= $row["nro_parte_ack"];
		$registro["fecha_promesa"] 	= $row["fecha_promesa"];
		$registro["cantidad_ack"] 	= $row["cantidad_ack"];
		$registro["unidad_ack"] 	= $row["unidad_ack"];
		$registro["tipo_ack"] 		= $row["tipo_ack"];
		$registro["dif_precio"] 	= calc_dif_precio($row["precio"], $row["precio_ack"]);
		$registro["dif_cantidad"] 	= calc_dif_cantidad($row["cantidad"], $row["cantidad_ack"]);
		$registro["dif_nro_parte"] 	= calc_nro_parte($row["nro_parte"], $row["nro_parte_ack"]);
		$registro["modificada"]		= calc_modificada($row["dif_precio"], $row["dif_cantidad"], $row["dif_nro_parte"]);
		
		$retorno[] = $registro;
	}
	sqlsrv_close($conexion);
	return $retorno;
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
	
	
	if($dif < '0.00')
		$result = '';
		
	$dif = trataPrecio($dif);
	$dif = $result.$dif.'%';
	
	return $dif;
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

function calc_nro_parte($nro_parte, $nro_parte_ack)
{
	$dif = "SI";
	$nparte = trim($nro_parte);
	$nparteack = trim($nro_parte_ack);
	if($nparte == $nparteack)
		$dif = "NO";
		
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

function calc_modificada($difprecio, $difcantidad, $difparte)
{
	$dif = 0;
	
	if( ($difprecio == '+0.00%' || $difprecio == '-0.00%' || $difprecio == '0.00%') && ($difcantidad == '0' || $difcantidad == 0) && $difparte == 'NO' ){
		$dif = 1;
	}else if( ($difprecio != '+0.00%' && $difprecio != '-0.00%' && $difprecio != '0.00%') || ($difcantidad != '0' && $difcantidad != 0) || $difparte == 'SI' ){
		$dif = 2;
	}else if( ($difprecio != '+0.00%' && $difprecio != '-0.00%' && $difprecio != '0.00%') && ($difcantidad == '0' && $difcantidad == 0) && $difparte == 'SI' ){
		$dif = 3;
	}else {}
	return $dif;
		
}

?>


