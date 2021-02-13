<?php
//error_reporting(0);
session_start();
			//$_SESSION["email"]=$email;
	include_once("SegAjax.php");
	$id_usuario=$_SESSION["email"];//_POST["idUsuario"];
	$accion=$_POST["accion_subida"];
	require_once($_SERVER['DOCUMENT_ROOT']."/funciones/fx_util.php");
	
	$mesajeError="";
	$mensajeProceso="";
	
	if($accion=='SUBIR_EDI')
	{
		if(isset($_FILES['archivo'])){
			echo "EXISTE";
		}
		
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
				
					if($extension=="csv")
					{	
						//$fechaArchivo=fechaFormateadaParaNombreArchivo();
						$NuevoArchivo='archivos/csv/'.$id_usuario.'_'.$fechaArchivo.'.csv';
						
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
						//Obtendremos el archivo guardado para procesarlo
						if($archivo_subido == true)
						{
							$nombreArchivo=$id_usuario.'_'.$fechaArchivo.'';
							echo "<script type='text/javascript'>parent.ProcesarCSV810('$id_usuario','$nombreArchivo');</script>";
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
			$proveedor=$_POST["txtIdProveedor"];
			
			if(trim($proveedor)=="")
			{
				$mesajeError="(!)- Error no se ingreso el proveedor para la carga -(!)";
				echo "<script type='text/javascript'>parent.resultadoErroneo('$mesajeError');</script>";
				echo "<script type='text/javascript'>parent.DetenerCarga();</script>";
				die();
			}
			
			//CorrectamenteRecibido	
			//Verificamos si existe un archivo a subir.
			if(isset($_FILES['archivo'])){
				//Validaremos la extension del archivo si es EDI o TXT			
				$trozos = explode(".", $_FILES['archivo']['name']); 
				$extension = end($trozos);
				$extension = strtolower($extension);
					if($extension=="xlsx")
					{	
						$fechaArchivo=date('dmY');//fechaFormateadaParaNombreArchivo();
						$NuevoArchivo='archivos/carga_masiva/'.$id_usuario.'_'.$fechaArchivo.'.xlsx';
						
						//Si el archivo puede ser subido igualará la variable $archivo_subido a true, sino a false.
						if(move_uploaded_file($_FILES['archivo']['tmp_name'], $NuevoArchivo)){
							$archivo_subido = true;
							$mensajeProceso="- Archivo subido XLSX al servidor para ser procesado. -";
//$var = '../EDI/carga_masiva/EdiManual.php	';
							echo "<script type='text/javascript'>parent.IngresarLog('N','$mensajeProceso');</script>";
							include('../carga_masiva/EdiManual.php');

							//$_SERVER['DOCUMENT_ROOT'].'/EDI/ajax/'.$NuevoArchivo;

							$nombre_fichero = $_SERVER['DOCUMENT_ROOT'].'/EDI/ajax/'.$NuevoArchivo;
							$CargarEDI_Manual = new CargarEDI_Manual($nombre_fichero,$proveedor,'<script type="text/javascript">parent.IngresarLog("#TIPO#","#LOG#");</script>');
							$CargarEDI_Manual->Iniciar();
							echo "<script type='text/javascript'>parent.FinCargaManual('".$_FILES['archivo']['name']."');</script>";
							echo "<script type='text/javascript'>parent.MensajeAlerta('Proceso de carga terminado');</script>";
							
							
						}else{
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
	if($accion=='SUBIR_ACTUALIZAR_EE')
	{
		if ($_FILES["archivo"]["error"] > 0) {
		  $mesajeError="(!)- ".FileUploadErrorMsg($_FILES["archivo"]["error"]." -(!)");
		  $archivo_subido = false;
		  echo "<script type='text/javascript'>parent.resultadoErroneoActualizarEE('$mesajeError');</script>";
		  echo "<script type='text/javascript'>parent.DetenerCargaActualizarEE();</script>";
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
					if($extension=="xlsx")
					{
						session_start();
						$id_usuario=$_SESSION["name"];
						$fechaArchivo=fechaFormateadaParaNombreArchivo();
						$NuevoArchivo='archivos/actualizar_ee/'.$id_usuario.'_'.$fechaArchivo.'.xlsx';
						
						//Si el archivo puede ser subido igualará la variable $archivo_subido a true, sino a false.
						if(move_uploaded_file($_FILES['archivo']['tmp_name'], $NuevoArchivo)){
							$archivo_subido = true;
							require_once($_SERVER['DOCUMENT_ROOT'].'/EDI/ActualizarEE.php');
							$nombre_fichero = $_SERVER['DOCUMENT_ROOT'].'/EDI/ajax/'.$NuevoArchivo;
							$Extraer_EE = new Extraer_EE($nombre_fichero,'<script type="text/javascript">parent.LogRegistroActualizarEE("#TIPO#","#LOG#");</script>');
							$Extraer_EE->Iniciar();
							
							echo "<script type='text/javascript'>parent.resultadoCorrectoActualizarEE('Datos cargados exitosamente');</script>";
							echo "<script type='text/javascript'>parent.DetenerCargaActualizarEE();</script>";
							
						}else{
							$archivo_subido = false;
							$mesajeError="(!)- Error en el servidor, no se pudo guardar el archivo para ser procesado -(!)";
							echo "<script type='text/javascript'>parent.resultadoErroneoActualizarEE('$mesajeError');</script>";
							echo "<script type='text/javascript'>parent.DetenerCargaActualizarEE();</script>";
						}
					}
					else
					{
						$archivo_subido = false;
						$mesajeError="(!)- El archivo para ser procesado tiene que ser .xlsx -(!)";
						echo "<script type='text/javascript'>parent.resultadoErroneoActualizarEE('$mesajeError');</script>";
						echo "<script type='text/javascript'>parent.DetenerCargaActualizarEE();</script>";
					}	
			}
			else
			{
				$mesajeError='(!)- No se encontro el archivo -(!)';
				echo "<script type='text/javascript'>parent.resultadoErroneoActualizarEE('$mesajeError');</script>";
				echo "<script type='text/javascript'>parent.DetenerCargaActualizarEE();</script>";
			}
		}
	}
	if($accion=='SUBIR_CARGA_MANUAL_COMEX')
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
				//Validaremos la extension del archivo si es EDI o TXT			
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
							require_once($_SERVER['DOCUMENT_ROOT'].'/EDI/carga_masiva/ComexManual.php');
							//$_SERVER['DOCUMENT_ROOT'].'/EDI/ajax/'.$NuevoArchivo;

							$nombre_fichero = $_SERVER['DOCUMENT_ROOT'].'/EDI/ajax/'.$NuevoArchivo;
							$CargaComex_Manual = new CargaComex_Manual($nombre_fichero,'<script type="text/javascript">parent.IngresarLog("#TIPO#","#LOG#");</script>');
							$CargaComex_Manual->Iniciar();
							echo "<script type='text/javascript'>parent.FinCargaManual('".$_FILES['archivo']['name']."');</script>";
							echo "<script type='text/javascript'>parent.MensajeAlerta('Proceso de carga terminado');</script>";
							
							
						}else{
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
	if($accion=='SUBIR_CARGA_MANUAL_CARGA_COMPRAS_COMEX')
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
				//Validaremos la extension del archivo si es EDI o TXT			
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
							require_once($_SERVER['DOCUMENT_ROOT'].'/EDI/carga_masiva/ComprasComexManual.php');
							//$_SERVER['DOCUMENT_ROOT'].'/EDI/ajax/'.$NuevoArchivo;

							$nombre_fichero = $_SERVER['DOCUMENT_ROOT'].'/EDI/ajax/'.$NuevoArchivo;
							$CargaComprasComex_Manual = new CargaComprasComex_Manual($nombre_fichero,'<script type="text/javascript">parent.IngresarLog("#TIPO#","#LOG#");</script>');
							$CargaComprasComex_Manual->Iniciar();
							echo "<script type='text/javascript'>parent.FinCargaManual('".$_FILES['archivo']['name']."');</script>";
							echo "<script type='text/javascript'>parent.MensajeAlerta('Proceso de carga terminado');</script>";
							
							
						}else{
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