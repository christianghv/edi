<?php
	require_once("TranformarEDI810aXML.php");
	require_once("ProcesarXML810.php");
	include_once("SegAjax.php");
	$sociedadObtenida= $_POST["sociedad"];
	
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
	
	if ($_FILES["archivo"]["error"] > 0) {
	  $mesajeError="(!)- ".FileUploadErrorMsg($_FILES["archivo"]["error"]." -(!)");
	  $archivo_subido = false;
	  echo "<script type='text/javascript'>parent.resultadoErroneo('$mesajeError');</script>";
	  echo "<script type='text/javascript'>parent.DetenerCarga();</script>";
	}
	else
	{
		//CorrectamenteRecibido		
		//Verificamos si existe un archivo a subir.
		if(isset($_FILES['archivo'])){
		
			//Validaremos la extension del archivo si es EDI o TXT			
			$trozos = explode(".", $_FILES['archivo']['name']); 
			$extension = end($trozos);
			$extension = strtolower($extension);
			//Formato
			$rdb_formato = $_POST["rdb_formato"];
			//Tipo documento
			$cboTipo = $_POST["cboTipo"];
			
			if($rdb_formato=="edi")
			{	
				if($extension=="edi" || $extension=="txt")
				{
					$fechaArchivo=fechaFormateadaParaNombreArchivo();
					$NuevoArchivo='archivos/bak/'.$_POST["sociedad"].'_'.$fechaArchivo.'.bak';
					
					//Si el archivo puede ser subido igualará la variable $archivo_subido a true, sino a false.
					if(move_uploaded_file($_FILES['archivo']['tmp_name'], $NuevoArchivo)){
						$archivo_subido = true;
						$mensajeProceso="- Archivo EDI subido al servidor para ser procesado. -";
						echo "<script type='text/javascript'>parent.resultadoActual('$mensajeProceso');</script>";
										
					}else{
						$archivo_subido = false;
						$mesajeError="(!)- Error en el servidor, no se pudo guardar el archivo para ser procesado -(!)";
						 echo "<script type='text/javascript'>parent.resultadoErroneo('$mesajeError');</script>";
						 echo "<script type='text/javascript'>parent.DetenerCarga();</script>";
					}
					
					//Obtendremos el archivo guardado en formato XML y que sea EDI810
					if($archivo_subido == true && (string)$cboTipo=="0")
					{
						$xmlEDI810=tranformarEDI810_XML($NuevoArchivo);
						$mensajeProceso="- Archivo Leido por el servidor -";
						echo "<script type='text/javascript'>parent.resultadoActual('$mensajeProceso');</script>";
						$NombreArchivo=$_POST["sociedad"]."_".$fechaArchivo;
						$ArchivoXmlCreado="archivos/xml/".$_POST["sociedad"]."_".$fechaArchivo.".xml";
						
						try {
								$fp=fopen($ArchivoXmlCreado,"x");
								fwrite($fp,$xmlEDI810);
								fclose($fp);
								$mensajeProceso="- XML creado y respaldado -";
								echo "<script type='text/javascript'>parent.resultadoActual('$mensajeProceso');</script>";
								$mensajeProceso="- Procesando XML creado..... -";
								echo "<script type='text/javascript'>parent.resultadoActual('$mensajeProceso');</script>";
								//echo "<script type='text/javascript'>parent.DetenerCarga();</script>";
								
								//$mensajeProceso=ProcesarXML810('$ArchivoXmlCreado', '$sociedadObtenida');
								echo "<script type='text/javascript'>parent.ProcesarXML810('$sociedadObtenida','$NombreArchivo');</script>";
								
							} catch (Exception $e) {
								$mesajeError="(!)- ".$e->getMessage()." -(!)";
								echo "<script type='text/javascript'>parent.resultadoErroneo('$mesajeError');</script>";
								echo "<script type='text/javascript'>parent.DetenerCarga();</script>";
							}
					}
					if($archivo_subido == true && (string)$cboTipo=="1")
					{
						$mesajeError="EDI 855 no soportado";
						echo "<script type='text/javascript'>parent.resultadoErroneo('$mesajeError');</script>";
						echo "<script type='text/javascript'>parent.DetenerCarga();</script>";
					}
					if($archivo_subido == true && (string)$cboTipo=="2")
					{
						$mesajeError="EDI 856 no soportado";
						echo "<script type='text/javascript'>parent.resultadoErroneo('$mesajeError');</script>";
						echo "<script type='text/javascript'>parent.DetenerCarga();</script>";
					}
				}
				else
				{
					$archivo_subido = false;
					$mesajeError="(!)- El archivo para ser procesado tiene que ser .edi o .txt -(!)";
					echo "<script type='text/javascript'>parent.resultadoErroneo('$mesajeError');</script>";
					echo "<script type='text/javascript'>parent.DetenerCarga();</script>";
				}	
			}
			if($rdb_formato=="csv")
			{
				if($extension=="csv")
				{	
					$fechaArchivo=fechaFormateadaParaNombreArchivo();
					$NuevoArchivo='archivos/csv/'.$_POST["sociedad"].'_'.$fechaArchivo.'.csv';
					
					//Si el archivo puede ser subido igualará la variable $archivo_subido a true, sino a false.
					if(move_uploaded_file($_FILES['archivo']['tmp_name'], $NuevoArchivo)){
						$archivo_subido = true;
						$mensajeProceso="- Archivo subido CSV al servidor para ser procesado. -";
						echo "<script type='text/javascript'>parent.resultadoActual('$mensajeProceso');</script>";
					}else{
						$archivo_subido = false;
						$mesajeError="(!)- Error en el servidor, no se pudo guardar el archivo para ser procesado -(!)";
						 echo "<script type='text/javascript'>parent.resultadoErroneo('$mesajeError');</script>";
						 echo "<script type='text/javascript'>parent.DetenerCarga();</script>";
					}
					//Obtendremos el archivo guardado en formato XML y que sea EDI810
					if($archivo_subido == true && (string)$cboTipo=="0")
					{
						$nombreArchivo=$_POST["sociedad"].'_'.$fechaArchivo.'';
						echo "<script type='text/javascript'>parent.ProcesarCSV810('$sociedadObtenida','$nombreArchivo');</script>";
					}
					if($archivo_subido == true && (string)$cboTipo=="1")
					{
						$mesajeError="EDI 855 no soportado";
						echo "<script type='text/javascript'>parent.resultadoErroneo('$mesajeError');</script>";
						echo "<script type='text/javascript'>parent.DetenerCarga();</script>";
					}
					if($archivo_subido == true && (string)$cboTipo=="2")
					{
						$mesajeError="EDI 856 no soportado";
						echo "<script type='text/javascript'>parent.resultadoErroneo('$mesajeError');</script>";
						echo "<script type='text/javascript'>parent.DetenerCarga();</script>";
					}
				}
				else
				{
					$archivo_subido = false;
					$mesajeError="(!)- El archivo para ser procesado tiene que ser .csv -(!)";
					echo "<script type='text/javascript'>parent.resultadoErroneo('$mesajeError');</script>";
					echo "<script type='text/javascript'>parent.DetenerCarga();</script>";
				}	
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

