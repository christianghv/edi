<?
require 'clases/class.templates.php';
require_once ("conect/conect.php");
include_once("SegAjax.php");

$link = conectar_srvdev();
require_once 'config.php';
require_once 'google/Google_Client.php';
require_once 'google/contrib/Google_Oauth2Service.php';

$client = new Google_Client();
$client->setApplicationName("Edi 2.0");
$client->setClientId(CLIENT_ID);
$client->setClientSecret(CLIENT_SECRET);
$client->setRedirectUri(REDIRECT_URI);
$client->setApprovalPrompt(APPROVAL_PROMPT);
$client->setAccessType(ACCESS_TYPE);
$oauth2 = new Google_Oauth2Service($client);

try {

	if (isset($_GET['code'])) {
		$client->authenticate($_GET['code']);
		$_SESSION['token'] = $client->getAccessToken();
		echo '<script type="text/javascript">window.close();</script>'; exit;
	}
	if (isset($_SESSION['token'])) {
		$client->setAccessToken($_SESSION['token']);
	}
	if (isset($_REQUEST['error'])) {
		echo '<script type="text/javascript">window.close();</script>'; exit;
	}

	if ($client->getAccessToken()) {
		$acceso 	= false;
		
		$token=$client->getAccessToken();
		$Dato=json_decode($token);
		
		$q = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$Dato->access_token;
		
		$json = file_get_contents($q);
		$userInfoArray = json_decode($json,true);
		$email = $userInfoArray['email'];
		$name = $userInfoArray['given_name'];
		$apellidos = $userInfoArray['family_name'];
		
		$sql  = "SELECT perfil FROM cfg_usuarios ";
		$sql .= "WHERE email = '" . $email . "'";
		$cur_e = sqlsrv_query($sql,$link);
		echo sqlsrv_has_rows($cur_e);
		if (sqlsrv_has_rows($cur_e) == 0)
		{
			header('Location: '.$base_url.'/logout.php');
		}
		else
		{
			$acceso = true;
		}

		$cur_usr = sqlsrv_fetch_array($cur_e);
		
		$perfil 	= $cur_usr["perfil"];

		if (empty($img))
			$img = "img/photo.png";
		$_SESSION['token'] = $client->getAccessToken();
		
		

		if ($acceso) {
			session_start();
			$_SESSION["email"]=$email;
			$email = $email;
			$image = $img;
			$name = $name;
			$apellidos = $apellidos;
			$personalMarkup = "$img?sz=50";
			session_register("email");
			session_register("image");
			session_register("name");
			session_register("apellidos");
			session_register("personalMarkup");
			session_register("perfil");
			header('Location: '.$base_url.'/internos.php');
		}
		else
			header('Location: '.$base_url.'/logout.php?error=Permi');
	} else {
		$authUrl = $client->createAuthUrl();
	}
} catch (Exception $e) {
		header('Location: '.$base_url.'/logout.php');
}

if(isset($authUrl)){$url = $authUrl;}else{ $url = '';}


$template = new templates("templates/index.html");
$template->setParams( array (
	'authUrl' => $url
));
$template->show();

$errorRecibido=$_GET['varE'];

if($errorRecibido=="Permi")
{
	echo '<script type="text/javascript">alert("El usuario no tiene los permisos para acceder al sitio");</script>';
}

?>


