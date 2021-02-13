<?php
session_start();
require_once("../../conect/conect.php");
require_once("../../funciones/fx_util.php");
include_once("SegAjax.php");
$accion = $_REQUEST["accion"];	
$conexion = conectar_srvdev();


if($accion == "buscarProveedores")
{
		$sql = "SELECT prov.[id_proveedor],prov.[cod_sap],prov.[nombre],prov.[tipo_entrada],prov.[tipo_salida],
		(SELECT descripcion FROM cfg_TipoEs WHERE id_tipoes= prov.tipo_entrada) as descripcion_entrada,
		(SELECT descripcion FROM cfg_TipoEs WHERE id_tipoes= prov.tipo_salida) as descripcion_salida
		FROM [cfg_Proveedores] prov;";

	$result = sqlsrv_query($conexion, $sql);
	
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		//$data[] = utf8_encode($row["descripcion"]);
		array_push($data,$row);
	}
	//sqlsrv_close($conexion);
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
	$sql = "SELECT [id_formato],[descripcion] FROM [cfg_Formato]
			WHERE [id_formato]=1 OR [id_formato]=2";

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
	$afectadas=sqlsrv_rows_affected($result);
	sqlsrv_close($conexion);
	echo $afectadas;
}

if($accion == "BuscarTipoProcesoPorProveedor")
{
	$IdProvedor = $_POST["IdProvedor"];
	
	$sql = "SELECT 
				  procesProv.id_proveedor
				  ,procesProv.servidor
				  ,procesProv.usr
				  ,procesProv.pass
				  ,procesProv.llave
				  ,procesProv.puerto
				  ,procesProv.ruta_remota
				  ,procesProv.ruta_local
				  ,procesProv.patron
				  ,procesProv.id_tipoProceso
				  ,tipo.descripcion as desc_tipoProceso
				  ,procesProv.id_formato
				  ,formato.descripcion as desc_formato
				  ,procesProv.ruta_remota_ftp 
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
if($accion == "grabarDetalleProveedor")
{
	$respuesta="";
	
	//Se obtendran las variables
	$Proveedor_id = $_POST["Proveedor_id"];
	$Tipo = $_POST["Tipo"];
	$Formato = $_POST["Formato"];
	$ruta_remota_ftp = $_POST["CarpetaFtpEntrada"];
	$Servidor = $_POST["Servidor"];
	$Puerto = $_POST["Puerto"];
	$Usuario = $_POST["Usuario"];
	$Contrasena = $_POST["Contrasena"];
	$Llave = $_POST["Llave"];
	$RutaRemota = $_POST["RutaRemota"];
	$RutaLocal = $_POST["RutaLocal"];
	$Patron = $_POST["Patron"];
	$TipoSalida = $_POST["TipoSalida"];
	
	$TipoBD=0;
	
	switch ($Tipo) {
		case '810':
			$TipoBD='1';
			break;
		case '855':
			$TipoBD='2';
			break;
		case '856':
			$TipoBD='3';
			break;
		case '850':
			$TipoBD='5';
			break;
	}
	
	//Si es XCBL
	if($Formato==2)
	{
		$TipoBD='0';
	}
	
	//Si es 850
	if($TipoBD=='5')
	{
		$Formato=1;
	}
	
	//Consulta para saber si tiene un registro tipo EDI registrado en la Base de datos
	$RegistroEncontrado=0;
	$sql = "SELECT [id_proveedor]
			FROM [cfg_TipoProcesoxProveedor]
			WHERE [id_proveedor]='$Proveedor_id'
			AND id_tipoProceso=$TipoBD";

	$result = sqlsrv_query($conexion, $sql);

	while( $row = sqlsrv_fetch_array($result) )
	{
		$RegistroEncontrado++;
	}
	///sqlsrv_close($conexion);
	
	//Actualizar Salida si el tipo es 850
	if($TipoBD=='5')
	{
		//Se realizara un UPDATE		
		$sql = "UPDATE [cfg_Proveedores] SET [tipo_salida]=$TipoSalida WHERE [id_proveedor]='$Proveedor_id';";

		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($result);
	}
	
	//Se se encontraron registros se debe realizar un UPDATE, si no UN INSERT
	if($RegistroEncontrado>0)
	{
		//Se realizara un UPDATE		
		$sql = "UPDATE [cfg_TipoProcesoxProveedor] SET [servidor]='$Servidor',[usr]='$Usuario' ,[pass]= '$Contrasena', [llave]='$Llave',[puerto]=$Puerto, [ruta_remota]='$RutaRemota'
		,[ruta_local]='$RutaLocal', [patron]='$Patron', [id_formato]=$Formato, [ruta_remota_ftp]='$ruta_remota_ftp' 
		WHERE [id_tipoProceso]=$TipoBD AND [id_proveedor]='$Proveedor_id'";

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
		$sql = "INSERT INTO [cfg_TipoProcesoxProveedor]([id_proveedor],[servidor],[usr],[pass],[llave],[puerto],
		[ruta_remota],[ruta_local],[patron],[id_tipoProceso],[id_formato],[ruta_remota_ftp]) 
		VALUES('$Proveedor_id','$Servidor','$Usuario','$Contrasena','$Llave',$Puerto,'$RutaRemota','$RutaLocal','$Patron',$TipoBD,'$Formato','$ruta_remota_ftp');";
		
		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($result);
		//sqlsrv_close($conexion);
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
	$Proveedor_CodigoSap = FormatoCeros($Proveedor_CodigoSap,10);
	$Proveedor_Nombre = $_POST["Proveedor_Nombre"];
	$Proveedor_Contrasena = $_POST["Proveedor_Contrasena"];	
	$Proveedor_Contrasena=md5($Proveedor_Contrasena);
	$CboTipoEntrada = $_POST["CboTipoEntrada"];
	$CboTipoSalida = $_POST["CboTipoSalida"];
	$CambioClave   = $_POST["CambioClave"];
	
	//Consulta para saber si tiene un registro de provedor
	$RegistroEncontrado=0;
	$sql = "SELECT [id_proveedor],[cod_sap],[nombre],[tipo_entrada],[tipo_salida]  FROM [cfg_Proveedores] WHERE [id_proveedor]='$Proveedor_id';";

	$result = sqlsrv_query($conexion, $sql);

	while( $row = sqlsrv_fetch_array($result))
	{
		$RegistroEncontrado++;
	}
	
	
	//Se se encontraron registros se debe realizar un UPDATE, si no UN INSERT
	if($RegistroEncontrado>0)
	{
		if($CambioClave=="1")
		{
			//Se realizara un UPDATE		
			$sql = "UPDATE [cfg_Proveedores] SET [cod_sap]='$Proveedor_CodigoSap',[nombre]='$Proveedor_Nombre' ,[tipo_entrada]= $CboTipoEntrada,[tipo_salida]=NULL,[pass]='$Proveedor_Contrasena' WHERE [id_proveedor]='$Proveedor_id';";
		}
		else
		{
			//Se realizara un UPDATE		
			$sql = "UPDATE [cfg_Proveedores] SET [cod_sap]='$Proveedor_CodigoSap',[nombre]='$Proveedor_Nombre' ,[tipo_entrada]= $CboTipoEntrada,[tipo_salida]=NULL WHERE [id_proveedor]='$Proveedor_id';";
		}


		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($result);
		//sqlsrv_close($conexion);
		if($afectadas>0)
		{
			$respuesta="actualizado";
		}		
	}
	else
	{
		$sql = "INSERT INTO cfg_Proveedores(id_proveedor,cod_sap,nombre,tipo_entrada,tipo_salida,pass) 
		VALUES('$Proveedor_id','$Proveedor_CodigoSap','$Proveedor_Nombre',$CboTipoEntrada,'','$Proveedor_Contrasena')";
		$result = sqlsrv_query($conexion, $sql);

/*
if(sqlsrv_query($conexion, $sql)){

		$file = fopen("sql_inser.txt","a+");
			fwrite($file,"\n\r ".$sql."   ".$result);
			fclose($file);
}else{
$file = fopen("sql_inser.txt","a+");
			fwrite($file,"\n\r ERROR".$sql);
			fclose($file);	
}*/
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
