<?
ini_set('memory_limit', "240M");
ini_set('max_execution_time', 600);
ini_set("display_errors", 1);
ini_set("max_input_time ", 600);
set_time_limit(600);
error_reporting(0);

require_once($_SERVER['DOCUMENT_ROOT']."/conect/conect.php");
include_once("SegAjax.php");
require_once('Writer.php');


$sociedad			= $_REQUEST["sociedad"];
$nro_ocs   			= $_REQUEST["nro_ocs"];
$facturas  			= $_REQUEST["facturas"];
$documentos_embarque= $_REQUEST["documentos_embarque"];
$entregas_entrante 	= $_REQUEST["entregas_entrante"];
$proveedor  		= $_REQUEST["proveedor"];
$fecha_inicio   	= $_REQUEST["fecha_inicio"];
$fecha_termino  	= $_REQUEST["fecha_termino"];
$fecha_inicio_sap  	= $_REQUEST["fecha_inicio_sap"];
$fecha_termino_sap  = $_REQUEST["fecha_termino_sap"];

if($sociedad!="")
{
	//try{
	//////////// F O R M A T O //////////////////
	$nombrearchivo = "EDI1666_".$sociedad;
	$fecha = date("Ymd_his"); 
	$NombreFinal=$nombrearchivo.'_'.$fecha.'.xls';
	$arc = "archivos/edi1666/".$NombreFinal;
	$workbook = new Spreadsheet_Excel_Writer($arc);
	$num_format =& $workbook->addFormat();
	$num_format->setNumFormat('###,##0.00');
	$num_format1 =& $workbook->addFormat();
	$num_format1->setNumFormat('#');
	$num_format1->setBorder(1);
	
	$workbook->setCustomColor(12, 0, 32, 96);
	$format_our_green =& $workbook->addFormat();
	$format_our_green->setFgColor(12);
	
	$format_bold =& $workbook->addFormat();
	$format_bold->setBold();
	$format_bold->setBorder(2);
	$format_bold->setFgColor(12);
	$format_bold->setColor('white');
	$format_bold->setTextWrap();
	///////////////////////////////////////////
	$verde =& $workbook->addFormat();
	$verde->setFgColor('green');
	$verde->setBorder(1);
	$rojo =& $workbook->addFormat();
	$rojo->setFgColor('red');
	$rojo->setBorder(1);
	$amarillo =& $workbook->addFormat();
	$amarillo->setFgColor('yellow');
	$amarillo->setBorder(1);
	$celda =& $workbook->addFormat();
	$celda->setBorder(1);
	///////////////////////////////////////////
	//$workbook->send($NombreFinal);
	//////////////////////////////////////////	
	$worksheet =& $workbook->addWorksheet('CABECERA');
	//$worksheet->write(0, 0, 'INFORME CONFIG. LIMITES X ELEMENTO' ,$format_bold);
	
	//////////////////// CABECERAS /////////////////////////////////////////////
	$worksheet->write(0, 0, 'Sociedad', $format_bold);
	$worksheet->write(0, 1, utf8_decode('N° Factura'), $format_bold);
	$worksheet->write(0, 2, utf8_decode('Posición en factura'), $format_bold);
	$worksheet->write(0, 3, 'OC / Urgencia / Grupo de Compras', $format_bold);
	$worksheet->write(0, 4, utf8_decode('Posición SAP OC'),$format_bold );
	$worksheet->write(0, 5, 'Material',$format_bold );
	$worksheet->write(0, 6, utf8_decode('Descripción Material'), $format_bold);
	$worksheet->write(0, 7, 'Unidad Medida', $format_bold);
	$worksheet->write(0, 8, 'Cantidad Facturada', $format_bold);
	$worksheet->write(0, 9, 'Precio Unitario', $format_bold);
	$worksheet->write(0, 10, 'Moneda', $format_bold);		
	$worksheet->write(0, 11, utf8_decode('País de Origen'), $format_bold);
	$worksheet->write(0, 12, 'Monto Facturado', $format_bold);
	$worksheet->write(0, 13, utf8_decode('N° Tracking'), $format_bold);
	$worksheet->write(0, 14, utf8_decode('Código Bulto'), $format_bold);
	$worksheet->write(0, 15, 'Transportista en Origen', $format_bold);
	$worksheet->write(0, 16, utf8_decode('Fecha de Factura'), $format_bold);
	$worksheet->write(0, 17, utf8_decode('Recepción de Embarcador'), $format_bold);
	$worksheet->write(0, 18, utf8_decode('N° de Bultos '), $format_bold);
	$worksheet->write(0, 19, utf8_decode('Código Interno de bultos Embarcador'), $format_bold);
	$worksheet->write(0, 20, 'Largo(CM)', $format_bold);
	$worksheet->write(0, 21, 'Ancho(CM)', $format_bold);
	$worksheet->write(0, 22, 'Alto(CM)', $format_bold);
	$worksheet->write(0, 23, 'Peso Total Bulto (Kg)', $format_bold);
	$worksheet->write(0, 24, utf8_decode('Volument en MT³'), $format_bold);
	$worksheet->write(0, 25, 'Tipo Bulto', $format_bold);
	$worksheet->write(0, 26, utf8_decode('Status / Compras'), $format_bold);
	$worksheet->write(0, 27, utf8_decode('Instrucción de Compras Internacionales a Embarcador'), $format_bold);
	$worksheet->write(0, 28, 'Comentario', $format_bold);
	$worksheet->write(0, 29, utf8_decode('N° documento embarque'), $format_bold);
	$worksheet->write(0, 30, 'Forma Tipo embarque a Chile', $format_bold);
	$worksheet->write(0, 31, utf8_decode('Via Aéreo/ Marítimo'), $format_bold);
	$worksheet->write(0, 32, utf8_decode('Tipo de consolidación de contenedor'), $format_bold);
	$worksheet->write(0, 33, utf8_decode('Código Contenedor'), $format_bold);
	$worksheet->write(0, 34, utf8_decode('Costo total de Guía Importación'), $format_bold);
	$worksheet->write(0, 35, 'Fecha estimada arribo Aeropuerto/Puerto', $format_bold);
	$worksheet->write(0, 36, 'Nombre Vuelo/Barco', $format_bold);
	$worksheet->write(0, 37, 'Fecha envio Embarcador', $format_bold);
	$worksheet->write(0, 38, 'Puerto / Aeropuerto Destino', $format_bold);
	$worksheet->write(0, 39, 'Entrega Entrante', $format_bold);
	$worksheet->write(0, 40, 'Urgencia Material', $format_bold);
	$worksheet->write(0, 41, utf8_decode('Código de destino SAP'), $format_bold);
	$worksheet->write(0, 42, utf8_decode('Via de despacho Nacional'), $format_bold);
	$worksheet->write(0, 43, 'Coordinador Responsable Compras', $format_bold);
	$worksheet->write(0, 44, utf8_decode('Responsable Tramitación / Internacion Nacional'), $format_bold);
	$worksheet->write(0, 45, 'Responsable por Sociedad en caso de Incosistencia OC', $format_bold);
	$worksheet->write(0, 46, utf8_decode('Status Documento Importación'), $format_bold);
	$worksheet->write(0, 47, utf8_decode('Fecha pago derechos Importación'), $format_bold);
	$worksheet->write(0, 48, 'Horario pago Comex ', $format_bold);
	$worksheet->write(0, 49, utf8_decode('Hora pago Derechos Importación'), $format_bold);
	$worksheet->write(0, 50, utf8_decode('Fecha creación Guia de Despacho Aduana'), $format_bold);
	$worksheet->write(0, 51, utf8_decode('Hora creación Guia de Despacho Aduana'), $format_bold);
	$worksheet->write(0, 52, utf8_decode('Número Guia de Despacho Aduana'), $format_bold);
	$worksheet->write(0, 53, 'Fecha Retiro Puerto /Aeropuerto', $format_bold);
	$worksheet->write(0, 54, utf8_decode('N° Seguimiento Transporte Nacional'), $format_bold);
	$worksheet->write(0, 55, utf8_decode('Fecha de Recepción de Materiales a destino O Bodega CD'), $format_bold);
	//SELECT distinct TOP 30000 Det810.InvoiceNumber
	$sql = "
	SELECT distinct TOP 30000 Det810.InvoiceNumber
		  ,Det810.InvoicePosition
		  ,Det810.PONumber
		  ,Det810.POPosition
		  ,Det810.ProductID
		  ,Det810.ProductDesciption
		  ,Det810.ProductMeasure
		  ,Det810.ProductQuantity
		  ,Det810.PorductPrice
		  ,(SELECT InvoiceCurrency
			FROM InvoiceHeader
			WHERE InvoiceNumber=Det810.InvoiceNumber) as InvoiceCurrency
		  ,Det810.PaisOrigen
		  ,(CAST(Det810.ProductQuantity AS FLOAT) * CAST(Det810.PorductPrice AS FLOAT))as PXQ
		  ,Det856.it_refnumber
		  ,Det856.it_packingsleep
		  ,(SELECT TOP 1 ship_transname 
			FROM [856HEADER] 
			WHERE segm_id=Det810.InvoiceNumber) as CarrierDomestico
		  ,(SELECT TOP 1 CONVERT(char(10), segm_date, 103) 
			FROM [856HEADER] 
			WHERE segm_id=Det810.InvoiceNumber) as segm_date
		  ,CONVERT(char(10), Head810.InvoiceDate, 103) as InvoiceDate
		  ,convert(char(10),emb.DateReceived, 103) as DateReceived
		  ,Bulto.peso
		  ,Bulto.longitud
		  ,Bulto.ancho
		  ,Bulto.alto
		  ,CONVERT(char(10), EDI855.ACK_Date, 103) as ACK_Date
		  ,CONVERT(char(10), EDI855.PO_Date, 103) as PO_Date 
		  ,(SELECT TOP 1 ship_lading
			FROM [856HEADER]
			WHERE segm_id=Det810.InvoiceNumber) as ship_lading
		  ,CONVERT(char(10), emb.ETD, 103) as ETD 
		  ,emb.Overpack
		  ,Bulto.volumen
		  ,(Convert(FLOAT, Bulto.longitud)*Convert(FLOAT, Bulto.ancho)*Convert(FLOAT, Bulto.alto)) as CBM
		  ,Bulto.tipoBulto
		  ,emb.InstruccionCompras
		  ,CONVERT(char(10), emb.FechaInstruccion, 103) as FechaInstruccion
		  ,emb.Comentario
		  ,emb.MawbBL as MAWB_B_L
		  ,emb.PMC_CargoContenedor as PMC
		  ,emb.ship_transport as AereoMaritimo
		  ,emb.LCL_FCL as LCL_FCL
		  ,emb.ID_Contenedor as IdContenedor
		  ,emb.Value_Total_BL_AWB as ValueTotal_B_L_AWB
		  ,CONVERT(char(10), emb.ETA, 103) as ETA
		  ,emb.Flight_Vessel as Flight_Vessel
		  ,(SELECT TOP 1 segm_time 
			FROM [856HEADER] 
			WHERE segm_id=Det810.InvoiceNumber) as segm_time
		  ,emb.Puerto_Aeropuerto as PuertoAeropuerto
		  ,Det810.EntregaEntrante
		  ,CASE WHEN Det810.PONumber IS NULL THEN '' ELSE SUBSTRING(Det810.PONumber, 11, 2) END as Urgencia
		  ,NULL as CeSAP
		  ,(SELECT TOP 1 ship_transport 
			FROM [856HEADER] 
			WHERE segm_id=Det810.InvoiceNumber) as ship_transport
		  ,compras.RespCompras
		  ,compras.RespComex
		  ,compras.RespPartOperations
		  ,compras.StatusAWB_BL_ParaEE
		  ,CONVERT(char(10), comex.FechaPagoDerechos, 103) as FechaPagoDerechos 
		  ,comex.[1erPago_2doPago]
		  ,comex.HoraPago
		  ,CONVERT(char(10), comex.FechaG_D, 103) as FechaG_D
		  ,comex.HoraG_D
		  ,comex.G_DAduana as G_D_Aduana
		  ,CONVERT(char(10), comex.FechaRetiroPuerto_Aeropuerto, 103) as FechaRetiroPuerto_Aeropuerto 
		  ,comex.BoletoTransportes
		  ,convert(char(10),FechaSAP.FechaIngresoSap, 103) as FechaIngresoSap 
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
	
	$sql.=" ORDER BY Det810.InvoiceNumber,Det856.it_packingsleep;";
	/**
	$file = fopen("sql1666_detalle_excel.txt",'w+');
	fwrite($file,$sql);
	fclose($file);
	*/
	//echo $sql;die();
	$conexion = conectar_srvdev();
	$result = sqlsrv_query($conexion, $sql);
	
	if (!$result) {
		// La consulta ha fallado, muestra un mensaje de error
		// utilizando sqlsrv_get_last_message()
		echo 'sNok';die();
	}
	
	$retorno = array();	
	
	$JSON_PONumber=array();
	$PONumber_Posiciones=array();
	$PONumberAcatual="";
	$ContadorPONumber=0;
	$it_packingsleep="";
	$fil = 1;
	
    while ($row = sqlsrv_fetch_array($result))
	{
		$JSON_PONumber[substr($row["PONumber"],0, 10)]=substr($row["PONumber"],0, 10);
		
		//Verificar si no existe
		if(!isset($PONumber_Posiciones[substr($row["PONumber"],0, 10)]))
		{
			$PONumber_Posiciones[substr($row["PONumber"],0, 10)]=array();
		}
		
		array_push($PONumber_Posiciones[substr($row["PONumber"],0, 10)],$fil);
		
		$worksheet->write($fil, 0, $sociedad, $celda);
		$worksheet->write($fil, 1, $row["InvoiceNumber"], $celda);
		$worksheet->write($fil, 2, $row["InvoicePosition"], $celda);
		$worksheet->write($fil, 3, $row["PONumber"], $num_format1);
		$worksheet->write($fil, 4, $row["POPosition"], $celda);
		$worksheet->writeString($fil, 5, $row["ProductID"]);
		$worksheet->writeString($fil, 6, utf8_decode($row["ProductDesciption"]), $celda);
		$worksheet->write($fil, 7, $row["ProductMeasure"], $celda);
		$worksheet->write($fil, 8, $row["ProductQuantity"], $celda);
		$worksheet->write($fil, 9, $row["PorductPrice"], $celda);
		$worksheet->write($fil, 10, $row["InvoiceCurrency"], $celda);
		$worksheet->write($fil, 11, $row["PaisOrigen"], $celda);
		$worksheet->write($fil, 12, $row["PXQ"], $celda);
		$worksheet->write($fil, 13, $row["it_refnumber"], $num_format1);
		$worksheet->writeString($fil, 14, $row["it_packingsleep"], $celda);
		$worksheet->write($fil, 15, $row["CarrierDomestico"], $celda);
		$worksheet->write($fil, 16, $row["InvoiceDate"], $celda);
		$worksheet->write($fil, 17, $row["DateReceived"], $celda);
		
		//it_packingsleep
		if($it_packingsleep=="")
		{
			$it_packingsleep=$row["it_packingsleep"];
			$worksheet->write($fil, 18, "1", $celda);
		}
		else
		{
			if($it_packingsleep!=$row["it_packingsleep"])
			{
				$it_packingsleep=$row["it_packingsleep"];
				$worksheet->write($fil, 18, "1", $celda);
			}
			else
			{
				$worksheet->write($fil, 18, "", $celda);
			}
		}
		
		
		$worksheet->write($fil, 19, $row["Overpack"], $celda);
		$worksheet->write($fil, 20, $row["longitud"], $celda);
		$worksheet->write($fil, 21, $row["ancho"], $celda);
		$worksheet->write($fil, 22, $row["alto"], $celda);
		$worksheet->write($fil, 23, $row["peso"], $celda);
		$worksheet->write($fil, 24, $row["volumen"], $celda);
		$worksheet->write($fil, 25, $row["tipoBulto"], $celda);
		$worksheet->write($fil, 26, $row["InstruccionCompras"], $celda);
		$worksheet->write($fil, 27, $row["FechaInstruccion"], $celda);
		$worksheet->write($fil, 28, $row["Comenario"], $celda);
		$worksheet->write($fil, 29, $row["MAWB_B_L"], $celda);
		$worksheet->write($fil, 30, $row["PMC"], $celda);
		$worksheet->write($fil, 31, $row["AereoMaritimo"], $celda);
		$worksheet->write($fil, 32, $row["LCL_FCL"], $celda);
		$worksheet->write($fil, 33, $row["IdContenedor"], $celda);
		$worksheet->write($fil, 34, $row["ValueTotal_B_L_AWB"], $celda);
		$worksheet->write($fil, 35, $row["ETA"], $celda);
		$worksheet->write($fil, 36, $row["Flight_Vessel"], $celda);
		
		$segm_time=trim($row["segm_time"]);
		if(strlen($segm_time)==4)
		{
			$segm_time=substr($segm_time,0,2).':'.substr($segm_time,2,2);
		}
		
		$worksheet->write($fil, 37, $row["ETD"], $celda);
		$worksheet->write($fil, 38, $row["PuertoAeropuerto"], $celda);
		$worksheet->write($fil, 39, $row["EntregaEntrante"], $celda);
		$worksheet->write($fil, 40, $row["Urgencia"], $celda);
		$worksheet->write($fil, 41, $row["CeSAP"], $celda);
		$worksheet->write($fil, 42, $row["ship_transport"], $celda);
		$worksheet->write($fil, 43, $row["RespCompras"], $celda);
		$worksheet->write($fil, 44, $row["RespComex"], $celda);
		$worksheet->write($fil, 45, $row["RespPartOperations"], $celda);
		$worksheet->write($fil, 46, $row["StatusAWB_BL_ParaEE"], $celda);
		$worksheet->write($fil, 47, $row["FechaPagoDerechos"], $celda);
		$worksheet->write($fil, 48, $row["1erPago_2doPago"], $celda);
		$worksheet->write($fil, 49, $row["HoraPago"], $celda);
		$worksheet->write($fil, 50, $row["FechaG_D"], $celda);
		$worksheet->write($fil, 51, $row["HoraG_D"], $celda);
		$worksheet->write($fil, 52, $row["G_D_Aduana"], $celda);
		$worksheet->write($fil, 53, $row["FechaRetiroPuerto_Aeropuerto"], $celda);
		$worksheet->write($fil, 54, $row["BoletoTransportes"], $celda);
		$worksheet->write($fil, 55, $row["FechaIngresoSap"], $celda);
		
		$fil++;	
	}
	
	sqlsrv_close($conexion);
	
	//Llamando al WS para buscar centro
	//echo $JSON_PONumber;
	require_once('ws_buscarGEECentro.php');
	$DatosCentro=WS_BuscarCentros($JSON_PONumber);
	
	foreach($DatosCentro as $Cen)
	{
		$Key=''.$Cen['POGNUMBER'];
		$Centro=''.$Cen['WERKS'];
		
		//Ingresando centro a celdas anteriores si no es vacio
		if(trim($Centro)!="")
		{
			foreach($PONumber_Posiciones[$Key] as $Posicion)
			{
				$worksheet->write($Posicion, 41, $Centro, $celda);
			}
		}
	}
	
	/**
	//Llamando al WS
	require_once('ws_buscarGEECentro.php');
	$DatosCentro=WS_BuscarCentros($JSON_PONumber);
	
	foreach($DatosCentro['T_OC'] as $Cen)
	{
		$Key=''.$Cen['POGNUMBER'];
		$Centro=''.$Cen['WERKS'];
		
		//Ingresando centro a facturas
		for($i=0;$i<count($retorno);$i++)
		{
			//Si existe la llave actualizar Centro
			if(isset($retorno[$Key.'_'.$i]))
			{
				$retorno[$Key.'_'.$i]['CeSAP']=$Centro;
			}
			else
			{
				//si no existe se cerrara el for
				break;
			}
		}
	}
	*/
	$workbook->close();
	//header("location: $arc");
	echo $NombreFinal;
	//}
	//catch (Exception $e) {
		//echo $e.'Nok';
	//}
}
?>