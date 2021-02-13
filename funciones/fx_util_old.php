<?php
	function ms_escape_string($data) {
		if(trim($data)=="0")
		{
			return "0";
		}
        if ( !isset($data) or empty($data) ) return '';
        if ( is_numeric($data) ) return $data;

        $non_displayables = array(
            '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
            '/%1[0-9a-f]/',             // url encoded 16-31
            '/[\x00-\x08]/',            // 00-08
            '/\x0b/',                   // 11
            '/\x0c/',                   // 12
            '/[\x0e-\x1f]/'             // 14-31
        );
        foreach ( $non_displayables as $regex )
            $data = preg_replace( $regex, '', $data );
        $data = str_replace("'", "''", $data );
        return $data;
    }
	function FormatoNumber($Valor)
	{
		return number_format($Valor,0, ',', '.');
	}
	function FechaCompletaFormateada($Fecha)
	{
		$dia=substr($Fecha, 0,2);
		$mes=substr($Fecha, 3,2);
		$anio=substr($Fecha, 6,4);
		
		$FechaRetorno=$anio.'-'.$mes.'-'.$dia.' 00:00:00';
		
		return $FechaRetorno;
	}
	function FormatoCeros($Valor,$Ceros)
	{
		$NuevoValor=$Valor;
		if($Ceros>strlen($Valor.''))
		{
			$NuevoValor=trim(sprintf("%'0".$Ceros."d\n", $Valor));
		}
		return $NuevoValor;
	}
	function FileUploadErrorMsg($error_code) {
		switch ($error_code) {
			case UPLOAD_ERR_INI_SIZE: 
				return "El archivo es más grande que lo permitido por el Servidor."; 
			case UPLOAD_ERR_FORM_SIZE: 
				return "El archivo subido es demasiado grande."; 
			case UPLOAD_ERR_PARTIAL: 
				return "El archivo subido no se terminó de cargar."; 
			case UPLOAD_ERR_NO_FILE: 
				return "No se subió ningún archivo"; 
			case UPLOAD_ERR_NO_TMP_DIR: 
				return "Error del servidor: Falta el directorio temporal."; 
			case UPLOAD_ERR_CANT_WRITE: 
				return "Error del servidor: Error de escritura en disco"; 
			case UPLOAD_ERR_EXTENSION: 
				return "Error del servidor: Subida detenida por la extención";
		  default: 
				return "Error del servidor: ".$error_code; 
		}
	}
	function fechaFormateadaParaNombreArchivo()
	{
		$fecha=(string)date("Y-m-d h:i:s");
		//Se Creara el nombre del archivoGuardardado
		$reemplazar = array("-");
		$fechaSinGuion = str_replace($reemplazar, "", $fecha);
				
		//Se reemplazara el espacio en blanco por un guionbajo
		$reemplazar = array(" ");
		$fechaSeparada = str_replace($reemplazar, "_", $fechaSinGuion);
						
		//Se quitaran los :
		$reemplazar = array(":");
		$fechaSinDosPuntos = str_replace($reemplazar, "", $fechaSeparada);
		return $fechaSinDosPuntos;
	}
	function getFechaFormateada($excelDate)
	{
		$stringDate = PHPExcel_Style_NumberFormat::toFormattedString($excelDate, 'DD-MM-YYYY');
		return $stringDate;
	}
	function getHoraFormateada($excelDate)
	{
		$stringDate = PHPExcel_Style_NumberFormat::toFormattedString($excelDate, 'H:i:s');
		return $stringDate;
	}
	function getAnioDeFecha($excelDate)
	{
		$stringDate = PHPExcel_Style_NumberFormat::toFormattedString($excelDate, 'YYYY');
		return $stringDate;
	}
	function getHoraMinutoFecha($excelDate)
	{
		$stringDate = PHPExcel_Style_NumberFormat::toFormattedString($excelDate, 'H:i');
		return $stringDate;
	}
	function getPartNumber($Sociedad,$PartNumber)
	{
		$NuevoPartNumber=$PartNumber;
		//Validar si no son solo digitos (Es alfa Numerico)
		if(!ctype_digit(str_replace("-", "", $PartNumber)))
		{
			return $NuevoPartNumber;
		}
		if($Sociedad=="3003")
		{
			$FinPartNumber=substr($PartNumber, 7, 2);
			if($FinPartNumber === "00")
			{
				$NuevoPartNumber=substr($PartNumber, 0, 7).'-00';
			}
			else
			{
				$NuevoPartNumber=$PartNumber;
			}
		}
		return FormatoCeros($NuevoPartNumber,9);
	}
	function getInvoiceNumber($Sociedad,$InvoiceNumber,$Anio)
	{
		$NuevoInvoiceNumber=$InvoiceNumber;
		
		if($Sociedad=="3002" || $Sociedad=="3005")
		{
			$NuevoInvoiceNumber=$InvoiceNumber.'-'.$Anio;
		}
		return $NuevoInvoiceNumber;
	}
	function QuitarComasYPuntos($Valor)
	{
		$Eliminar = array(".", ",");
		return str_replace($Eliminar, "", $Valor);
	}
	function NumeroSinExponente($Valor)
	{
		$Respuesta="0";
		
		$pos = strpos($Valor, "-");
		
		if($pos)
		{
			$Respuesta= str_replace("-", "", $Valor);
		}
		else
		{
			$Respuesta= PHPExcel_Style_NumberFormat::toFormattedString($Valor, '0');
		}
		if(trim($Respuesta)=="")
		{
			$Respuesta="0";
		}
		return $Respuesta;
	}
	function FechaFormatoAnioMesDia($Fecha)
	{
		$dia=substr($Fecha, 0,2);
		$mes=substr($Fecha, 3,2);
		$anio=substr($Fecha, 6,4);
		
		$FechaRetorno=$anio.'-'.$mes.'-'.$dia;
		
		return $FechaRetorno;
	}
?>