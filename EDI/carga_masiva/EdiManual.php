<?php
	require_once('../../plugins/PHPExcel/Classes/PHPExcel.php');
	require_once('../funciones/Funciones_edi.php');
	ini_set('memory_limit', '1040M');
	require_once('../ajax/ws_buscarOC_855.php');
	require_once("../../conf_sap.php");

//	$_SERVER['DOCUMENT_ROOT'].
	set_time_limit(600);
	//error_reporting(E_ERROR | E_WARNING | E_PARSE);
//$CargarEDI_Manual = new CargarEDI_Manual($nombre_fichero,'10001','<script type="text/javascript">parent.IngresarLog("#TIPO#","#LOG#");</script>');
//$CargarEDI_Manual->Iniciar();
	class CargarEDI_Manual
	{
	
		private $sFileName;
		private $log_proceso;
		private $proveedor;
		
		function __construct($sFileName,$proveedor,$log) {
			
			//echo $proveedor;
			//$sFileName='../ajax/archivos/carga_masiva/_20201203_082358.xlsx';
echo $this->sFileName=$sFileName;
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

			//echo $this->sFileName;
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
			$Viene855=true;
			$Viene856=false;
			
			$Errores_EDI810=0;
			$Errores_EDI855=0;
			$Errores_EDI856=0;
			$ErrorGlobal=0;
			
			$OC_855=array();
			
			$ArrayCombinacion=array();
			$ValidadorCombinacion=true;
			
			//810
			$EDI810_InvoiceHeader=getEDI810_InvoiceHeader();
//print_r($EDI810_InvoiceHeader);
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
			$SociedadOk=false;
									
			for ($row = 2; $row < $highestRow+1; $row++) {
				$SecuencialPosicion++;
				$Sociedad=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();

				//Validar si viene o no la sociedad
				if(trim($Sociedad)=="")
				{
					if(!$SociedadOk)
					{
						LogProcesoError($this->log_proceso,"-No se encontro la sociedad en el documento-");
					}
					break;
				}
				else
				{
					$SociedadOk=true;
				}
				//echo $objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
				//Validacion de Combinacion si viene OC
				if(trim($objWorksheet->getCellByColumnAndRow(6, $row)->getValue().'')=="" && trim($objWorksheet->getCellByColumnAndRow(5, $row)->getValue().'')!="")
				{
					//Verificar si vienen los valores	
//echo "<qui";
					$Fact_OC_PN=trim(getInvoiceNumber2($Sociedad,$objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),getAnioDeFecha($objWorksheet->getCellByColumnAndRow(2, $row)->getValue())).'').'_'.trim($objWorksheet->getCellByColumnAndRow(5, $row)->getValue().'').'_'.trim($objWorksheet->getCellByColumnAndRow(12, $row)->getValue().'');
					//echo "fin";
					foreach ($ArrayCombinacion as $Val) {
						if($Fact_OC_PN==$Val['Fact_OC_PN'])
						{
							$ValidadorCombinacion=false;
							$Errores_EDI810++;
							LogProcesoError($this->log_proceso,"Combinacion Fact|OC|PN rota en fila ".$row." 
												(".getInvoiceNumber2($Sociedad,$objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),getAnioDeFecha($objWorksheet->getCellByColumnAndRow(2, $row)->getValue()))."|
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
//echo "factura".$objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
					if(trim($objWorksheet->getCellByColumnAndRow(5, $row)->getValue().'')!="")
					{
						$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
						$FacturaActual=getInvoiceNumber2($Sociedad,$objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),getAnioDeFecha($objWorksheet->getCellByColumnAndRow(2, $row)->getValue()));
					}
					
					//Control invoice number
					if(trim(getInvoiceNumber2($Sociedad,$objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),getAnioDeFecha($objWorksheet->getCellByColumnAndRow(2, $row)->getValue())).'')!="")
					{
						$ControlInvoiceNumber=getInvoiceNumber2($Sociedad,$objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),getAnioDeFecha($objWorksheet->getCellByColumnAndRow(2, $row)->getValue()));
					}
					
				}


				//Si viene la OC de la factura
				if(trim($objWorksheet->getCellByColumnAndRow(5, $row)->getValue().'')!="")
				{
					$Viene810=true;
				}
				
				$PO_Number=NumeroSinExponente($objWorksheet->getCellByColumnAndRow(5, $row)->getValue());
				
				//Verificar EDI 855
				if($PO_Number!="0")
				{
					$Viene855=true;
				}
				
				//Viene EDI 856

				if(trim($objWorksheet->getCellByColumnAndRow(19, $row)->getValue().'')!="")
				{
					$Viene856=true;
				}
				
				//Nueva factura
				if($FacturaActual!=getInvoiceNumber2($Sociedad,$objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),getAnioDeFecha($objWorksheet->getCellByColumnAndRow(2, $row)->getValue())))
				{
					if($Viene810)
					{
						//Validando EDI810_InvoiceHeader
						$Val_EDI810_InvoiceHeader=ValidarDatosNoVacios($EDI810_InvoiceHeader,'EDI810 Cabecera Factura');
						$Intepretacion=InterpretarValidacionNoVacio($Val_EDI810_InvoiceHeader,$row);
						if($Intepretacion!='')
						{
							$Errores_EDI810++;
							LogProcesoError($this->log_proceso,$Intepretacion);
							$ErrorGlobal++;
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

					$FacturaActual=getInvoiceNumber2($Sociedad,$objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),getAnioDeFecha($objWorksheet->getCellByColumnAndRow(2, $row)->getValue()));
					
$EDI810_InvoiceHeader['InvoiceNumber']=$FacturaActual;
					$EDI810_InvoiceHeader['InvoiceDate']=getFechaFormateada($objWorksheet->getCellByColumnAndRow(2, $row)->getValue());
//$EDI810_InvoiceHeader['InvoiceDate']=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();

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
					$EDI856_856HEADER['segm_id']=getInvoiceNumber2($Sociedad,$objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),getAnioDeFecha($objWorksheet->getCellByColumnAndRow(19, $row)->getValue()));
					$EDI856_856HEADER['segm_date']=getFechaFormateada($objWorksheet->getCellByColumnAndRow(19, $row)->getValue());
//echo $EDI856_856HEADER['segm_date']=$objWorksheet->getCellByColumnAndRow(19, $row)->getValue();
					$EDI856_856HEADER['ship_unit']=$objWorksheet->getCellByColumnAndRow(21, $row)->getValue();
					$EDI856_856HEADER['ship_transport']=$objWorksheet->getCellByColumnAndRow(24, $row)->getValue();
					$EDI856_856HEADER['ship_trailernumber']=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
					//$EDI856_856HEADER['ship_trailernumber']='';
					$EDI856_856HEADER['ship_transname']=$objWorksheet->getCellByColumnAndRow(26, $row)->getValue();
					$EDI856_856HEADER['Sociedad']=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
					
					//Validando EDI856_856HEADER
					$Val_EDI856_856HEADER=ValidarDatosNoVacios($EDI856_856HEADER,'EDI856 Cabecera');
					$Intepretacion=InterpretarValidacionNoVacio($Val_EDI856_856HEADER,$row);

					if($Intepretacion!='')
					{
						$Errores_EDI856++;
						LogProcesoError($this->log_proceso,$Intepretacion);
						$ErrorGlobal++;
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
					//$EDI855_PO_HEADER['PO_Date']=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
					//$EDI855_PO_HEADER['ACK_Date']=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
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
						$DatosOC855=$OC_YaRegistrada['DatosOC855']['DatosOC'];
						$EDI855_PO_HEADER['PO_Items']=count($DatosOC855);
						LogProceso($this->log_proceso,'OC 855 Encontrada no se necesita llamar al WS');
					}
					else
					{
						//Si no esta almacenada la OC Buscarla en el WS
						//Buscar OC 855
						LogProceso($this->log_proceso,'Buscando en WS OC '.substr($PO_Number,0,10));
						$DatosOC855=$WS_BuscarOC_855->WS_detalleordencompra(substr($PO_Number,0,10));
						LogProceso($this->log_proceso,'WS ejecutado para OC '.substr($PO_Number,0,10));
						
						$EDI855_PO_HEADER['PO_Items']=count($DatosOC855);
						//Almacenado datos en Array
						$ItemOC=getItemOrdenDeCompra(substr($PO_Number,0,10),$DatosOC855);
						$OC_855[substr($PO_Number,0,10)]=$ItemOC;
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
							LogProcesoError($this->log_proceso,$Intepretacion);
							$ErrorGlobal++;
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
						//Validar si PO_Item 
						if(!EsMultiplo(intval($objWorksheet->getCellByColumnAndRow(6, $row)->getValue()),10))
						{
							LogProcesoError($this->log_proceso,"El PO_Item (".$objWorksheet->getCellByColumnAndRow(6, $row)->getValue().") no es multiplo de 10 en la fila ".$row." verifique el valor ingresado");
							$ErrorGlobal++;
							break;
						}
						else
						{
							$EDI855_PO_DETAIL['PO_Item']=$objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
						}

					}
						
					//Validar PO_Item si es igual al de sap o sigue siendo null
					if($EDI855_PO_DETAIL['PO_Item']=="")
					{
						$Errores_EDI855++;
						LogProcesoError($this->log_proceso,"No se encontro relación de PO_Item(".$objWorksheet->getCellByColumnAndRow(6, $row)->getValue().") en SAP en la fila ".$row." verifique relación Numero OC con Product Number en SAP");
						$ErrorGlobal++;
					}
					
					//Buscar Faltantes por el PO_Item
					$DatosFaltantes=DatosDetalleOC_PO_Item($EDI855_PO_DETAIL['PO_Item'],$DatosOC855);
					
					//print_r($DatosFaltantes);
					
					//Validar que este correcta la relacion segun la posicion obtenida
					if(trim($DatosFaltantes['PO_PartNumber'])=="")
					{
						$Errores_EDI855++;
						LogProcesoError($this->log_proceso,"No se encontro el Numero de Parte en SAP en la fila ".$row." verifique relación Numero OC con PO_Item(".$EDI855_PO_DETAIL['PO_Item'].") en SAP");
						$ErrorGlobal++;
					}
					else
					{

						//Cargando datos de 855
						$EDI855_PO_DETAIL['PO_Number']=$PO_Number;
						$EDI855_PO_DETAIL['PO_PartNumber']=getPartNumber($Sociedad,$DatosFaltantes['PO_PartNumber']);
						$EDI855_PO_DETAIL['PO_Description']=$objWorksheet->getCellByColumnAndRow(11, $row)->getValue();
						$EDI855_PO_DETAIL['PO_Quantity']=$DatosFaltantes['PO_Quantity'];
						$EDI855_PO_DETAIL['PO_Unit']= $DatosFaltantes['PO_Unit'];
						$EDI855_PO_DETAIL['PO_Price']= $objWorksheet->getCellByColumnAndRow(9, $row)->getValue();
						$EDI855_PO_DETAIL['PO_Money']= $DatosFaltantes['PO_Money'];
						$EDI855_PO_DETAIL['ACK_PartNumber']=getPartNumber($Sociedad,$objWorksheet->getCellByColumnAndRow(12, $row)->getValue());
						$EDI855_PO_DETAIL['ACK_Date']=getFechaFormateada($objWorksheet->getCellByColumnAndRow(2, $row)->getValue());
//$EDI855_PO_DETAIL['ACK_Date']=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
						$EDI855_PO_DETAIL['ACK_Quantity']= $objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
						$EDI855_PO_DETAIL['ACK_Unit']= $objWorksheet->getCellByColumnAndRow(23, $row)->getValue();
						$EDI855_PO_DETAIL['PO_PriceOrig']= $DatosFaltantes['PO_Price'];
						
							
						//Validando EDI855_PO_DETAIL
						$Val_EDI855_PO_DETAIL=ValidarDatosNoVacios($EDI855_PO_DETAIL,'EDI855 Detalle');
						$Intepretacion=InterpretarValidacionNoVacio($Val_EDI855_PO_DETAIL,$row);
						if($Intepretacion!='')
						{
							$Errores_EDI855++;
							LogProcesoError($this->log_proceso,$Intepretacion);
							$ErrorGlobal++;
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
					$EDI810_InvoiceDetail['InvoiceNumber']=getInvoiceNumber2($Sociedad,$objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),getAnioDeFecha($objWorksheet->getCellByColumnAndRow(2, $row)->getValue()));
					
					//Validar InvoicePosition si es vacio tomar secuencial
					if(trim($objWorksheet->getCellByColumnAndRow(3, $row)->getValue())=="")
					{
						$EDI810_InvoiceDetail['InvoicePosition']=$SecuencialPosicion;
					}
					else
					{
						$EDI810_InvoiceDetail['InvoicePosition']=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
					}
					
					$EDI810_InvoiceDetail['PONumber']=$PO_Number;
					$EDI810_InvoiceDetail['POPosition']=$objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
					$EDI810_InvoiceDetail['ProductID']=getPartNumber($Sociedad,$objWorksheet->getCellByColumnAndRow(12, $row)->getValue());
					//$EDI810_InvoiceDetail['ProductDesciption']=$DatosFaltantes['PO_Description'];
					$EDI810_InvoiceDetail['ProductDesciption']=$objWorksheet->getCellByColumnAndRow(11, $row)->getValue();
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
						LogProcesoError($this->log_proceso,$Intepretacion);
						$ErrorGlobal++;
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
					if(trim(NumeroSinExponente($objWorksheet->getCellByColumnAndRow(5, $row)->getValue()))!="0")
					{
						LogProceso($this->log_proceso,'Buscando en excel');
						$FALTANTE_EDI856=getFALTANTE_EDI856();
						$FALTANTE_EDI856['ProductMeasure']     = $objWorksheet->getCellByColumnAndRow(23, $row)->getValue();
						$FALTANTE_EDI856['POPosition']    	   = $objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
						$FALTANTE_EDI856['PONumber']    	   = NumeroSinExponente($objWorksheet->getCellByColumnAndRow(5, $row)->getValue());
						$FALTANTE_EDI856['InvoicePosition']    = $EDI810_InvoiceDetail['InvoicePosition'];
						$FALTANTE_EDI856['PO_Quantity']   	   = $EDI810_InvoiceDetail['ProductQuantity'];
					}
					else
					{
						LogProceso($this->log_proceso,'Buscando en BD');
						
						//Validar si viene el Po Position
						if(trim($objWorksheet->getCellByColumnAndRow(6, $row)->getValue())=="")
						{
							$Errores_EDI856++;
							LogProcesoError($this->log_proceso,"No se encontro la PO Position en la fila ".$row."");
							$ErrorGlobal++;
						}
						
						$FALTANTE_EDI856=getDatosFaltantes_EDI856($Sociedad,getInvoiceNumber2($Sociedad,$objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),getAnioDeFecha($objWorksheet->getCellByColumnAndRow(19, $row)->getValue())),$objWorksheet->getCellByColumnAndRow(6, $row)->getValue(),getPartNumber($Sociedad,$objWorksheet->getCellByColumnAndRow(12, $row)->getValue()));
					}
					
					//Detalle 856
					$segm_Id=getInvoiceNumber2($Sociedad,$objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),getAnioDeFecha($objWorksheet->getCellByColumnAndRow(19, $row)->getValue()));
					$EDI856_856DETAIL = getEDI856_856DETAIL();
					$EDI856_856DETAIL['segm_Id']=$segm_Id;
					$EDI856_856DETAIL['it_prodid']=getPartNumber($Sociedad,$objWorksheet->getCellByColumnAndRow(12, $row)->getValue());
					$EDI856_856DETAIL['it_unitshiped']=$FALTANTE_EDI856['PO_Quantity'];
					$EDI856_856DETAIL['it_unitmeasurement']=$FALTANTE_EDI856['ProductMeasure'];
					$EDI856_856DETAIL['it_po']=$FALTANTE_EDI856['PONumber'];
					//$EDI856_856DETAIL['it_refnumber']=$objWorksheet->getCellByColumnAndRow(18, $row)->getValue();
					$EDI856_856DETAIL['it_refnumber']='0';
					$EDI856_856DETAIL['it_packingsleep']=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
					$EDI856_856DETAIL['it_poPosition']=$FALTANTE_EDI856['POPosition'];
					$EDI856_856DETAIL['invoicePosition']=$FALTANTE_EDI856['InvoicePosition'];
					$EDI856_856DETAIL['NET_WEIGHT']=$objWorksheet->getCellByColumnAndRow(10, $row)->getValue();
					$EDI856_856DETAIL['GROSS_WEIGHT']=$objWorksheet->getCellByColumnAndRow(16, $row)->getValue();
				
					
					//Validando EDI856_856DETAIL
					$Val_EDI856_856DETAIL=ValidarDatosNoVacios($EDI856_856DETAIL,'EDI856 Detalle');
					$Intepretacion=InterpretarValidacionNoVacio($Val_EDI856_856DETAIL,$row);
					if($Intepretacion!='')
					{
						$Errores_EDI856++;
						LogProcesoError($this->log_proceso,$Intepretacion);
						$ErrorGlobal++;
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

					$EDI856_856BULTO['fechaDespacho']=FechaCompletaFormateada(getFechaFormateada($objWorksheet->getCellByColumnAndRow(19, $row)->getValue()));
//$EDI856_856BULTO['fechaDespacho']=$objWorksheet->getCellByColumnAndRow(19, $row)->getValue();
					$EDI856_856BULTO['invNumber']=getInvoiceNumber2($Sociedad,$objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),getAnioDeFecha($objWorksheet->getCellByColumnAndRow(19, $row)->getValue()));
					
					//Calculando volumen
					$VolumenBulto=floatval($EDI856_856BULTO['longitud'])*floatval($EDI856_856BULTO['ancho'])*floatval($EDI856_856BULTO['alto']);
					$EDI856_856BULTO['volumen']=$VolumenBulto/1000000;
					$EDI856_856BULTO['unidVolumen']=utf8_decode("MT³");
					
					//Validando EDI856_856BULTO
					$Val_EDI856_856DETAIL=ValidarDatosNoVacios($EDI856_856BULTO,'EDI856 Bulto');
					$Intepretacion=InterpretarValidacionNoVacio($Val_EDI856_856DETAIL,$row);
					if($Intepretacion!='')
					{
						$Errores_EDI856++;
						LogProcesoError($this->log_proceso,$Intepretacion);
						$ErrorGlobal++;
					}
					else
					{
						array_push($A_856BULTO,$EDI856_856BULTO);
					}
				}
				//Control invoice number
				if($ControlInvoiceNumber!=getInvoiceNumber2($Sociedad,$objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),getAnioDeFecha($objWorksheet->getCellByColumnAndRow(2, $row)->getValue())))
				{
					$ControlInvoiceNumber=getInvoiceNumber2($Sociedad,$objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),getAnioDeFecha($objWorksheet->getCellByColumnAndRow(2, $row)->getValue()));
					$SecuencialPosicion=0;
				}
