<?php
	set_include_path('../plugins/phpseclib');
	include('Net/SSH2.php');
	include('Net/SFTP.php');
	include('Crypt/RSA.php');

	$objFtp = new Net_SFTP('172.24.4.67');
	$key = new Crypt_RSA();
	$key->loadKey(file_get_contents('sftp_user.txt'));
	if (!$objFtp->login('sftp_user2', $key)) {
		exit('Login Failed');
	}

	$ruta_remota  = "pub_in/pub_2/pub_3";
	$carpetas = explode("/", $ruta_remota);

	foreach ($carpetas as $carpeta) {
		$objFtp->mkdir($carpeta);
		$objFtp->chdir($carpeta);
	}

	//$objFtp->chdir('pub_in');
	//$objFtp->mkdir('pub_in/pub_2/pub_3');

	// outputs the contents of filename.remote to the screen
	// copies filename.remote to filename.local from the SFTP server

	//echo $objFtp->pwd() . "\r\n";

	echo "<br /> Listo";

	$objFtp->disconnect();
?>