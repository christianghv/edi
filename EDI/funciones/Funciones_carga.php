<?php 

require_once("../../conect/conect.php");
ini_set ( 'sqlsrv.connect_timeout' , '600' );
ini_set ( 'sqlsrv.timeout' , '600' );

$CONNECCION_UNICA_FUNCIONES_EDI = conectar_srvdev();

function ActualizarBD($query)
{
	global $CONNECCION_UNICA_FUNCIONES_EDI;
	//Limpiar Sql
	$query = str_replace("'NULL'", "NULL", $query);
	$result = sqlsrv_query($CONNECCION_UNICA_FUNCIONES_EDI, $query);
		
	if (!$result) {
		$CONNECCION_UNICA_FUNCIONES_EDI = conectar_srvdev();
		$result = sqlsrv_query($CONNECCION_UNICA_FUNCIONES_EDI, $query);
		//echo "NOK";
	}else{
		//echo "OK";	
	}
}
function ValidarDatosNoVacios($ArregloMultiSimple,$EDI)
{
	$Respuesta='ok';
	$Campo_errado='';
		
	foreach(array_keys($ArregloMultiSimple) as $key){
		//Recorriendo para ver si hay un campo vacio
		if(trim($ArregloMultiSimple[$key])=="")
		{
			//Se encontro dato vacio
			$Campo_errado=$EDI.' -> '.$key;
			$Respuesta='nok';
			break;
		}
	}
		
	$RespuestaValidarDatosNoVacios=array(
		'Respuesta'=>$Respuesta,
		'Campo_errado'=>$Campo_errado
	);
		
	return $RespuestaValidarDatosNoVacios;
}
function getArregloDatosNoVacios($ArregloMultiSimple)
{
	$NuevoArreglo=array();
			
	foreach(array_keys($ArregloMultiSimple) as $key){
		
		//Recorriendo para ver si hay un campo vacio
		if(trim($ArregloMultiSimple[$key])!="")
		{
			//Se encontro dato vacio
			$item=array($key=>$ArregloMultiSimple[$key]);
			array_push($NuevoArreglo,$item);
		}
	}
			
	return $NuevoArreglo;
}
function LimpiarString($Valor)
{
	$buscar=array(chr(13).chr(10), "\r\n", "\n", "\r");
	$reemplazar=array("", "", "", "");
	$Valor=str_ireplace($buscar,$reemplazar,$Valor);
	return $Valor;
}
function CerrarConeccionUnica()
{
	global $CONNECCION_UNICA_FUNCIONES_EDI;
	sqlsrv_close($CONNECCION_UNICA_FUNCIONES_EDI);
}
function LogProceso($Log,$Mensaje)
{
	//Limpiar mensaje
	$Mensaje=LimpiarString($Mensaje);
	//Asignar tipo
	$MensajeScript=str_replace('#TIPO#', 'N', $Log);
			
	echo str_replace('#LOG#', trim($Mensaje), $MensajeScript);
}
function LogProcesoError($Log,$Mensaje)
{
	//Limpiar mensaje
	$Mensaje=LimpiarString($Mensaje);
	//Asignar tipo
	$MensajeScript=str_replace('#TIPO#', 'E', $Log);
			
	echo str_replace('#LOG#', trim($Mensaje), $MensajeScript);
}
function LogProcesoExito($Log,$Mensaje)
{
	//Limpiar mensaje
	$Mensaje=LimpiarString($Mensaje);
	//Asignar tipo
	$MensajeScript=str_replace('#TIPO#', 'S', $Log);
			
	echo str_replace('#LOG#', trim($Mensaje), $MensajeScript);
}
function LogProcesoPrimario($Log,$Mensaje)
{
	//Limpiar mensaje
	$Mensaje=LimpiarString($Mensaje);
	//Asignar tipo
	$MensajeScript=str_replace('#TIPO#', 'P', $Log);
			
	echo str_replace('#LOG#', trim($Mensaje), $MensajeScript);
}
function LogProcesoInfo($Log,$Mensaje)
{
	//Limpiar mensaje
	$Mensaje=LimpiarString($Mensaje);
	//Asignar tipo
	$MensajeScript=str_replace('#TIPO#', 'I', $Log);
			
	echo str_replace('#LOG#', trim($Mensaje), $MensajeScript);
}
function LogProcesoAdvertencia($Log,$Mensaje)
{
	//Limpiar mensaje
	$Mensaje=LimpiarString($Mensaje);
	//Asignar tipo
	$MensajeScript=str_replace('#TIPO#', 'W', $Log);
			
	echo str_replace('#LOG#', trim($Mensaje), $MensajeScript);
}
function ExisteFacturaYBulto($InvoiceNumber,$PackingSlip)
{
	global $CONNECCION_UNICA_FUNCIONES_EDI;
	
	$Registros=0;
	
	$query="SELECT COUNT([idenBulto]) as registros
			FROM [856BULTO]
			WHERE [invNumber]='$InvoiceNumber'
			AND [idenBulto]='$PackingSlip'";
	//Limpiar Sql
	$query = str_replace("'NULL'", "NULL", $query);
	$result = sqlsrv_query($CONNECCION_UNICA_FUNCIONES_EDI, $query);
		
	if (!$result) {
		$CONNECCION_UNICA_FUNCIONES_EDI = conectar_srvdev();
		$result = sqlsrv_query($CONNECCION_UNICA_FUNCIONES_EDI, $query);
	}
	
	while( $row = sqlsrv_fetch_array($result) )
	{
		$Registros = intval($row["registros"]);
	}
	
	return $Registros;
}
?>
