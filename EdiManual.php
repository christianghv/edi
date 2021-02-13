<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/plugins/PHPExcel/Classes/PHPExcel.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/EDI/Funciones_edi.php');
	ini_set('memory_limit', '512M');
	require_once($_SERVER['DOCUMENT_ROOT'].'/EDI/ajax/ws_buscarOC_855.php');
	
	$sFileName=$_SERVER['DOCUMENT_ROOT'].'/EDI/ajax/archivos/carga_masiva/EDIFINAL.xlsx';
	
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	$objReader->setReadDataOnly(true);

	$objPHPExcel = $objReader->load($sFileName);
	
	//Recorriendo Hoja
	$objPHPExcel->setActiveSheetIndex(0);
	$objWorksheet = $objPHPExcel->getActiveSheet();
	$highestRow = $objWorksheet->getHighestRow(); 
	
	$FacturaActual = "";
	$DetalleFacturaActual= array();
	$Detalle855= array();
	
	//810
	$EDI810_InvoiceHeader=getEDI810_InvoiceHeader();
	$EDI810_InvoiceDetail=getEDI810_InvoiceDetail();
	//855
	$EDI855_PO_HEADER=getEDI855_PO_HEADER();
	$EDI855_PO_DETAIL=getEDI855_PO_DETAIL();
	
	$EDI855Cargado=array();
	
	echo "$UrlWSDespi,$PuertoWSDespi,$usuarioWSDespi,$contrasenaWSDespi <br /><br />";
	
	$WS_BuscarOC_855 = new WS_BuscarOC_855($UrlWSDespi,$PuertoWSDespi,$usuarioWSDespi,$contrasenaWSDespi);
							
	for ($row = 1; $row < $highestRow; $row++) {
		if(trim($FacturaActual)!=trim($objWorksheet->getCellByColumnAndRow(0, $row)->getValue()))
		{
			//Ingresando factura anterior
			
			//Reiniciando variables
			
			//Nueva factura 
			//Cabecera EDI 810
			$FacturaActual=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
			$EDI810_InvoiceHeader['InvoiceNumber']=$FacturaActual;
			$EDI810_InvoiceHeader['InvoiceDate']=getFechaFormateada($objWorksheet->getCellByColumnAndRow(2, $row)->getValue());
			$EDI810_InvoiceHeader['InvoiceCurrency']=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
			$EDI810_InvoiceHeader['InvoiceVendor']=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
			$EDI810_InvoiceHeader['Sociedad']=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
		}
		//Ingresando detalle EDI 810
		$EDI810_InvoiceDetail['InvoiceNumber']=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
		$EDI810_InvoiceDetail['InvoicePosition']=FormatoCeros($objWorksheet->getCellByColumnAndRow(5, $row)->getValue(),5);
		$EDI810_InvoiceDetail['PONumber']=$objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
		$EDI810_InvoiceDetail['POPosition']=FormatoCeros($objWorksheet->getCellByColumnAndRow(7, $row)->getValue(),5);
		$EDI810_InvoiceDetail['ProductID']=FormatoCeros($objWorksheet->getCellByColumnAndRow(8, $row)->getValue(),9);
		$EDI810_InvoiceDetail['ProductDesciption']=$objWorksheet->getCellByColumnAndRow(9, $row)->getValue();
		$EDI810_InvoiceDetail['ProductMeasure']=$objWorksheet->getCellByColumnAndRow(10, $row)->getValue();
		$EDI810_InvoiceDetail['ProductQuantity']=$objWorksheet->getCellByColumnAndRow(11, $row)->getValue();
		$EDI810_InvoiceDetail['PorductPrice']=$objWorksheet->getCellByColumnAndRow(12, $row)->getValue();
		$EDI810_InvoiceDetail['PaisOrigen']=$objWorksheet->getCellByColumnAndRow(13, $row)->getValue();
		array_push($DetalleFacturaActual,$EDI810_InvoiceDetail);
		
		//Verificar si el PO Number no ha sido ingresado
		
		$EDI855_PO_HEADER=getEDI855_PO_HEADER();
		$EDI855_PO_DETAIL=getEDI855_PO_DETAIL();
		
		//Agregando EDI 855
		//Cabecera EDI 855 si no esta registado el PO Number
		$EDI855_PO_HEADER=getEDI855_PO_HEADER();
		$EDI855_PO_HEADER['PO_Number']=$objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
		$EDI855_PO_HEADER['PO_Date']='FALTA';
		$EDI855_PO_HEADER['ACK_Date']='FALTA';
		$EDI855_PO_HEADER['PO_ShipTo']='FALTA';
		$EDI855_PO_HEADER['PO_Sociedad']=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
		
		//Detalle EDI 855
		$DatosOC855=$WS_BuscarOC_855->WS_detalleordencompra(substr($EDI855_PO_HEADER['PO_Number'],0,10));
		$EDI855_PO_HEADER['PO_Items']=count($DatosOC855);
		$DatosFaltantes=DatosDetalleOC($EDI810_InvoiceDetail['POPosition'],$DatosOC855);
		//Cargando datos de 855
		$EDI855_PO_DETAIL['PO_Number']=$EDI855_PO_HEADER['PO_Number'];
		$EDI855_PO_DETAIL['PO_Item']=$EDI810_InvoiceDetail['POPosition'];
		$EDI855_PO_DETAIL['PO_PartNumber']=getPartNumber($EDI810_InvoiceDetail['ProductID']);
		$EDI855_PO_DETAIL['PO_Quantity']=$EDI810_InvoiceDetail['ProductQuantity'];
		$EDI855_PO_DETAIL['PO_Unit']=$EDI810_InvoiceDetail['ProductMeasure'];
		$EDI855_PO_DETAIL['PO_Money']=$DatosFaltantes['PO_Money'];
		$EDI855_PO_DETAIL['ACK_PartNumber']=$DatosFaltantes['ACK_PartNumber'];
		$EDI855_PO_DETAIL['ACK_Date']='FALTA';
		$EDI855_PO_DETAIL['ACK_Quantity']=$DatosFaltantes['ACK_Quantity'];
		$EDI855_PO_DETAIL['ACK_Unit']=$DatosFaltantes['ACK_Unit'];
		$EDI855_PO_DETAIL['PO_Price']=$DatosFaltantes['PO_Price'];
		$EDI855_PO_DETAIL['PO_Description']=$DatosFaltantes['PO_Description'];
		$EDI855_PO_DETAIL['PO_Money']=$DatosFaltantes['PO_Money'];
		
		array_push($Detalle855,$EDI855_PO_DETAIL);
	}
	//Fin de hoja
	print_r($Detalle855);
	die();
			
	//Limpiar Sql
	//$Sql810 = str_replace("'NULL'", "NULL", $Sql810);
?>