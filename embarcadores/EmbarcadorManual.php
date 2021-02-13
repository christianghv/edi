<?php
	error_reporting(E_ALL);
	require_once($_SERVER['DOCUMENT_ROOT'].'/plugins/PHPExcel/Classes/PHPExcel.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/embarcadores/Funciones_embarcadores.php');
	ini_set('memory_limit', '512M');
	require_once($_SERVER['DOCUMENT_ROOT']."/conf_sap.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/funciones/fx_util.php");
	
	set_time_limit(600);
		
	class CargarEmabarcador_Manual
	{
		private $sFileName;
		private $log_proceso;
		private $proveedor;
		private $validacion_confirmacion;
		
		function __construct($sFileName,$proveedor,$log,$validacion_confirmacion) {
			$this->sFileName=$sFileName;
			$this->proveedor=$proveedor;
			
			if($log!='')
			{
				$this->log_proceso=$log;
			}
			$this->validacion_confirmacion=$validacion_confirmacion;
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
			
			$Sociedad='';
			
			$A_856BULTO= array();
			$A_EmbarqueSegundaInstancia= array();
			$A_EmbarqueTerceraInstancia= array();
			
			$Errores_856BULTO=0;
			$Errores_EmbarqueSegundaInstancia=0;
			$Errores_EmbarqueTerceraInstancia=0;
			
			$Viene856BULTO=false;
			$VieneEmbarqueSegundaInstancia=false;
			$VieneEmbarqueTerceraInstancia=false;
			$Factura="";
			$Guia="";
			
			$Errores_EnEmbarque=0;
			
			for ($row = 2; $row < $highestRow+1; $row++) {
				$Sociedad=trim($objWorksheet->getCellByColumnAndRow(0, $row)->getValue());
				$BultoDeclarado=false;
				
				//Validar si viene o no la sociedad
				if($Sociedad=="")
				{
					LogProcesoError($this->log_proceso,"-No se encontro la sociedad en el documento en la fila ".$row."-");
					break;
				}
				
				//Iniciando variables
				if($Factura=="")
				{
					$Factura=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
					$Guia=$objWorksheet->getCellByColumnAndRow(17, $row)->getValue();
					
					//Validar si existe el numero de fatura y el bulto en la base de datos
					$ValRegistro=ExisteFacturaYBulto($objWorksheet->getCellByColumnAndRow(3, $row)->getValue(),$objWorksheet->getCellByColumnAndRow(4, $row)->getValue());
					if($ValRegistro==0 && $this->validacion_confirmacion=="Nok")
					{
						LogProcesoError($this->log_proceso,"-El registro en la fila ".$row." no esta ingresado en la Base de datos: Factura ".$objWorksheet->getCellByColumnAndRow(3, $row)->getValue().", Bulto ".$objWorksheet->getCellByColumnAndRow(4, $row)->getValue());
						$Errores_EnEmbarque++;
						echo "<script type='text/javascript'>parent.MensajeAlerta('Error: Bulto no registrado en la Base de datos, confirme si desea registrarlo de todos modos para una factura sin bulto asociado, luego reinicie la carga');</script>";
						break;
					}
					
					//Verificando si se debe registrar de todos modos
					if($ValRegistro==0 && $this->validacion_confirmacion=="ok")
					{
						//Viene bulto y se forzara el registro de este bulto
						$EDI856_856BULTO_PrimeraInstanciaEmbarcador=getEDI856_856BULTO_PrimeraInstanciaEmbarcador();
						$EDI856_856BULTO_PrimeraInstanciaEmbarcador['RegistrarBultoConDetalle']='Si';
						$BultoDeclarado=true;
					}
				}
				
				//Validar si cambio factura
				if($objWorksheet->getCellByColumnAndRow(3, $row)->getValue()!="" && $objWorksheet->getCellByColumnAndRow(3, $row)->getValue()!=$Factura)
				{
					//Ha Cambiado la factura
					$Factura=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
					$Guia=$objWorksheet->getCellByColumnAndRow(17, $row)->getValue();
					
					
					//Validar si existe el numero de fatura y el bulto en la base de datos
					$ValRegistro=ExisteFacturaYBulto($objWorksheet->getCellByColumnAndRow(3, $row)->getValue(),$objWorksheet->getCellByColumnAndRow(4, $row)->getValue());
					if($ValRegistro==0 && $this->validacion_confirmacion=="Nok")
					{
						LogProcesoError($this->log_proceso,"-El registro en la fila ".$row." no esta ingresado en la Base de datos: Factura ".$objWorksheet->getCellByColumnAndRow(3, $row)->getValue().", Bulto ".$objWorksheet->getCellByColumnAndRow(4, $row)->getValue());
						$Errores_EnEmbarque++;
						echo "<script type='text/javascript'>parent.MensajeAlerta('Error: Bulto no registrado en la Base de datos, confirme si desea registrarlo de todos modos para una factura sin bulto asociado, luego reinicie la carga');</script>";
						break;
					}
					
					//Verificando si se debe registrar de todos modos
					if($ValRegistro==0 && $this->validacion_confirmacion=="ok")
					{
						//Viene bulto y se forzara el registro de este bulto
						$EDI856_856BULTO_PrimeraInstanciaEmbarcador=getEDI856_856BULTO_PrimeraInstanciaEmbarcador();
						$EDI856_856BULTO_PrimeraInstanciaEmbarcador['RegistrarBultoConDetalle']='Si';
						$BultoDeclarado=true;
					}
				}
				
				//Validar si guia ha cambiado
				if(trim($objWorksheet->getCellByColumnAndRow(17, $row)->getValue())!="")
				{
					if($objWorksheet->getCellByColumnAndRow(17, $row)->getValue()!=$Guia)
					{
						LogProcesoError($this->log_proceso,"-Error en Fila ".$row." el numero de Guia ingresado(".$objWorksheet->getCellByColumnAndRow(17, $row)->getValue().") es diferente al que ya estaba asociado(".$Guia.") de la factura ".$Factura."-");
						$Errores_EnEmbarque++;
						break;
					}
				}
				
				//Verificar si viene Bulto 856
				if(trim($objWorksheet->getCellByColumnAndRow(6, $row)->getValue())!="")
				{
					$Viene856BULTO=true;
					//Bulto 856
					if(!$BultoDeclarado)
					{
						$EDI856_856BULTO_PrimeraInstanciaEmbarcador=getEDI856_856BULTO_PrimeraInstanciaEmbarcador();
					}
					
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['Sociedad']=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['PONumber']=NumeroSinExponente($objWorksheet->getCellByColumnAndRow(1, $row)->getValue());
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['InvoiceVendor']=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['invNumber']=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['idenBulto']=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['tipoBulto']=$objWorksheet->getCellByColumnAndRow(12, $row)->getValue();
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['Overpack']=NumeroSinExponente($objWorksheet->getCellByColumnAndRow(5, $row)->getValue());
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['fechaDespacho']=FechaCompletaFormateada(getFechaFormateada($objWorksheet->getCellByColumnAndRow(6, $row)->getValue()));
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['longitud']=$objWorksheet->getCellByColumnAndRow(7, $row)->getValue();
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['ancho']=$objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['alto']=$objWorksheet->getCellByColumnAndRow(9, $row)->getValue();
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['peso']=$objWorksheet->getCellByColumnAndRow(10, $row)->getValue();
					
					//Calculando volumen
					$VolumenBulto=floatval($EDI856_856BULTO_PrimeraInstanciaEmbarcador['longitud'])*floatval($EDI856_856BULTO_PrimeraInstanciaEmbarcador['ancho'])*floatval($EDI856_856BULTO_PrimeraInstanciaEmbarcador['alto']);
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['volumen']=$VolumenBulto/1000000;
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['unidVolumen']=utf8_decode("MT³");
					
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['tipoCargaAerea']=$objWorksheet->getCellByColumnAndRow(13, $row)->getValue();
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['unidPeso']='KG';
					$EDI856_856BULTO_PrimeraInstanciaEmbarcador['unidDimension']='CM';
						
					//Validando EDI856_856BULTO_PrimeraInstanciaEmbarcador
					$Val_EDI856_856DETAIL=ValidarDatosNoVacios($EDI856_856BULTO_PrimeraInstanciaEmbarcador,'EDI856 Bulto');
					$Intepretacion=InterpretarValidacionNoVacio($Val_EDI856_856DETAIL,$row);
					if($Intepretacion!='')
					{
						$Errores_856BULTO++;
						LogProcesoError($this->log_proceso,$Intepretacion);
					}
					else
					{
						array_push($A_856BULTO,$EDI856_856BULTO_PrimeraInstanciaEmbarcador);
					}
				}
				
				//Verificar si viene primera parte Embarque(2da instancia)
				if(trim($objWorksheet->getCellByColumnAndRow(14, $row)->getValue())!="")
				{
					$VieneEmbarqueSegundaInstancia=true;
					
					$EmbarqueSegundaInstancia = getEmbarqueSegundaInstancia();
					$EmbarqueSegundaInstancia['Sociedad']=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
					$EmbarqueSegundaInstancia['PONumber']=NumeroSinExponente($objWorksheet->getCellByColumnAndRow(1, $row)->getValue());
					$EmbarqueSegundaInstancia['InvoiceVendor']=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
					$EmbarqueSegundaInstancia['InvoiceNumber']=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
					$EmbarqueSegundaInstancia['Ship_trailernumber']=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
					$EmbarqueSegundaInstancia['Overpack']=NumeroSinExponente($objWorksheet->getCellByColumnAndRow(5, $row)->getValue());
					$EmbarqueSegundaInstancia['InstruccionCompras']=$objWorksheet->getCellByColumnAndRow(14, $row)->getValue();
					$EmbarqueSegundaInstancia['FechaInstruccion']=FechaCompletaFormateada(getFechaFormateada($objWorksheet->getCellByColumnAndRow(15, $row)->getValue()));
					$EmbarqueSegundaInstancia['Comentario']=$objWorksheet->getCellByColumnAndRow(16, $row)->getValue();
					$EmbarqueSegundaInstancia['MawbBL']=$objWorksheet->getCellByColumnAndRow(17, $row)->getValue();
					$EmbarqueSegundaInstancia['PMC_CargoContenedor']=$objWorksheet->getCellByColumnAndRow(18, $row)->getValue();
					$EmbarqueSegundaInstancia['ship_transport']=$objWorksheet->getCellByColumnAndRow(19, $row)->getValue();
					$EmbarqueSegundaInstancia['LCL_FCL']=$objWorksheet->getCellByColumnAndRow(20, $row)->getValue();
					$EmbarqueSegundaInstancia['ID_Contenedor']=$objWorksheet->getCellByColumnAndRow(21, $row)->getValue();
					$EmbarqueSegundaInstancia['Value_Total_BL_AWB']=$objWorksheet->getCellByColumnAndRow(22, $row)->getValue();
					$EmbarqueSegundaInstancia['ETA']=FechaCompletaFormateada(getFechaFormateada($objWorksheet->getCellByColumnAndRow(23, $row)->getValue()));
					$EmbarqueSegundaInstancia['Flight_Vessel']=$objWorksheet->getCellByColumnAndRow(24, $row)->getValue();
					$EmbarqueSegundaInstancia['ETD']=FechaCompletaFormateada(getFechaFormateada($objWorksheet->getCellByColumnAndRow(25, $row)->getValue()));
					$EmbarqueSegundaInstancia['Puerto_Aeropuerto']=$objWorksheet->getCellByColumnAndRow(26, $row)->getValue();
					
					//Validando EmbarqueSegundaInstancia
					$Val_EmbarqueSegundaInstancia=ValidarDatosNoVacios($EmbarqueSegundaInstancia,'Embarque Segunda Instancia');
					$Intepretacion=InterpretarValidacionNoVacio($Val_EmbarqueSegundaInstancia,$row);
					if($Intepretacion!='')
					{
						$Errores_EmbarqueSegundaInstancia++;
						LogProcesoError($this->log_proceso,$Intepretacion);
					}
					else
					{
						array_push($A_EmbarqueSegundaInstancia,$EmbarqueSegundaInstancia);
					}
				}
				
				//Validando segunda parte (3era instancia)
				if(trim($objWorksheet->getCellByColumnAndRow(29, $row)->getValue())!="")
				{
					$VieneEmbarqueTerceraInstancia=true;
					
					$EmbarqueTerceraInstancia = getEmbarqueTerceraInstancia();
					$EmbarqueTerceraInstancia['Sociedad']=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
					$EmbarqueTerceraInstancia['PONumber']=NumeroSinExponente($objWorksheet->getCellByColumnAndRow(1, $row)->getValue());
					$EmbarqueTerceraInstancia['InvoiceVendor']=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
					$EmbarqueTerceraInstancia['InvoiceNumber']=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
					$EmbarqueTerceraInstancia['Ship_trailernumber']=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
					$EmbarqueTerceraInstancia['Overpack']=NumeroSinExponente($objWorksheet->getCellByColumnAndRow(5, $row)->getValue());
					$EmbarqueTerceraInstancia['FechaArribo']=FechaCompletaFormateada(getFechaFormateada($objWorksheet->getCellByColumnAndRow(27, $row)->getValue()));
					$EmbarqueTerceraInstancia['FechaEntrega']=FechaCompletaFormateada(getFechaFormateada($objWorksheet->getCellByColumnAndRow(28, $row)->getValue()));
					$EmbarqueTerceraInstancia['HoraEntrega']=getHoraFormateada($objWorksheet->getCellByColumnAndRow(29, $row)->getValue());
					
					//Validando EmbarqueTerceraInstancia
					$Val_EmbarqueTerceraInstancia=ValidarDatosNoVacios($EmbarqueTerceraInstancia,'Embarque Tercera Instancia');
					$Intepretacion=InterpretarValidacionNoVacio($Val_EmbarqueTerceraInstancia,$row);
					if($Intepretacion!='')
					{
						$Errores_EmbarqueTerceraInstancia++;
						LogProcesoError($this->log_proceso,$Intepretacion);
					}
					else
					{
						array_push($A_EmbarqueTerceraInstancia,$EmbarqueTerceraInstancia);
					}
				}
				
			}//Fin recorriendo excel
			
			//Si viene Bulto856
			if($Viene856BULTO && $Errores_856BULTO==0 && $Errores_EnEmbarque==0)
			{
				$BanderaValido=true;
				
				LogProcesoPrimario($this->log_proceso,"-Documento validado correctamente ingresando registros-");
				//EDI Bulto856//
				foreach ($A_856BULTO as $EDI856_856BULTO_PrimeraInstanciaEmbarcador) {
					LogProcesoInfo($this->log_proceso,Ingresar856BULTO_Manual($EDI856_856BULTO_PrimeraInstanciaEmbarcador));
					LogProcesoInfo($this->log_proceso,IngresarEmbarquePrimeraInstancia($EDI856_856BULTO_PrimeraInstanciaEmbarcador));
				}
				LogProcesoAdvertencia($this->log_proceso,"-EDI856_856BULTO y embarcador Primera Instancia ingreso terminado-");
			}
				
			//Si viene EmbarqueSegundaInstancia
			if($VieneEmbarqueSegundaInstancia && $Errores_EmbarqueSegundaInstancia==0 && $Errores_EnEmbarque==0)
			{
				$BanderaValido=true;
				
				LogProcesoPrimario($this->log_proceso,"-Documento validado correctamente ingresando registros-");
				//EmbarqueSegundaInstancia//
				foreach ($A_EmbarqueSegundaInstancia as $EmbarqueSegundaInstancia) {
					LogProcesoInfo($this->log_proceso,IngresarEmbarqueSegundaInstancia($EmbarqueSegundaInstancia));
				}
				LogProcesoAdvertencia($this->log_proceso,"-Ingresar Embarque Segunda Instancia ingreso terminado-");
			}
				
			//Si viene EmbarqueTerceraInstancia
			if($VieneEmbarqueTerceraInstancia && $Errores_EmbarqueTerceraInstancia==0 && $Errores_EnEmbarque==0)
			{
				$BanderaValido=true;
				
				LogProcesoPrimario($this->log_proceso,"-Documento validado correctamente ingresando registros-");
				//EmbarqueTerceraInstancia//
				foreach ($A_EmbarqueTerceraInstancia as $EmbarqueTerceraInstancia) {
					LogProcesoInfo($this->log_proceso,IngresarEmbarqueTerceraInstancia($EmbarqueTerceraInstancia));
				}
				LogProcesoAdvertencia($this->log_proceso,"-Ingresar Embarque Tercera Instancia ingreso terminado-");
			}
			
			if(!$BanderaValido)
			{
				LogProcesoError($this->log_proceso,"-El documento posee uno o más errores, debe ser verificado-");
			}
			
			//Limpiar Sql
			CerrarConeccionUnica();
			
			unlink($this->sFileName);
		}
	}
?>