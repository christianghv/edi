<?php
	include_once("../conect/conect.php");
	function VerificarPermiso($idPermiso)
	{
		$respuesta=false;
		//Validar PERMISOS
		
		$conexion = conectar_srvdev();
		
		$SqlPermiso="SELECT TOP 1 PermiUsuario.[id_permiso]
					FROM [cfg_PermisosUsuario] PermiUsuario
					where PermiUsuario.email='".$_SESSION['email']."'
					and PermiUsuario.id_permiso=".$idPermiso.";";
		
		
		$result = sqlsrv_query($conexion, $SqlPermiso);
		
		$IdRetorno = "";
		while( $row = sqlsrv_fetch_array($result) )
		{
			$IdRetorno = $row["id_permiso"];
		}
		sqlsrv_close($conexion);
		$idPermiso.="";
		
		//Verificando Retorno
		if($idPermiso == $IdRetorno)
		{
			$respuesta=true;
		}
		
		return $respuesta;
	}
	function VerificarSiEsProveedor($idProvedor)
	{
		$respuesta=false;
		//Validar PERMISOS
		
		$conexion = conectar_srvdev();
		
		$SqlPermiso="SELECT TOP 1 LOWER([id_proveedor]) as id_proveedor, nombre
					FROM [cfg_Proveedores] 
					WHERE id_proveedor='".$idProvedor."';";
		$result = sqlsrv_query($conexion, $SqlPermiso);
		
		$IdRetorno = "";
		while( $row = sqlsrv_fetch_array($result) )
		{
			$IdRetorno = $row["id_proveedor"];
		}
		sqlsrv_close($conexion);
		$idPermiso.="";
		
		//Verificando Retorno
		if($idProvedor == $IdRetorno)
		{
			$respuesta=true;
		}
		
		return $respuesta;
	}
	
	function VerificarSiEsEmbarcador($idEmbarcador)
	{
		$respuesta=false;
		//Validar PERMISOS
		
		$conexion = conectar_srvdev();
		
		$SqlPermiso="SELECT TOP 1 LOWER([id_embarcador]) as id_embarcador, [nombre]
					FROM [cfg_Embarcadores] 
					WHERE [id_embarcador]='".$idEmbarcador."';";
		$result = sqlsrv_query($conexion, $SqlPermiso);

		while( $row = sqlsrv_fetch_array($result) )
		{
			$IdRetorno = $row["id_embarcador"];
		}
		sqlsrv_close($conexion);
		$idPermiso.="";
		
		//Verificando Retorno
		if($idEmbarcador == $IdRetorno)
		{
			$respuesta=true;
		}
		return $respuesta;
	}
	
?>