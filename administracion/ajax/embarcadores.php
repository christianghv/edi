<?php
session_start();
include("../../conect/conect.php");
include_once("SegAjax.php");
$accion = $_POST["accion"];	
$conexion = conectar_srvdev();

if($accion == "buscarEmbarcadores")
{
	$sql = "SELECT emba.[id_embarcador],emba.[cod_sap],emba.[nombre],emba.[tipo_entrada],tipoEs.[descripcion]
	FROM [cfg_Embarcadores] emba
    LEFT JOIN cfg_TipoEs tipoEs ON
    tipoEs.id_tipoes=emba.tipo_entrada";

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

if($accion == "buscarTipoEs")
{
	$sql = "SELECT [id_tipoes],[descripcion] FROM [cfg_TipoEs]";

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

if($accion == "buscarFormatos")
{
	$sql = "SELECT [id_formato],[descripcion] FROM [cfg_Formato]";

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
if($accion == "EliminarEmbarcadores")
{
	$JSON_ArrayIdEmb = $_POST["JSON_ArrayIdEmbarcador"];
	
	$ArrayIdEmbar=json_decode($JSON_ArrayIdEmb);
	
	$sql="";
	foreach($ArrayIdEmbar->ArrayIdEmab as $Embarcador)
	{
		$sql.="DELETE FROM [cfg_Embarcadores] WHERE [id_embarcador]='".$Embarcador->ID_Embarcador."'; ";
		$sql.="DELETE FROM [cfg_TipoProcesoxEmbarcador] WHERE [id_embarcador]='".$Embarcador->ID_Embarcador."'; ";
	}
	$result = sqlsrv_query($conexion, $sql);
	$afectadas=sqlsrv_rows_affected($result);
	sqlsrv_close($conexion);
	echo $afectadas;
	//echo $sql;
}

if($accion == "BuscarTipoProcesoPorEmbarcador")
{
	$IdEmbarcador = $_POST["IdEmbarcador"];
	
	$sql = "SELECT emba.[id_embarcador],emba.[cod_sap],emba.[nombre],emba.[tipo_entrada],tipoEs.[descripcion],proces.[servidor]
			,proces.[puerto],proces.[usr],proces.[pass], proces.[ruta_remota], proces.[ruta_local],proces.[ruta_salida],proces.[patron]
			FROM [cfg_Embarcadores] emba
			INNER JOIN cfg_TipoEs tipoEs ON
			tipoEs.id_tipoes=emba.tipo_entrada
			INNER JOIN cfg_TipoProcesoxEmbarcador proces ON
			proces.id_embarcador=emba.id_embarcador
			WHERE emba.id_embarcador='$IdEmbarcador';";

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
if($accion == "grabarDetalleEmbarcador")
{
	$respuesta="";
	
	//Se obtendran las variables
	$Embarcador_id = $_POST["Embarcador_id"];
	$Servidor = $_POST["Servidor"];
	$Puerto = $_POST["Puerto"];
	$Usuario = $_POST["Usuario"];
	$Contrasena = $_POST["Contrasena"];
	$RutaRemota = $_POST["RutaRemota"];
	$RutaLocal = $_POST["RutaLocal"];
	$RutaSalida = $_POST["RutaSalida"];
	$Patron = $_POST["Patron"];	
	
	//Consulta para saber si tiene un registro tipo EDI810 registrado en la Base de datos
	$RegistroEncontrado=0;
	$sql = "SELECT [id_embarcador],[servidor],[usr],[pass],[puerto],[ruta_remota],[ruta_local],[patron]
			FROM [cfg_TipoProcesoxEmbarcador]
			WHERE [id_embarcador]='$Embarcador_id'";

	$result = sqlsrv_query($conexion, $sql);

	while( $row = sqlsrv_fetch_array($result) )
	{
		$RegistroEncontrado++;
	}
	sqlsrv_close($conexion);
	
	//Se se encontraron registros se debe realizar un UPDATE, si no UN INSERT
	if($RegistroEncontrado>0)
	{
		//Se realizara un UPDATE		
		$sql = "UPDATE [cfg_TipoProcesoxEmbarcador] SET [servidor]='$Servidor',[usr]='$Usuario' ,[pass]= '$Contrasena',
		[puerto]=$Puerto, [ruta_remota]='$RutaRemota',[ruta_local]='$RutaLocal',[ruta_salida]='$RutaSalida',[patron]='$Patron',[id_tipoProceso]=3,[id_formato]=2
		 WHERE [id_embarcador]='$Embarcador_id'";
		 
		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($result);
		sqlsrv_close($conexion);
		if($afectadas>0)
		{
			$respuesta="actualizado detalle";
		}
	}
	else
	{
		
		$sql = "INSERT INTO [cfg_TipoProcesoxEmbarcador]([id_embarcador],[servidor],[usr],[pass],[puerto],[ruta_remota],[ruta_local],[ruta_salida],[patron],[id_tipoProceso],[id_formato]) 
		 VALUES('$Embarcador_id','$Servidor','$Usuario','$Contrasena',$Puerto,'$RutaRemota','$RutaLocal','$RutaSalida','$Patron',3,2);";
		
		//echo $sql;
		//die();
		
		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($result);
		sqlsrv_close($conexion);
		if($afectadas>0)
		{
			$respuesta=$afectadas;
		}
	}
	echo $respuesta;
}

if($accion == "grabarCabeceraEmbarcador")
{
	$respuesta="";
	
	//Se obtendran las variables
	$Embarcador_id = $_POST["Embarcador_id"];
	$Embarcador_CodigoSap = $_POST["Embarcador_CodigoSap"];
	$Embarcador_Nombre = $_POST["Embarcador_Nombre"];
	$Embarcador_Contrasena = $_POST["Embarcador_Contrasena"];	
	$Embarcador_Contrasena=md5($Embarcador_Contrasena);
	
	//Consulta para saber si tiene un registro de provedor
	$RegistroEncontrado=0;
	$sql = "SELECT [id_embarcador],[cod_sap],[nombre],[tipo_entrada],[pass] FROM [cfg_Embarcadores] WHERE [id_embarcador]='$Embarcador_id';";

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
		$sql = "UPDATE [cfg_Embarcadores] SET [cod_sap]='$Embarcador_CodigoSap',[nombre]='$Embarcador_Nombre',[pass]='$Embarcador_Contrasena' WHERE [id_embarcador]='$Embarcador_id';";

		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($result);
		sqlsrv_close($conexion);
		if($afectadas>0)
		{
			$respuesta="actualizado Header ";
		}		
	}
	else
	{
		$sql = "INSERT INTO [cfg_Embarcadores]([id_embarcador],[cod_sap],[nombre],[tipo_entrada],[pass]) 
		VALUES('$Embarcador_id','$Embarcador_CodigoSap','$Embarcador_Nombre',1,'$Embarcador_Contrasena');";

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
