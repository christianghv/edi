<?php
session_start();
include("../../conect/conect.php");
include_once("SegAjax.php");
$accion = $_POST["accion"];	
$conexion = conectar_srvdev();

if($accion == "buscarPaisOrigen")
{
	$id_proveedor = $_POST["id_proveedor"];
	
	$sql = "
		SELECT [codigo]
			  ,[pais_origen]
		FROM [cfg_PaisOrigen]
		WHERE [id_proveedor]='$id_proveedor';";

	$result = sqlsrv_query($conexion, $sql);
	
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		//$data[] = utf8_encode($row["descripcion"]);
		array_push($data,$row);
	}
	sqlsrv_close($conexion);
	$data=json_encode($data);
	//print_r($ArrayDeNumeros);
	//echo $sql;
	echo $data;

}
if($accion == "EliminarPaisOrigen")
{
	$JSON_ArrayCodigo = $_POST["JSON_ArrayCodigo"];
	$id_proveedor = $_POST["id_proveedor"];
	
	$ArrayCodigo=json_decode($JSON_ArrayCodigo);
	
	$sql="";
	foreach($ArrayCodigo->ArrayCodigo as $Codigo)
	{
		$sql.="DELETE FROM [cfg_PaisOrigen] WHERE [id_proveedor]=$id_proveedor and [codigo]='".$Codigo->codigo."'; ";
	}
	$result = sqlsrv_query($conexion, $sql);
	$afectadas=sqlsrv_rows_affected($result);
	sqlsrv_close($conexion);
	echo $afectadas;
	//echo $sql;
}
if($accion == "grabarPaisOrigen")
{
	$respuesta="";
	
	//Se obtendran las variables
	$Id_proveedor 	= $_POST["Id_proveedor"];
	$OldCodigo 		= $_POST["OldCodigo"];
	$Codigo			= $_POST["Codigo"];
	$PaisOrigen 	= $_POST["PaisOrigen"];
	
	//Consulta para saber si tiene un registro de provedor
	$RegistroEncontrado=0;
	$sql = "SELECT [id_proveedor],[codigo] FROM [cfg_PaisOrigen] where id_proveedor='$Id_proveedor' and codigo='$OldCodigo';";

	$result = sqlsrv_query($conexion, $sql);

	while( $row = sqlsrv_fetch_array($result))
	{
		$RegistroEncontrado++;
	}
	sqlsrv_close($conexion);
	
	//Se se encontraron registros se debe realizar un UPDATE, si no UN INSERT
	if($RegistroEncontrado>0)
	{
		//Se realizara un UPDATE		
		$sql = "UPDATE cfg_PaisOrigen SET codigo='$Codigo',pais_origen='$PaisOrigen' WHERE id_proveedor='$Id_proveedor' and codigo='$OldCodigo';";
		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($result);
		
		sqlsrv_close($conexion);
		
		if($afectadas>0)
		{
			$respuesta="actualizado";
		}		
	}
	else
	{
		$sql = "INSERT INTO [cfg_PaisOrigen]([id_proveedor],[codigo],[pais_origen]) VALUES ('$Id_proveedor','$Codigo','$PaisOrigen');";
		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($result);
		
		sqlsrv_close($conexion);
		if($afectadas>0)
		{
			$respuesta="insertado";
		}
	}
	echo $respuesta;
}

	
?>
