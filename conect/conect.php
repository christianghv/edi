<?php
set_time_limit (180);
//error_reporting(0);
/* DES*/
/**
function conectar_srvdev() {
	// Conectando, seleccionando la base de datos
	$host = 'sqlmikccqa.671bccbb2d55.database.windows.net'.//'172.21.1.44';
	$user = 'user_EDI'.//'usr_ediv2';
	$pass ='Edi2020QAS'.// '$Ediv2%!';
	$dbname = 'EDI'.//'EDI_v2';
	
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

/*function conectar_srvdev() {
	// Conectando, seleccionando la base de datos
	$host = 'SRVDEV';
	$user = 'user_d-edi-v2';
	$pass = '$3diU53r';
	$dbname = '[d-edi-v2]';
	
	$conectID = sqlsrv_connect($host,$user,$pass) or DIE("NO SE HA PODIDO ESTABLECER CONEXION CON EL SERVIDOR $hostname.");
	sqlsrv_select_db($dbname) or DIE("LA BASE DE DATOS $dbname NO RESPONDE");
	return $conectID;
}*/
function conectar_srvdev() {

$host = 'sqlmikccqa.671bccbb2d55.database.windows.net';
	$user = 'user_EDI';
	$pass = 'Edi2020QAS';
	$dbname = 'EDI';


$serverName = "sqlmikccqa.671bccbb2d55.database.windows.net"; 
        $connectionOptions = array("Database"=>"$dbname",  
            "Uid"=>"user_EDI", "PWD"=>"Edi2020QAS"); 
        $conn = sqlsrv_connect($serverName, $connectionOptions);  
		//print_r($connectionOptions);
		if( $conn ) {
     //echo "Conexión establecida.<br />";
}else{
     echo "Conexión no se pudo establecer.<br />";
     die( print_r( sqlsrv_errors(), true));
}
		
		
        return $conn;
}
?>