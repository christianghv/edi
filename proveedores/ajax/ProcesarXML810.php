<?php
	include_once("SegAjax.php");
	function ProcesarXML810($rutaArchivo, $sociedadIngresada)
	{
	$respuesta="";
    $xml = simplexml_load_file($rutaArchivo); //declaramos la ubicacion del XML

	$sociedadEnviadaParaSubir=$sociedadIngresada;
	//$json = json_encode($xml);
	//$jsonDec=json_decode($json);
	//echo $jsonDec;
	//print_r($jsonDec);
	$Facturas=array();
	//810 header
	$H_proveedor="";
	$H_sociedad="";
	$H_NumeroFactura="";
	$H_FechaFactura="";
	$H_MontoNeto="";
	$H_MontoTotal="";
	
	//810 detail
	$Detalle_Factura=array();
	
	$P_Cantidad=0;
	$P_UnidadDeMedida="";
	$P_PrecioUnitario=0;
	$P_CodigoProducto="";
	$P_NumeroDeOC=0;
	$P_PO_position="";
	$P_descripcion="";
	$P_Correlativo="";
	$P_PaisOrigen="";
	

	foreach($xml->interchange as $interchange)
	{
		//print_r($interchange);
		
		$H_proveedor=$interchange->sender->address->attributes()->Id;
		$H_sociedad=$interchange->group->attributes()->ApplReceiver;
			$i=1;
			foreach($interchange->group->transaction  as $transaction){
				//Recorreremos los segmentos 
				foreach($transaction->segment  as $segment){
					//echo $segment->attributes()->Id."<br />";
					if((string)$segment->attributes()->Id=="BIG")
					{
						foreach($segment->element  as $element){
							//echo $element->attributes()->Id;
							if((string)$element->attributes()->Id=="BIG01")
							{
								$H_FechaFactura=$element;
							}

							if((string)$element->attributes()->Id=="BIG02")
							{
								$H_NumeroFactura=$element;
							}							
							
						}
						//echo $segment->attributes()->Id;
					}
					if((string)$segment->attributes()->Id=="TDS")
					{
						$H_MontoNeto=$segment->element;
						$H_MontoTotal=$segment->element;
								
						$FacturaHeader=array( proveedor => $H_proveedor,
												  sociedad=>$H_sociedad,
												  NumeroFactura => $H_NumeroFactura,
												  FechaFactura=>$H_FechaFactura,
												  MontoNeto=>$H_MontoNeto,
												  MontoTotal=>$H_MontoTotal
												);
						array_push($Facturas,$FacturaHeader);
						//echo "Factura Cargada<br />";
					}
				}
			//Loop para obtener el detalle
			foreach($transaction->loop  as $loop){
			
					//echo $loop->attributes()->Id."<br />";
					
					if((string)$loop->attributes()->Id=="IT1")
					{
						//echo "IT1 encontrado <br />";
						foreach($loop->segment  as $segmentLoop){
							//echo "loop segment IT1 ".$segmentLoop->attributes()->Id."<br />";
							if((string)$segmentLoop->attributes()->Id=="IT1")
							{
								foreach($segmentLoop->element  as $elementSegmentIT1){
									//echo "Segmen loop IT1 ".$elementSegmentIT1->attributes()->Id."<br />";
									if((string)$elementSegmentIT1->attributes()->Id=="IT102")
									{
										$P_Cantidad=$elementSegmentIT1;
									}
									if((string)$elementSegmentIT1->attributes()->Id=="IT103")
									{
										$P_UnidadDeMedida=$elementSegmentIT1;
									}
									if((string)$elementSegmentIT1->attributes()->Id=="IT104")
									{
										$P_PrecioUnitario=$elementSegmentIT1;
									}
									if((string)$elementSegmentIT1->attributes()->Id=="IT107")
									{
										$P_CodigoProducto=$elementSegmentIT1;
									}
									if((string)$elementSegmentIT1->attributes()->Id=="IT109")
									{
										$P_PaisOrigen=$elementSegmentIT1;
									}
									if((string)$elementSegmentIT1->attributes()->Id=="IT111")
									{
										$P_NumeroDeOC=$elementSegmentIT1;
									}
									if((string)$elementSegmentIT1->attributes()->Id=="IT113")
									{
										$P_PO_position=$elementSegmentIT1;
									}
								}
							}//Fin segmento IT1
							
							if((string)$segmentLoop->attributes()->Id=="REF")
							{
								$vnEncontrado=0;
								foreach($segmentLoop->element  as $elementSegmentREF){
									if((string)$elementSegmentREF->attributes()->Id=="REF01")
									{
										//Se validara que seaVN
										if((string)$elementSegmentREF=="VN")
										{
											$vnEncontrado=1;
										}
									}
									if((string)$elementSegmentREF->attributes()->Id=="REF02")
									{
										if($vnEncontrado==1)
										{
											//echo "VN encontrado :".$elementSegmentREF." <br />";
											$P_Correlativo=$elementSegmentREF;
											$vnEncontrado=0;
										}
										
									}
								}
							}//Fin REF
						}
						//echo $segment->attributes()->Id;
						foreach($loop->loop  as $loopPID){
						//echo "<br /> Loop pid ".$loopPID->attributes()->Id;
							if((string)$loopPID->attributes()->Id=="PID")
							{
								foreach($loopPID->segment->element  as $segmentElementPID){
									//echo "<br /> element PID ".$segmentElementPID->attributes()->Id;
									if((string)$segmentElementPID->attributes()->Id=="PID05")
									{
										$P_descripcion=$segmentElementPID;
										
										$ProductoFactura=array( 
												  NumeroFactura=> $H_NumeroFactura,
												  Cantidad => $P_Cantidad,
												  UnidadDeMedida=>$P_UnidadDeMedida,
												  PrecioUnitario => $P_PrecioUnitario,
												  CodigoProducto=>$P_CodigoProducto,
												  PaisOrigen=>$P_PaisOrigen,
												  NumeroDeOC=>$P_NumeroDeOC,
												  PO_position=>$P_PO_position,
												  descripcion=>$P_descripcion,
												  Correlativo=>$P_Correlativo
												);
										array_push($Detalle_Factura,$ProductoFactura);
										//echo "<br />Producto agregado de ".$H_NumeroFactura;
									}
								}
							}
						}
					}//Fin loop IT1
			}
			//echo "transaction ".$i."<br />";
			$i++;
			};
			//echo "----Cambio Interchange---- <br />";
	}
		//Recorrer Facturas
	$sociedadObtenidaBD="";
	$validacionDeSociedad=0;
	$i=0;
	foreach($Facturas as $rowFac)
	{
		//echo $row[NumeroFactura]."<br />";
		
		require_once("../../conect/conect.php");

		$idSociedad="";
		$conexion = conectar_srvdev();
		$querySociedad="SELECT [id_receiver],[id_sociedad] FROM [cfg_sociedades] where [id_receiver]='".$rowFac[sociedad]."'";
		$result = sqlsrv_query($querySociedad, $conexion);		
		//ObtenerSociedad
		
		
		while ($row = sqlsrv_fetch_array($result)) {
				$sociedadObtenidaBD=$row['id_sociedad'];
		}
		//Validaremos que la factura contenga solo el id de la sociedad enviada en el formultario
		if($sociedadEnviadaParaSubir==$sociedadObtenidaBD)
		{
			//Correcto
			$Facturas[$i][sociedad]=(string)$sociedadObtenidaBD;
		}
		else
		{
			$validacionDeSociedad=1;
		}
		$i++;
		sqlsrv_close($conexion);	
	}
	$NumeroFacuturaParaDetalle="";
	$RespuestasFacturas="";
	if($validacionDeSociedad==0)
	{
		foreach($Facturas as $rowFactura)
		{
			//echo "Sociedad de factura". $rowFactura[sociedad];
			$sqlHeader="Insert into InvoiceHeader (InvoiceNumber,InvoiceVendor,InvoiceDate,InvoiceCurrency,InvoiceNetValue, InvoiceGrossValue, Sociedad) ";
			$sqlHeader.="values('".$rowFactura[NumeroFactura]."','".$rowFactura[proveedor]."','".$rowFactura[FechaFactura]."','USD','".$rowFactura[MontoNeto]."','".$rowFactura[MontoTotal]."','".$rowFactura[sociedad]."');";
			//echo "SQL HEADER: ".$sqlHeader."<br />";
			$conexion = conectar_srvdev();
			$result = sqlsrv_query($sqlHeader, $conexion);
			$afectadas=sqlsrv_rows_affected($conexion);
			$NumeroFacuturaParaDetalle=$rowFactura[NumeroFactura];
			sqlsrv_close($conexion);
			if($afectadas>0)
			{
			$RespuestasFacturas.="<br /><br /> -------------- Factura ".$rowFactura[NumeroFactura]." -------------- ";
			$RespuestasFacturas.="<br /> --- Proveedor: ".$rowFactura[proveedor]." -----";
			$RespuestasFacturas.="<br /> --- Fecha : ".$rowFactura[FechaFactura]." -----";
			$RespuestasFacturas.="<br /> --- Monto Neto: ".$rowFactura[MontoNeto]." -----";
			$RespuestasFacturas.="<br /> --- Monto Total ".$rowFactura[MontoTotal]." -----";
			$RespuestasFacturas.="<br /> --- Sociedad ".$rowFactura[sociedad]." -----";
			foreach($Detalle_Factura as $producto)
			{
				if((string)$producto[NumeroFactura]==(string)$NumeroFacuturaParaDetalle)
				{
				$QueryProducto="Insert into InvoiceDetail (InvoiceNumber, InvoicePosition,ProductID, ProductDesciption, ProductMeasure, ProductQuantity, PorductPrice, PONumber, PaisOrigen,POPosition) ";
				$QueryProducto.="values('".$producto[NumeroFactura]."','".$producto[Correlativo]."','".$producto[CodigoProducto]."','".$producto[descripcion]."',";
				$QueryProducto.="'".$producto[UnidadDeMedida]."','".$producto[Cantidad]."','".$producto[PrecioUnitario]."','".$producto[NumeroDeOC]."','".$producto[PaisOrigen]."','".$producto[PO_position]."');";
				$conexion = conectar_srvdev();
				$result = sqlsrv_query($QueryProducto, $conexion);
				$afectadas=sqlsrv_rows_affected($conexion);
				if($afectadas>0)
				{
					$RespuestasFacturas.="<br /><br /> -- Producto ".$producto[CodigoProducto]." --";
					$RespuestasFacturas.="<br /> -- Correlativo: ".$producto[Correlativo]." --";
					$RespuestasFacturas.="<br /> -- Descripcion: ".$producto[descripcion]." --";
					$RespuestasFacturas.="<br /> -- UnidadDeMedida: ".$producto[UnidadDeMedida]." --";
					$RespuestasFacturas.="<br /> -- Cantidad: ".$producto[Cantidad]." --";
					$RespuestasFacturas.="<br /> -- Num O/C: ".$producto[NumeroDeOC]." --";
					$RespuestasFacturas.="<br /> -- Pais Origen: ".$producto[PaisOrigen]." --";
					$RespuestasFacturas.="<br /> -- PO position: ".$producto[PO_position]." --";
				}
				sqlsrv_close($conexion);
				//echo "Query producto: ".$QueryProducto."<br />";
				}
			}
			$respuesta=$RespuestasFacturas;
			}else{
			$respuesta="(!)- No se pudo ingresar el archivo ingresado con el formato indicado -(!)";
			}
		}
	}
	else
	{
		$respuesta="(!)- La archivo ingresado no corresponde a la sociedad ingresada -(!)";
	}
	 return $respuesta;
	}
	$sociedadObtenida= $_POST["sociedad"];
	$rutaXML= $_POST["rutaXML"];
	$rutaXML='archivos/xml/'.$rutaXML.'.xml';
	//echo $rutaXML;
	$respuestaIngreso=ProcesarXML810($rutaXML, $sociedadObtenida);
	echo $respuestaIngreso;
?>