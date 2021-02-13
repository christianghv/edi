<?php
include_once("SegAjax.php");
include_once("../../conect/conect.php");
function ObtenerRutSociedad($IdSociedad)
{
	$RutSociedad="";
	$sql="SELECT [id_receiver],[id_sociedad]
		  FROM [cfg_sociedades] 
		  WHERE [id_sociedad]='".$IdSociedad."'";
	
	$conexion = conectar_srvdev();
	
	$result = sqlsrv_query($conexion, $sql);
	
	while( $row = sqlsrv_fetch_array($result) )
	{
		$RutSociedad = utf8_encode($row["id_receiver"]);
	}	
	sqlsrv_close($conexion);
	
	return $RutSociedad;	
}
function RellenarValorConEspacios($Cadena, $Largo)
{
	$NuevaCadena=$Cadena;
	for ( $i = 1 ; $i <$Largo+1 ; $i ++) 
	{
		$NuevaCadena.=" ";
		if(strlen($NuevaCadena)==$Largo)
		{
			break;
		}
	}
	return $NuevaCadena;
}
?>