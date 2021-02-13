<?php
//require_once($_SERVER['DOCUMENT_ROOT'].'/conect/conect.php');	
error_reporting(0);
require_once('../../conect/conect.php');

include_once("SegAjax.php");
//echo $accion = $_REQUEST["accion"];	
include_once("funcion_VerificarEE.php");
	$accion = $_REQUEST["accion"];	

if($accion == 'cargarFacturas2')
	{
	$sociedad		= $_REQUEST["sociedad"];
	$estatus_ee		= $_REQUEST["estatus_ee"];
	$fechaInicio	= $_REQUEST["fechaInicio"];
	$fechaTermino	= $_REQUEST["fechaTermino"];
	$facturas		= $_REQUEST["facturas"];
	$TipoDocumentoBusqueda	= $_REQUEST["TipoDocumentoBusqueda"];
	
	$conexion = conectar_srvdev();
	$sql = " SELECT  Head.[InvoiceNumber], convert(char(10),Head.[InvoiceDate],105) as InvoiceDate, 
					  Head.[InvoiceCurrency], Head.[InvoiceNetValue], 
					  Head.[InvoiceGrossValue], Head.[InvoiceGastos], Head.[InvoiceVendor], 
					  Head.[Sociedad], Head.[Enviado], Head.[RespIntercomex], 
					  Head.[EntregaEntrante], Head.[tipoIngresp], (SELECT Estado856=CASE 
			((SELECT COUNT([InvoicePosition]) 
			  FROM [InvoiceDetail]
			  WHERE [InvoiceNumber]=Head.[InvoiceNumber])-
			(SELECT COUNT(invoicePosition) 
			  FROM [856DETAIL]
			  WHERE segm_Id=Head.[InvoiceNumber]))
			  WHEN 0 THEN 'OK'
			  ELSE 'Nok'
			  END) as Estado856
						FROM [InvoiceHeader] Head
						WHERE [Sociedad] = '$sociedad'";
	
	if($estatus_ee != "")
	{		
		if( $estatus_ee == '1' ) {
			$sql.= " AND Head.[EntregaEntrante] = '1' ";
		} 
		if( $estatus_ee == '3' ) {
			$sql.= " AND (Head.[EntregaEntrante] = '2' or Head.[EntregaEntrante] = '3') ";
		}
	}
	//echo $sql;die();
	
	if($facturas != '') 
	{
		if($TipoDocumentoBusqueda=="Factura")
		{
			$NumerosEncontrados=0;
			$sqlNumeros= " AND Head.InvoiceNumber in ( ";
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
			else
			{
				$sql.= " AND (Head.[InvoiceDate] BETWEEN CONVERT(DATETIME,'$fechaInicio  00:00:00',103) AND CONVERT(DATETIME,'$fechaTermino 23:59:59',103) )";
			}
		}
		
		if($TipoDocumentoBusqueda=="MawbBL")
		{
			$NumerosEncontrados=0;
			$sqlNumeros= " AND Head.InvoiceNumber in (
				SELECT DISTINCT[InvoiceNumber]
				FROM [embarque]  
				WHERE MawbBL IN (";
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
			$sqlNumeros.= " )) ";
			if($NumerosEncontrados>0)
			{
				$sql.=$sqlNumeros;
			}
			else
			{
				$sql.= " AND (Head.[InvoiceDate] BETWEEN CONVERT(DATETIME,'$fechaInicio 00:00:00',103) AND CONVERT(DATETIME,'$fechaTermino 23:59:59',103) )";
			}
		}
	}
	else 
	{
		$sql.= " AND (Head.[InvoiceDate] BETWEEN CONVERT(DATETIME,'$fechaInicio 00:00:00',103) AND CONVERT(DATETIME,'$fechaTermino 23:59:59',103) )";
	}	
				$file = fopen("sql2.txt",'w+');
				fwrite($file,$sql);
				fclose($file);
	
	$result = sqlsrv_query($conexion, $sql );

	$data 	= array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		array_push($data,$row);
	}
	sqlsrv_close($conexion);
	
	echo json_encode($data);
}

if($accion == "cargarSociedadCombo")
{
	$conexion = conectar_srvdev();
	$sql = "SELECT [id_sociedad],[desc_sociedad]
            FROM [d_edi].[dbo].[cfg_sociedades_desc]";
	$result = sqlsrv_query($conexion, $sql);
	
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		array_push($data,$row);
	}
	sqlsrv_close($conexion);
	echo json_encode($data);
}

