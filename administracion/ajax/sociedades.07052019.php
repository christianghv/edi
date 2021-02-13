<?php
session_start();
include("../../conect/conect.php");
include_once("SegAjax.php");
$accion = $_POST["accion"];	
$conexion = conectar_srvdev();

if($accion == "buscarSociedades")
{
	$sql = "
	  SELECT sociedad.id_sociedad
      ,sociedad.desc_sociedad
      ,(SELECT tolerancia FROM tolerancia WHERE bukrs=sociedad.id_sociedad) as tolerancia 
	  ,(SELECT ISNULL(lineas,1) FROM tolerancia WHERE bukrs=sociedad.id_sociedad) as lineas 
	  FROM cfg_sociedades_desc sociedad 
	  INNER JOIN cfg_SociedadxUsuario SocXUsu ON 
	  SocXUsu.id_sociedad=sociedad.id_sociedad 
	  WHERE SocXUsu.email='".$_SESSION["email"]."';";

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
if($accion == "buscarSociedadesMantenedor")
{
	$sql = "
	  SELECT sociedad.id_sociedad
      ,sociedad.desc_sociedad
      ,(SELECT tolerancia FROM tolerancia WHERE bukrs=sociedad.id_sociedad) as tolerancia
	  ,(SELECT ISNULL(lineas,1) FROM tolerancia WHERE bukrs=sociedad.id_sociedad) as lineas 
	  FROM cfg_sociedades_desc sociedad;";

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
if($accion == "EliminarSociedad")
{
	$JSON_ArrayId_sociedad = $_POST["JSON_ArrayId_sociedad"];
	
	$ArrayId_sociedad=json_decode($JSON_ArrayId_sociedad);
	
	$sql="";
	foreach($ArrayId_sociedad->ArrayId_sociedad as $Sociedad)
	{
		$sql.="DELETE FROM [cfg_sociedades_desc] WHERE [id_sociedad]='".$Sociedad->id_sociedad."'; ";
		$sql.="DELETE FROM [tolerancia] WHERE [bukrs]='".$Sociedad->id_sociedad."'; ";
	}
	$result = sqlsrv_query($conexion, $sql);
	$afectadas=sqlsrv_rows_affected($conexion);
	sqlsrv_close($conexion);
	echo $afectadas;
	//echo $sql;
}
if($accion == "grabarSociedad")
{
	$respuesta="";
	
	//Se obtendran las variables
	$Sociedad_id_sociedad 	= $_POST["Sociedad_id_sociedad"];
	$Sociedad_nombre		= $_POST["Sociedad_nombre"];
	$Sociedad_tolerancia 	= $_POST["Sociedad_tolerancia"];
	$Sociedad_lineas 		= $_POST["Sociedad_lineas"];
	
	//Consulta para saber si tiene un registro de provedor
	$RegistroEncontrado=0;
	$sql = "SELECT id_sociedad FROM cfg_sociedades_desc where id_sociedad='".$Sociedad_id_sociedad."';";

	$result = sqlsrv_query($conexion, $sql);

	while( $row = sqlsrv_fetch_array($result))
	{
		$RegistroEncontrado++;
	}
	
	//Se se encontraron registros se debe realizar un UPDATE, si no UN INSERT
	if($RegistroEncontrado>0)
	{
		//Se realizara un UPDATE		
		$sql = "UPDATE cfg_sociedades_desc SET desc_sociedad='$Sociedad_nombre' WHERE id_sociedad='$Sociedad_id_sociedad';";
		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($conexion);
		
		$sql = "UPDATE tolerancia SET tolerancia=$Sociedad_tolerancia,lineas=$Sociedad_lineas WHERE bukrs='$Sociedad_id_sociedad';";
		$result = sqlsrv_query($conexion, $sql);
		$afectadas+=sqlsrv_rows_affected($conexion);
		sqlsrv_close($conexion);
		
		if($afectadas>0)
		{
			$respuesta="actualizado Header ";
		}		
	}
	else
	{
		$sql = "INSERT INTO [tolerancia]([bukrs],[tolerancia],[lineas]) VALUES ('$Sociedad_id_sociedad',$Sociedad_tolerancia,$Sociedad_lineas);";
		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($conexion);
		
		$sql = "INSERT INTO [cfg_sociedades_desc]([id_sociedad],[desc_sociedad]) VALUES ('$Sociedad_id_sociedad','$Sociedad_nombre');";
		$result = sqlsrv_query($conexion, $sql);
		$afectadas+=sqlsrv_rows_affected($conexion);
		
		sqlsrv_close($conexion);
		if($afectadas>0)
		{
			$respuesta="insertado";
		}
	}
	echo $respuesta;
}
if($accion == "buscarIdReceiverAsociados")
{
	$id_sociedad = $_POST["id_sociedad"];	
	
	$sql = "
		SELECT [id_receiver]
		FROM [cfg_sociedades]
		WHERE id_sociedad='$id_sociedad';";

	$result = sqlsrv_query($conexion, $sql);
	
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		//$data[] = utf8_encode($row["descripcion"]);
		array_push($data,$row);
	}
	sqlsrv_close($conexion);
	$data=json_encode($data);
	echo $data;
}
if($accion == "BuscarParametrosDistribucion")
{
	$id_sociedad = $_POST["id_sociedad"];	
	
	$sql = "
		SELECT[urgencia]
		  ,[peso]
		  ,[longitud]
		  ,[ancho]
		  ,[alto]
		FROM [cfg_parametros_distribucion]
		WHERE [sociedad]='$id_sociedad';";

	$result = sqlsrv_query($conexion, $sql);
	
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		//$data[] = utf8_encode($row["descripcion"]);
		array_push($data,$row);
	}
	sqlsrv_close($conexion);
	$data=json_encode($data);
	echo $data;
}
if($accion == "grabarIdReceiver")
{
	$respuesta="";
	
	//Se obtendran las variables
	$IdReceiver			 	= $_POST["IdReceiver"];
	$H_OldIdReceiver		= $_POST["H_OldIdReceiver"];
	$IdSociedad				= $_POST["IdSociedad"];
	
	//Se se encontraron registros se debe realizar un UPDATE, si no UN INSERT
	if($H_OldIdReceiver!="")
	{
		//Se realizara un UPDATE		
		$sql = "UPDATE cfg_sociedades SET id_receiver='$IdReceiver' 
				WHERE id_receiver='$H_OldIdReceiver'
				AND id_sociedad='$IdSociedad';";
		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($conexion);
		
		sqlsrv_close($conexion);
		
		$respuesta=$afectadas;
	}
	else
	{
		//Se realizara un INSERT		
		$sql = "INSERT INTO [cfg_sociedades]([id_receiver],[id_sociedad]) 
				VALUES ('$IdReceiver','$IdSociedad');";
		
		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($conexion);
		
		sqlsrv_close($conexion);
		
		$respuesta=$afectadas;
	}
	echo $respuesta;
}
if($accion == "grabarParametroDistribucion")
{
	$respuesta="";
	
	//Se obtendran las variables
	$Urgencia			= $_POST["Urgencia"];
	$H_OldUrgencia		= $_POST["H_OldUrgencia"];
	$Peso				= $_POST["Peso"];
	$Largo				= $_POST["Largo"];
	$Ancho				= $_POST["Ancho"];
	$Alto				= $_POST["Alto"];
	$IdSociedad			= $_POST["IdSociedad"];
	
	//Se se encontraron registros se debe realizar un UPDATE, si no UN INSERT
	if($H_OldUrgencia!="")
	{
		//Se realizara un UPDATE		
		$sql = "UPDATE cfg_parametros_distribucion 
				SET urgencia='$Urgencia',peso='$Peso',longitud='$Largo',ancho='$Ancho',alto='$Alto' 
				WHERE urgencia='$H_OldUrgencia'
				AND sociedad='$IdSociedad';";
		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($conexion);
		
		sqlsrv_close($conexion);
		
		$respuesta=$afectadas;
	}
	else
	{
		//Se realizara un INSERT		
		$sql = "INSERT INTO [cfg_parametros_distribucion]([sociedad],[urgencia],[peso],[longitud],[ancho],[alto]) 
				VALUES ('$IdSociedad','$Urgencia','$Peso','$Largo','$Ancho','$Alto');";
		
		$result = sqlsrv_query($conexion, $sql);
		$afectadas=sqlsrv_rows_affected($conexion);
		
		sqlsrv_close($conexion);
		
		$respuesta=$afectadas;
	}
	echo $respuesta;
}
if($accion == "EliminarIdReceiverAsociado")
{
	$respuesta="";
	
	//Se obtendran las variables
	$IdReceiver		= $_POST["IdReceiver"];
	$IdSociedad		= $_POST["id_sociedad"];
	
	//Se realizara un UPDATE		
	$sql = "DELETE FROM cfg_sociedades 
			WHERE id_receiver='$IdReceiver' 
			AND id_sociedad='$IdSociedad';";
			
	$result = sqlsrv_query($conexion, $sql);
	$afectadas=sqlsrv_rows_affected($conexion);
		
	sqlsrv_close($conexion);
		
	$respuesta=$afectadas;
	
	echo $respuesta;
}
if($accion == "EliminarParametroDistribucion")
{
	$respuesta="";
	
	//Se obtendran las variables
	$Urgencia		= $_POST["Urgencia"];
	$IdSociedad		= $_POST["id_sociedad"];
	
	//Se realizara un UPDATE		
	$sql = "DELETE FROM cfg_parametros_distribucion 
			WHERE urgencia='$Urgencia' 
			AND sociedad='$IdSociedad';";
	
	$result = sqlsrv_query($conexion, $sql);
	$afectadas=sqlsrv_rows_affected($conexion);
		
	sqlsrv_close($conexion);
		
	$respuesta=$afectadas;
	
	echo $respuesta;
}
?>