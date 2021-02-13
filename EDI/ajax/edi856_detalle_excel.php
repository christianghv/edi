<?php
$sociedad = $_REQUEST['sociedad'];
$nro_oc = $_REQUEST['nro_oc'];
$inicio= $_REQUEST['inicio'];
$termino = $_REQUEST['termino'];
$factura = $_REQUEST['factura'];

include("../../conect/conect.php");
include_once("SegAjax.php");
require_once('Writer.php');
	//////////// F O R M A T O //////////////////
	
		
	$nombrearchivo = "EDI856_DETALLE";
	$fecha = date("Ymd");
	$arc = $nombrearchivo.'_'.$fecha.'.xls';	
	$workbook = new Spreadsheet_Excel_Writer($arc);
	
	//			$workbook = new Spreadsheet_Excel_Writer();
	$num_format =& $workbook->addFormat();
	$num_format->setNumFormat('###,##0.00');
	$num_format1 =& $workbook->addFormat();
	$num_format1->setNumFormat('###,##0');
	$format_bold =& $workbook->addFormat();
	$format_bold->setBold();
	$format_bold->setBorder(2);
	$format_bold->setFgColor(23);
	$format_bold->setTextWrap();
	///////////////////////////////////////////
	$verde =& $workbook->addFormat();
	$verde->setFgColor('green');
	$verde->setBorder(1);
	$rojo =& $workbook->addFormat();
	$rojo->setFgColor('red');
	$rojo->setBorder(1);
	$amarillo =& $workbook->addFormat();
	$amarillo->setFgColor('yellow');
	$amarillo->setBorder(1);
	$celda =& $workbook->addFormat();
	$celda->setBorder(1);
	///////////////////////////////////////////
	$nombrearchivo = "EDI856_DETALLE";
	$fecha = date("Ymd"); 
	//$workbook->send($arc);
	//////////////////////////////////////////	
	$worksheet =& $workbook->addWorksheet('CABECERA');
	//$worksheet->write(0, 0, 'INFORME CONFIG. LIMITES X ELEMENTO' ,$format_bold);
	
	//////////////////// CABECERAS /////////////////////////////////////////////
	$worksheet->write(0, 0, 'Nro Factura', $format_bold);
	$worksheet->write(0, 1, 'Fecha Despacho', $format_bold);
	$worksheet->write(0, 2, utf8_decode('Nro. Camión'),$format_bold );
	$worksheet->write(0, 3, 'Urgencia',$format_bold );
	$worksheet->write(0, 4, 'Grupo de compra',$format_bold );
	$worksheet->write(0, 5, 'Nro Parte',$format_bold );
	$worksheet->write(0, 6, 'Orden de Compra',$format_bold );
	$worksheet->write(0, 7, 'PO Position', $format_bold);
	$worksheet->write(0, 8, utf8_decode('Unidad Medición'), $format_bold);
	$worksheet->write(0, 9, 'Tracking Number', $format_bold);
	$worksheet->write(0, 10, 'Cantidad Despachada', $format_bold);
	$worksheet->write(0, 11, 'Bulto', $format_bold);
	$data = array();
	$data = getDataExcel($sociedad, $nro_oc, $inicio, $termino, $factura);
	$fil = 1;
	foreach($data as $registro)
	{	
		//////////// VALORES CABECERA //////////////////////////////////////////////
		
		$InvoiceNumber = str_replace("'", "", "".$registro["nro_factura"]);
		
		$worksheet->writeString($fil, 0, $InvoiceNumber, $celda);
		$worksheet->writeString($fil, 1, $registro["segm_date"], $celda);
		$worksheet->writeString($fil, 2, $registro["ship_transname"], $celda);
		$worksheet->writeString($fil, 3, $registro["UN"], $celda);
		$worksheet->writeString($fil, 4, $registro["Zona"], $celda);
		$worksheet->writeString($fil, 5, $registro["nro_parte"], $celda);
		$worksheet->writeString($fil, 6, $registro["orden_compra"], $celda);
		$worksheet->writeString($fil, 7, $registro["it_poPosition"], $celda);
		$worksheet->writeString($fil, 8, $registro["unidad_medida"], $celda);
		$worksheet->writeString($fil, 9, $registro["tracking_number"], $celda);
		$worksheet->writeString($fil, 10, $registro["cantidad_despachada"], $celda);
		$worksheet->writeString($fil, 11, $registro["packing_slip"], $celda);
		$fil++;	
	}
	$workbook->close();
	
	header("location: $arc");			
////////////////// FUNCIONES //////////////////////
function getDataExcel($sociedad, $nro_ocs, $inicio, $termino, $facturas)
{
	$sql = " SELECT de.segm_Id as nro_factura, de.it_prodid as nro_parte, de.it_unitshiped as cantidad_despachada, 
			de.it_unitmeasurement as unidad_medida, de.it_po as orden_compra,de.it_poPosition, de.it_refnumber as tracking_number, 
			de.it_packingsleep as packing_slip,  
			CONVERT(VARCHAR(10),he.segm_date,105) as segm_date, he.ship_transname, he.ship_trailernumber 
			FROM [856HEADER] he 
			INNER JOIN [856DETAIL] de ON he.segm_id = de.segm_Id 
			WHERE 1=1 ";
	
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
	
	$conexion = conectar_srvdev();
	$result = sqlsrv_query($conexion, $sql);
	$retorno = array();			
    while ($row = sqlsrv_fetch_array($result))
	{
		$registro 						= array();	
		$registro["nro_factura"] 		= "'".$row["nro_factura"]."'";
		$registro["nro_parte"] 			= $row["nro_parte"];
		$registro["packing_slip"] 		= $row["packing_slip"];
		$registro["orden_compra"] 		= $row["orden_compra"];
		$UnZona= substr($row["orden_compra"].'', 10);
		$registro["UN"] 				= substr($UnZona, 0,2);
		$registro["Zona"] 				= substr($UnZona, 2);
		$registro["unidad_medida"] 		= $row["unidad_medida"];
		$registro["tracking_number"]	= $row["tracking_number"];
		$registro["cantidad_despachada"]= $row["cantidad_despachada"];
		$registro["segm_date"] 			= $row["segm_date"];
		$registro["ship_transname"] 	= $row["ship_transname"];
		$registro["ship_trailernumber"] = $row["ship_trailernumber"];
		$registro["it_poPosition"]		= $row["it_poPosition"];
		
		
		$retorno[] = $registro;
	}
	sqlsrv_close($conexion);
	return $retorno;
}

?>


