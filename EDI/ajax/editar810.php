<?php
error_reporting(0);
//require_once($_SERVER['DOCUMENT_ROOT']."/conect/conect.php");
require_once('../../conect/conect.php');
require_once('../../funciones/fx_util.php');
//require_once($_SERVER['DOCUMENT_ROOT']."/funciones/fx_util.php");
include_once("SegAjax.php");
include_once("funcion_VerificarEE.php");
$accion = $_REQUEST["accion"];


if($accion == "buscarHeaderFactura")
{
	
	$invoiceNumber=ms_escape_string($_REQUEST["invoiceNumber"]);
	
	$conexion = conectar_srvdev();
	
	$sql = "  SELECT TOP 1 Header.InvoiceNumber,CONVERT(VARCHAR(10), Header.InvoiceDate, 103) as InvoiceDate																															            ,[InvoiceCurrency],[InvoiceNetValue],[InvoiceGrossValue],[InvoiceGastos]
						,Header.InvoiceVendor,Header.Sociedad,Header.Enviado,Header.RespIntercomex,
						Header.EntregaEntrante,Header.tipoIngresp, Tol.tolerancia
			  FROM InvoiceHeader Header
			  INNER JOIN tolerancia Tol ON
			  Tol.bukrs=Header.Sociedad
			  WHERE Header.InvoiceNumber='$invoiceNumber';";
			
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

if($accion == "editarInvoiceDetail")
{
	
	$invoiceNumber=ms_escape_string($_POST["invoiceNumber"]);
	
	$conexion = conectar_srvdev();
	$sql = "SELECT TOP 1 [InvoiceNumber],CONVERT(VARCHAR(10), [InvoiceDate], 103) as InvoiceDate																															            ,[InvoiceCurrency],[InvoiceNetValue],[InvoiceGrossValue],[InvoiceGastos]
      		,[InvoiceVendor],[Sociedad],[Enviado],[RespIntercomex],[EntregaEntrante],[tipoIngresp]
			FROM [InvoiceHeader] where InvoiceNumber='$invoiceNumber';";
			
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
if($accion == "obtenerToleranciaPorSociedad")
{	
	$Sociedad=ms_escape_string($_POST["Sociedad"]);
	
	$conexion = conectar_srvdev();
	$sql = "SELECT TOP 1 bukrs,tolerancia
			FROM tolerancia
			WHERE bukrs='$Sociedad';";
			
	$result = sqlsrv_query($conexion, $sql);
	
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		array_push($data,$row);
	}
	sqlsrv_close($conexion);
	$data=json_encode($data);
	echo $data;
}
if($accion == "UpdateEditarInvoiceDetailHeader")
{
	//Datos modificados
	$InvoiceNumber=ms_escape_string($_POST["InvoiceNumber"]);
	$InvoiceDate=ms_escape_string($_POST["InvoiceDate"]);	
	$InvoiceCurrency=ms_escape_string($_POST["InvoiceCurrency"]);
	$InvoiceNetValue=ms_escape_string($_POST["InvoiceNetValue"]);
	$InvoiceGrossValue=ms_escape_string($_POST["InvoiceGrossValue"]);
	$InvoiceGastos=ms_escape_string($_POST["InvoiceGastos"]);
	$InvoiceVendor=ms_escape_string($_POST["InvoiceVendor"]);
	$Sociedad=ms_escape_string($_POST["Sociedad"]);

	$conexion = conectar_srvdev();
	$sql = "UPDATE [InvoiceHeader] SET [InvoiceDate]=convert(datetime, '$InvoiceDate', 103), [InvoiceCurrency]='$InvoiceCurrency',
	[InvoiceNetValue]='$InvoiceNetValue', [InvoiceGrossValue]='$InvoiceGrossValue', [InvoiceGastos]='$InvoiceGastos',
    [InvoiceVendor]='$InvoiceVendor', [Sociedad]='$Sociedad' WHERE [InvoiceNumber]='$InvoiceNumber';";
	
	$result = sqlsrv_query($conexion, $sql);
	$afectadas=sqlsrv_rows_affected($result);

$file = fopen("sql_upd.txt",'w+');
	fwrite($file,$afectadas);
	fclose($file);

	sqlsrv_close($conexion);
	
	echo $afectadas;
}

if($accion == "InsertEditarInvoiceDetailHeader")
{
	//Datos modificados
	$InvoiceNumber=ms_escape_string($_POST["InvoiceNumber"]);
	$InvoiceDate=ms_escape_string($_POST["InvoiceDate"]);	
	$InvoiceCurrency=ms_escape_string($_POST["InvoiceCurrency"]);
	$InvoiceNetValue=ms_escape_string($_POST["InvoiceNetValue"]);
	$InvoiceGrossValue=ms_escape_string($_POST["InvoiceGrossValue"]);
	$InvoiceGastos=ms_escape_string($_POST["InvoiceGastos"]);
	$InvoiceVendor=ms_escape_string($_POST["InvoiceVendor"]);
	$Sociedad=ms_escape_string($_POST["Sociedad"]);

	$conexion = conectar_srvdev();
	$sql = "INSERT INTO [InvoiceHeader] ([InvoiceNumber],[InvoiceDate],[InvoiceCurrency],[InvoiceNetValue], [InvoiceGrossValue]
	, [InvoiceGastos],[InvoiceVendor],[Sociedad],[EntregaEntrante])
	VALUES('$InvoiceNumber',convert(datetime, '$InvoiceDate', 103),'$InvoiceCurrency','$InvoiceNetValue','$InvoiceGrossValue',
	'$InvoiceGastos','$InvoiceVendor','$Sociedad',0);";
	//echo $sql;	
	$result = sqlsrv_query($conexion, $sql);
	$afectadas=sqlsrv_rows_affected($result);
	sqlsrv_close($conexion);
	echo $afectadas;
}


