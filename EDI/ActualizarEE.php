<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/plugins/PHPExcel/Classes/PHPExcel.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/EDI/funciones/Funciones_edi.php');
	require_once($_SERVER['DOCUMENT_ROOT']."/funciones/fx_util.php");
	
	ini_set('memory_limit', '512M');
	ini_set('precision', '15');
	set_time_limit(600);
		
	class Extraer_EE
	{
	
		private $sFileName;
		private $log_proceso;
		
		function __construct($sFileName,$log) {
			$this->sFileName=$sFileName;
			
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
			
			$FacturasIngresadas=array();
			$FacturaActual='';
			
			for ($row = 2; $row < $highestRow+1; $row++) {
				
				$InvoiceNumber=trim($objWorksheet->getCellByColumnAndRow(0, $row)->getValue());
				$PONumber=floatval(substr(trim(NumeroSinExponente($objWorksheet->getCellByColumnAndRow(1, $row)->getValue())),0,10));
				$POPosition=floatval(trim($objWorksheet->getCellByColumnAndRow(2, $row)->getValue()));
				$EE=trim($objWorksheet->getCellByColumnAndRow(3, $row)->getValue());
				
				if($InvoiceNumber!="" && $PONumber!="" && $POPosition!="" && $EE!="")
				{
					if($FacturaActual!=$InvoiceNumber)
					{
						$FacturaActual=$InvoiceNumber;
						array_push($FacturasIngresadas, $FacturaActual);
					}
					$Ingreso=ActualizarEE_PorPosicion($InvoiceNumber,$PONumber,$POPosition,$EE);
					//$this->LogProceso('S',$Ingreso);
				}
				else
				{
					break;
				}
				
				//Actualizando los estados de las facturas de EE
				foreach ($FacturasIngresadas as $Factura) {
					$Actualizacion=ActualizarEstadoEE_Factura($Factura);
				}
				
			}//Fin recorriendo excel
			
			CerrarConeccionUnica();
			
			unlink($this->sFileName);
		}
		public function LogProceso($Tipo,$Mensaje)
		{
			//Asignar tipo
			$MensajeScript=str_replace('#TIPO#', $Tipo, $this->log_proceso);
			
			echo str_replace('#LOG#', LimpiarString(trim($Mensaje)), $MensajeScript);
		}
	}
?>