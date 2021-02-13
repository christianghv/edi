<?php
	error_reporting(0);
	$tamanoMaximoMB=2;
	$archivos_disp_ar = array('jpg', 'jpeg', 'gif','tif','png', 'tiff', 'bmp'); 
	
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
	
	$mesajeError="";
	$mensajeProceso="";
	$EDI=$_POST['Subir_Key_EDI'];
	
	if ($_FILES["Proveedor_key"]["error"] > 0) {
	  $mesajeError="(!)- ".FileUploadErrorMsg($_FILES["Proveedor_key"]["error"]." -(!)");
	  $archivo_subido = false;
	  $respuesta="Error en archivo recibido: $mesajeError";
	  echo "<script type='text/javascript'>parent.RespuestaSubidaKey('".$respuesta."','$EDI');</script>";
	}
	else
	{
		//CorrectamenteRecibido		
		//Verificamos si existe un archivo a subir.
		if(isset($_FILES['Proveedor_key'])){
			
			if (round(intval($_FILES["Proveedor_key"]["size"])/1048576, 2) > $tamanoMaximoMB)
			{
				$mesajeError.= " El archivo no puede superar los $tamanoMaximoMB Mb.";
			}
			//Validaremos la extension del archivo si es EDI o TXT			
			$trozos = explode(".", $_FILES['Proveedor_key']['name']); 
			$extension = end($trozos);
			$extension = strtolower($extension);
			
			if(empty($mesajeError))
			{
				$fechaArchivo=fechaFormateadaParaNombreArchivo();
				$NuevoArchivo='key/'.$fechaArchivo.'.'.$extension;
				//Si el archivo puede ser subido igualará la variable $archivo_subido a true, sino a false.
				//Convertirlo a base64
				$Datos = file_get_contents($_FILES['Proveedor_key']['tmp_name']);
				$Key64 = base64_encode($Datos);
				
				if($Key64!=''){
						$archivo_subido = true;
						echo "<script type='text/javascript'>parent.KeySubidaOK('$Key64','$EDI');</script>";
				}else{
						$archivo_subido = false;
						$mesajeError="(!)- Error en el servidor, no se pudo guardar el archivo para ser procesado -(!)";
						 echo "<script type='text/javascript'>alert('$mesajeError');</script>";
				}
			}
			else
			{
				$respuestaError="Error: ".$mesajeError."";
				echo "<script type='text/javascript'>parent.RespuestaSubidaKey('".$respuesta."','$EDI');</script>";
			}
		}
	}
?> 
    <!DOCTYPE html>
    <html>
        <head>
        </head>
        <body>
        </body>
    </html>

