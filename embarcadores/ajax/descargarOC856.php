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
	$sociedad = $_POST["sociedad"];
	$nro_oc   = $_POST["nro_oc"];
	$factura  = $_POST["factura"];
	$inicio   = $_POST["inicio"];
	$termino  = $_POST["termino"];
	
	$conexion = conectar_srvdev();
	$sql = "";
	if( $nro_oc != "") {
		$sql = " SELECT DISTINCT he.segm_id as nro_factura, he.segm_date as fecha_despacho, he.segm_time as hora, he.ship_measurement as peso_carga, ";
		$sql.= " he.ship_unit as unidad_medida, he.ship_packing as cod_embalaje, he.ship_lading as tot_unid_embarcadas, ";
		$sql.= " he.ship_transport as tipo_transporte, he.ship_transname as descripcion, he.ship_trailernumber as nro_camion, ";
		$sql.= " he.Sociedad as sociedad, he.enviado as enviado, he.ship_cantBultos as cant_bulto ";
		$sql.= " FROM [856HEADER] as he ";
		$sql.= " INNER JOIN [856DETAIL] as de ON he.segm_id = de.segm_Id ";
		//$sql.= " LEFT JOIN [856BULTO] as bu ON he.segm_id = bu.invNumber "; 
		$sql.= " WHERE 1=1 ";
		$sql.= " AND de.it_po = '".$nro_oc."' ";
		
		if($factura != "" && $factura != null) {
			$sql.= " AND de.segm_Id = '".$factura."' ";
		}
	}else {
		
		$sql = " SELECT DISTINCT he.segm_id as nro_factura, he.segm_date as fecha_despacho, he.segm_time as hora, he.ship_measurement as peso_carga, ";
		$sql.= " he.ship_unit as unidad_medida, he.ship_packing as cod_embalaje, he.ship_lading as tot_unid_embarcadas, ";
		$sql.= " he.ship_transport as tipo_transporte, he.ship_transname as descripcion, he.ship_trailernumber as nro_camion, ";
		$sql.= " he.Sociedad as sociedad, he.enviado as enviado, he.ship_cantBultos as cant_bulto ";
		$sql.= " FROM [856HEADER] as he ";
		//$sql.= " LEFT JOIN [856BULTO] as bu ON he.segm_id = bu.invNumber "; 
		$sql.= " WHERE 1=1 ";
		if($factura != "" && $factura != null) {
			$sql.= " AND he.segm_id = '".$factura."' ";
		} else {
			
			if($sociedad != "" && $sociedad != null ) {
				$sql.= " AND he.Sociedad = '".$sociedad."' ";
			}
			if($inicio != "" && $inicio != null) {
				$sql.= " AND he.segm_date >= CONVERT(DATETIME, '".$inicio."', 103) ";
			}
			if($termino != "" && $termino != null) {
				$sql.= " AND he.segm_date <= CONVERT(DATETIME, '".$termino."', 103) ";
			}
		}	
	}	
	$result = sqlsrv_query($conexion, $sql);
	
	//$file = fopen("sql.txt",'w+');
	//fwrite($file,$sql);
	//fclose($file);
	
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		$row = array_map("eliminaNull",$row);
		$row["fecha_despacho"] = date("d-m-Y", strtotime($row["fecha_despacho"])); 
		$row["descripcion"] = utf8_decode($row["descripcion"]);
		array_push($data,$row);
	}
	sqlsrv_close($conexion);
	echo json_encode($data);
}

if($accion == "obtenerDetalleOC856EDI")
{
	$NumeroOC856=$_POST["NumeroOC"];
	
	$remplazar = array("E09-");
	$NumeroOC856Corto = str_replace($remplazar, "", $NumeroOC856);
	$Edi856="";
	
	$conexion = conectar_srvdev();
	//856HEADER
	
	$sql = "
	SELECT TOP 1 Header.[segm_id],convert(varchar, Header.[segm_date], 12) AS segm_date,Header.[segm_time],Header.[ship_measurement],Header.[ship_unit],Header.[ship_packing], 
    Header.[ship_lading],Header.[ship_transport],Header.[ship_transname],Header.[ship_trailernumber],Header.[Sociedad],Header.[enviado],Header.[ship_cantBultos], 
    extras.[interchange_SenderID],extras.interchange_ReceiverID,extras.Bsn_creation_time,extras.SCAC_code 
    FROM [856HEADER] Header 
    INNER JOIN [856EXTRAS] extras ON 
    extras.segm_id=Header.segm_id 
	WHERE Header.[segm_id]='$NumeroOC856'";

	$result = sqlsrv_query($conexion, $sql);
	
	$Header856 = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		array_push($Header856,$row);
	}
	
	$fechaNueva = strtotime($FechaOC850);
	$FechaOC850Formteada = date('ymd',$fechaNueva);
	
	
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
		
		$Isa="ISA*00*          *00*          *01*".$Header[interchange_SenderID]."      *01*".$Header[interchange_ReceiverID]."      *".$FechaFormateada."*".$Header[segm_time]."*U*00300*1*0*P*>";
		$Gs="<GS*SH*".$Header[interchange_SenderID]."*".$RutSociedad."*".$FechaFormateada."*".$Header[segm_time]."*1*X*003030";
		$St="<ST*856*1";
		$Bsn="<BSN*00*".$NumeroOC856Corto."*".$FechaFormateada."*".$Header[Bsn_creation_time]."";
		$Dmt="<DTM*011*".$FechaFormateada."*".$Header[Bsn_creation_time]."*LT";
		$Hl_1="<HL*1*1*S";
		$Mea="<MEA*PD*G*".$Header[ship_measurement]."*".$Header[ship_unit]."";
		$Td1="<TD1*".$Header[ship_packing]."*".$Header[ship_lading]."";
		$Td5="<TD5*B*2*".$Header[SCAC_code]."*".$Header[ship_transport]."*".$Header[ship_transname]."";
		$Td3="<TD3*TL**".$Header[ship_trailernumber]."";
		$RefHeader="<REF*BM*".substr($NumeroOC856Corto, 0, 7)."";
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
	
	$d=1;
	foreach($Detail856 as $Detail)
	{
		$Hl_2="<HL*".$d."*1*I";
		$Lin="<LIN**BP*".$Detail[it_prodid]."";
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
	$Se="<SE*27*1";
	$Ge="<GE*1*1";
	$Iea="<IEA*1*1<";
	
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

?>
