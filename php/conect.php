<?
function conexion(){

/*   $hostname = "srvdev"; // DESARROLLO
      $username = "user_d_bpm";
      $password = "BpM2310";
      $dbName = "d_BPM";
	  
         $hostname = "AVELLANO"; 
      $username = "user_gps";
      $password = "UserGPS2008";
      $dbName = "GPS_V2";


 $hostname = "srvsql2005"; // DESARROLLO
      $username = "Ubpm";
      $password = "u22bpm06";
      $dbName = "BPM";
*/
	 
	  $hostname = "srvsql2005"; // DES
      $username = "user_d_bpm";
      $password = "BpM2310";
      $dbName = "d_BPM";



 
  $conectID = sqlsrv_connect($hostname,$username,$password) or DIE("NO SE HA PODIDO ESTABLECER CONEXIONnn CON EL SERVIDOR.");
  sqlsrv_select_db($dbName) or DIE("LA BASE DE DATOS NO RESPONDE");
  return $conectID;
}
 
function depurar_texto($texto){
	$val = str_replace("'","",$texto);
	$val = str_replace('"',"",$val);
	$val = str_replace("(","",$val);
	$val = str_replace(")","",$val);
	$val = str_replace("/","",$val);
	return $val;
}

function primera_mayuscula($texto){
	$cadena = ucfirst(strtolower($texto));
	return $cadena;
}

function primeras_en_mayuscula($texto){
	$cadena = ucwords(strtolower($texto));
	return $cadena;
}

function nombre_usuario($rut_usu){
	$sql = "SELECT nombre_usuario FROM Usuario WHERE (ltrim(rtrim(rut_usuario)) = ltrim(rtrim('$rut_usu')))";
	$rec_usu = sqlsrv_query($sql);
	$row_usu = sqlsrv_fetch_array($rec_usu);
	return $row_usu[0];
}

function ver_correo($rut_usu){
	$sql = "SELECT email FROM Usuario WHERE (ltrim(rtrim(rut_usuario)) = ltrim(rtrim('$rut_usu')))";
	$rec_usu = sqlsrv_query($sql);
	$row_usu = sqlsrv_fetch_array($rec_usu);
	return $row_usu[0];
}

function enviar_correo($email_usuario,$asunto,$mensaje){
	$cabeceras  = "From: Sistema de Gestión de Transportes\n";
	$cabeceras .= "MIME-Version: 1.0\r\n";
	$cabeceras .= "Content-type: text/html; charset=iso-8859-1\r\n";
	//$mail_usu  = "fernando.delarosa@kcl.cl";
	$email_usuario  = "christian.hernandez@kcl.cl";
	//$mail_usu  = "christian.hernandez@kcl.cl";
	mail($email_usuario, $asunto, $mensaje, $cabeceras);
}

function enviar_correo_jefe($email_usuario,$asunto,$mensaje){
	$cabeceras  = "From: Sistema de Gestión de Transportes\n";
	$cabeceras .= "MIME-Version: 1.0\r\n";
	$cabeceras .= "Content-type: text/html; charset=iso-8859-1\r\n";
	//$mail_usu  = "fernando.delarosa@kcl.cl";
	$email_usuario  = "christian.hernandez@kcl.cl";
	//$mail_usu  = "christian.hernandez@kcl.cl";
	mail($email_usuario, $asunto, $mensaje, $cabeceras);
}

?>