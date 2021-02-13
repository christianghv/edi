<?php
	include_once("SegAjax.php");
	
	$id_usuario=$_POST["idUsuario"];
	
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
	
	if ($_FILES["archivoXLS"]["error"] > 0) {
	  $mesajeError="(!)- ".FileUploadErrorMsg($_FILES["archivoXLS"]["error"]." -(!)");
	  $archivo_subido = false;
	  echo "<script type='text/javascript'>parent.resultadoErroneoXLS('$mesajeError');</script>";
	  echo "<script type='text/javascript'>parent.DetenerCargaXLS();</script>";
	}
	else
	{
		//CorrectamenteRecibido
		//Verificamos si existe un archivo a subir.
		if(isset($_FILES['archivoXLS'])){
		
			//Validaremos la extension del archivo si es EDI o TXT			
			$trozos = explode(".", $_FILES['archivoXLS']['name']); 
			$extension = end($trozos);
			$extension = strtolower($extension);
			
				if($extension=="xls" || $extension=="xlsx")
				{	
					$fechaArchivo=fechaFormateadaParaNombreArchivo();
					$NuevoArchivo='archivos/xls/'.$id_usuario.'_'.$fechaArchivo.'.xls';
					
					//Si el archivo puede ser subido igualará la variable $archivo_subido a true, sino a false.
					if(move_uploaded_file($_FILES['archivoXLS']['tmp_name'], $NuevoArchivo)){
						$archivo_subido = true;
						$mensajeProceso="- Archivo subido XLS al servidor para ser procesado. -";
						echo "<script type='text/javascript'>parent.resultadoActualXLS('$mensajeProceso');</script>";
					}else{
						$archivo_subido = false;
						$mesajeError="(!)- Error en el servidor, no se pudo guardar el archivo para ser procesado -(!)";
						 echo "<script type='text/javascript'>parent.resultadoErroneoXLS('$mesajeError');</script>";
						 echo "<script type='text/javascript'>parent.DetenerCargaXLS();</script>";
					}
					//Obtendremos el archivo guardado para procesarlo
					if($archivo_subido == true)
					{
						$nombreArchivo=$id_usuario.'_'.$fechaArchivo.'';
						echo "<script type='text/javascript'>parent.ProcesarXLS810('$nombreArchivo');</script>";
					}
				}
				else
				{
					$archivo_subido = false;
					$mesajeError="(!)- El archivo para ser procesado tiene que ser .xls -(!)";
					echo "<script type='text/javascript'>parent.resultadoErroneoXLS('$mesajeError');</script>";
					echo "<script type='text/javascript'>parent.DetenerCargaXLS();</script>";
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

