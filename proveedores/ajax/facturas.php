<?php
include("../../conect/conect.php");
include_once("SegAjax.php");
$accion = $_POST["accion"];	
$conexion = conectar_srvdev();

if($accion == "buscarProveedores")
{
	$sql = "SELECT prov.[id_proveedor],prov.[cod_sap],prov.[nombre],prov.[tipo_entrada],prov.[tipo_salida],tipoEs.descripcion 
	FROM [cfg_Proveedores] prov
    INNER JOIN cfg_TipoEs tipoEs ON
    tipoEs.id_tipoes=prov.tipo_entrada
    AND tipoEs.id_tipoes=prov.tipo_salida;";

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
if($accion == "EliminarProveedores")
{
	$JSON_ArrayIdProv = $_POST["JSON_ArrayIdProv"];
	
	$ArrayIdProv=json_decode($JSON_ArrayIdProv);
	
	$sql="";
	foreach($ArrayIdProv->ArrayIdProv as $Provedor)
	{
		$sql.="DELETE FROM [cfg_Proveedores] WHERE [id_proveedor]='".$Provedor->ID_Proveedor."'; ";
		$sql.="DELETE FROM [cfg_TipoProcesoxProveedor] WHERE [id_proveedor]='".$Provedor->ID_Proveedor."'; ";
	}
	$result = sqlsrv_query($conexion, $sql);
	$afectadas=sqlsrv_rows_affected($conexion);
	sqlsrv_close($conexion);
	echo $afectadas;
}

if($accion == "BuscarTipoProcesoPorProveedor")
{
	$IdProvedor = $_POST["IdProvedor"];
	
	$sql = "SELECT procesProv.id_proveedor
				  ,procesProv.servidor
				  ,procesProv.usr
				  ,procesProv.pass
				  ,procesProv.puerta
				  ,procesProv.ruta_remota
				  ,procesProv.ruta_local
				  ,procesProv.patron
				  ,procesProv.id_tipoProceso
				  ,tipo.descripcion as desc_tipoProceso
				  ,procesProv.id_formato
				  ,formato.descripcion as desc_formato
			FROM [cfg_TipoProcesoxProveedor] procesProv
			INNER JOIN cfg_TipoProceso tipo ON
				tipo.id_tipo=procesProv.id_tipoProceso
			INNER JOIN cfg_Formato formato ON
				formato.id_formato=procesProv.id_formato
			WHERE procesProv.id_proveedor='$IdProvedor';";

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

if($accion == "traerFormatos")
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

if($accion == "traerTipoProcesos")
{	
	$sql = "SELECT [id_tipo],[descripcion] 
			FROM [cfg_TipoProceso]
			WHERE id_tipo=0
			OR id_tipo=1
			OR id_tipo=2;";

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


if($accion == "grabar810")
{
	$respuesta="";
	
	//Se obtendran las variables
	$Proveedor_id = $_POST["Proveedor_id"];
	$Formato = $_POST["Formato"];
	$OldFormato810 = $_POST["OldFormato810"];
	$Servidor = $_POST["Servidor"];
	$Puerto = $_POST["Puerto"];
	$Usuario = $_POST["Usuario"];
	$Contrasena = $_POST["Contrasena"];
	$RutaRemota = $_POST["RutaRemota"];
	$RutaLocal = $_POST["RutaLocal"];
	$Patron = $_POST["Patron"];	
	
	//Consulta para saber si tiene un registro tipo EDI810 registrado en la Base de datos
	$RegistroEncontrado=0;
	$sql = "SELECT [id_proveedor]
			FROM [cfg_TipoProcesoxProveedor]
			WHERE [id_proveedor]='$Proveedor_id'
			AND id_tipoProceso=0";

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
		$sql = "UPDATE [cfg_TipoProcesoxProveedor] SET [servidor]='$Servidor',[usr]='$Usuario' ,[pass]= '$Contrasena',[puerta]=$Puerto, [ruta_remota]='$RutaRemota'
		,[ruta_local]='$RutaLocal', [patron]='$Patron', [id_formato]=$Formato
		WHERE [id_tipoProceso]=0 AND [id_proveedor]='$Proveedor_id'";

		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($conexion);
		sqlsrv_close($conexion);
		if($afectadas>0)
		{
			$respuesta="actualizado";
		}		
	}
	else
	{
		$sql = "INSERT INTO [cfg_TipoProcesoxProveedor]([id_proveedor],[servidor],[usr],[pass],[puerta],
		[ruta_remota],[ruta_local],[patron],[id_tipoProceso],[id_formato]) 
		VALUES('$Proveedor_id','$Servidor','$Usuario','$Contrasena',$Puerto,'$RutaRemota','$RutaLocal','$Patron',0,'$Proveedor_id');";

		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($conexion);
		sqlsrv_close($conexion);
		if($afectadas>0)
		{
			$respuesta=$afectadas;
		}
	}
	echo $respuesta;
}

if($accion == "grabar855")
{
	$respuesta="";
	
	//Se obtendran las variables
	$Proveedor_id = $_POST["Proveedor_id"];
	$Formato = $_POST["Formato"];
	$OldFormato855 = $_POST["OldFormato855"];
	$Servidor = $_POST["Servidor"];
	$Puerto = $_POST["Puerto"];
	$Usuario = $_POST["Usuario"];
	$Contrasena = $_POST["Contrasena"];
	$RutaRemota = $_POST["RutaRemota"];
	$RutaLocal = $_POST["RutaLocal"];
	$Patron = $_POST["Patron"];	
	
	//Consulta para saber si tiene un registro tipo EDI810 registrado en la Base de datos
	$RegistroEncontrado=0;
	$sql = "SELECT [id_proveedor]
			FROM [cfg_TipoProcesoxProveedor]
			WHERE [id_proveedor]='$Proveedor_id'
			AND id_tipoProceso=1";

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
		$sql = "UPDATE [cfg_TipoProcesoxProveedor] SET [servidor]='$Servidor',[usr]='$Usuario' ,[pass]= '$Contrasena',[puerta]=$Puerto, [ruta_remota]='$RutaRemota'
		,[ruta_local]='$RutaLocal', [patron]='$Patron', [id_formato]=$Formato
		WHERE [id_tipoProceso]=1 AND [id_proveedor]='$Proveedor_id'";

		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($conexion);
		sqlsrv_close($conexion);
		if($afectadas>0)
		{
			$respuesta="actualizado";
		}		
	}
	else
	{
		$sql = "INSERT INTO [cfg_TipoProcesoxProveedor]([id_proveedor],[servidor],[usr],[pass],[puerta],
		[ruta_remota],[ruta_local],[patron],[id_tipoProceso],[id_formato]) 
		VALUES('$Proveedor_id','$Servidor','$Usuario','$Contrasena',$Puerto,'$RutaRemota','$RutaLocal','$Patron',1,'$Proveedor_id');";

		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($conexion);
		sqlsrv_close($conexion);
		if($afectadas>0)
		{
			$respuesta=$afectadas;
		}
	}
	echo $respuesta;
}

