<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/plugins/PHPExcel/Classes/PHPExcel.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/EDI/funciones/Funciones_comex.php');
	ini_set('memory_limit', '512M');
	
	set_time_limit(600);
	//error_reporting(E_ERROR | E_WARNING | E_PARSE);
		
	class CargaComex_Manual
	{
		private $sFileName;
		private $log_proceso;
		
		function __construct($sFileName,$log) {
			$this->sFileName=$sFileName;
			//$sFileName=$_SERVER['DOCUMENT_ROOT'].'/EDI/ajax/archivos/carga_masiva/EDIFINAL.xlsx';
			
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
			
			//Validadores
			$MAWB_B_L_OK=false;
			$MAWB_B_L='';
			
			$SecuencialPosicion=0;
			$Errores_Comex=0;
			$Factura="";
			$Guia="";
			
			$Comex_Extraidos= array();
			
			for ($row = 2; $row < $highestRow+1; $row++) {
				$SecuencialPosicion++;
				$MAWB_B_L=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
				//Validar si viene o no la sociedad
				if(trim($MAWB_B_L)=="")
				{
					if(!$MAWB_B_L_OK)
					{
						LogProcesoError($this->log_proceso,"-No se encontro MAWB/B/L# en el documento-");
					}
					break;
				}
				else
				{
					$MAWB_B_L_OK=true;
				}
				
				//Obteniendo DATOS
				$Comex=getComex();
				
				//Iniciando variables
				if($Factura=="")
				{
					$Factura=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
					$Guia=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
					
					//Validar si existe el numero de fatura y el bulto en la base de datos
					$ValRegistro=ExisteFacturaYBulto($objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),$objWorksheet->getCellByColumnAndRow(2, $row)->getValue());
					if($ValRegistro==0)
					{
						LogProcesoError($this->log_proceso,"-El registro en la fila ".$row." no esta ingresado en la Base de datos: Factura ".$objWorksheet->getCellByColumnAndRow(1, $row)->getValue().", Bulto ".$objWorksheet->getCellByColumnAndRow(2, $row)->getValue());
						$Errores_Comex++;
						break;
					}
				}
				
				//Validar si cambio factura
				if($objWorksheet->getCellByColumnAndRow(1, $row)->getValue()!="" && $objWorksheet->getCellByColumnAndRow(1, $row)->getValue()!=$Factura)
				{
					//Ha Cambiado la factura
					$Factura=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
					$Guia=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
					
					//Validar si existe el numero de fatura y el bulto en la base de datos
					$ValRegistro=ExisteFacturaYBulto($objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),$objWorksheet->getCellByColumnAndRow(2, $row)->getValue());
					if($ValRegistro==0)
					{
						LogProcesoError($this->log_proceso,"-El registro en la fila ".$row." no esta ingresado en la Base de datos: Factura ".$objWorksheet->getCellByColumnAndRow(1, $row)->getValue().", Bulto ".$objWorksheet->getCellByColumnAndRow(2, $row)->getValue());
						$Errores_Comex++;
						break;
					}
				}
				
				//Validar si guia ha cambiado
				if($objWorksheet->getCellByColumnAndRow(0, $row)->getValue()!=$Guia)
				{
					LogProcesoError($this->log_proceso,"-Error en Fila ".$row." el numero de Guia ingresado(".$objWorksheet->getCellByColumnAndRow(0, $row)->getValue().") es diferente al que ya estaba asociado(".$Guia.") de la factura ".$Factura."-");
					$Errores_Comex++;
					break;
				}
				
				$Comex['MAWB_B_L']=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
				$Comex['Invoice']=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
				$Comex['PackingSlip']=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
				$Comex['FechaPagoDerechos']=getFechaFormateada($objWorksheet->getCellByColumnAndRow(3, $row)->getValue());
				$Comex['1erPago_2doPago']=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
				$Comex['HoraPago']=getHoraMinutoFecha($objWorksheet->getCellByColumnAndRow(5, $row)->getValue());
				$Comex['FechaG_D']=getFechaFormateada($objWorksheet->getCellByColumnAndRow(6, $row)->getValue());
				$Comex['HoraG_D']=getHoraMinutoFecha($objWorksheet->getCellByColumnAndRow(7, $row)->getValue());
				$Comex['G_DAduana']=$objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
				$Comex['FechaRetiroPuerto_Aeropuerto']=getFechaFormateada($objWorksheet->getCellByColumnAndRow(9, $row)->getValue());
				$Comex['BoletoTransportes']=$objWorksheet->getCellByColumnAndRow(10, $row)->getValue();
				$Comex['ObservacionesComex']=$objWorksheet->getCellByColumnAndRow(11, $row)->getValue();
				
				//Validando Si trae las llaves primarias
				$Val_Comex=ValidarDatosNoVaciosComex($Comex);
				$Intepretacion=InterpretarValidacionNoVacioComex($Val_Comex,$row);
				if($Intepretacion!='')
				{
					$Errores_Comex++;
					LogProcesoError($this->log_proceso,$Intepretacion);
				}
				else
				{
					array_push($Comex_Extraidos,$Comex);
				}
				
			}//Fin recorriendo excel
			
			//Reniciando variables
			$Comex=getComex();
			
			$BanderaValido=false;
			
			//Verificar si viene completo
			if($Errores_Comex==0 && $MAWB_B_L_OK)
			{
				$BanderaValido=true;
				
				LogProcesoPrimario($this->log_proceso,"-Documento validado correctamente ingresando registros-");
				//Hacer grabar en base de datos
				
				//Ingresando Datos
				foreach ($Comex_Extraidos as $Comex) {
					LogProcesoInfo($this->log_proceso,IngresarDatosComex_Manual($Comex));
				}
				LogProcesoAdvertencia($this->log_proceso,"-Datos Comex, ingreso terminado-");
			}
			
			if(!$BanderaValido)
			{
				LogProcesoError($this->log_proceso,"-El documento posee uno o más errores, debe ser verificado-");
			}
			
			//Fin de hoja
			//Limpiar Sql
			CerrarConeccionUnica();
			
			unlink($this->sFileName);
		}
	}
?>