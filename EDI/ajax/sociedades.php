<?php
include("../../conect/conect.php");
//include_once("SegAjax.php");
$conexion = conectar_srvdev();
session_start();
$sql = "  SELECT soc.id_sociedad, soc.desc_sociedad 
		  FROM cfg_sociedades_desc soc
		  INNER JOIN cfg_SociedadxUsuario SocXUsu ON 
		  SocXUsu.id_sociedad=soc.id_sociedad
		  WHERE SocXUsu.email='".$_SESSION["email"]."';";
$result = sqlsrv_query($conexion, $sql);

$data = array();
while( $row = sqlsrv_fetch_array($result) )
{
	array_push($data,$row);
}
sqlsrv_close($conexion);

echo json_encode($data);
	
?>
