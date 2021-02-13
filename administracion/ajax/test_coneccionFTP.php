<?php
	
	error_reporting(0);
	set_time_limit(120);
	
	$tipo_ftp = $_POST["tipo_ftp"];
	$Resultado='';
	$respuesta = array();
	$log = array();
	
	if($tipo_ftp == "1")
	{
		
		$Servidor 	= $_POST["Servidor"];
		$Puerto 	= $_POST["Puerto"];
		$Usuario  	= $_POST["Usuario"];
		$Contrasena = $_POST["Contrasena"];
		$RutaRemota = $_POST["RutaRemota"];
		
		$conn_id = ftp_connect($Servidor,$Puerto);
		
		if(!$conn_id)
		{
			array_push($log,'<i class="fa fa-times" aria-hidden="true"></i> No se pudo encontrar el servidor');
			$Resultado='Nok';
		}
		else
		{
			array_push($log,'<i class="fa fa-check" aria-hidden="true"></i> Servidor encontrado');
			
			// intentar iniciar sesión
			if (!ftp_login($conn_id, $Usuario, $Contrasena)) {
				array_push($log,'<i class="fa fa-times" aria-hidden="true"></i> Autentificación de usuario fallida');
				$Resultado='Nok';
			}
			else
			{
				array_push($log,'<i class="fa fa-check" aria-hidden="true"></i> Autentificación de usuario correcta');
				
				$carpetas = explode("/", $RutaRemota);
			
					try {
						//Creando carpetas de la ruta
						foreach ($carpetas as $carpeta) {
							ftp_mkdir($conn_id,$carpeta);
							ftp_chdir($conn_id,$carpeta);
						}
						
						//Validar
						$folder_exists = is_dir("ftp://$Usuario:$Contrasena@$Servidor/$RutaRemota");
						
						if(!$folder_exists)
						{
							array_push($log,'<i class="fa fa-times" aria-hidden="true"></i> Error al verificar directorio, verificar ruta o permisos del usuario para crear directorio');
							$Resultado='Nok';
						}
						
					} catch (Exception $e) {
						ftp_close($conn_id);
						array_push($log,'<i class="fa fa-times" aria-hidden="true"></i> Error al verificar directorio, verificar ruta o permisos del usuario para crear directorio');
						$Resultado='Nok';
					}
					
					if($Resultado!='Nok')
					{
						$Resultado='ok';
						array_push($log,'<i class="fa fa-check" aria-hidden="true"></i> Directorio verificado');
					}
					
					// cerrar la conexión ftp
					ftp_close($conn_id);
			}
		}
		
		array_push($respuesta,array(
					'Resultado'=>$Resultado,
					'Detalle'=>$log));
		
		echo json_encode($respuesta);
	}

	//Si es SFTP
	if($tipo_ftp == "2")
	{
		$Servidor 	= $_POST["Servidor"];
		$Puerto 	= $_POST["Puerto"];
		$Usuario  	= $_POST["Usuario"];
		$Contrasena = $_POST["Contrasena"];
		$Llave    	= $_POST["Llave"];
		$RutaRemota = $_POST["RutaRemota"];
		
		set_include_path($_SERVER['DOCUMENT_ROOT'].'/plugins/phpseclib');
		include('Net/SSH2.php');
		include('Net/SFTP.php');
		include('Crypt/RSA.php');

		$objFtp = new Net_SFTP($Servidor,$Puerto);
		
		$key=null;
		
		if($Llave!='')
		{
			try {
			$key = new Crypt_RSA();
			
			$LlaveDecode= base64_decode($Llave);
			
			$key->loadKey($LlaveDecode);
			
			}catch (Exception $e) {
				array_push($log,'<i class="fa fa-times" aria-hidden="true"></i> Error al cargar la llave RSA');
				$Resultado='Nok';
			}
			if($Resultado=='')
			{
				array_push($log,'<i class="fa fa-check" aria-hidden="true"></i> Llave RSA Cargada correctamente');
			}
		}
		else
		{
			$key=$Contrasena;
		}

		if (!$objFtp->login($Usuario, $key)) {
			array_push($log,'<i class="fa fa-times" aria-hidden="true"></i> No se pudo autentificar en el servidor');
			$Resultado='Nok';
		}
		else
		{
			array_push($log,'<i class="fa fa-check" aria-hidden="true"></i> Autentificado en el servidor correctamente');
			$carpetas = explode("/", $RutaRemota);
		
			try {
				//Creando carpetas de la ruta
				foreach ($carpetas as $carpeta) {
					$objFtp->mkdir($carpeta);
					$objFtp->chdir($carpeta);
				}
				
				//Validar si existe el directorio
				if(!$objFtp->is_dir('/'.$RutaRemota))
				{
					array_push($log,'<i class="fa fa-times" aria-hidden="true"></i> Error al verificar directorio, verificar ruta o permisos del usuario para crear directorio (/'.$RutaRemota.')');
					$Resultado='Nok';
					$objFtp->disconnect();
				}
			} catch (Exception $e) {
				array_push($log,'<i class="fa fa-times" aria-hidden="true"></i> Error al verificar directorio, verificar ruta o permisos del usuario para crear directorio');
				$Resultado='Nok';
				$objFtp->disconnect();
			}
			
			
			if($Resultado!='Nok')
			{
				$Resultado='ok';
				array_push($log,'<i class="fa fa-check" aria-hidden="true"></i> Directorio verificado');
			}
					
			// cerrar la conexión ftp
			$objFtp->disconnect();
		}
		
		array_push($respuesta,array(
					'Resultado'=>$Resultado,
					'Detalle'=>$log));
		
		echo json_encode($respuesta);
	}
?>