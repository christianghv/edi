<?php
set_time_limit (180);
/* DES*/
/**
function conectar_srvdev() {
	// Conectando, seleccionando la base de datos
	$host = '172.21.1.44';
	$user = 'usr_ediv2';
	$pass = '$Ediv2%!';
	$dbname = 'EDI_v2';
	
	$conectID = sqlsrv_connect($host,$user,$pass) or DIE("NO SE HA PODIDO ESTABLECER CONEXION CON EL SERVIDOR $hostname.");
	sqlsrv_select_db($dbname) or DIE("LA BASE DE DATOS $dbname NO RESPONDE");
	return $conectID;
}

//Fase 2 EDI_v3
function conectar_srvdev() {
	// Conectando, seleccionando la base de datos
	$host = 'SRVDEV';
	$user = 'ediwasdes';
	$pass = 'ediweas2017';
	$dbname = 'EDI';
	
	$conectID = sqlsrv_connect($host,$user,$pass) or DIE("NO SE HA PODIDO ESTABLECER CONEXION CON EL SERVIDOR $hostname.");
	sqlsrv_select_db($dbname) or DIE("LA BASE DE DATOS $dbname NO RESPONDE");
	return $conectID;
}

*/

function conectar_srvdev() {
	// Conectando, seleccionando la base de datos
	$host = 'arrayanv2';
	$user = 'user_pcg';
	$pass = 'pcg1409';
	$dbname = '[EDI]';
	
	$conectID = sqlsrv_connect($host,$user,$pass) or DIE("NO SE HA PODIDO ESTABLECER CONEXION CON EL SERVIDOR $hostname.");
	sqlsrv_select_db($dbname) or DIE("LA BASE DE DATOS $dbname NO RESPONDE");
	return $conectID;
}
?>