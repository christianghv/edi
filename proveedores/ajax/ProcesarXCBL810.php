<?php
	include_once("SegAjax.php");
	function ProcesarXML810($rutaArchivo, $sociedadIngresada)
	{
	$respuesta="";
    $xml = simplexml_load_file($rutaArchivo); //declaramos la ubicacion del XML

	$sociedadEnviadaParaSubir=$sociedadIngresada;
	//$json = json_encode($xml);
	//$jsonDec=json_decode($json);
	//echo $jsonDec;
	//print_r($jsonDec);
	$Facturas=array();
	//810 header
	$H_NumeroFactura="";
	$H_proveedor="";
	$H_sociedad="";
	$H_currency="";
	$H_FechaFactura="";
	$H_MontoNeto="";
	$H_GrossValue="";
	$H_MontoTotal="";
	
	//810 detail
	$Detalle_Factura=array();
	
	$P_Cantidad=0;
	$P_UnidadDeMedida="";
	$P_PrecioUnitario=0;
	$P_CodigoProducto="";
	$P_NumeroDeOC=0;
	$P_PO_position="";
	$P_descripcion="";
	$P_Correlativo="";
	$P_PaisOrigen="";
	
	//print_r($xml);
	
	foreach($xml->InvoiceHeader as $InvoiceHeader)
	{
		//print_r($InvoiceHeader);
		$H_NumeroFactura = $InvoiceHeader->InvoiceNumber->Reference->RefNum;
		$H_currency = $InvoiceHeader->InvoiceCurrency->Currency->CurrencyCoded;
		$dateXCBL=$InvoiceHeader->InvoiceIssueDate;
		
		//Se Obtendra fecha y reemplazara T
		$reemplazar = array("T");
		$dateXCBL = str_replace($reemplazar, " ", $dateXCBL);		
		$date = strtotime($dateXCBL);
		$H_FechaFactura= date('Ymd H:i:s', $date);
		$H_sociedad=$InvoiceHeader->InvoiceParty->BuyerParty->Party->PartyID->Identifier->Ident;
	}//Fin foreach InvoiceHeader
	
	foreach($xml->InvoiceSummary as $InvoiceSummary)
	{
		$H_MontoNeto=$InvoiceSummary->InvoiceTotals->NetValue->MonetaryValue->MonetaryAmount;
		$H_GrossValue=$InvoiceSummary->InvoiceTotals->GrossValue->MonetaryValue->MonetaryAmount;
	}
	
	
	return "";
	}//Fin funcion recorrer xml
	
	$respuestaIngreso=ProcesarXML810('archivos/xcbl/Invoice-0793-21890.xml','3002');
	echo $respuestaIngreso;
	
?>