if($accion == "cargarInvoiceDetail")
{
	$invoiceNumber = $_POST["invoiceNumber"];
	
	$conexion = conectar_srvdev();
	$sql = "SELECT detalle.[InvoiceNumber],
				   detalle.[InvoicePosition],
				   detalle.[PONumber],
				   detalle.[POPosition],
				   detalle.[ProductID],
				   detalle.[ProductDesciption],
				   detalle.[ProductMeasure],
				   detalle.[ProductQuantity],
				   detalle.[PorductPrice],
				   detalle.[EntregaEntrante],
				   detalle.[PaisOrigen]
				  ,(SELECT Estado856=CASE (SELECT COUNT(*) 
				   FROM [856DETAIL]
				   WHERE segm_Id=detalle.[InvoiceNumber]
				   AND it_prodid=detalle.[ProductID]
				   AND it_po=detalle.PONumber
				   AND CAST((CASE it_poPosition WHEN '' THEN 0 WHEN NULL THEN 0 ELSE it_poPosition END) AS DECIMAL(10, 0))=CAST((CASE detalle.POPosition WHEN '' THEN 0 WHEN NULL THEN 0 ELSE detalle.POPosition END) AS DECIMAL(10, 0))
				   AND CAST((CASE invoicePosition WHEN '' THEN 0 WHEN NULL THEN 0 ELSE invoicePosition END) AS DECIMAL(10, 0))=CAST((CASE detalle.[InvoicePosition] WHEN '' THEN 0 WHEN NULL THEN 0 ELSE detalle.[InvoicePosition] END) AS DECIMAL(10, 0))
				   AND CAST((CASE it_unitshiped WHEN '' THEN 0 WHEN NULL THEN 0 ELSE REPLACE(it_unitshiped,'.00','') END) AS DECIMAL(10, 0))=CAST((CASE detalle.ProductQuantity WHEN '' THEN 0 WHEN NULL THEN 0 ELSE REPLACE(detalle.ProductQuantity,'.00','') END) AS DECIMAL(10, 0))
				   )
				   WHEN 1 THEN 'OK'
						  ELSE 'Nok'
						  END) AS EDI_856
			FROM [InvoiceDetail] detalle
			WHERE [InvoiceNumber]='$invoiceNumber'
			";
			
	//echo $sql;die();
	$result = sqlsrv_query($conexion, $sql);
	$file = fopen("sqlinvoice2.txt",'w+');
	fwrite($file,$sql);
	fclose($file);
	$data = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		//$data[] = utf8_encode($row["descripcion"]);
		array_push($data,$row);
	}
	sqlsrv_close($conexion);
	$data=json_encode($data);
	//print_r($data);
	echo $data;
}
if($accion == "cargarTablaGEE")
{
	$JSON_ArrayDeGEE=$_REQUEST["JSON_ArrayDeGEE"];
	$sociedad		= $_REQUEST["sociedad"];
	
	$ArrayInvoiceNumber=json_decode($JSON_ArrayDeGEE);

	//print_r($ArrayInvoiceNumber);
	$file = fopen("sqlcargatablaparametros.txt",'w+');
	fwrite($file,$accion);
fwrite($file,$JSON_ArrayDeGEE);
fwrite($file,$sociedad);
	fclose($file);
	
	//Validar si todos son el mismo proveedor
	$sql="SELECT COUNT(DISTINCT[InvoiceVendor]) as Cantidad_InvoiceVendor
		  FROM [InvoiceHeader] ";
	
	$i=0;
	foreach($ArrayInvoiceNumber->ArrayDeGEE as $N_Factura)
	{	
	//echo "hola";
		if($i==0)
		{
			$sql.=" WHERE InvoiceNumber='".$N_Factura->InvoiceNumber."'";
		}
		else
		{
			$sql.=" OR InvoiceNumber='".$N_Factura->InvoiceNumber."'";
		}
		$i++;
	}
	$sql.=" AND Sociedad='$sociedad';";
	
	$file = fopen("sqlcargatabla111.txt",'w+');
	fwrite($file,$sql);
	fclose($file);
	
	$Registro_InvoiceVendor="0";
	$Cantidad_InvoiceVendor=0;
	
	$conexion = conectar_srvdev();
	
	$result = sqlsrv_query($conexion, $sql);	
	while( $row = sqlsrv_fetch_array($result) )
	{
		$Registro_InvoiceVendor=trim($row['Cantidad_InvoiceVendor']);
	}
	sqlsrv_close($conexion);
	
	if($Registro_InvoiceVendor!="")
	{
		$Cantidad_InvoiceVendor=intval($Registro_InvoiceVendor);
		$Cantidad_InvoiceVendor=$Registro_InvoiceVendor;
	}
		
	//Si es diferente de 1
	if(!$Cantidad_InvoiceVendor==1)
	{
		echo "ERROR: proveedor no unico";
		die();
	}
	
	//Validar si todos son del mismo transporte
	$sql="SELECT COUNT(DISTINCT[ship_transport]) as Cantidad_ship_transport
		  FROM [embarque] ";
	
	$i=0;
	foreach($ArrayInvoiceNumber->ArrayDeGEE as $N_Factura)
	{
		if($i==0)
		{
			$sql.=" WHERE InvoiceNumber='".$N_Factura->InvoiceNumber."'";
		}
		else
		{
			$sql.=" OR InvoiceNumber='".$N_Factura->InvoiceNumber."'";
		}
		$i++;
	}
	$sql.=" AND Sociedad='$sociedad';";
	
	
	$Registro_ship_transport="0";
	$Cantidad_ship_transport=0;
	
	$conexion = conectar_srvdev();
	
	$file = fopen("sqlcargatabla2.txt",'w+');
	fwrite($file,$sql);
	fclose($file);
	$result = sqlsrv_query($conexion, $sql);	
	while($row = sqlsrv_fetch_array($result) )
	{
		$Registro_ship_transport=$row['Cantidad_ship_transport'];
	}
	sqlsrv_close($conexion);
	
	if($Registro_ship_transport!="")
	{
		$Cantidad_ship_transport=intval($Registro_ship_transport);
		$Cantidad_ship_transport=$Registro_ship_transport;
		}
	
	//Si es diferente de 1
//$Cantidad_ship_transport = 1;
	if(!$Cantidad_ship_transport==1)
	{
		if($Cantidad_ship_transport==0)
		{
			echo " ERROR: transporte no registrado";
			die();
		}
		else
		{
			echo "ERROR: transporte no unico";
			die();
		}
	}
	
	$sql="SELECT DISTINCT detalle856.[it_po] as PONumber,
			  detalle856.segm_Id as InvoiceNumber,
			  detalle856.it_packingsleep as idenBulto,
			  (SELECT TOP 1 peso FROM [856BULTO] WHERE invNumber=detalle856.segm_Id AND idenBulto=detalle856.it_packingsleep) as peso,
			  (SELECT TOP 1 volumen FROM [856BULTO] WHERE invNumber=detalle856.segm_Id AND idenBulto=detalle856.it_packingsleep) as volumen,
			  (SELECT TOP 1 longitud FROM [856BULTO] WHERE invNumber=detalle856.segm_Id AND idenBulto=detalle856.it_packingsleep) as longitud,
			  (SELECT TOP 1 ancho FROM [856BULTO] WHERE invNumber=detalle856.segm_Id AND idenBulto=detalle856.it_packingsleep) as ancho,
			  (SELECT TOP 1 alto FROM [856BULTO] WHERE invNumber=detalle856.segm_Id AND idenBulto=detalle856.it_packingsleep) as alto,
			  NULL as Centro,
			  NULL as Incoterms,
			  parametros.urgencia as sociedad_urgencia,
			  parametros.peso as sociedad_peso,
			  parametros.longitud as sociedad_longitud,
			  parametros.ancho as sociedad_ancho,
			  parametros.alto as sociedad_alto,
			  embarque.ship_transport,
			  embarque.ID_Contenedor,
			   (select count(*) from [856DETAIL] detalle856Interno
									where detalle856Interno.segm_Id= detalle856.segm_Id
									AND detalle856Interno.it_po=detalle856.it_po
									AND detalle856Interno.it_packingsleep=detalle856.it_packingsleep) 
                                     as Lineas
		  FROM [856DETAIL] detalle856
		  INNER JOIN [InvoiceDetail] detalle810 ON
		  detalle810.[InvoiceNumber]=detalle856.[segm_Id]
		  AND detalle810.[PONumber]=detalle856.it_po
		  AND CAST((CASE detalle810.POPosition WHEN '' THEN 0 ELSE ISNULL(detalle810.POPosition,0) END) AS DECIMAL(10, 0)) = CAST((CASE detalle856.it_poPosition WHEN '' THEN 0 ELSE ISNULL(detalle856.it_poPosition,0) END) AS DECIMAL(10, 0)) 
		  AND CAST((CASE detalle810.InvoicePosition WHEN '' THEN 0 ELSE ISNULL(detalle810.InvoicePosition,0) END) AS DECIMAL(10, 0)) = CAST((CASE detalle856.invoicePosition WHEN '' THEN 0 ELSE ISNULL(detalle856.invoicePosition,0) END) AS DECIMAL(10, 0)) 
		  INNER JOIN [embarque] embarque ON
		  embarque.Sociedad='$sociedad' 
		  AND embarque.PONumber=detalle856.it_po
		  AND embarque.InvoiceNumber=detalle856.segm_Id
		  AND embarque.[Ship_trailernumber]=detalle856.it_packingsleep
		  LEFT JOIN [cfg_parametros_distribucion] parametros ON
		  parametros.sociedad='$sociedad' 
		  AND parametros.urgencia=(SELECT SUBSTRING(detalle856.it_po, 11,2)) 
		  ";
	
	$i=0;
	//print_r($ArrayInvoiceNumber);
	//die();
	foreach($ArrayInvoiceNumber->ArrayDeGEE as $N_Factura)
	{
		if($i==0)
		{
			$sql.=" WHERE detalle856.[segm_Id]='".$N_Factura->InvoiceNumber."'";
		}
		else
		{
			$sql.=" OR detalle856.[segm_Id]='".$N_Factura->InvoiceNumber."'";
		}
		$i++;
	}
	$sql.=" AND (detalle810.EntregaEntrante='0' 
				OR detalle810.EntregaEntrante is null 
				OR detalle810.EntregaEntrante = '') 
			AND detalle856.it_packingsleep IN (SELECT DISTINCT idenBulto FROM [856BULTO] WHERE invNumber=detalle856.segm_Id AND estado='D')
			ORDER BY detalle856.[segm_Id];";
	

			//echo $sql;
	$file = fopen("sqlinvoice11111.txt",'w+');
	fwrite($file,$sql);
	fclose($file);

	$conexion = conectar_srvdev();
	

	$result = sqlsrv_query($conexion, $sql);	
	$data = array();
	//Se Crea DataWsPara Evitar Duplicados al WS
	$dataWS= array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		$dataWS[$row['PONumber']]=$row['PONumber'];
		array_push($data,$row);
	}
	sqlsrv_close($conexion);
	
	
	if(count($dataWS)>0)
	{
		require_once($_SERVER['DOCUMENT_ROOT'].'/EDI/ajax/ws_buscarGEECentro.php');
		
		$DatosCentro=WS_BuscarCentros($dataWS);
		
		$OrdenesCentro=array();
		foreach($DatosCentro as $Cen)
		{
			$Key=''.$Cen['POGNUMBER'];
			$Centro=''.$Cen['WERKS'];
			$Incoterms=''.$Cen['INCOTERMS'];
			
			
			//Si no existe la llave actualizar Centro
			if(!isset($OrdenesCentro[$Key]))
			{
				$OrdenesCentro[$Key]['centro']=$Centro;
				$OrdenesCentro[$Key]['Incoterms']=$Incoterms;
			}
		}
		
		//Asignando a cada Filta su Centro
		for ($i = 0; $i <count($data); $i++) {
			$data[$i]['Centro']=$OrdenesCentro[substr($data[$i]['PONumber'], 0, 10)]['centro'];
			$data[$i]['Incoterms']=$OrdenesCentro[substr($data[$i]['PONumber'], 0, 10)]['Incoterms'];
		}
	}
	
	echo json_encode($data);
}
if($accion == "BuscarCentros")
{
	$JSON_PONumber = $_POST["JSON_PONumber"];
	
	echo WS_BuscarCentros($JSON_PONumber);
}
if($accion == "VerificarEECompleta")
{
	$JSON_ArrayDeGEE=$_POST["JSON_ArrayDeGEE"];
	
	$ArrayInvoiceNumber=json_decode($JSON_ArrayDeGEE);
	
	 $data = array();
	
	foreach($ArrayInvoiceNumber->ArrayDeGEE as $Factura)
	{
		$VerificacionEE=VerificarEstadoFactura($Factura->InvoiceNumber);
		
		//Si es estado 1 significa que esta completa
		if($VerificacionEE==1)
		{
			$item=array(
						"InvoiceNumber"=>$Factura->InvoiceNumber,
						"PONumber"=>$Factura->PONumber,
						"Centro"=>$Factura->Centro
						);
			array_push($data,$item);
		}
		
		$i++;
	}
	
	$data=json_encode($data);
	//print_r($data);
	echo $data;	
}

