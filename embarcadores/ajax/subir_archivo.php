<?php
session_start();
	include_once("SegAjax.php");
	require_once("../../funciones/fx_util.php");
	$id_usuario=$_SESSION["email"];//_POST["idUsuario"];
	$accion=$_POST["accion_subida"];
	$validacion_confirmacion=$_POST["validacion_confirmacion"];
	
	$mesajeError="";
	$mensajeProceso="";

	if($accion=='SUBIR_CARGA_MANUAL')
	{
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
				//Validaremos la extension del archivo si es xlsx
				$trozos = explode(".", $_FILES['archivo']['name']); 
				$extension = end($trozos);
				$extension = strtolower($extension);
					if($extension=="xlsx")
					{	
						$fechaArchivo=fechaFormateadaParaNombreArchivo();
						$NuevoArchivo='archivos/carga_masiva/'.$id_usuario.'_'.$fechaArchivo.'.xlsx';
						
						//Si el archivo puede ser subido igualará la variable $archivo_subido a true, sino a false.
						if(move_uploaded_file($_FILES['archivo']['tmp_name'], $NuevoArchivo)){
							$archivo_subido = true;
							$mensajeProceso="- Archivo subido XLSX al servidor para ser procesado. -";
							echo "<script type='text/javascript'>parent.IngresarLog('N','$mensajeProceso');</script>";
							require_once($_SERVER['DOCUMENT_ROOT'].'/embarcadores/EmbarcadorManual.php');

							$nombre_fichero = $_SERVER['DOCUMENT_ROOT'].'/embarcadores/ajax/'.$NuevoArchivo;
							$CargarEmabarcador_Manual = new CargarEmabarcador_Manual($nombre_fichero,$proveedor,'<script type="text/javascript">parent.IngresarLog("#TIPO#","#LOG#");</script>',$validacion_confirmacion);
							$CargarEmabarcador_Manual->Iniciar();
							echo "<script type='text/javascript'>parent.FinCargaManual('".$_FILES['archivo']['name']."');</script>";
							echo "<script type='text/javascript'>parent.MensajeAlerta('Proceso de carga terminado');</script>";
						}
						else
						{
							$archivo_subido = false;
							$mesajeError="(!)- Error en el servidor, no se pudo guardar el archivo para ser procesado -(!)";
							 echo "<script type='text/javascript'>parent.resultadoErroneo('$mesajeError');</script>";
							 echo "<script type='text/javascript'>parent.DetenerCarga();</script>";
						}
					}
					else
					{
						$archivo_subido = false;
						$mesajeError="(!)- El archivo para ser procesado tiene que ser .xlsx -(!)";
						echo "<script type='text/javascript'>parent.resultadoErroneo('$mesajeError');</script>";
						echo "<script type='text/javascript'>parent.DetenerCarga();</script>";
					}
			}
			else
			{
				$mesajeError='(!)- No se encontro el archivo -(!)';
				echo "<script type='text/javascript'>parent.resultadoErroneo('$mesajeError');</script>";
				echo "<script type='text/javascript'>parent.DetenerCarga();</script>";
			}
		}
	}
?>