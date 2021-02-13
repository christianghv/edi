<?php 
	
	//require_once($_SERVER['DOCUMENT_ROOT']."/funciones/fx_util.php");
	//require_once($_SERVER['DOCUMENT_ROOT'].'/EDI/funciones/Funciones_carga.php');
	//include('../../funciones/fx_util.php');
	include('Funciones_carga.php');
	//Funciones
	function getInvoiceNetValue($InvoiceHeader,$InvoiceNumber)
	{
		$Valor=0;
		$InvoiceHeader=array_reverse($InvoiceHeader);
		foreach ($InvoiceHeader as $EDI810) {
			if($EDI810['InvoiceNumber']==$InvoiceNumber)
			{
				$Valor=0+floatval($EDI810['InvoiceNetValue']);
				break;
			}
		}
		return $Valor;
	}
	function getInvoiceGastos($InvoiceHeader,$InvoiceNumber)
	{
		$Valor=0;
		$InvoiceHeader=array_reverse($InvoiceHeader);
		foreach ($InvoiceHeader as $EDI810) {
			if($EDI810['InvoiceNumber']==$InvoiceNumber)
			{
				$Valor=0+floatval($EDI810['InvoiceGastos']);
				break;
			}
		}
		return $Valor;
	}
	function updateTotalEDI810($InvoiceHeader,$InvoiceNumber,$TotalEdi810)
	{
		$Valor=0;
		$InvoiceHeader=array_reverse($InvoiceHeader);
		
		for ($i = 0; $i <= count($InvoiceHeader); $i++) {
			if($InvoiceHeader[$i]['InvoiceNumber']==$InvoiceHeader)
			{
				$InvoiceHeader[$i]['InvoiceNetValue']=$TotalEdi810;
				$InvoiceHeader[$i]['InvoiceGrossValue']=$TotalEdi810;
				break;
			}
		}
		
		return $InvoiceHeader;
	}
	function getEDI810_InvoiceHeader()
	{
		$EDI810_InvoiceHeader=array(
							'InvoiceNumber'=>'',
							'InvoiceDate'=>'',
							'InvoiceCurrency'=>'',
							'InvoiceNetValue'=>'0',
							'InvoiceGrossValue'=>'0',
							'InvoiceGastos'=>'0',
							'InvoiceVendor'=>'',
							'Sociedad'=>'');
		return $EDI810_InvoiceHeader;
	}
	function getEDI810_InvoiceDetail()
	{
		$EDI810_InvoiceDetail=array(
							'InvoiceNumber'=>'',
							'InvoicePosition'=>'',
							'PONumber'=>'',
							'POPosition'=>'',
							'ProductID'=>'',
							'ProductDesciption'=>'',
							'ProductMeasure'=>'',
							'ProductQuantity'=>'',
							'PorductPrice'=>'',
							'PaisOrigen'=>'',
							'idenBulto'=>'NULL'
							);
		return $EDI810_InvoiceDetail;
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
							'ACK_Unit'=>''
							);
		return $EDI855_PO_DETAIL;
	}
	
	function getEDI856_856HEADER()
	{
		$EDI855_PO_HEADER=array(
							'segm_id'=>'',
							'segm_date'=>'',
							'ship_unit'=>'',
							'ship_lading'=>'0',
							'ship_transport'=>'',
							'ship_transname'=>'',
							'ship_trailernumber'=>'NULL',
							'ship_cantBultos'=>'0'
							);
		return $EDI855_PO_HEADER;
	}
	function getEDI856_856DETAIL()
	{
		$EDI856_856DETAIL=array(
							'segm_Id'=>'',
							'it_prodid'=>'',
							'it_unitshiped'=>'',
							'it_unitmeasurement'=>'',
							'it_po'=>'',
							'it_refnumber'=>'',
							'it_packingsleep'=>'',
							'it_poPosition'=>'',
							'invoicePosition'=>'',
							'NET_WEIGHT'=>'',
							'GROSS_WEIGHT'=>''
							);
		return $EDI856_856DETAIL;
	}
	function getEDI856_856BULTO()
	{
		$EDI856_856BULTO=array(
							'idenBulto'=>'',
							'unidPeso'=>'',
							'volumen'=>'',
							'unidVolumen'=>'',
							'longitud'=>'',
							'ancho'=>'',
							'alto'=>'',
							'unidDimension'=>'',
							'fechaDespacho'=>'',
							'invNumber'=>''
							);
		return $EDI856_856BULTO;
	}
	function getEDI855_PO_DETAIL_Faltante()
	{
		$EDI855_PO_DETAIL_Faltante=array(
							'PO_Item'=>'',
							'PO_PartNumber'=>'',
							'PO_Unit'=>'',
							'PO_Quantity'=>'',
							'PO_Price'=>'',
							'PO_Description'=>'',
							'PO_Money'=>'',
							'it_unitmeasurement'=>''
							);
		
		return $EDI855_PO_DETAIL_Faltante;
	}
	function getFALTANTE_EDI856()
	{
		$FALTANTE_EDI856=array(
							'ProductMeasure'=>'',
							'POPosition'=>'',
							'PONumber'=>'',
							'InvoicePosition'=>'',
							'PO_Quantity'=>''
							);
		return $FALTANTE_EDI856;
	}
	function getDatosFaltantes_EDI856($Sociedad,$InvoiceNumber,$POPosition,$ProductID)
	{
		global $CONNECCION_UNICA_FUNCIONES_EDI;
		
		$query 	= " SELECT TOP 01 Cabecera.[InvoiceNumber],
						   DetFatura.[ProductMeasure],
						   DetFatura.[POPosition],
						   DetFatura.[PONumber],
						   DetFatura.[InvoicePosition],
						   DetFatura.[ProductQuantity]
					FROM [InvoiceHeader] Cabecera
					INNER JOIN [InvoiceDetail] DetFatura ON 
					DetFatura.InvoiceNumber=Cabecera.[InvoiceNumber] 
					WHERE Cabecera.[InvoiceNumber]='".ms_escape_string($InvoiceNumber)."' 
					AND Cabecera.Sociedad='".ms_escape_string($Sociedad)."' 
					AND CAST((CASE DetFatura.[POPosition] WHEN '' THEN 0 WHEN NULL THEN 0 ELSE DetFatura.[POPosition] END) AS DECIMAL(10, 0)) = CAST((CASE '".ms_escape_string($POPosition)."' WHEN '' THEN 0 WHEN NULL THEN 0 ELSE '".ms_escape_string($POPosition)."' END) AS DECIMAL(10, 0)) 
					AND DetFatura.[ProductID]='".ms_escape_string($ProductID)."';";
		//echo $query;
		
		$result = sqlsrv_query($CONNECCION_UNICA_FUNCIONES_EDI, $query);
		
		if (!$result) {
			$CONNECCION_UNICA_FUNCIONES_EDI = conectar_srvdev();
			$result = sqlsrv_query($CONNECCION_UNICA_FUNCIONES_EDI, $query);
		}
		
		$FALTANTE_EDI856=getFALTANTE_EDI856();

		while($row = sqlsrv_fetch_array($result) )
		{	
			$FALTANTE_EDI856['ProductMeasure']     = $row['ProductMeasure'];
			$FALTANTE_EDI856['POPosition']    	   = $row['POPosition'];
			$FALTANTE_EDI856['PONumber']    	   = $row['PONumber'];
			$FALTANTE_EDI856['InvoicePosition']    = $row['InvoicePosition'];
			$FALTANTE_EDI856['PO_Quantity']   	   = $row['ProductQuantity'];
			
		}
		
		return $FALTANTE_EDI856;
	}
	function DatosDetalleOC_PO_PartNumber($PO_PartNumber,$Datos)
	{
		$DatosFaltantes=getEDI855_PO_DETAIL_Faltante();
		foreach ($Datos as $dato) {
			if(trim($dato['MATNR'])==trim($PO_PartNumber))
			{
				$DatosFaltantes['PO_Item']=$dato['EBELP'];
				$DatosFaltantes['PO_PartNumber']=$dato['MATNR'];
				$DatosFaltantes['PO_Unit']=$dato['MEINS'];
				$DatosFaltantes['PO_Quantity']=$dato['MENGE'];
				$DatosFaltantes['PO_Price']=$dato['NETPR'];
				$DatosFaltantes['PO_Description']=$dato['TXZ01'];
				$DatosFaltantes['PO_Money']=$dato['WAERS'];
				$DatosFaltantes['it_unitmeasurement']=$dato['MEINS'];
			}
		}
		return $DatosFaltantes;
	}
	function DatosDetalleOC_PO_Item($PO_Item,$Datos)
	{
		$DatosFaltantes=getEDI855_PO_DETAIL_Faltante();
		foreach ($Datos as $dato) {
			if(trim($dato['EBELP'])==trim($PO_Item))
			{
				$DatosFaltantes['PO_Item']=$dato['EBELP'];
				$DatosFaltantes['PO_PartNumber']=$dato['MATNR'];
				$DatosFaltantes['PO_Unit']=$dato['MEINS'];
				$DatosFaltantes['PO_Quantity']=$dato['MENGE'];
				$DatosFaltantes['PO_Price']=$dato['NETPR'];
				$DatosFaltantes['PO_Description']=$dato['TXZ01'];
				$DatosFaltantes['PO_Money']=$dato['WAERS'];
				$DatosFaltantes['it_unitmeasurement']=$dato['MEINS'];
				break;
			}
		}
		return $DatosFaltantes;
	}
	function getItemOrdenDeCompra($PO_Number,$DatosOC_WS)
	{
		$EDI855_PO_HEADER=array(
							'PO_Number'=>$PO_Number,
							'DatosOC'=>$DatosOC_WS
							);
		return $EDI855_PO_HEADER;
	}
	function buscarOC855($PO_Number,$OC_855)
	{
		$DatosOC855=array();
		$Respuesta='Nok';
		
		if(isset($OC_855[$PO_Number]))
		{
			//Existe y se retornaran los valores de OC
			$Respuesta='Ok';
			$DatosOC855=$OC_855[$PO_Number];
		}
		
		$RespuestaDatos=array(
							'respuesta'=>$Respuesta,
							'DatosOC855'=>$DatosOC855
							);
		
		return $RespuestaDatos;
	}
	function IngresarCabeceraEDI810_Manual($InvoiceHeader)
	{
		$borrarQuery=" delete from [InvoiceHeader] where [InvoiceNumber]='".ms_escape_string($InvoiceHeader['InvoiceNumber'])."';";
		
		$borrarQuery.=" delete from [InvoiceDetail] where [InvoiceNumber]='".ms_escape_string($InvoiceHeader['InvoiceNumber'])."';";

		
		$InvoiceDate=FechaCompletaFormateada($InvoiceHeader['InvoiceDate']);
		
		//Borrar query
		$query="Insert into [InvoiceHeader]([InvoiceNumber],[InvoiceDate],[InvoiceCurrency],[InvoiceNetValue],[InvoiceGrossValue],[InvoiceGastos],[InvoiceVendor],[Sociedad]) 
               values('".ms_escape_string($InvoiceHeader['InvoiceNumber'])."','".ms_escape_string($InvoiceDate)."','".ms_escape_string($InvoiceHeader['InvoiceCurrency'])."',
			  '".ms_escape_string($InvoiceHeader['InvoiceNetValue'])."','".ms_escape_string($InvoiceHeader['InvoiceGrossValue'])."', 
              '".ms_escape_string($InvoiceHeader['InvoiceGastos'])."','".ms_escape_string($InvoiceHeader['InvoiceVendor'])."','".ms_escape_string($InvoiceHeader['Sociedad'])."');";
		
		ActualizarBD($borrarQuery);
		ActualizarBD($query);
		
		//return $borrarQuery.'<br />'.$query.'<br /><br />';
		return 'EDI810 '.$InvoiceHeader['InvoiceNumber'].' ingresado';
	}
	function IngresarDetalleEDI810_Manual($InvoiceDetail)
	{	
		$query="Insert into InvoiceDetail (InvoiceNumber, InvoicePosition,ProductID, ProductDesciption, ProductMeasure, 
											ProductQuantity, PorductPrice, PONumber, PaisOrigen,POPosition,idenBulto)  
				values('".ms_escape_string($InvoiceDetail['InvoiceNumber'])."','".ms_escape_string($InvoiceDetail['InvoicePosition'])."',
				'".ms_escape_string($InvoiceDetail['ProductID'])."','".ms_escape_string($InvoiceDetail['ProductDesciption'])."',
				'".ms_escape_string($InvoiceDetail['ProductMeasure'])."','".ms_escape_string($InvoiceDetail['ProductQuantity'])."',
				'".ms_escape_string($InvoiceDetail['PorductPrice'])."','".ms_escape_string($InvoiceDetail['PONumber'])."',
				'".ms_escape_string($InvoiceDetail['PaisOrigen'])."','".ms_escape_string($InvoiceDetail['POPosition'])."','".ms_escape_string($InvoiceDetail['idenBulto'])."');";
		
		//ActualizarBD($borrarQuery);
		ActualizarBD($query);
		
		//return $borrarQuery.'<br /><br />'.$query;
		return 'EDI810 detalle de '.$InvoiceDetail['InvoiceNumber'].' con posición '.$InvoiceDetail['InvoicePosition'].' ingresado';
	}
	function GenerarPosicionesEDI810($InvoiceHeader)
	{
		$QueryGenerar=" EXEC [SP_INVOICEPOSITION_810] '".ms_escape_string($InvoiceHeader['InvoiceNumber'])."'";
		
		ActualizarBD($QueryGenerar);
		
		//return $borrarQuery.'<br />'.$query.'<br /><br />';
		return 'EDI810 '.$InvoiceHeader['InvoiceNumber'].' posiciones generadas';
	}
	function GenerarPosicionesEDI856($EDI856_856HEADER)
	{
		$QueryGenerar=" EXEC [SP_INVOICEPOSITION_856] '".ms_escape_string($EDI856_856HEADER['segm_id'])."'";
		
		ActualizarBD($QueryGenerar);
		
		//return $borrarQuery.'<br />'.$query.'<br /><br />';
		return 'EDI856 '.$EDI856_856HEADER['segm_id'].' posiciones generadas';
	}
	function IngresarPO_HEADER_EDI855_Manual($PO_HEADER)
	{
		//Verificar si existe el 855
		$borrarQuery=" DELETE 
					 FROM [PO_HEADER] 
					 WHERE [PO_Number]='".ms_escape_string($PO_HEADER['PO_Number'])."';";
		
		$borrarQuery.=" DELETE FROM [PO_DETAIL]
					WHERE [PO_Number]='".ms_escape_string($PO_HEADER['PO_Number'])."'";
		
		//Borrar query

		echo $query="insert into [PO_HEADER] ([PO_Number],[PO_Date],[ACK_Date],[PO_Items],[PO_ShipTo],[PO_Sociedad]) 
		values 
		('".ms_escape_string($PO_HEADER['PO_Number'])."','".FechaCompletaFormateada(ms_escape_string($PO_HEADER['PO_Date']))."',
		'".FechaCompletaFormateada(ms_escape_string($PO_HEADER['ACK_Date']))."',".ms_escape_string($PO_HEADER['PO_Items']).",
		'".ms_escape_string($PO_HEADER['PO_ShipTo'])."','".ms_escape_string($PO_HEADER['PO_Sociedad'])."');";
		
		ActualizarBD($borrarQuery);
		ActualizarBD($query);
		
		//return $query.'<br /><br />';
		return 'EDI855 Cabecera '.$PO_HEADER['PO_Number'].' ingresado';
	}
	function ActualizarESTADO_DIF_855_Manual($PO_HEADER)
	{
		$QueryActualizar=" EXEC [SP_ESTADO_DIF_855] '".ms_escape_string($PO_HEADER['PO_Number'])."'";
		
		ActualizarBD($QueryActualizar);
		
		//return $borrarQuery.'<br />'.$query.'<br /><br />';
		return 'EDI855 Estado Dif '.$PO_HEADER['PO_Number'].' actualizado';
	}
	function ActualizarESTADO_855_Manual($PO_HEADER)
	{
		$QueryActualizar=" EXEC [SP_ESTADO_855] '".ms_escape_string($PO_HEADER['PO_Number'])."'";
		
		ActualizarBD($QueryActualizar);
		
		//return $borrarQuery.'<br />'.$query.'<br /><br />';
		return 'EDI855 Estado '.$PO_HEADER['PO_Number'].' actualizado';
	}
	function IngresarPO_DETAIL_EDI855_Manual($PO_DETAIL)
	{	
		//Insertar Item
		$query="insert into [PO_DETAIL] ([PO_Number],[PO_Item],[PO_PartNumber],[PO_Description],[PO_Quantity],[PO_Unit],[PO_Price],[PO_Money], 
                    [ACK_PartNumber],[ACK_Date],[ACK_Quantity],[ACK_Unit],[PO_PriceOrig]) 
                     values ('".ms_escape_string($PO_DETAIL['PO_Number'])."','".ms_escape_string($PO_DETAIL['PO_Item'])."',
					 '".ms_escape_string($PO_DETAIL['PO_PartNumber'])."','".ms_escape_string($PO_DETAIL['PO_Description'])."',
					 ".ms_escape_string($PO_DETAIL['PO_Quantity']).",'".ms_escape_string($PO_DETAIL['PO_Unit'])."',
					 ".ms_escape_string($PO_DETAIL['PO_Price']).",'".ms_escape_string($PO_DETAIL['PO_Money'])."',
					 '".ms_escape_string($PO_DETAIL['ACK_PartNumber'])."','".FechaCompletaFormateada(ms_escape_string($PO_DETAIL['ACK_Date']))."',
					 ".ms_escape_string($PO_DETAIL['ACK_Quantity']).",'".ms_escape_string($PO_DETAIL['ACK_Unit'])."',".ms_escape_string($PO_DETAIL['PO_PriceOrig']).");";
					 
		ActualizarBD($query);
					 
		//return $query.'<br /><br />';
		return 'EDI855 detalle de '.$PO_DETAIL['PO_Number'].' ingresado';
	}
	function Ingresar856HEADER_Manual($EDI856_856HEADER)
	{
		$borrarQuery=" delete from [856HEADER] where [segm_id]='".ms_escape_string($EDI856_856HEADER['segm_id'])."';";
		ActualizarBD($borrarQuery);
		
		$segm_date=FechaCompletaFormateada($EDI856_856HEADER['segm_date']);
		
		//Borrar query
		$query=" Insert into [856HEADER]([segm_id],[segm_date],[ship_unit],[ship_lading],[ship_transport],[ship_trailernumber],[ship_transname],[Sociedad],[ship_cantBultos]) 
				 SELECT '".ms_escape_string($EDI856_856HEADER['segm_id'])."',
						 '".ms_escape_string($segm_date)."',
						 '".ms_escape_string($EDI856_856HEADER['ship_unit'])."',
						 (SELECT SUM(CAST((CASE it_unitshiped WHEN '' THEN 0 WHEN NULL THEN 0 ELSE it_unitshiped END) AS DECIMAL(10, 0)))
						  FROM [856DETAIL]
						  WHERE segm_Id='".ms_escape_string($EDI856_856HEADER['segm_id'])."'),
						 '".ms_escape_string($EDI856_856HEADER['ship_transport'])."',
						 '".ms_escape_string($EDI856_856HEADER['ship_trailernumber'])."',
						 '".ms_escape_string($EDI856_856HEADER['ship_transname'])."',
						 '".ms_escape_string($EDI856_856HEADER['Sociedad'])."',
						 (SELECT COUNT(DISTINCT(
							  [it_packingsleep]))
						  FROM [856DETAIL]
						  WHERE [segm_Id]='".ms_escape_string($EDI856_856HEADER['segm_id'])."');";
		
		ActualizarBD($query);
		
		//return $borrarQuery.'<br />'.$query.'<br /><br />';
		return 'EDI856 Cabecera '.$EDI856_856HEADER['segm_id'].' ingresado';
	}
	function BorrarDetalle856($EDI856_856HEADER)
	{
		$borrarQuery="Delete from [856DETAIL] where [segm_id]='".ms_escape_string($EDI856_856HEADER['segm_id'])."';";
		ActualizarBD($borrarQuery);
		
		//return $borrarQuery.'<br /><br />';
		return 'EDI856 Detalle '.$EDI856_856HEADER['segm_id'].', eliminado';
	}	
	function ActualizarPesoCarga856HEADER_Manual($EDI856_856DETAIL)
	{
		$query="  UPDATE [856HEADER] SET [ship_measurement]=(
						(SELECT ISNULL(
							(SELECT [ship_measurement] 
							FROM [856HEADER] 
							WHERE [segm_id]='".ms_escape_string($EDI856_856DETAIL['segm_Id'])."'), 0)
							)+(SELECT ".ms_escape_string($EDI856_856DETAIL['GROSS_WEIGHT'])."))
						WHERE [segm_id]='".ms_escape_string($EDI856_856DETAIL['segm_Id'])."' ";
		ActualizarBD($query);
		
		//return $query.'<br /><br />';it_prodid
		return 'EDI856 Detalle '.$EDI856_856DETAIL['it_prodid'].', peso añadido a '.$EDI856_856DETAIL['segm_Id'];
	}
	function Ingresar856DETAIL_Manual($EDI856_856DETAIL)
	{		
		//Borrar query
		$query="Insert into [856DETAIL]([segm_Id],[it_prodid],[it_unitshiped],[it_unitmeasurement],[it_po],[it_packingsleep],[it_poPosition],[invoicePosition]) 
				values ('".ms_escape_string($EDI856_856DETAIL['segm_Id'])."','".ms_escape_string($EDI856_856DETAIL['it_prodid'])."',
				'".ms_escape_string($EDI856_856DETAIL['it_unitshiped'])."','".ms_escape_string($EDI856_856DETAIL['it_unitmeasurement'])."',
				'".ms_escape_string($EDI856_856DETAIL['it_po'])."','".ms_escape_string($EDI856_856DETAIL['it_packingsleep'])."',
                '".ms_escape_string($EDI856_856DETAIL['it_poPosition'])."','".ms_escape_string($EDI856_856DETAIL['invoicePosition'])."');";

		ActualizarBD($query);
		
		//return $borrarQuery.'<br />'.$query.'<br /><br />';
		return 'EDI856 detalle de '.$EDI856_856DETAIL['segm_Id'].' ingresado';
	}
	function Ingresar856BULTO_Manual($EDI856_856BULTO)
	{
		$borrarQuery=" DELETE from [856BULTO] 
						WHERE [idenBulto]='".ms_escape_string($EDI856_856BULTO['idenBulto'])."' 
						AND [invNumber]='".ms_escape_string($EDI856_856BULTO['invNumber'])."';";
						
		//Borrar query
		echo $query="Insert into [856BULTO]([idenBulto],[peso],[unidPeso],[volumen],[unidVolumen],[longitud],[ancho],[alto],[unidDimension],[fechaDespacho],[invNumber]) 
				values ('".ms_escape_string($EDI856_856BULTO['idenBulto'])."',
				'".ms_escape_string($EDI856_856BULTO['peso'])."','".ms_escape_string($EDI856_856BULTO['unidPeso'])."',
				'".ms_escape_string($EDI856_856BULTO['volumen'])."','".ms_escape_string($EDI856_856BULTO['unidVolumen'])."',
				'".ms_escape_string($EDI856_856BULTO['longitud'])."','".ms_escape_string($EDI856_856BULTO['ancho'])."',
                '".ms_escape_string($EDI856_856BULTO['alto'])."','".ms_escape_string($EDI856_856BULTO['unidDimension'])."',
				'".ms_escape_string($EDI856_856BULTO['fechaDespacho'])."','".ms_escape_string($EDI856_856BULTO['invNumber'])."');";
		
		ActualizarBD($borrarQuery);
		ActualizarBD($query);
		
		//return $borrarQuery.'<br />'.$query.'<br /><br />';
		return 'EDI856 Bulto '.$EDI856_856BULTO['idenBulto'].' ingresado';
	}
	function ActualizarEE_PorPosicion($InvoiceNumber,$PONumber,$POPosition,$EE)
	{
		//Borrar query
		$query="UPDATE InvoiceDetail SET EntregaEntrante='".ms_escape_string($EE)."' 
				WHERE [InvoiceNumber]='".ms_escape_string($InvoiceNumber)."' 
				AND SUBSTRING([PONumber], 0, 11)='".ms_escape_string($PONumber)."' 
				AND convert(int,[POPosition])=convert(int,'".ms_escape_string($POPosition)."')";
		
		ActualizarBD($query);
		
		//return $query;
		return 'Ok';
	}
	function ActualizarEstadoEE_Factura($InvoiceNumber)
	{
		//Borrar query
		$query="EXEC [SP_EE_FACTURA] '".ms_escape_string($InvoiceNumber)."';";
		
		ActualizarBD($query);
		
		//return $query;
		return 'Ok';
	}
	function VerificarRegistroEDI855($PO_Number,$PO_HEADER)
	{
		$registrado=false;
		foreach ($PO_HEADER as $EDI855) {
			if($EDI855['PO_Number']==$PO_Number)
			{
				$registrado=true;
				break;
			}
		}
		return $registrado;
	}
	function getSumatoriaUnidades($InvoiceNumber)
	{
		global $CONNECCION_UNICA_FUNCIONES_EDI;
		
		
		$query 	= "   SELECT SUM(convert(float,[ProductQuantity])) as ship_lading
					  FROM [InvoiceDetail]
					  WHERE [InvoiceNumber]='".ms_escape_string($InvoiceNumber)."';";
		
		$result = sqlsrv_query($CONNECCION_UNICA_FUNCIONES_EDI, $query);
		
		if (!$result) {
			$CONNECCION_UNICA_FUNCIONES_EDI = conectar_srvdev();
			$result = sqlsrv_query($CONNECCION_UNICA_FUNCIONES_EDI, $query);
		}
		
		$ship_lading='0';

		while($row = sqlsrv_fetch_array($result) )
		{	
			$ship_lading     = $row['ship_lading'];			
		}
		
		return $ship_lading;
	}
	function InterpretarValidacionNoVacio($Resultado,$FilaExcel)
	{
		$Respuesta='';
		if($Resultado['Respuesta']=='nok')
		{
			switch ($Resultado['Campo_errado']) {
				case 'it_unitshiped':
					$Respuesta= 'No se encontro un valor para el campo '.$Resultado['Campo_errado'].' en la fila '.$FilaExcel.' verifique las posiciones del EDI855 con SAP correspondientes a la factura ingresada';
					break;
				default:
				$Respuesta= 'No se encontro un valor para el campo '.$Resultado['Campo_errado'].' en la fila '.$FilaExcel.'';
			}
		}
		return $Respuesta;
	}
?>