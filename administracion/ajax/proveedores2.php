<?php
session_start();
require_once("../../conect/conect.php");
require_once("../../funciones/fx_util.php");
include_once("SegAjax.php");
$accion = $_REQUEST["accion"];	
$conexion = conectar_srvdev();

		$sql = "INSERT INTO cfg_Proveedores(id_proveedor,cod_sap,nombre,tipo_entrada,tipo_salida,pass) 
		VALUES('345678','0005645645','sdfsdfds',1,'','d9729feb74992cc3482b350163a1a010')";




		$result = sqlsrv_query($conexion, $sql);


if(sqlsrv_query($conexion, $sql)){

		$file = fopen("sql_inser.txt","a+");
			fwrite($file,"\n\r ".$sql."   ".$result);
			fclose($file);
}else{
$file = fopen("sql_inser.txt","a+");
			fwrite($file,"\n\r ERROR".$sql);
			fclose($file);	
}
		$afectadas=sqlsrv_rows_affected($result);
		sqlsrv_close($conexion);
		if($afectadas>0)
		{
			$respuesta="insertado";
		}
	
	echo $respuesta;


	
?>
