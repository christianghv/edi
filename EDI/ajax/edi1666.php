<?php
include("../../conect/conect.php");
include_once("SegAjax.php");
$accion = $_POST["accion"];	

function eliminaNull($valor) {
	return str_replace("null","",$valor);
}

function eliminaNull2($valor) {
	return str_replace("NULL","",$valor);
}

if($accion == "cargaCabecera")
{
	$sociedad			= $_POST["sociedad"];
	$nro_ocs   			= $_POST["nro_ocs"];
	$facturas  			= $_POST["facturas"];
	$documentos_embarque  = $_POST["documentos_embarque"];
	$entregas_entrante 	= $_POST["entregas_entrante"];
	$proveedor  		= $_POST["proveedor"];
	$fecha_inicio   	= $_POST["fecha_inicio"];
	$fecha_termino  	= $_POST["fecha_termino"];
	$fecha_inicio_sap  	= $_POST["fecha_inicio_sap"];
	$fecha_termino_sap  = $_POST["fecha_termino_sap"];

	
	$conexion = conectar_srvdev();
		
	$sql = "SELECT DISTINCT TOP 1000 Det810.[InvoiceNumber],
			 Det810.InvoicePosition,
			 Det810.ProductID,
			 Det810.ProductQuantity,
			 Bulto.idenBulto,
			 convert(char(10),Head810.InvoiceDate, 105) as InvoiceDate,
			 convert(char(10),emb.DateReceived, 105) as DateReceived,
			 convert(char(10),emb.ETD, 105) as ETD,
			 emb.MawbBL,
			 convert(char(10),emb.ETA, 105) as ETA,
			 Det810.EntregaEntrante,
			 compras.StatusAWB_BL_ParaEE,
			 convert(char(10),comex.FechaPagoDerechos, 105) as FechaPagoDerechos,
			 convert(char(10),comex.FechaG_D, 105) as FechaG_D,
			 comex.G_DAduana,
			 comex.BoletoTransportes,
			 convert(char(10),FechaSAP.FechaIngresoSap, 105) as FechaIngresoSap 
			FROM [InvoiceHeader] Head810
			LEFT JOIN [InvoiceDetail] Det810 ON
			Det810.InvoiceNumber=Head810.InvoiceNumber
			LEFT JOIN [PO_HEADER] EDI855 ON
			EDI855.PO_Number=Det810.PONumber
			LEFT JOIN [856DETAIL] Det856 ON 
			Det856.segm_Id=Det810.[InvoiceNumber] 
			AND Det856.it_po=Det810.PONumber
			AND CAST((CASE Det856.it_poPosition WHEN '' THEN 0 WHEN NULL THEN 0 ELSE Det856.it_poPosition END) AS DECIMAL(10, 0)) = CAST((CASE Det810.POPosition WHEN '' THEN 0 WHEN NULL THEN 0 ELSE Det810.POPosition END) AS DECIMAL(10, 0))
			AND CAST((CASE Det856.invoicePosition WHEN '' THEN 0 WHEN NULL THEN 0 ELSE Det856.invoicePosition END) AS DECIMAL(10, 0))  = CAST((CASE Det810.InvoicePosition WHEN '' THEN 0 WHEN NULL THEN 0 ELSE Det810.InvoicePosition END) AS DECIMAL(10, 0)) 
			AND Det856.it_prodid=Det810.ProductID 
			LEFT JOIN [856BULTO] Bulto ON
			Bulto.idenBulto=Det856.it_packingsleep
			AND Bulto.invNumber=Det810.[InvoiceNumber] 
			LEFT JOIN embarque emb ON
			emb.Sociedad=Head810.Sociedad
			AND emb.PONumber=Det810.PONumber
			AND emb.InvoiceNumber=Det810.InvoiceNumber
			AND emb.Ship_trailernumber=Bulto.idenBulto
			LEFT JOIN ComprasComex compras ON
			compras.Invoice=Det810.InvoiceNumber
			AND compras.PackingSlip=Bulto.idenBulto
			LEFT JOIN Comex comex ON
			comex.Invoice=Det810.InvoiceNumber
			AND comex.PackingSlip=Bulto.idenBulto
			LEFT JOIN FechasIngresoSap FechaSAP ON
			FechaSAP.PO_Number = SUBSTRING(Det810.PONumber, 0, 11)
			AND FechaSAP.InvoiceNumber=Det810.InvoiceNumber
			AND CAST((CASE FechaSAP.POPosition WHEN '' THEN 0 WHEN NULL THEN 0 ELSE FechaSAP.POPosition END) AS DECIMAL(10, 0))  = CAST((CASE Det810.POPosition WHEN '' THEN 0 WHEN NULL THEN 0 ELSE Det810.POPosition END) AS DECIMAL(10, 0)) 
			AND FechaSAP.InvoicePosition=Det810.InvoicePosition 
			WHERE 1=1 
				  ";
				  
	
	
	if($facturas != '') 
	{
		$NumerosEncontrados=0;
		$sqlNumeros= " AND Det810.InvoiceNumber in ( ";
		$arr_facturas = explode('|',$facturas);
		$nfacturas = count($arr_facturas);
		
		for($i = 0; $i < $nfacturas; $i++)
		{
			$factura = trim($arr_facturas[$i]);
			if($factura != "") 
			{
				$sqlNumeros.= " '$factura', ";
				$NumerosEncontrados++;
			}			
		}
		$aux = trim($arr_facturas[0]);
		$sqlNumeros.= " '$aux' ";
		$sqlNumeros.= " ) ";
		if($NumerosEncontrados>0)
		{
			$sql.=$sqlNumeros;
		}
	}
	
	if($nro_ocs != '') 
	{
		$NumerosEncontrados=0;
		$sqlNumeros= " AND SUBSTRING(Det810.PONumber, 0, 11) in ( ";
		$arr_oc = explode('|',$nro_ocs);
		$nfacturas = count($arr_oc);
		
		for($i = 0; $i < $nfacturas; $i++)
		{
			$OC = trim($arr_oc[$i]);
			if($OC != "") 
			{
				$sqlNumeros.= " SUBSTRING('$OC', 0, 11), ";
				$NumerosEncontrados++;
			}
		}
		$aux = trim($arr_oc[0]);
		$sqlNumeros.= " '$aux' ";
		$sqlNumeros.= " ) ";
		if($NumerosEncontrados>0)
		{
			$sql.=$sqlNumeros;
		}
	}
	
	if($documentos_embarque != '') 
	{
		$NumerosEncontrados=0;
		$sqlNumeros= " AND emb.MawbBL in ( ";
		$arr_documento = explode('|',$documentos_embarque);
		$registros = count($arr_documento);
		
		for($i = 0; $i < $registros; $i++)
		{
			$Doc = trim($arr_documento[$i]);
			if($Doc != "") 
			{
				$sqlNumeros.= " '$Doc', ";
				$NumerosEncontrados++;
			}
		}
		$aux = trim($arr_documento[0]);
		$sqlNumeros.= " '$aux' ";
		$sqlNumeros.= " ) ";
		if($NumerosEncontrados>0)
		{
			$sql.=$sqlNumeros;
		}
	}
	
	if($entregas_entrante != '') 
	{
		$NumerosEncontrados=0;
		$sqlNumeros= " AND Det810.EntregaEntrante in ( ";
		$arr_ee = explode('|',$entregas_entrante);
		$registros = count($arr_ee);
		
		for($i = 0; $i < $registros; $i++)
		{
			$EE = trim($arr_ee[$i]);
			if($EE != "") 
			{
				$sqlNumeros.= " '$EE', ";
				$NumerosEncontrados++;
			}
		}
		$aux = trim($arr_ee[0]);
		$sqlNumeros.= " '$aux' ";
		$sqlNumeros.= " ) ";
		if($NumerosEncontrados>0)
		{
			$sql.=$sqlNumeros;
		}
	}
	
	//Si viene sociedad
	if($sociedad != '')
	{
		$sql.= " AND Head810.Sociedad = '".$sociedad."' ";
		
	}
	
	//Si viene proveedor
	if($proveedor != '')
	{
		$sql.= " AND Head810.InvoiceVendor = '".$proveedor."' ";
		
	}
	
	
	//Si no vienen numeros de OC o Numero de factura buscar por las 4 fechas
	if($nro_ocs == '' && $facturas == '' && $documentos_embarque=='' && $entregas_entrante==''  && $proveedor != '')
	{
		//Fechas
		if($fecha_inicio != "" && $fecha_inicio != null) {
			$sql.= " AND Head810.InvoiceDate >= CONVERT(DATETIME, '".$fecha_inicio." 00:00:00', 103) ";
		}
		
		if($fecha_termino != "" && $fecha_termino != null) {
			$sql.= " AND Head810.InvoiceDate <= CONVERT(DATETIME, '".$fecha_termino." 23:59:59', 103) ";
		}
		
		if($fecha_inicio_sap != "" && $fecha_inicio_sap != null) {
			$sql.= " AND FechaSAP.FechaIngresoSap >= CONVERT(DATETIME, '".$fecha_inicio_sap." 00:00:00', 103) ";
		}
		
		if($fecha_termino_sap != "" && $fecha_termino_sap != null) {
			$sql.= " AND FechaSAP.FechaIngresoSap <= CONVERT(DATETIME, '".$fecha_termino_sap." 23:59:59', 103) ";
		}
	}
	else
	{
		//Si es solo por fechas
		if($nro_ocs == '' && $facturas == '' && $documentos_embarque=='' && $entregas_entrante==''  && $proveedor == '')
		{
			//Fechas
			if($fecha_inicio != "" && $fecha_inicio != null) {
				$sql.= " AND Head810.InvoiceDate >= CONVERT(DATETIME, '".$fecha_inicio." 00:00:00', 103) ";
			}
			
			if($fecha_termino != "" && $fecha_termino != null) {
				$sql.= " AND Head810.InvoiceDate <= CONVERT(DATETIME, '".$fecha_termino." 23:59:59', 103) ";
			}
			
			if($fecha_inicio_sap != "" && $fecha_inicio_sap != null) {
				$sql.= " AND FechaSAP.FechaIngresoSap >= CONVERT(DATETIME, '".$fecha_inicio_sap." 00:00:00', 103) ";
			}
			
			if($fecha_termino_sap != "" && $fecha_termino_sap != null) {
				$sql.= " AND FechaSAP.FechaIngresoSap <= CONVERT(DATETIME, '".$fecha_termino_sap." 23:59:59', 103) ";
			}
		}
	
	}
	
	$sql.=";";
	
	$result = sqlsrv_query($conexion, $sql);
	
	$file = fopen("sql1666.txt",'w+');
	fwrite($file,$sql);
	fclose($file);
	
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		$row = array_map("eliminaNull",$row);
		array_push($data,$row);
	}
	sqlsrv_close($conexion);
	echo json_encode($data);
}
?>
