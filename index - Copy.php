<?php
//error_reporting(0);
//echo "hola";


require 'clases/class.templates.php';
require_once ("conect/conect.php");
include_once("SegAjax.php");

$link = conectar_srvdev();

require_once 'config.php';
require_once 'azure/Azure_AD_Helper.php';
$client = new AzureADHelper();
$client->setClientId(CLIENT_ID);
$client->setClientSecret(CLIENT_SECRET);
$client->setRedirectUri(REDIRECT_URI);


try {
	if (isset($_GET['code'])) {
		//$client->authenticate($_GET['code']);
		echo $_SESSION['token'] = $client->getAccessToken($_GET['code']);
//alert($client->getAccessToken($_GET['code']));		
echo '<script type="text/javascript">window.close();</script>'; exit;
	}

		//$_SESSION['token'] = "dsdsd";
	//$token = "sdsdsd";
	//$email = "cdiaz_scl@kccl.cl";
	if (isset($_SESSION['token'])) {
	//if (isset($token)) {
		print_r($client);
		//$token =  $_SESSION['token'];

		session_start();
		$_SESSION['token'] = $token;
		$acceso = true;	
		
		//error_log('sesssion');
		//error_log($_SESSION['token']);
		
		$email = $client->getEmail($token);
		$name = $client->getGivenName($token);
		$apellidos = $client->getFamilyName($token);	
		
		//$email = "cdiaz_scl@kccl.cl";
		//$name = "cristian";
		//$apellidos = "diaz";
		
       echo  $sql  = "SELECT perfil FROM cfg_usuarios ";
		echo $sql .= "WHERE email = '" . $email . "'";
		$cur_e = sqlsrv_query($link, $sql);
			exit(0);
		if (sqlsrv_has_rows($cur_e) == 0)
		{
            $acceso = false;
			$e = 1;
			//header('Location: '.$base_url.'/logout.php');
		}
		else
		{
			$acceso = true;
			$e = 2;
		}
		$cur_usr = sqlsrv_fetch_array($cur_e);
		
		$perfil = $cur_usr["perfil"];

		if (empty($img))
			$img = "img/photo.png";
            
		if ($acceso) {
            session_start();
			$_SESSION["email"]=$email;
			$email = $email;
			$image = $img;
			$name = $name;
			$apellidos = $apellidos;
			$personalMarkup = "$img?sz=50";
			$_SESSION['email'] = $email	;
			$_SESSION['image'] = $image;
			$_SESSION['name'] = $name;
			$_SESSION['apellidos'] = $apellidos;
			$_SESSION['personalMarkup'] = $personalMarkup;
			$_SESSION['perfil'] = $perfil;
			/*session_register("email");
			session_register("image");
			session_register("name");
			session_register("apellidos");
			session_register("personalMarkup");
			session_register("perfil");*/
			//header('Location: '.$base_url.'/internos.php');
			header('Location: /internos.php');
		}
	} 
} catch (Exception $e) {
		//header('Location: '.$base_url.'/logout.php');
		header('Location: /logout.php');
}



if(isset($authUrl)){$url = $authUrl;}else{ $url = '';}


$template = new templates("templates/index.html");
$template->setParams( array (
	'authUrl' => $client->getAutorizationUrl()
));
$template->show();

$errorRecibido=$_GET['varE'];

if($errorRecibido=="Permi")
{
	echo '<script type="text/javascript">alert("El usuario no tiene los permisos para acceder al sitio");</script>';
}

?>