if($accion == "ActualizarEE")
{
	$JsonAEE=$_POST["JsonAEE"];
	$afectadas=0;
	$ArrayEE=json_decode($JsonAEE);
	$retorno = array();
	
	foreach($ArrayEE->ArrayDeAEE as $Factura)
	{
		$EE=$Factura->EE;
		$PONumber=$Factura->PONumber;
		$InvoiceNumber=$Factura->InvoiceNumber;
		
		$sql="UPDATE [InvoiceDetail] 
			  SET EntregaEntrante='".$EE."' 
			  WHERE PONumber='".$PONumber."' 
			  AND InvoiceNumber='".$InvoiceNumber."'";
		
		$conexion = conectar_srvdev();
		
		$result = sqlsrv_query($conexion, $sql);
		$afectadas++;
		$afectadas=sqlsrv_rows_affected($result);
		if($afectadas>0)
		{
			array_push($retorno,$Factura);
			
			$numeroActualFactura=$InvoiceNumber;
		
			$sqlUpdateHeader="EXEC [SP_EE_FACTURA] '$numeroActualFactura';";
			
			$conexion = conectar_srvdev();
			$result = sqlsrv_query($conexion, $sqlUpdateHeader);
			$afectadas=sqlsrv_rows_affected($result);
			sqlsrv_close($conexion);
		}
		sqlsrv_close($conexion);
		
	}
	
	$retorno=json_encode($retorno);
	//print_r($data);
	echo $retorno;
}
if($accion == "BuscarMaterialEnFacturas")
{
	$Material=$_POST["Material"];
	$JSON_ArrayDeGEE=$_POST["JSON_ArrayDeGEE"];
		
	$sql="	SELECT DISTINCT detalle.PONumber,
			detalle.InvoiceNumber
			FROM [InvoiceDetail] detalle    ";
	
	$ArrayInvoiceNumber=json_decode($JSON_ArrayDeGEE);
	
	$sql.=" WHERE detalle.ProductID='".$Material."'
			AND detalle.InvoiceNumber IN(";
	
	foreach($ArrayInvoiceNumber->ArrayDeGEE as $N_Factura)
	{
		$sql.="'".$N_Factura->InvoiceNumber."',";
	}
	
	$sql=substr($sql, 0,strlen($sql)-1);
	$sql.=");";
	
	$conexion = conectar_srvdev();
	
	$result = sqlsrv_query($conexion, $sql);	
	$data = array();
	
	while($row = sqlsrv_fetch_array($result))
	{
		array_push($data,$row);
	}
	sqlsrv_close($conexion);
	
	echo json_encode($data);
}
if($accion == "CargaDetalleCompletoBulto")
{
	$IdBulto   = $_POST["IdBulto"];
	$InvoiceNumber   = $_POST["InvoiceNumber"];
	$conexion = conectar_srvdev();
	
	$sql = " SELECT TOP 1 [idenBulto]
				  ,[tipoBulto]
				  ,[peso]
				  ,[unidPeso]
				  ,[volumen]
				  ,[unidVolumen]
				  ,[longitud]
				  ,[ancho]
				  ,[alto]
				  ,[unidDimension]
				  ,CONVERT(varchar, [fechaDespacho],105) as [fechaDespacho]
				  ,[instEspeciales]
				  ,[invNumber]
			  FROM [856BULTO]
			  WHERE [idenBulto]='$IdBulto'
			  AND [invNumber]='$InvoiceNumber'"; 
	$result = sqlsrv_query($conexion, $sql);
	
	//echo $sql;
	//die();
	$Cabecera = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		$row = array_map("utf8_encode", $row );
		array_push($Cabecera,$row);
	}
	
	$sql = " SELECT [segm_Id]
				  ,[it_prodid]
				  ,[it_unitshiped]
				  ,[it_unitmeasurement]
				  ,[it_po]
				  ,[it_refnumber]
				  ,[it_packingsleep]
				  ,[it_poPosition]
				  ,[it_prodid2]
				  ,[invoicePosition]
				  ,[it_codEmbalaje]
			  FROM [856DETAIL]
			  WHERE [it_packingsleep]='$IdBulto'
			  AND [segm_Id]='$InvoiceNumber';"; 
	$result = sqlsrv_query($conexion, $sql);
	
	$Detalle = array();
	while( $row = sqlsrv_fetch_array($result) )
	{
		$row = array_map("utf8_encode", $row );
		array_push($Detalle,$row);
	}
	
	$data= array("Cabecera"=>$Cabecera,
				 "Detalle"=>$Detalle);
	
	sqlsrv_close($conexion);
	echo json_encode($data);
}
?>