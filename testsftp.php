<?php
$connection = ssh2_connect('52.89.228.174', 22, array('hostkey'=>'ssh-rsa'));

if (ssh2_auth_pubkey_file($connection, 'sftp_user',
                          '',
                          'sftp_user.pem', '')) {
  echo "Public Key Authentication Successful\n";
} else {
  die('Public Key Authentication Failed');
}

/**
// Creando conexión a servidor SSH, puerto 22
$conexion = ssh2_connect("52.89.228.174", 22);
// Autenticandose en el servidor
ssh2_auth_password($conn, "sftp_user", "password");
// Solicitando subsistema SFTP
$sftp = ssh2_sftp($conn);
*/
?>