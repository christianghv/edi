<?	
include_once("SegAjax.php");
error_reporting(0);
$ambiente = "QAS";

if ($ambiente == "DEV")
{
	$UrlWSDespi="172.21.1.71";
	$PuertoWSDespi="50000";
	$usuarioWSDespi="chernandez_s";
	$contrasenaWSDespi="cobreloa98";
//	$host = "10.4.52.113";
//	$port = "50000";
}

if ($ambiente == "QAS")
{
	/*$UrlWSDespi="cvwqpo";
	$PuertoWSDespi="50000";
	$usuarioWSDespi="chernandez_s";
	$contrasenaWSDespi="cobreloa98";*/
	$UrlWSDespi="cvwqpo.kccl.cl";
	$PuertoWSDespi="50000";
	$usuarioWSDespi="pormeno_qym";
	$contrasenaWSDespi="sappi03";	
}

if ($ambiente == "PRO")
{
	$UrlWSDespi="cmwppi01";
	$PuertoWSDespi="50000";
	$usuarioWSDespi="";
	$contrasenaWSDespi="";
}


		

?>
