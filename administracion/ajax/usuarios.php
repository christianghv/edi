<?php
error_reporting(0);
include("../../conect/conect.php");
include_once("SegAjax.php");
$accion = $_REQUEST["accion"];	
$conexion = conectar_srvdev();

if($accion == "buscarUsuarios")
{
	
	$invoiceNumber=$_POST["invoiceNumber"];
		
	$sql = "SELECT usuarios.email,usuarios.nombre,usuarios.perfil, perfil.descripcion
			FROM [cfg_usuarios] usuarios INNER JOIN [cfg_perfil] perfil ON perfil.id_perfil=usuarios.perfil";

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

if($accion == "buscarPerfiles")
{
	$sql = "SELECT [id_perfil],[descripcion] FROM [cfg_perfil]";

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

if($accion == "GrabarUsuario")
{
	$Usuario_email = $_POST["Usuario_email"];
	$Usuario_Nombre = $_POST["Usuario_Nombre"];
	$PerfilUsuario = $_POST["PerfilUsuario"];

	$sql = "INSERT INTO [cfg_usuarios]([email],[nombre],[perfil]) values('$Usuario_email','$Usuario_Nombre','$PerfilUsuario');";

	$result = sqlsrv_query($conexion, $sql);
	$afectadas=sqlsrv_rows_affected($result);
	sqlsrv_close($conexion);
	echo $afectadas;
}
if($accion == "ActualizarUsuario")
{
	$Usuario_email_old = $_POST["Usuario_email_old"];
	$Usuario_email = $_POST["Usuario_email"];
	$Usuario_Nombre = $_POST["Usuario_Nombre"];
	$PerfilUsuario = $_POST["PerfilUsuario"];
	
	$sql = "UPDATE [cfg_usuarios] SET [email]='$Usuario_email',[nombre]='$Usuario_Nombre' ,[perfil]= '$PerfilUsuario'
	WHERE [email]='$Usuario_email_old';";

	$result = sqlsrv_query($conexion, $sql);
	$afectadas=sqlsrv_rows_affected($result);
	sqlsrv_close($conexion);
	echo $afectadas;
}
if($accion == "EliminarUsuarios")
{
	$JsonEmailUsuarios = $_POST["JSON_ArrayEmail"];
	
	$ArrayEmail=json_decode($JsonEmailUsuarios);
	
	$sql="";
	foreach($ArrayEmail->ArrayEmail as $email)
	{
		$sql.="DELETE FROM [cfg_usuarios] WHERE [email]='".$email->email."'; ";
	}
	$result = sqlsrv_query($conexion, $sql);
	$afectadas=sqlsrv_rows_affected($result);
	sqlsrv_close($conexion);
	echo $afectadas;
}

// The function
function utf8_encode_deep(&$input) {
    if (is_string($input)) {
        $input = utf8_encode($input);
    } else if (is_array($input)) {
        foreach ($input as &$value) {
            utf8_encode_deep($value);
        }

        unset($value);
    } else if (is_object($input)) {
        $vars = array_keys(get_object_vars($input));

        foreach ($vars as $var) {
            utf8_encode_deep($input->$var);
        }
    }
}

if($accion == "buscarTodosLosPermisos")
{
	$sql = "SELECT tipo.id_tipoPermiso,tipo.descripcion, permisos.id_permiso, permisos.nombre
			FROM cfg_TipoPermiso tipo
			INNER JOIN cfg_Permisos permisos on
			permisos.id_tipoPermiso=tipo.id_tipoPermiso";
	ini_set('mssql.charset', 'utf-8');
	$result = sqlsrv_query($conexion, $sql);
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		//$data[] = utf8_encode($row["descripcion"]);
		array_push($data,$row);
	}
	utf8_encode_deep($data);
	sqlsrv_close($conexion);
	$data=json_encode($data);
	//print_r($data);
	//echo $sql;
	echo $data;
}
if($accion == "buscarTodosLasSociedades")
{
	$sql = "SELECT [id_sociedad],[desc_sociedad]
			FROM [cfg_sociedades_desc]";
	ini_set('mssql.charset', 'utf-8');
	$result = sqlsrv_query($conexion, $sql);
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		//$data[] = utf8_encode($row["descripcion"]);
		array_push($data,$row);
	}
	utf8_encode_deep($data);
	sqlsrv_close($conexion);
	$data=json_encode($data);
	//print_r($data);
	//echo $sql;
	echo $data;
}



if($accion == "grabarPermisos")
{
	$email = $_POST["email"];
	
	$JSON_ArrayDePermisos=$_POST["JSON_Permisos"];
	$JSON_Sociedades=$_POST["JSON_Sociedades"];
	
	$ArrayDePermisos = json_decode($JSON_ArrayDePermisos);
	$ArrayDeSociedades = json_decode($JSON_Sociedades);
	
	//Permisos
	$sqlLimpiar="delete from cfg_PermisosUsuario where email='".$email."'; ";
	
	$SqlContinuacionConsulta="";
	
	foreach($ArrayDePermisos->ArrayDePerm as $Permiso)
	{
		$SqlContinuacionConsulta.="INSERT INTO cfg_PermisosUsuario([email],[id_permiso])
							  VALUES('".$email."',".$Permiso->Permiso."); ";
	}
	$SqlCompleto=$sqlLimpiar.$SqlContinuacionConsulta;
	//echo $SqlCompleto;

	$result = sqlsrv_query($conexion,$SqlCompleto);
	$afectadas=sqlsrv_rows_affected($result);
$file = fopen("sql_permisos.txt",'w+');
	fwrite($file,$SqlCompleto + ' ' + $afectadas);
	fclose($file);
	sqlsrv_close($conexion);
	
	//Sociedades
	$sqlLimpiar="delete from cfg_SociedadxUsuario where email='".$email."'; ";
	
	$SqlContinuacionConsulta="";
	
	foreach($ArrayDeSociedades->ArrayDeSociedades as $Sociedad)
	{
		$SqlContinuacionConsulta.="INSERT INTO cfg_SociedadxUsuario([email],[id_sociedad])
							  VALUES('".$email."',".$Sociedad->Sociedad."); ";
	}
	$SqlCompleto=$sqlLimpiar.$SqlContinuacionConsulta;
	//echo $SqlCompleto;
	$result = sqlsrv_query($conexion,$SqlCompleto);
	$afectadas=sqlsrv_rows_affected($result);
	sqlsrv_close($conexion);
	
	echo "OK";	
}
if($accion == "ObtenerPermisosUsuario")
{
	$email=$_POST["email"];
	
	$sqlPermisos="SELECT [email],[id_permiso] FROM [cfg_PermisosUsuario] where email='".$email."';";
	
	$result = sqlsrv_query($conexion,$sqlPermisos);
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		array_push($data,$row);
	}
	utf8_encode_deep($data);
	sqlsrv_close($conexion);
	$data=json_encode($data);
	
	echo $data;	
}
if($accion == "ObtenerSociedadesUsuario")
{
	$email=$_POST["email"];
	
	$sqlPermisos="SELECT [id_sociedad] FROM [cfg_SociedadxUsuario] WHERE [email]='$email';";
	
	$result = sqlsrv_query($conexion,$sqlPermisos);
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		array_push($data,$row);
	}
	utf8_encode_deep($data);
	sqlsrv_close($conexion);
	$data=json_encode($data);
	
	echo $data;	
}
	
?>
