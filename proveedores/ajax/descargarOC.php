<?php
include_once($_SERVER['DOCUMENT_ROOT']."/conect/conect.php");
include_once($_SERVER['DOCUMENT_ROOT']."/proveedores/ajax/SegAjax.php");
include_once($_SERVER['DOCUMENT_ROOT']."/proveedores/ajax/funciones.php");
$accion = $_POST["accion"];	
$conexion = conectar_srvdev();
//error_reporting(0);

if($accion == "obtenerDetalleOC850")
{
	$NumeroOC850=$_POST["NumeroOC"];
	$FechaOC850=$_POST["FechaOC"];
	$MontoOC850=$_POST["MontoOC"];
	$Sociedad=$_POST["Sociedad"];
	$Proveedor=$_POST["Proveedor"];
	
	$MontoOC850=floatval($MontoOC850);
	
	require_once($_SERVER['DOCUMENT_ROOT']."/conf_sap.php");
	global $UrlWSDespi,$PuertoWSDespi,$usuarioWSDespi,$contrasenaWSDespi;
	
	$_POST["NumeroOC"]="";
	
	include_once($_SERVER['DOCUMENT_ROOT']."/EDI/ajax/ws_buscarOC_855.php");
	$WS_BuscarOC_855 = new WS_BuscarOC_855($UrlWSDespi,$PuertoWSDespi,$usuarioWSDespi,$contrasenaWSDespi);
	$data=$WS_BuscarOC_855->WS_detalleordencompra($NumeroOC850);
	print_r($data);die();
	
	//-->Creado cabecera<--
	//Formato salida: NumeroOC, Fecha, Sociedad, Proveedor, Monto
	$cabecera="$NumeroOC850,$FechaOC850,$Sociedad,$Proveedor,$MontoOC850";
	$cabecera.=PHP_EOL;

	//-->Creando detalle<--
	$detalleOC="";
	//Formato Salida: PO_Position, PO_PartNumber, PO_description, PO_Quantity, PO_Price, PO_Unit, PO_Money, PaisOrigen
	$Detalle_OC850=json_decode($Json_detalle);
	//print_r($Detalle_OC850);
	
	foreach($Detalle_OC850 as $detalle)
	{
		$detalleOC.=$detalle->EBELP;
		$detalleOC.=",";		
		$detalleOC.=$detalle->MATNR;
		$detalleOC.=",";
		$detalleOC.=$detalle->TXZ01;
		$detalleOC.=",";	
		$detalleOC.=$detalle->MENGE;
		$detalleOC.=",";	
		$detalleOC.=$detalle->NETPR;
		$detalleOC.=",";	
		$detalleOC.=$detalle->MEINS;
		$detalleOC.=",";	
		$detalleOC.=$detalle->WAERS;
		$detalleOC.=",";	
		$detalleOC.=$detalle->CAMP1;			
		$detalleOC.=PHP_EOL;
		
	}
	$factura850= $cabecera.$detalleOC;
	
	$ArchivoTxtCreado="archivos/850/$NumeroOC850.txt";
	
	if (file_exists($ArchivoTxtCreado)) {
    unlink($ArchivoTxtCreado);
	}
	
	$fp=fopen($ArchivoTxtCreado,"x");
	fwrite($fp,$factura850);
	fclose($fp);
	echo $ArchivoTxtCreado;
}

