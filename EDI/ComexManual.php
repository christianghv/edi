<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/plugins/PHPExcel/Classes/PHPExcel.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/EDI/funciones/Funciones_edi.php');
	ini_set('memory_limit', '512M');
	
	set_time_limit(600);
		
	class CargarEDI_Manual
	{
	
		private $sFileName;
		private $log_proceso;
		private $proveedor;
		
		function __construct($sFileName,$proveedor,$log) {
			$this->sFileName=$sFileName;
			//$sFileName=$_SERVER['DOCUMENT_ROOT'].'/EDI/ajax/archivos/carga_masiva/EDIFINAL.xlsx';
			$this->proveedor=$proveedor;
			
			if($log!='')
			{
				$this->log_proceso=$log;
			}
		}
	
		public function Iniciar()
		{
			$objReader = PHPExcel_IOFactory::createReader('Excel2007');
			$objReader->setReadDataOnly(true);

			$objPHPExcel = $objReader->load($this->sFileName);
			
			//Recorriendo Hoja
			$objPHPExcel->setActiveSheetIndex(0);
			$objWorksheet = $objPHPExcel->getActiveSheet();
			$highestRow = $objWorksheet->getHighestRow(); 
			
			$FacturaActual = '';
			$ControlInvoiceNumber = '';
			$Sociedad='';
			$SecuencialPosicion=0;
			
			$InvoiceHeader= array();
			$InvoiceDetail= array();
			
			$PO_HEADER= array();
			$PO_DETAIL= array();
			
			$A_856HEADER= array();
			$A_856DETAIL= array();
			$A_856BULTO= array();
			
			$Viene810=false;
			$Viene855=false;
			$Viene856=false;
			
			$Errores_EDI810=0;
			$Errores_EDI855=0;
			$Errores_EDI856=0;
			
			$OC_855=array();
			
			$ArrayCombinacion=array();
			$ValidadorCombinacion=true;
			
			//810
			$EDI810_InvoiceHeader=getEDI810_InvoiceHeader();
			$EDI810_InvoiceDetail=getEDI810_InvoiceDetail();
			//855
			$EDI855_PO_HEADER=getEDI855_PO_HEADER();
			$EDI855_PO_DETAIL=getEDI855_PO_DETAIL();
			//856
			$EDI856_856HEADER=getEDI856_856HEADER();
			$EDI856_856DETAIL=getEDI856_856DETAIL();
			$EDI856_856BULTO=getEDI856_856BULTO();
			
			$EDI855Cargado=array();
			
			global $UrlWSDespi,$PuertoWSDespi,$usuarioWSDespi,$contrasenaWSDespi;
			
			$WS_BuscarOC_855 = new WS_BuscarOC_855($UrlWSDespi,$PuertoWSDespi,$usuarioWSDespi,$contrasenaWSDespi);
									
			for ($row = 2; $row < $highestRow+1; $row++) {
				$SecuencialPosicion++;
				$Sociedad=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
				//Validar si viene o no la sociedad
				if(trim($Sociedad)=="")
				{
					$this->LogProcesoError("-No se encontro la sociedad en el documento-");
					break;
				}
				
				//Validacion de Combinacion si viene OC
				if(trim($objWorksheet->getCellByColumnAndRow(6, $row)->getValue().'')=="" && trim($objWorksheet->getCellByColumnAndRow(5, $row)->getValue().'')!="")
				{
					//Verificar si vienen los valores
					$Fact_OC_PN=trim($objWorksheet->getCellByColumnAndRow(1, $row)->getValue().'').'_'.trim($objWorksheet->getCellByColumnAndRow(5, $row)->getValue().'').'_'.trim($objWorksheet->getCellByColumnAndRow(12, $row)->getValue().'');
						
					foreach ($ArrayCombinacion as $Val) {
						if($Fact_OC_PN==$Val['Fact_OC_PN'])
						{
							$ValidadorCombinacion=false;
							$Errores_EDI810++;
							$this->LogProcesoError("Combinacion Fact|OC|PN rota en fila ".$row." 
												(".$objWorksheet->getCellByColumnAndRow(1, $row)->getValue()."|
												".$objWorksheet->getCellByColumnAndRow(5, $row)->getValue()."|
												".$objWorksheet->getCellByColumnAndRow(12, $row)->getValue()."
												 anteriormente detectada en ".$Val['Fila'].")");
							break;
						}
					}
						
					if($ValidadorCombinacion)
					{
						$ItemValidadorCombinacion=array(
							'Fact_OC_PN'=>$Fact_OC_PN,
							'Fila'=>$row
						);
						array_push($ArrayCombinacion,$ItemValidadorCombinacion);
					}
					//Reiniciando validador
					$ValidadorCombinacion=true;
				}
				
				//PrimeraFila
				if($FacturaActual=='')
				{
					//Verificar si viene factura 810
					//Si viene la OC de la factura
					if(trim($objWorksheet->getCellByColumnAndRow(5, $row)->getValue().'')!="")
					{
						$FacturaActual=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
					}
					
					//Control invoice number
					if(trim($objWorksheet->getCellByColumnAndRow(1, $row)->getValue().'')!="")
					{
						$ControlInvoiceNumber=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
					}
					
				}
				
				//Si viene la OC de la factura
				if(trim($objWorksheet->getCellByColumnAndRow(5, $row)->getValue().'')!="")
				{
					$Viene810=true;
				}
				
				$PO_Number=$objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
				//Verificar EDI 855
				if($PO_Number!="")
				{
					$Viene855=true;
				}
				
				//Viene EDI 856
				if(trim($objWorksheet->getCellByColumnAndRow(18, $row)->getValue().'')!="")
				{
					$Viene856=true;
				}
				
				//Nueva factura
				if($FacturaActual!=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue())
				{
					if($Viene810)
					{
						//Validando EDI810_InvoiceHeader
						$Val_EDI810_InvoiceHeader=ValidarDatosNoVacios($EDI810_InvoiceHeader,'EDI810 Cabecera Factura');
						$Intepretacion=InterpretarValidacionNoVacio($Val_EDI810_InvoiceHeader,$row);
						if($Intepretacion!='')
						{
							$Errores_EDI810++;
							$this->LogProcesoError($Intepretacion);
						}
						else
						{
							array_push($InvoiceHeader,$EDI810_InvoiceHeader);
						}
					}
					//Reniciando variables
					$EDI810_InvoiceHeader=getEDI810_InvoiceHeader();
					$EDI856_856HEADER=getEDI856_856HEADER();
				}
				
				//Cabecera EDI 810
				//Viene EDI 810
				if($Viene810)
				{
					$FacturaActual=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
					$EDI810_InvoiceHeader['InvoiceNumber']=$FacturaActual;
					$EDI810_InvoiceHeader['InvoiceDate']=getFechaFormateada($objWorksheet->getCellByColumnAndRow(2, $row)->getValue());
					$EDI810_InvoiceHeader['InvoiceCurrency']=$objWorksheet->getCellByColumnAndRow(22, $row)->getValue();
					$EDI810_InvoiceHeader['InvoiceVendor']=$this->proveedor;
					$EDI810_InvoiceHeader['Sociedad']=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
				}
				//Si viene EDI 856 
				if($Viene856)
				{
					$Viene856=true;
					//Agregando 856HEADER
					$EDI856_856HEADER=getEDI856_856HEADER();
					$EDI856_856HEADER['segm_id']=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
					$EDI856_856HEADER['segm_date']=getFechaFormateada($objWorksheet->getCellByColumnAndRow(19, $row)->getValue());
					$EDI856_856HEADER['ship_unit']=$objWorksheet->getCellByColumnAndRow(21, $row)->getValue();
					$EDI856_856HEADER['ship_transport']=$objWorksheet->getCellByColumnAndRow(24, $row)->getValue();
					$EDI856_856HEADER['ship_trailernumber']=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
					$EDI856_856HEADER['ship_transname']=$objWorksheet->getCellByColumnAndRow(26, $row)->getValue();
					$EDI856_856HEADER['Sociedad']=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
					
					//Validando EDI856_856HEADER
					$Val_EDI856_856HEADER=ValidarDatosNoVacios($EDI856_856HEADER,'EDI856 Cabecera');
					$Intepretacion=InterpretarValidacionNoVacio($Val_EDI856_856HEADER,$row);
					if($Intepretacion!='')
					{
						$Errores_EDI856++;
						$this->LogProcesoError($Intepretacion);
					}
					else
					{
						array_push($A_856HEADER,$EDI856_856HEADER);
						$EDI856_856HEADER=getEDI856_856HEADER();
					}
				}
				
				//EDI 855
				if($Viene855)
				{
					$EDI855_PO_HEADER=getEDI855_PO_HEADER();
					$EDI855_PO_HEADER['PO_Number']=$PO_Number;
					$EDI855_PO_HEADER['PO_Date']=getFechaFormateada($objWorksheet->getCellByColumnAndRow(2, $row)->getValue());
					$EDI855_PO_HEADER['ACK_Date']=getFechaFormateada($objWorksheet->getCellByColumnAndRow(2, $row)->getValue());
					if(trim($objWorksheet->getCellByColumnAndRow(26, $row)->getValue())!="")
					{
						$EDI855_PO_HEADER['PO_ShipTo']=$objWorksheet->getCellByColumnAndRow(26, $row)->getValue();
					}
					else
					{
						$EDI855_PO_HEADER['PO_ShipTo']='NULL';
					}
					$EDI855_PO_HEADER['PO_Sociedad']=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
					
					//Detalle EDI 855
					$EDI855_PO_DETAIL=getEDI855_PO_DETAIL();
					$OC_YaRegistrada=buscarOC855(substr($PO_Number,0,10),$OC_855);
					
					if($OC_YaRegistrada['respuesta']=='Ok')
					{
						$DatosOC855=$OC_YaRegistrada['DatosOC855'];
						$EDI855_PO_HEADER['PO_Items']=count($DatosOC855);
						$this->LogProceso('OC 855 Encontrada no se necesita llamar al WS');
					}
					else
					{
						//Si no esta almacenada la OC Buscarla en el WS
						//Buscar OC 855
						$this->LogProceso('Buscando en WS OC '.substr($PO_Number,0,10));
						$DatosOC855=$WS_BuscarOC_855->WS_detalleordencompra(substr($PO_Number,0,10));
						$this->LogProceso('WS ejecutado para OC '.substr($PO_Number,0,10));
						
						$EDI855_PO_HEADER['PO_Items']=count($DatosOC855);
						
						//Almacenado datos en Array
						$ItemOC=getItemOrdenDeCompra(substr($PO_Number,0,10),$DatosOC855);
						array_push($OC_855,$ItemOC);
					}
					
					//Ingresando PO_HEADER si no se ha registrado
					$EDI855_Registrado=VerificarRegistroEDI855($PO_Number,$PO_HEADER);
					if(!$EDI855_Registrado)
					{
						//Validando EDI855_PO_HEADER
						$Val_EDI855_PO_HEADER=ValidarDatosNoVacios($EDI855_PO_HEADER,'EDI855 Cabecera');
						$Intepretacion=InterpretarValidacionNoVacio($Val_EDI855_PO_HEADER,$row);
						if($Intepretacion!='')
						{
							$Errores_EDI855++;
							$this->LogProcesoError($Intepretacion);
						}
						else
						{
							array_push($PO_HEADER,$EDI855_PO_HEADER);
						}
					}

					//Si no viene el PO_Item hay que buscarlo en SAP
					if(trim($objWorksheet->getCellByColumnAndRow(6, $row)->getValue())=="")
					{
						$DatosFaltantes=DatosDetalleOC_PO_PartNumber(getPartNumber($Sociedad,$objWorksheet->getCellByColumnAndRow(12, $row)->getValue()),$DatosOC855);
						$EDI855_PO_DETAIL['PO_Item']=$DatosFaltantes['PO_Item'];
					}
					else
					{
						$EDI855_PO_DETAIL['PO_Item']=FormatoCeros($objWorksheet->getCellByColumnAndRow(6, $row)->getValue(),5);
					}
						
					//Validar PO_Item si es igual al de sap o sigue siendo null
					if($EDI855_PO_DETAIL['PO_Item']=="")
					{
						$Errores_EDI855++;
						$this->LogProcesoError("No se encontro el PO_Item en SAP en la fila ".$row." verifique relación Numero OC con Product Number en SAP");
					}
					
					//Buscar Faltantes por el PO_Item
					$DatosFaltantes=DatosDetalleOC_PO_Item($EDI855_PO_DETAIL['PO_Item'],$DatosOC855);
					
					//Validar que este correcta la relacion segun la posicion obtenida
					if(trim($DatosFaltantes['PO_PartNumber'])=="")
					{
						$Errores_EDI855++;
						$this->LogProcesoError("No se encontro el Numero de Parte en SAP en la fila ".$row." verifique relación Numero OC con PO_Item en SAP");
					}
					else
					{
						//Cargando datos de 855
						$EDI855_PO_DETAIL['PO_Number']=$PO_Number;
						$EDI855_PO_DETAIL['PO_PartNumber']=getPartNumber($Sociedad,$DatosFaltantes['PO_PartNumber']);
						$EDI855_PO_DETAIL['PO_Description']=$objWorksheet->getCellByColumnAndRow(11, $row)->getValue();
						$EDI855_PO_DETAIL['PO_Quantity']=$DatosFaltantes['PO_Quantity'];
						$EDI855_PO_DETAIL['PO_Unit']=$DatosFaltantes['PO_Unit'];
						$EDI855_PO_DETAIL['PO_Price']=$DatosFaltantes['PO_Price'];
						$EDI855_PO_DETAIL['PO_Money']=$DatosFaltantes['PO_Money'];
						$EDI855_PO_DETAIL['ACK_PartNumber']=getPartNumber($Sociedad,$objWorksheet->getCellByColumnAndRow(12, $row)->getValue());
						$EDI855_PO_DETAIL['ACK_Date']=getFechaFormateada($objWorksheet->getCellByColumnAndRow(2, $row)->getValue());
						$EDI855_PO_DETAIL['ACK_Quantity']=$objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
						$EDI855_PO_DETAIL['PO_PriceOrig']=$objWorksheet->getCellByColumnAndRow(9, $row)->getValue();
						$EDI855_PO_DETAIL['ACK_Unit']=$objWorksheet->getCellByColumnAndRow(23, $row)->getValue();
							
						//Validando EDI855_PO_DETAIL
						$Val_EDI855_PO_DETAIL=ValidarDatosNoVacios($EDI855_PO_DETAIL,'EDI855 Detalle');
						$Intepretacion=InterpretarValidacionNoVacio($Val_EDI855_PO_DETAIL,$row);
						if($Intepretacion!='')
						{
							$Errores_EDI855++;
							$this->LogProcesoError($Intepretacion);
						}
						else
						{
							array_push($PO_DETAIL,$EDI855_PO_DETAIL);
						}
					}
				}
				
				//Ingresando detalle EDI 810
				//Si viene EDI 810
				if($Viene810)
				{
					$EDI810_InvoiceDetail=getEDI810_InvoiceDetail();
					$EDI810_InvoiceDetail['InvoiceNumber']=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
					
					//Validar InvoicePosition si es vacio tomar secuencial
					if(trim($objWorksheet->getCellByColumnAndRow(3, $row)->getValue())=="")
					{
						$EDI810_InvoiceDetail['InvoicePosition']=FormatoCeros($SecuencialPosicion,5);
					}
					else
					{
						$EDI810_InvoiceDetail['InvoicePosition']=FormatoCeros($objWorksheet->getCellByColumnAndRow(3, $row)->getValue(),5);
					}
					
					$EDI810_InvoiceDetail['PONumber']=$objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
					$EDI810_InvoiceDetail['POPosition']=FormatoCeros($objWorksheet->getCellByColumnAndRow(6, $row)->getValue(),5);
					$EDI810_InvoiceDetail['ProductID']=getPartNumber($Sociedad,$objWorksheet->getCellByColumnAndRow(12, $row)->getValue());
					$EDI810_InvoiceDetail['ProductDesciption']=$DatosFaltantes['PO_Description'];
					$EDI810_InvoiceDetail['ProductMeasure']=$objWorksheet->getCellByColumnAndRow(23, $row)->getValue();
					$EDI810_InvoiceDetail['ProductQuantity']=$objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
					$EDI810_InvoiceDetail['PorductPrice']=$objWorksheet->getCellByColumnAndRow(9, $row)->getValue();
					$EDI810_InvoiceDetail['PaisOrigen']=$objWorksheet->getCellByColumnAndRow(17, $row)->getValue();
					$EDI810_InvoiceDetail['idenBulto']=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
					
				
					//Buscar HEADER 810 y obtener el Precio 
					$TotalEdi810=$EDI810_InvoiceHeader['InvoiceNetValue']+(floatval($EDI810_InvoiceDetail['ProductQuantity'])*floatval($EDI810_InvoiceDetail['PorductPrice']));
					$TotalGastosEDI810=floatval($EDI810_InvoiceHeader['InvoiceGastos'])+floatval($objWorksheet->getCellByColumnAndRow(25, $row)->getValue());
					//Actualiazando el Header
					$EDI810_InvoiceHeader['InvoiceNetValue']=$TotalEdi810;
					$EDI810_InvoiceHeader['InvoiceGastos']=$TotalGastosEDI810;
					$EDI810_InvoiceHeader['InvoiceGrossValue']=$TotalEdi810+$TotalGastosEDI810;
					
					
					//Validando EDI810_InvoiceDetail
					$Val_EDI810_InvoiceDetail=ValidarDatosNoVacios($EDI810_InvoiceDetail,'EDI810 Detalle');
					$Intepretacion=InterpretarValidacionNoVacio($Val_EDI810_InvoiceDetail,$row);
					if($Intepretacion!='')
					{
						$Errores_EDI810++;
						$this->LogProcesoError($Intepretacion);
					}
					else
					{
						array_push($InvoiceDetail,$EDI810_InvoiceDetail);
					}
				}
				//Si viene un EDI 856
				if($Viene856)
				{
					//Faltantes EDI856
					//Si no viene OC la buscara en la BD
					if(trim($objWorksheet->getCellByColumnAndRow(5, $row)->getValue().'')!="")
					{
						$FALTANTE_EDI856=getFALTANTE_EDI856();
						$FALTANTE_EDI856['ProductMeasure']     = $objWorksheet->getCellByColumnAndRow(23, $row)->getValue();
						$FALTANTE_EDI856['POPosition']    	   = FormatoCeros($objWorksheet->getCellByColumnAndRow(6, $row)->getValue(),5);
						$FALTANTE_EDI856['PONumber']    	   = $objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
						$FALTANTE_EDI856['InvoicePosition']    = $EDI810_InvoiceDetail['InvoicePosition'];
						$FALTANTE_EDI856['PO_Quantity']   	   = $EDI810_InvoiceDetail['ProductQuantity'];
					}
					else
					{
						$FALTANTE_EDI856=getDatosFaltantes_EDI856($objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),getPartNumber($Sociedad,$objWorksheet->getCellByColumnAndRow(12, $row)->getValue()),$objWorksheet->getCellByColumnAndRow(4, $row)->getValue());
					}
					
					//Detalle 856
					$segm_Id=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
					$EDI856_856DETAIL = getEDI856_856DETAIL();
					$EDI856_856DETAIL['segm_Id']=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
					$EDI856_856DETAIL['it_prodid']=FormatoCeros($objWorksheet->getCellByColumnAndRow(12, $row)->getValue(),9);
					$EDI856_856DETAIL['it_unitshiped']=$FALTANTE_EDI856['PO_Quantity'];
					$EDI856_856DETAIL['it_unitmeasurement']=$FALTANTE_EDI856['ProductMeasure'];
					$EDI856_856DETAIL['it_po']=$FALTANTE_EDI856['PONumber'];
					$EDI856_856DETAIL['it_refnumber']=$objWorksheet->getCellByColumnAndRow(18, $row)->getValue();
					$EDI856_856DETAIL['it_packingsleep']=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
					$EDI856_856DETAIL['it_poPosition']=$FALTANTE_EDI856['POPosition'];
					$EDI856_856DETAIL['invoicePosition']=$FALTANTE_EDI856['InvoicePosition'];
				
					
					//Validando EDI856_856DETAIL
					$Val_EDI856_856DETAIL=ValidarDatosNoVacios($EDI856_856DETAIL,'EDI856 Detalle');
					$Intepretacion=InterpretarValidacionNoVacio($Val_EDI856_856DETAIL,$row);
					if($Intepretacion!='')
					{
						$Errores_EDI856++;
						$this->LogProcesoError($Intepretacion);
					}
					else
					{
						//Verificar si existe en el arreglo el indice de factura
						array_push($A_856DETAIL,$EDI856_856DETAIL);
					}
						
					//Bulto 856
					$EDI856_856BULTO=getEDI856_856BULTO();
					
					$EDI856_856BULTO['idenBulto']=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
					/** FALTA MIX90
					$EDI856_856BULTO['tipoBulto']=$objWorksheet->getCellByColumnAndRow(20, $row)->getValue();
					*/
					$EDI856_856BULTO['peso']=$objWorksheet->getCellByColumnAndRow(16, $row)->getValue();
					$EDI856_856BULTO['unidPeso']=$objWorksheet->getCellByColumnAndRow(21, $row)->getValue();
					$EDI856_856BULTO['longitud']=$objWorksheet->getCellByColumnAndRow(13, $row)->getValue();
					$EDI856_856BULTO['ancho']=$objWorksheet->getCellByColumnAndRow(14, $row)->getValue();
					$EDI856_856BULTO['alto']=$objWorksheet->getCellByColumnAndRow(15, $row)->getValue();
					$EDI856_856BULTO['unidDimension']=$objWorksheet->getCellByColumnAndRow(20, $row)->getValue();
					$EDI856_856BULTO['fechaDespacho']=getFechaFormateada($objWorksheet->getCellByColumnAndRow(19, $row)->getValue());
					$EDI856_856BULTO['invNumber']=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
					
					//Calculando volumen
					$VolumenBulto=floatval($EDI856_856BULTO['longitud'])*floatval($EDI856_856BULTO['ancho'])*floatval($EDI856_856BULTO['alto']);
					
					$EDI856_856BULTO['volumen']=$VolumenBulto;
					$EDI856_856BULTO['unidVolumen']=$EDI856_856BULTO['unidDimension'];
					
					//Validando EDI856_856BULTO
					$Val_EDI856_856DETAIL=ValidarDatosNoVacios($EDI856_856BULTO,'EDI856 Bulto');
					$Intepretacion=InterpretarValidacionNoVacio($Val_EDI856_856DETAIL,$row);
					if($Intepretacion!='')
					{
						$Errores_EDI856++;
						$this->LogProcesoError($Intepretacion);
					}
					else
					{
						array_push($A_856BULTO,$EDI856_856BULTO);
					}
				}
				//Control invoice number
				if($ControlInvoiceNumber!=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue())
				{
					$ControlInvoiceNumber=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
					$SecuencialPosicion=0;
				}
			}//Fin recorriendo excel
			
			//Ingresando ultima factura
			if($Viene810)
			{
				//Validando EDI810_InvoiceHeader
				$Val_EDI810_InvoiceHeader=ValidarDatosNoVacios($EDI810_InvoiceHeader,'EDI810 Cabecera factura');
				$Intepretacion=InterpretarValidacionNoVacio($Val_EDI810_InvoiceHeader,$row);
				if($Intepretacion!='')
				{
					$Errores_EDI810++;
					$this->LogProcesoError($Intepretacion);
				}
				else
				{
					array_push($InvoiceHeader,$EDI810_InvoiceHeader);
				}
			}
			
			//Reniciando variables
			$EDI810_InvoiceHeader=getEDI810_InvoiceHeader();
			
			$BanderaValido=false;
			
			//Verificar si viene completo
			if($Errores_EDI810==0 && $Viene810 && $Errores_EDI855==0 && $Viene855 && $Errores_EDI856==0 && $Viene856)
			{
				$BanderaValido=true;
				
				$this->LogProcesoPrimario("-Documento validado correctamente ingresando registros-");
				//Hacer grabar en base de datos
				
				//EDI 810//
				//Cabecera EDI810
				foreach ($InvoiceHeader as $Header) {
					$this->LogProcesoInfo(IngresarCabeceraEDI810_Manual($Header));
				}
				//Detalle EDI810
				foreach ($InvoiceDetail as $Detalle) {
					$this->LogProcesoInfo(IngresarDetalleEDI810_Manual($Detalle));
				}
				$this->LogProcesoAdvertencia("-EDI810 ingreso terminado-");
				
				//EDI 855//
				//PO_HEADER EDI855
				foreach ($PO_HEADER as $PO_Header) {
					$this->LogProcesoInfo(IngresarPO_HEADER_EDI855_Manual($PO_Header));
				}
				//PO_DETAIL EDI855
				foreach ($PO_DETAIL as $PO_Detail) {
					$this->LogProcesoInfo(IngresarPO_DETAIL_EDI855_Manual($PO_Detail));
				}
				$this->LogProcesoAdvertencia("-EDI855 ingreso terminado-");
				
				//EDI 856//
				//856DETAIL EDI856
				foreach ($A_856DETAIL as $Detalle) {
					$this->LogProcesoInfo(Ingresar856DETAIL_Manual($Detalle));
				}
				//856BULTO EDI856
				foreach ($A_856BULTO as $Bulto) {
					$this->LogProcesoInfo(Ingresar856BULTO_Manual($Bulto));
				}
				$this->LogProcesoAdvertencia("-EDI856 ingreso terminado-");
				
				//856HEADER EDI856
				foreach ($A_856HEADER as $Header) {
					$this->LogProcesoInfo(Ingresar856HEADER_Manual($Header));
				}
			}
			
			//Solo EDI810 y EDI855
			if($Errores_EDI810==0 && $Viene810 && $Errores_EDI855==0 && $Viene855)
			{
				$BanderaValido=true;
				
				$this->LogProcesoPrimario("-Documento validado correctamente ingresando registros EDI810 y EDI855-");
				//Hacer grabar en base de datos
				
				//EDI 810//
				//Cabecera EDI810
				foreach ($InvoiceHeader as $Header) {
					$this->LogProcesoInfo(IngresarCabeceraEDI810_Manual($Header));
				}
				//Detalle EDI810
				foreach ($InvoiceDetail as $Detalle) {
					$this->LogProcesoInfo(IngresarDetalleEDI810_Manual($Detalle));
				}
				$this->LogProcesoAdvertencia("-EDI810 ingreso terminado-");
				
				//EDI 855//
				//PO_HEADER EDI855
				foreach ($PO_HEADER as $PO_Header) {
					$this->LogProcesoInfo(IngresarPO_HEADER_EDI855_Manual($PO_Header));
				}
				//PO_DETAIL EDI855
				foreach ($PO_DETAIL as $PO_Detail) {
					$this->LogProcesoInfo(IngresarPO_DETAIL_EDI855_Manual($PO_Detail));
				}
				$this->LogProcesoAdvertencia("-EDI855 ingreso terminado-");
			}
			
			//Solo EDI856
			if($Errores_EDI856==0 && $Viene856)
			{
				$BanderaValido=true;
				
				$this->LogProcesoPrimario("-Documento validado correctamente ingresando registros EDI856-");
				
				//EDI 856//
				//856DETAIL EDI856
				foreach ($A_856DETAIL as $Detalle) {
					$this->LogProcesoInfo(Ingresar856DETAIL_Manual($Detalle));
				}
				//856BULTO EDI856
				foreach ($A_856BULTO as $Bulto) {
					$this->LogProcesoInfo(Ingresar856BULTO_Manual($Bulto));
				}
				
				//856HEADER EDI856
				foreach ($A_856HEADER as $Header) {
					$this->LogProcesoInfo(Ingresar856HEADER_Manual($Header));
				}
				
				$this->LogProcesoAdvertencia("-EDI856 ingreso terminado-");
			}
			
			if(!$BanderaValido)
			{
				$this->LogProcesoError("-El documento posee uno o más errores, debe ser verificado-");
			}
			
			//Fin de hoja
			//Limpiar Sql
			CerrarConeccionUnica();
			
			unlink($this->sFileName);
		}
		public function LogProceso($Mensaje)
		{
			//Limpiar mensaje
			$Mensaje=$this->LimpiarMensaje($Mensaje);
			//Asignar tipo
			$MensajeScript=str_replace('#TIPO#', 'N', $this->log_proceso);
			
			echo str_replace('#LOG#', trim($Mensaje), $MensajeScript);
		}
		public function LogProcesoError($Mensaje)
		{
			//Limpiar mensaje
			$Mensaje=$this->LimpiarMensaje($Mensaje);
			//Asignar tipo
			$MensajeScript=str_replace('#TIPO#', 'E', $this->log_proceso);
			
			echo str_replace('#LOG#', trim($Mensaje), $MensajeScript);
		}
		public function LogProcesoExito($Mensaje)
		{
			//Limpiar mensaje
			$Mensaje=$this->LimpiarMensaje($Mensaje);
			//Asignar tipo
			$MensajeScript=str_replace('#TIPO#', 'S', $this->log_proceso);
			
			echo str_replace('#LOG#', trim($Mensaje), $MensajeScript);
		}
		public function LogProcesoPrimario($Mensaje)
		{
			//Limpiar mensaje
			$Mensaje=$this->LimpiarMensaje($Mensaje);
			//Asignar tipo
			$MensajeScript=str_replace('#TIPO#', 'P', $this->log_proceso);
			
			echo str_replace('#LOG#', trim($Mensaje), $MensajeScript);
		}
		public function LogProcesoInfo($Mensaje)
		{
			//Limpiar mensaje
			$Mensaje=$this->LimpiarMensaje($Mensaje);
			//Asignar tipo
			$MensajeScript=str_replace('#TIPO#', 'I', $this->log_proceso);
			
			echo str_replace('#LOG#', trim($Mensaje), $MensajeScript);
		}
		public function LogProcesoAdvertencia($Mensaje)
		{
			//Limpiar mensaje
			$Mensaje=$this->LimpiarMensaje($Mensaje);
			//Asignar tipo
			$MensajeScript=str_replace('#TIPO#', 'W', $this->log_proceso);
			
			echo str_replace('#LOG#', trim($Mensaje), $MensajeScript);
		}
		public function LimpiarMensaje($Mensaje)
		{
			$buscar=array(chr(13).chr(10), "\r\n", "\n", "\r");
			$reemplazar=array("", "", "", "");
			$Mensaje=str_ireplace($buscar,$reemplazar,$Mensaje);
			return $Mensaje;
		}
	}
?>