//echo "fin";
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
					LogProcesoError($this->log_proceso,$Intepretacion);
					$ErrorGlobal++;
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
			if($Errores_EDI810==0 && $Viene810 && $Errores_EDI855==0 && $Viene855 && $Errores_EDI856==0 && $Viene856 && $ErrorGlobal==0)
			{
				$BanderaValido=true;
				
				LogProcesoPrimario($this->log_proceso,"-Documento validado correctamente ingresando registros-");
				//Hacer grabar en base de datos
				
				//EDI 810//
				//Cabecera EDI810
				foreach ($InvoiceHeader as $Header) {
					LogProcesoInfo($this->log_proceso,IngresarCabeceraEDI810_Manual($Header));
//echo "AQUI11";	
				}

				//Detalle EDI810
				foreach ($InvoiceDetail as $Detalle) {
					LogProcesoInfo($this->log_proceso,IngresarDetalleEDI810_Manual($Detalle));
				}
				//Posiciones EDI810
				foreach ($InvoiceHeader as $Header) {
					LogProcesoInfo($this->log_proceso,GenerarPosicionesEDI810($Header));
				}
				LogProcesoAdvertencia($this->log_proceso,"-EDI810 ingreso terminado-");
				//echo "header";
				//EDI 855//
				//PO_HEADER EDI855
				foreach ($PO_HEADER as $PO_Header) {
					LogProcesoInfo($this->log_proceso,IngresarPO_HEADER_EDI855_Manual($PO_Header));
				}
				//PO_DETAIL EDI855
				foreach ($PO_DETAIL as $PO_Detail) {
					LogProcesoInfo($this->log_proceso,IngresarPO_DETAIL_EDI855_Manual($PO_Detail));
				}
				//PO_HEADER ESTADO_DIF_855 EDI855
				foreach ($PO_HEADER as $PO_Header) {
					LogProcesoInfo($this->log_proceso,ActualizarESTADO_DIF_855_Manual($PO_Header));
				}
				//PO_HEADER ESTADO_855 EDI855
				foreach ($PO_HEADER as $PO_Header) {
					LogProcesoInfo($this->log_proceso,ActualizarESTADO_855_Manual($PO_Header));
				}
				
				LogProcesoAdvertencia($this->log_proceso,"-EDI855 ingreso terminado-");
				
				//EDI 856//
				//Borrando el detalle
				foreach ($A_856HEADER as $Header) {
					LogProcesoInfo($this->log_proceso,BorrarDetalle856($Header));
				}
				
				//856DETAIL EDI856
				foreach ($A_856DETAIL as $Detalle) {
					LogProcesoInfo($this->log_proceso,Ingresar856DETAIL_Manual($Detalle));
				}
				//856BULTO EDI856
				foreach ($A_856BULTO as $Bulto) {
					LogProcesoInfo($this->log_proceso,Ingresar856BULTO_Manual($Bulto));
				}
				LogProcesoAdvertencia($this->log_proceso,"-EDI856 ingreso terminado-");
				
				//856HEADER EDI856
				foreach ($A_856HEADER as $Header) {
					LogProcesoInfo($this->log_proceso,Ingresar856HEADER_Manual($Header));
				}
				
				//Actualizando Peso Header
				foreach ($A_856DETAIL as $Detalle) {
					LogProcesoInfo($this->log_proceso,ActualizarPesoCarga856HEADER_Manual($Detalle));
				}
				
				//Posiciones EDI856
				foreach ($A_856HEADER as $Header) {
					LogProcesoInfo($this->log_proceso,GenerarPosicionesEDI856($Header));
				}
			}
			else
			{
				//Solo EDI810 y EDI855
				if($Errores_EDI810==0 && $Viene810 && $Errores_EDI855==0 && $Viene855 && $ErrorGlobal==0)
				{
					$BanderaValido=true;
					
					LogProcesoPrimario($this->log_proceso,"-Documento validado correctamente ingresando registros EDI810 y EDI855-");
					//Hacer grabar en base de datos
					
					//EDI 810//
					//Cabecera EDI810
					foreach ($InvoiceHeader as $Header) {
						LogProcesoInfo($this->log_proceso,IngresarCabeceraEDI810_Manual($Header));
					}
					//Detalle EDI810
					foreach ($InvoiceDetail as $Detalle) {
						LogProcesoInfo($this->log_proceso,IngresarDetalleEDI810_Manual($Detalle));
					}
					//Posiciones EDI810
					foreach ($InvoiceHeader as $Header) {
						LogProcesoInfo($this->log_proceso,GenerarPosicionesEDI810($Header));
					}
					LogProcesoAdvertencia($this->log_proceso,"-EDI810 ingreso terminado-");
					
					//EDI 855//
					//PO_HEADER EDI855
					foreach ($PO_HEADER as $PO_Header) {
						LogProcesoInfo($this->log_proceso,IngresarPO_HEADER_EDI855_Manual($PO_Header));
					}
					//PO_DETAIL EDI855
					foreach ($PO_DETAIL as $PO_Detail) {
						LogProcesoInfo($this->log_proceso,IngresarPO_DETAIL_EDI855_Manual($PO_Detail));
					}
					//PO_HEADER ESTADO_DIF_855 EDI855
					foreach ($PO_HEADER as $PO_Header) {
						LogProcesoInfo($this->log_proceso,ActualizarESTADO_DIF_855_Manual($PO_Header));
					}
					//PO_HEADER ESTADO_855 EDI855
					foreach ($PO_HEADER as $PO_Header) {
						LogProcesoInfo($this->log_proceso,ActualizarESTADO_855_Manual($PO_Header));
					}
					LogProcesoAdvertencia($this->log_proceso,"-EDI855 ingreso terminado-");
				}
				
				//Solo EDI856
				if($Errores_EDI856==0 && $Viene856 && $ErrorGlobal==0)
				{
					$BanderaValido=true;
					
					LogProcesoPrimario($this->log_proceso,"-Documento validado correctamente ingresando registros EDI856-");
					
					//EDI 856//
					//Borrando el detalle
					foreach ($A_856HEADER as $Header) {
						LogProcesoInfo($this->log_proceso,BorrarDetalle856($Header));
					}
					
					//856DETAIL EDI856
					foreach ($A_856DETAIL as $Detalle) {
						LogProcesoInfo($this->log_proceso,Ingresar856DETAIL_Manual($Detalle));
					}
					//856BULTO EDI856
					foreach ($A_856BULTO as $Bulto) {
						LogProcesoInfo($this->log_proceso,Ingresar856BULTO_Manual($Bulto));
					}
					
					//856HEADER EDI856
					foreach ($A_856HEADER as $Header) {
						LogProcesoInfo($this->log_proceso,Ingresar856HEADER_Manual($Header));
					}
					
					//Actualizando Peso Header
					foreach ($A_856DETAIL as $Detalle) {
						LogProcesoInfo($this->log_proceso,ActualizarPesoCarga856HEADER_Manual($Detalle));
					}
					
					//Posiciones EDI856
					foreach ($A_856HEADER as $Header) {
						LogProcesoInfo($this->log_proceso,GenerarPosicionesEDI856($Header));
					}
					
					LogProcesoAdvertencia($this->log_proceso,"-EDI856 ingreso terminado-");
				}
			}
			
			if(!$BanderaValido)
			{
				LogProcesoError($this->log_proceso,"-El documento posee uno o más errores, debe ser verificado-");
			}

			//Fin de hoja
			//Limpiar Sql

			//CerrarConeccionUnica();
			
			//unlink($this->sFileName);
		}
	}
?>