if($accion == "obtenerDetalleOC856EDI")
{
	$NumeroOC856=$_POST["NumeroOC"];
	$Edi856="";
	//856HEADER
	
	$sql = "
	SELECT TOP 1 [segm_id],[segm_date],[segm_time],[ship_measurement],[ship_unit],[ship_packing]
      ,[ship_lading],[ship_transport],[ship_transname],[ship_trailernumber],[Sociedad]
      ,[enviado],[ship_cantBultos]
	FROM [856HEADER]
	WHERE [segm_id]='$NumeroOC856'";

	$result = sqlsrv_query($conexion, $sql);
	
	$Header856 = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		array_push($Header856,$row);
	}
		
	$fechaNueva = strtotime($FechaOC850);
	$FechaOC850Formteada = date('ymd',$fechaNueva);
	
	//print_r($Header856);

	foreach($Header856 as $Header)
	{
		//Variables
		$Header856="";
		$Detalle856="";
		
		//Rut de sociedad
		$RutSociedad="";
		$Sociedad=$Header[Sociedad];
		
		$SqlRutSociedad="
	      SELECT TOP 1 [id_receiver]
			  ,[id_sociedad]
		  FROM [cfg_sociedades]
		  WHERE [id_sociedad]='".$Header[Sociedad]."';";
		  
		$result = sqlsrv_query($SqlRutSociedad, $conexion);

		while( $row = sqlsrv_fetch_array($result) )
		{
			$RutSociedad=$row["id_receiver"];
		}
		
		$FechaRecibida=$Header[segm_date];
		$SoloFecha = substr($FechaRecibida, 0, 10);
		
		$fechaNueva = strtotime($SoloFecha);
		$FechaFormateada = date('ymd',$fechaNueva);		
		
		$Isa="ISA*00*          *00*          *01*146708334      *01*968431407      *".$FechaFormateada."*".$Header[segm_time]."*U*00300*000017624*0*P*>";
		$Gs="<GS*SH*146708334*".$RutSociedad."*".$FechaFormateada."*".$Header[segm_time]."*17624*X*003030";
		$St="<ST*856*25846";
		$Bsn="<BSN*00*".$NumeroOC856."*".$FechaFormateada."*1136";
		$Dmt="<DTM*011*".$FechaFormateada."*1136*LT";
		$Hl_1="<HL*1*1*S";
		$Mea="<MEA*PD*G*".$Header[ship_measurement]."*".$Header[ship_unit]."";
		$Td1="<TD1*".$Header[ship_packing]."*".$Header[ship_lading]."";
		$Td5="<TD5*B*2*FEDI*".$Header[ship_transport]."*".$Header[ship_transname]."";
		$Td3="<TD3*TL**TRL2842144";
		$RefHeader="<REF*BM*".substr($NumeroOC856, 0, 7)."";
	}
	//Grabando Cabecera
	$Header856.=$Isa.$Gs.$St.$Bsn.$Dmt.$Hl_1.$Mea.$Td1.$Td5.$Td3.$RefHeader;
	
	//DETALLE 856
		
	$SqlDetalle="SELECT [segm_Id],[it_prodid],[it_unitshiped],[it_unitmeasurement],[it_po]
		  ,[it_refnumber],[it_packingsleep],[it_poPosition],[it_prodid2],[invoicePosition],[it_codEmbalaje]
		  FROM [856DETAIL]
		  WHERE [segm_Id]='".$NumeroOC856."';";
	
	$result = sqlsrv_query($SqlDetalle, $conexion);
	
	$Detail856 = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		array_push($Detail856,$row);
	}	
	
	$d=2;
	foreach($Detail856 as $Detail)
	{
		$Hl_2="<HL*".$d."*1*I";
		$Lin="<LIN*00220*BP*".$Detail[it_prodid]."";
		$Sn1="<SN1**1*".$Detail[it_unitmeasurement]."";
		$Prf="<PRF*".$Detail[it_unitshiped]."";
		
		$SqlTiPoBulto="SELECT TOP 1
						  [tipoBulto] 
					  FROM [856BULTO] where [invNumber]='$NumeroOC856' 
					  AND idenBulto='".$Detail[it_packingsleep]."'";
					  
		$result = sqlsrv_query($SqlTiPoBulto, $conexion);
		$TipoBulto="";
		while( $row = sqlsrv_fetch_array($result) )
		{
			$TipoBulto=$row["tipoBulto"];
		}
		$Cld="<CLD*1*1*".$TipoBulto."";
		
		$Ref_1="<REF*PK*".$Detail[it_packingsleep]."";
		$Ref_2="<REF*ZZ*".$Detail[it_refnumber]."";
		$d++;
		$Detalle856.=$Hl_2.$Lin.$Sn1.$Prf.$Cld.$Ref_1.$Ref_2;
	}
	
	//NAME 856
	
	$SqlName="
	SELECT [segm_Id],[ship_entity],[ship_idcodequali],[ship_idcode]
	FROM [856NAME]
	WHERE [segm_Id]='$NumeroOC856'";
		  
	$result = sqlsrv_query($SqlName, $conexion);

	while( $row = sqlsrv_fetch_array($result) )
	{
		if($row["ship_entity"]=="SF")
		{
			$Name1="<N1*SF**".$row["ship_idcodequali"]."*".$row["ship_idcode"]."";
		}
		if($row["ship_entity"]=="ST")
		{
			$Name2="<N1*ST**".$row["ship_idcodequali"]."*".$row["ship_idcode"]."";
		}
	}
	
	sqlsrv_close($conexion);
	
	$Header856.=$Name1;
	$Header856.=$Name2;
		
	$Ctt="<CTT*".$d."";
	$Se="<SE*27*26139";
	$Ge="<GE*1*17783";
	$Iea="<IEA*1*000017783<";
	
	$Edi856=$Header856.$Detalle856.$Ctt.$Se.$Ge.$Iea;
	
	$ArchivoEdiCreado="archivos/856/$NumeroOC856.edi";
	
	if (file_exists($ArchivoEdiCreado)) {
    unlink($ArchivoEdiCreado);
	}
	
	$fp=fopen($ArchivoEdiCreado,"x");
	fwrite($fp,$Edi856);
	fclose($fp);
	echo $ArchivoEdiCreado;	
}
if($accion == "obtenerDetalleOC850EDI")
{
	$RutDeSociedad="";
	$NumeroOC850=$_POST["NumeroOC"];
	$FechaOC850=$_POST["FechaOC"];
	$MontoOC850=$_POST["MontoOC"];
	$Sociedad=$_POST["Sociedad"];
	$Proveedor=$_POST["Proveedor"];
	$NombreEmbarcador=$_POST["NombreEmbarcador"];
	
	$MontoOC850=floatval($MontoOC850);
	
	$fechaNueva = strtotime($FechaOC850);
	$FechaOC850Formteada = date('ymd',$fechaNueva);

	$Json_detalle=ObtenerJsonDetalle($NumeroOC850);

	$RutDeSociedad=ObtenerRutSociedad($Sociedad);
	
	//--->Creando EDI 850
	
	$Isa="ISA*00*          *00*          *01*";
	$RutDeSociedad=RellenarValorConEspacios($RutDeSociedad,15);
	
	$Isa.=$RutDeSociedad;
	
	$Isa.="*01*042939959      *".$FechaOC850Formteada."*0000*U*00300*000014017*1*P*<";
	$Isa.=PHP_EOL;
	
	$Gs="GS*PO*".$RutDeSociedad."*042939959*".$FechaOC850Formteada."*0000*14017*X*003030<";
	$Gs.=PHP_EOL;
	
	$St="ST*850*0001<";
	$St.=PHP_EOL;
	
	$Beg="BEG*00*NE*".$NumeroOC850."**".$FechaOC850Formteada."***SST<";
	$Beg.=PHP_EOL;
	
	$Dtm="DTM*002*".$FechaOC850Formteada."<";
	$Dtm.=PHP_EOL;
	
	$Td5="TD5*B***7*SENATOR INTERNATIONAL<";
	$Td5.=PHP_EOL;
	
	$N1_1="N1*SO**92*55800<";
	$N1_1.=PHP_EOL;
	
	$N1_2="N1*ST*SENATOR INTERNATIONAL<";
	$N1_2.=PHP_EOL;
	
	$N3="N3*11250 NW 25th STREET - SUITE 124<";
	$N3.=PHP_EOL;
	
	$N4="N4*MIAMI*FL*33172*USA<";
	$N4.=PHP_EOL;

	//-->Creando detalle<--
	$detalleOC="";
	//Formato Salida: PO_Position, PO_PartNumber, PO_description, PO_Quantity, PO_Price, PO_Unit, PO_Money, PaisOrigen
	$Detalle_OC850=json_decode($Json_detalle);
	//print_r($Detalle_OC850);
	
	$CantidadProductos=0;
	$PO1="";
	foreach($Detalle_OC850 as $detalle)
	{
		$Cantidad=intval($detalle->MENGE);
		$PO1.="PO1*".$detalle->EBELP."*".$Cantidad."*".$detalle->MEINS."*".$detalle->NETPR."**BP*".$detalle->MATNR."<";
		$PO1.=PHP_EOL;
		$CantidadProductos++;
	}
	
	$Ctt="CTT*".$CantidadProductos."<";
	$Ctt.=PHP_EOL;
	
	$SE="SE*".($CantidadProductos+10)."*0001<";
	$SE.=PHP_EOL;
	
	$GE="GE*1*14017<";
	$GE.=PHP_EOL;
	
	$IEA="IEA*1*000014017<";
	$IEA.=PHP_EOL;
	
	$Edi850=$Isa.$Gs.$St.$Beg.$Dtm.$Td5.$N1_1.$N1_2.$N3.$N4.$PO1.$Ctt.$SE.$GE.$IEA;
	
	$ArchivoEdiCreado="archivos/850/edi/$NumeroOC850.edi";
	
	if (file_exists($ArchivoEdiCreado)) {
    unlink($ArchivoEdiCreado);
	}
	
	$fp=fopen($ArchivoEdiCreado,"x");
	fwrite($fp,$Edi850);
	fclose($fp);
	echo $ArchivoEdiCreado;
}
	
?>
