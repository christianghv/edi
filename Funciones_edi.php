<?php 
	function FormatoNumber($Valor)
	{
		return number_format($Valor,0, ',', '.');
	}
	function FormatoCeros($Valor,$Ceros)
	{
		return sprintf("%'0".$Ceros."d\n", $Valor);
	}
		//Funciones
	function getEDI810_InvoiceHeader()
	{
		$EDI810_InvoiceHeader=array(
							'InvoiceNumber'=>'',
							'InvoiceDate'=>'',
							'InvoiceCurrency'=>'',
							'InvoiceNetValue'=>'',
							'InvoiceGrossValue'=>'',
							'InvoiceGastos'=>'0',
							'InvoiceVendor'=>'',
							'Sociedad'=>'');
		return $EDI810_InvoiceHeader;
	}
	function getEDI855_PO_HEADER()
	{
		$EDI855_PO_HEADER=array(
							'PO_Number'=>'',
							'PO_Date'=>'',
							'ACK_Date'=>'',
							'PO_Items'=>'',
							'PO_ShipTo'=>'',
							'PO_Sociedad'=>'');
		return $EDI855_PO_HEADER;
	}
	function getEDI855_PO_DETAIL()
	{
		$EDI855_PO_DETAIL=array(
							'PO_Number'=>'',
							'PO_Item'=>'',
							'PO_PartNumber'=>'',
							'PO_Description'=>'',
							'PO_Quantity'=>'',
							'PO_Unit'=>'',
							'PO_Price'=>'',
							'PO_Money'=>'',
							'ACK_PartNumber'=>'',
							'ACK_Date'=>'',
							'ACK_Quantity'=>'',
							'ACK_Unit'=>'',
							'ACK_Type'=>''
							);
		return $EDI855_PO_DETAIL;
	}
	function getEDI810_InvoiceDetail()
	{
		$EDI810_InvoiceHeader=array(
							'InvoiceNumber'=>'',
							'InvoicePosition'=>'',
							'PONumber'=>'',
							'POPosition'=>'',
							'ProductID'=>'',
							'ProductDesciption'=>'',
							'ProductMeasure'=>'',
							'ProductQuantity'=>'',
							'PorductPrice'=>'',
							'PaisOrigen'=>''
							);
		return $EDI810_InvoiceHeader;
	}
	function getFechaFormateada($excelDate)
	{
		$stringDate = PHPExcel_Style_NumberFormat::toFormattedString($excelDate, 'DD-MM-YYYY');
		return $stringDate;
	}
	function getPartNumber($PartNumber)
	{
		$NuevoPartNumber='';
		$FinPartNumber=substr($PartNumber, 7, 2);
		if($FinPartNumber=='00')
		{
			$NuevoPartNumber=substr($PartNumber, 0, 7).'-00';
		}
		else
		{
			$NuevoPartNumber=$PartNumber;
		}
		return $NuevoPartNumber;
	}
	function getEDI855_PO_DETAIL_Faltante()
	{
		$EDI855_PO_DETAIL_Faltante=array(
							'PO_Description'=>'',
							'PO_Price'=>'',
							'PO_Money'=>'',
							'ACK_PartNumber'=>'',
							'ACK_Date'=>'',
							'ACK_Quantity'=>'',
							'ACK_Unit'=>'',
							'ACK_Type'=>'',
							'ACK_Type'=>''
							);
		return $EDI855_PO_DETAIL_Faltante;
	}
	function DatosDetalleOC($PO_Position,$Datos)
	{
		$DatosFaltantes=getEDI855_PO_DETAIL_Faltante();
		foreach ($Datos as $dato) {
			if(trim($dato['EBELP'])==trim($PO_Position))
			{
				$DatosFaltantes['ACK_PartNumber']=$dato['MATNR'];
				$DatosFaltantes['ACK_Unit']=$dato['MEINS'];
				$DatosFaltantes['ACK_Quantity']=$dato['MENGE'];
				$DatosFaltantes['PO_Price']=$dato['NETPR'];
				$DatosFaltantes['PO_Description']=$dato['TXZ01'];
				$DatosFaltantes['PO_Money']=$dato['WAERS'];
			}
		}
		return $DatosFaltantes;
	}
?>