if($accion == "grabar856")
{
	$respuesta="";
	
	//Se obtendran las variables
	$Proveedor_id = $_POST["Proveedor_id"];
	$Formato = $_POST["Formato"];
	$OldFormato856 = $_POST["OldFormato856"];
	$Servidor = $_POST["Servidor"];
	$Puerto = $_POST["Puerto"];
	$Usuario = $_POST["Usuario"];
	$Contrasena = $_POST["Contrasena"];
	$RutaRemota = $_POST["RutaRemota"];
	$RutaLocal = $_POST["RutaLocal"];
	$Patron = $_POST["Patron"];	
	
	//Consulta para saber si tiene un registro tipo EDI810 registrado en la Base de datos
	$RegistroEncontrado=0;
	$sql = "SELECT [id_proveedor]
			FROM [cfg_TipoProcesoxProveedor]
			WHERE [id_proveedor]='$Proveedor_id'
			AND id_tipoProceso=2";

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
		$sql = "UPDATE [cfg_TipoProcesoxProveedor] SET [servidor]='$Servidor',[usr]='$Usuario' ,[pass]= '$Contrasena',[puerta]=$Puerto, [ruta_remota]='$RutaRemota'
		,[ruta_local]='$RutaLocal', [patron]='$Patron', [id_formato]=$Formato
		WHERE [id_tipoProceso]=2 AND [id_proveedor]='$Proveedor_id'";

		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($conexion);
		sqlsrv_close($conexion);
		if($afectadas>0)
		{
			$respuesta="actualizado";
		}		
	}
	else
	{
		$sql = "INSERT INTO [cfg_TipoProcesoxProveedor]([id_proveedor],[servidor],[usr],[pass],[puerta],
		[ruta_remota],[ruta_local],[patron],[id_tipoProceso],[id_formato]) 
		VALUES('$Proveedor_id','$Servidor','$Usuario','$Contrasena',$Puerto,'$RutaRemota','$RutaLocal','$Patron',2,'$Proveedor_id');";

		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($conexion);
		sqlsrv_close($conexion);
		if($afectadas>0)
		{
			$respuesta=$afectadas;
		}
	}
	echo $respuesta;
}

if($accion == "grabarCabeceraProvedor")
{
	$respuesta="";
	
	//Se obtendran las variables
	$Proveedor_id = $_POST["Proveedor_id"];
	$Proveedor_CodigoSap = $_POST["Proveedor_CodigoSap"];
	$Proveedor_Nombre = $_POST["Proveedor_Nombre"];
	$Proveedor_Contrasena = $_POST["Proveedor_Contrasena"];	
	$Proveedor_Contrasena=md5($Proveedor_Contrasena);
	$CboTipoEntrada = $_POST["CboTipoEntrada"];
	$CboTipoSalida = $_POST["CboTipoSalida"];
	
	//Consulta para saber si tiene un registro de provedor
	$RegistroEncontrado=0;
	$sql = "SELECT [id_proveedor],[cod_sap],[nombre],[tipo_entrada],[tipo_salida]  FROM [cfg_Proveedores] WHERE [id_proveedor]='$Proveedor_id';";

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
		$sql = "UPDATE [cfg_Proveedores] SET [cod_sap]='$Proveedor_CodigoSap',[nombre]='$Proveedor_Nombre' ,[tipo_entrada]= $CboTipoEntrada,[tipo_salida]=$CboTipoSalida
		,[pass]='$Proveedor_Contrasena' WHERE [id_proveedor]='$Proveedor_id';";

		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($conexion);
		sqlsrv_close($conexion);
		if($afectadas>0)
		{
			$respuesta="actualizado";
		}		
	}
	else
	{
		$sql = "INSERT INTO [cfg_Proveedores]([id_proveedor],[cod_sap],[nombre],[tipo_entrada],[tipo_salida],[pass]) 
		VALUES('$Proveedor_id','$Proveedor_CodigoSap','$Proveedor_Nombre',$CboTipoEntrada,$CboTipoSalida,'$Proveedor_Contrasena');";

		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($conexion);
		sqlsrv_close($conexion);
		if($afectadas>0)
		{
			$respuesta="insertado";
		}
	}
	echo $respuesta;
}

	
?>
