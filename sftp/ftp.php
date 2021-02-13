
<?php
                     
	$ftp_server = "172.30.1.14 ";
	$ftp_user = "d_cotiza_kcl";
	$ftp_pass = "gH4rrm67";

	// establecer una conexión o finalizarla
	$conn_id = ftp_connect($ftp_server,21) or die("No se pudo conectar a $ftp_server"); 

	// intentar iniciar sesión
	if (!ftp_login($conn_id, $ftp_user, $ftp_pass)) {
		exit('NoLogin');
	}
	
	$ruta_remota  = 'hola/test';
		$carpetas = explode("/", $ruta_remota);
		
		try {
			//Creando carpetas de la ruta
			foreach ($carpetas as $carpeta) {
				ftp_mkdir($conn_id,$carpeta);
				ftp_chdir($conn_id,$carpeta);
			}
		} catch (Exception $e) {
			ftp_close($conn_id);  
			echo 'ErrorRuta';
		}

	// cerrar la conexión ftp
	ftp_close($conn_id);  
?>
