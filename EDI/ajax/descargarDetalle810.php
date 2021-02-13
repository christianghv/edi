<?php
	$NumeroFactura = $_POST["Desc_invoiceNumber"];	
	
	//require_once('../../plugins/excel/Spreadsheet/Writer.php');
	require_once('Writer.php');
	$JsonDetail=$_POST["Desc_jsonDetalle"];
	$ArrayDetail=json_decode($JsonDetail);	
	
	//////////// F O R M A T O //////////////////
	$nombrearchivo = $NumeroFactura;
	$fecha = date("Ymd");
	$arc = $nombrearchivo.'_'.$fecha.'.xls';
	$workbook = new Spreadsheet_Excel_Writer($arc);
	//$workbook = new Spreadsheet_Excel_Writer();
	$num_format =& $workbook->addFormat();
	$num_format->setNumFormat('###,##0.00');
	$num_format1 =& $workbook->addFormat();
	$num_format1->setNumFormat('###');
	
	$textF =& $workbook->addFormat();
	$textF->setNumFormat('###');
	
	$nombrearchivo = "EDI810_DETALLE";
	//$fecha = date("Ymd"); 
	if($NumeroFactura=="")
	{
		$NumeroFactura='InvoiceNumber';
	}
	$workbook->send($NumeroFactura.'.xls');
	
	$worksheet =& $workbook->addWorksheet('CABECERA');
	
	//////////////////// CABECERAS /////////////////////////////////////////////
	$worksheet->write(0, 0, 'Invoice Number');
	$worksheet->write(0, 1, 'Invoice Position');
	$worksheet->write(0, 2, 'PO Number');
	$worksheet->write(0, 3, 'PO Position');
	$worksheet->write(0, 4, 'Product ID');
	$worksheet->write(0, 5, 'Product Description');
	$worksheet->write(0, 6, 'Product Measure');
	$worksheet->write(0, 7, 'Product Quantity');
	$worksheet->write(0, 8, 'Product Price');
	$worksheet->write(0, 9, 'Pais de Origen');
	
	$fil = 1;
	
	foreach($ArrayDetail->ArrayDetalle as $registro)
	{	
		//////////// VALORES CABECERA //////////////////////////////////////////////
		$worksheet->write($fil, 0, $NumeroFactura);
		$worksheet->writeString($fil, 1, $registro->InvoicePosition);
		$worksheet->writeString($fil, 2, $registro->PONumber,$num_format1);
		$worksheet->write($fil, 3, $registro->POPosition);
		$worksheet->write($fil, 4, $registro->ProductID);
		$worksheet->write($fil, 5, $registro->ProductDesciption);
		$worksheet->write($fil, 6, $registro->ProductMeasure);
		$worksheet->write($fil, 7, $registro->ProductQuantity);
		$worksheet->write($fil, 8, $registro->PorductPrice);
		$worksheet->write($fil, 9, $registro->PaisOrigen);
		$fil++;	
	}
	
	$workbook->close();
	header("location: $arc");
?>