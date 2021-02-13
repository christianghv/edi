<?
function conexion_rrhh(){
  $hostname = "srvdev";
  $dbName = "rrhhv5";
  $username= "d_usr_rrhhv5";
  $password="RrhhV53009";

  $conectID = sqlsrv_connect($hostname,$username,$password) or DIE("NO SE HA PODIDO ESTABLECER CONEXION CON EL SERVIDOR.");
  sqlsrv_select_db($dbName) or DIE("LA BASE DE DATOS RRHH NO RESPONDE");
  return $conectID;
 }
?>
