<?
include_once("SegAjax.php");
session_start();
if (!isset($email))
	header('Location: index.php');

	require 'clases/class.templates.php';
	$html_administracion = "";
	$html_EDI = "";

	if ($perfil == 1) {
		$tmp_administracion = new templates("templates/menu_administracion.html");
		$html_administracion = $tmp_administracion->show(true);
	}

	$tmp_edi = new templates("templates/menu_edi.html");
	$html_edi = $tmp_edi->show(true);
	
	$tmp_proveedores = new templates("templates/menu_proveedoresInternos.html");
	$html_prov = $tmp_proveedores->show(true);
	
	$tmp_embarcadores = new templates("templates/menu_embarcadoresInternos.html");
	$html_embar = $tmp_embarcadores->show(true);

	//Permisos del usuario
	include("conect/conect.php");
	$conexion = conectar_srvdev();
	
	$sqlPermisos="SELECT PermiUsuario.[email]
					  ,PermiUsuario.[id_permiso]
					  ,Permisos.[nombre]
					  ,Permisos.[id_permiso]
				FROM [cfg_PermisosUsuario] PermiUsuario
				INNER JOIN cfg_Permisos Permisos on
				Permisos.id_permiso=PermiUsuario.id_permiso
				where PermiUsuario.email='".$_SESSION['email']."'";
				  
	$result = sqlsrv_query($sqlPermisos, $conexion);
	
	$permisosArray = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		//$data[] = utf8_encode($row["descripcion"]);
		array_push($permisosArray,$row);
	}
	sqlsrv_close($conexion);
	$permisosArray=json_encode($permisosArray);

	$template = new templates("templates/internos.html");
	$template->setParams( array (
		'menu_administracion' => utf8_encode($html_administracion),
		'menu_edi' => utf8_encode($html_edi),
		'menu_prov' => utf8_encode($html_prov),
		'menu_embar' => utf8_encode($html_embar)
	));
	$template->show();
?>
<script type="text/javascript">activarPermisos('<?php echo $permisosArray;?>');</script>
