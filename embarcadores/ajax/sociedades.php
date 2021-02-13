<?php
include("../../conect/conect.php");
include_once("SegAjax.php");
$conexion = conectar_srvdev();

$sql = " SELECT id_sociedad, desc_sociedad  FROM cfg_sociedades_desc ";
$result = sqlsrv_query($conexion, $sql);

$data = array();
while( $row = sqlsrv_fetch_array($result) )
{
	array_push($data,$row);
}
sqlsrv_close($conexion);

echo json_encode($data);
	
?>