if($accion == "UpdateEditarInvoiceDetail")
{
	$InvoiceNumber=ms_escape_string($_POST["InvoiceNumber"]);	
	$JsonDetail=utf8_encode($_POST["JsonDetail"]);
	
	$ArrayDetail=json_decode($JsonDetail);
	
	$sqlQuery="delete from [InvoiceDetail] where [InvoiceNumber]='$InvoiceNumber'; ";
	
	foreach($ArrayDetail->ArrayDetalle as $detail)
	{
		$sqlQuery.="Insert into [InvoiceDetail] ([InvoiceNumber],[InvoicePosition],[PONumber],[POPosition],[ProductID],[ProductDesciption],[ProductMeasure]";
		$sqlQuery.=",[ProductQuantity],[PorductPrice],[EntregaEntrante],[PaisOrigen])";
        $sqlQuery.=" values('".ms_escape_string($InvoiceNumber)."','".ms_escape_string($detail->InvoicePosition)."','".ms_escape_string($detail->PONumber)."','".ms_escape_string($detail->POPosition)."','".ms_escape_string($detail->ProductID);
		$sqlQuery.="','".ms_escape_string($detail->ProductDesciption)."','".ms_escape_string($detail->ProductMeasure)."','".ms_escape_string($detail->ProductQuantity)."','".ms_escape_string($detail->PorductPrice);
		$sqlQuery.="','".ms_escape_string($detail->EntregaEntrante)."','".ms_escape_string($detail->PaisOrigen)."'); ";
	}
	/**
	print_r($ArrayDetail);die();
	echo $sqlQuery;die();
	*/
	$conexion = conectar_srvdev();	
	
    $result = sqlsrv_query($conexion, $sqlQuery );
	
	/**
	$fq = fopen("insert_detail.txt", "w");
	fwrite($fq, $JsonDetail);
	fclose($fq);
	*/
	
	$afectadas=sqlsrv_rows_affected($result);
	sqlsrv_close($conexion);
		
	$sqlUpdateHeader="EXEC [SP_EE_FACTURA] '$InvoiceNumber';";
	$conexion = conectar_srvdev();
	$result = sqlsrv_query($conexion, $sqlUpdateHeader);
	
	$sqlUpdateHeader="EXEC [SP_INVOICEPOSITION_810] '$InvoiceNumber';";
	$conexion = conectar_srvdev();						
	$result = sqlsrv_query($conexion, $sqlUpdateHeader);
	
	$sqlUpdateHeader="EXEC [SP_INVOICEPOSITION_856] '$InvoiceNumber';";
	$conexion = conectar_srvdev();						
	$result = sqlsrv_query($conexion, $sqlUpdateHeader);
	
	
	$fq = fopen("insert_detail856.txt", "w");
	fwrite($fq, $sqlUpdateHeader);
	fclose($fq);
	
	//$afectadas=sqlsrv_rows_affected($result);
	sqlsrv_close($conexion);
		
	echo $afectadas;
}

if($accion == "EliminarFactura810")
{
	$InvoiceNumber=ms_escape_string($_POST["InvoiceNumber"]);	
	
	$sqlQuery="delete from [InvoiceDetail] where [InvoiceNumber]='$InvoiceNumber'; ";
	$sqlQuery.="delete from [InvoiceHeader] where [InvoiceNumber]='$InvoiceNumber'; ";
	
	//echo $sqlQuery;
	
	$conexion = conectar_srvdev();	
	
    $result = sqlsrv_query($conexion, $sqlQuery);
	$afectadas=sqlsrv_rows_affected($result);
	sqlsrv_close($conexion);
	echo $afectadas;
}

if($accion == "VerificarInvoiceNumber810")
{
	$InvoiceNumber=ms_escape_string($_POST["InvoiceNumber"]);	
	
	$sqlQuery="SELECT [InvoiceNumber] FROM [InvoiceHeader] WHERE InvoiceNumber='$InvoiceNumber';";
		
	$conexion = conectar_srvdev();	
	
    $result = sqlsrv_query($conexion, $sqlQuery);
	$encontrados=0;
	
	while( $row = sqlsrv_fetch_array($result) )
	{
		$encontrados++;
	}
	
	sqlsrv_close($conexion);
	echo $encontrados;
}

if ($accion == "cargarDetalleFromCSV") 
	{
		$retorno = "";
		$archivo = $_POST["archivo"];
		
		$fp 	 = fopen ("detalle_factura/".$archivo,"r");
		fgetcsv($fp, 1000, ";"); //encabezados
		$result  = false;
		$reg 	 = 0;
		$count 	 = 0;
		//limpiaPeriodo($periodo);
		$conexion = conexion_bd();
		while ($data = fgetcsv($fp, 1000, ";")) 
		{
			$count++;
			
			$sql = "  ";
			
			$result_ut = sqlsrv_query($conexion, $sql);	
			$row = sqlsrv_fetch_array($result_ut);
			if($row[0] == $data[0])
				$reg++;
		}
		fclose($fp);
		deleteFile($archivo);
		if($count == $reg) {
			$retorno = "Detalle cargado correctamente.";
		}else {
			$retorno = "Error al cargar detalle.";	
		}
		sqlsrv_close($conexion);
		echo $retorno;
	}
	
?>
