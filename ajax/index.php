<?php
session_start();
error_reporting(0);
include_once("SegAjax.php");
include("../conect/conect.php");

$filter = new InputFilter();

$accion = $filter->process($_POST['accion']);

function eliminaNull($valor) {
	return str_replace("null","",$valor);
}
	
if($accion == "validaIngreso")
{
	$user = $_POST['user'];
	$user = addslashes($user);
	$_MensajeError="ERROR: caracteres no admitidos";
	
	if (ereg("[^A-Za-z0-9]+",$user)) {
	//Caracteres no permitidos
	echo $_MensajeError;
	die();
	}
	else{
	$user=$user;
	} 
	//$user = $_POST["user"];
	$pass = $_POST['pass'];
	$pass = md5($pass);
	
	$link = conectar_srvdev();
	$retorno = array();
	
	$sql = " SELECT * FROM cfg_Proveedores WHERE id_proveedor = '".$user."' AND pass = '".$pass."' ";
	$consulta = sqlsrv_query($sql, $link);

	if (sqlsrv_has_rows($consulta) > 0) 
	{
		$_SESSION["id_proveedor"]=$user;
		$sql = " SELECT 'Los datos ingresados son correctos' as Mensaje, 'ok' as status ";
		$consulta = sqlsrv_query($sql, $link); 
		if (sqlsrv_has_rows($consulta) > 0) {
			$row = sqlsrv_fetch_array($consulta);
			array_push($retorno,$row);
		}
	}
	else 
	{ 
		$sql = " SELECT 'Los datos ingresados son incorrectos' as Mensaje, 'error' as status  ";			
		$consulta  	= sqlsrv_query($sql, $link); 
		if (sqlsrv_has_rows($consulta) > 0) 
		{
			$row = sqlsrv_fetch_array($consulta);
			array_push($retorno,$row);
		}
	}
	sqlsrv_close($link);
	echo json_encode($retorno);
}

if($accion == "validaIngresoEmbarcador")
{
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	$pass = md5($pass);
	
	$link = conectar_srvdev();
	$retorno = array();
	
	$sql = " SELECT * FROM cfg_Embarcadores WHERE id_embarcador = '".$user."' AND pass = '".$pass."' ";
	//echo $sql;
	//die();
	$consulta = sqlsrv_query($sql, $link);

	if (sqlsrv_has_rows($consulta) > 0) 
	{
		$_SESSION["id_embarcador"]=$user;
		$sql = " SELECT 'Los datos ingresados son correctos' as Mensaje, 'ok' as status ";
		$consulta = sqlsrv_query($sql, $link); 
		if (sqlsrv_has_rows($consulta) > 0) {
			$row = sqlsrv_fetch_array($consulta);
			array_push($retorno,$row);
		}
	}
	else
	{ 
		$sql = " SELECT 'Los datos ingresados son incorrectos' as Mensaje, 'error' as status  ";			
		$consulta  	= sqlsrv_query($sql, $link); 
		if (sqlsrv_has_rows($consulta) > 0) 
		{
			$row = sqlsrv_fetch_array($consulta);
			array_push($retorno,$row);
		}
	}
	sqlsrv_close($link);
	echo json_encode($retorno);
}
?>
