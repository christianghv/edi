<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/plugins/PHPExcel/Classes/PHPExcel.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/EDI/funciones/Funciones_compras_comex.php');
	ini_set('memory_limit', '512M');
	
	set_time_limit(600);
	//error_reporting(E_ERROR | E_WARNING | E_PARSE);
		
	class CargaComprasComex_Manual
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
			$Errores_CargaComex=0;
			$Factura="";
			$Guia="";
			
			$ComprasComex_Extraidos= array();
			
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
				$ComprasComex=getComprasComex();
				
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
				
				$ComprasComex['MAWB_B_L']=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
				$ComprasComex['Invoice']=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
				$ComprasComex['PackingSlip']=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
				$ComprasComex['Embarcador']=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
				$ComprasComex['RespCompras']=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
				$ComprasComex['RespComex']=$objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
				$ComprasComex['RespPartOperations']=$objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
				$ComprasComex['StatusAWB_BL_ParaEE']=$objWorksheet->getCellByColumnAndRow(7, $row)->getValue();
				$ComprasComex['ObservacionesCompras']=$objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
				
				//Validando Si trae las llaves primarias
				$Val_ComprasComex=ValidarDatosNoVaciosComprasComex($ComprasComex);
				$Intepretacion=InterpretarValidacionNoVacioComex($Val_ComprasComex,$row);
				if($Intepretacion!='')
				{
					$Errores_CargaComex++;
					LogProcesoError($this->log_proceso,$Intepretacion);
				}
				else
				{
					array_push($ComprasComex_Extraidos,$ComprasComex);
				}
				
			}//Fin recorriendo excel
			
			//Reniciando variables
			$ComprasComex=getComprasComex();
			
			$BanderaValido=false;
			
			//Verificar si viene completo
			if($Errores_CargaComex==0 && $MAWB_B_L_OK)
			{
				$BanderaValido=true;
				
				LogProcesoPrimario($this->log_proceso,"-Documento validado correctamente ingresando registros-");
				//Hacer grabar en base de datos
				
				//Ingresando Datos
				foreach ($ComprasComex_Extraidos as $ComprasComex) {
					LogProcesoInfo($this->log_proceso,IngresarDatosCargaComex_Manual($ComprasComex));
				}
				LogProcesoAdvertencia($this->log_proceso,"-Datos Compras Comex, ingreso terminado-");
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