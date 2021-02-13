<?php
	require_once($_SERVER['DOCUMENT_ROOT']."/plugins/nusoap/nusoap.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/conf_sap.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/EDI/ajax/SegAjax.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/conect/conect.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/EDI/funciones/Funciones_carga.php");
	session_start();
	
	//echo '[{"TYPE":"S","CODE":null,"MESSAGE":"38302529-3069 Se han creado 0001 suministros del n\u00famero 0180170388","LOG_NO":"EE","LOG_MSG_NO":null,"MESSAGE_V1":null,"MESSAGE_V2":null,"MESSAGE_V3":null,"MESSAGE_V4":null,"NumeroFactura":"38302529","Centro":"3069","PONumber":"450219869350102","EE":"0180170388","FECHA":"16-11-2017"},{"TYPE":"S","CODE":null,"MESSAGE":"38302529-3069 Se han creado 0001 suministros del n\u00famero 0180170388","LOG_NO":"EE","LOG_MSG_NO":null,"MESSAGE_V1":null,"MESSAGE_V2":null,"MESSAGE_V3":null,"MESSAGE_V4":null,"NumeroFactura":"38302529","Centro":"3069","PONumber":"450220236050102","EE":"0180170388","FECHA":"16-11-2017"},{"TYPE":"S","CODE":null,"MESSAGE":"38302529-3069 Se han creado 0001 suministros del n\u00famero 0180170388","LOG_NO":"EE","LOG_MSG_NO":null,"MESSAGE_V1":null,"MESSAGE_V2":null,"MESSAGE_V3":null,"MESSAGE_V4":null,"NumeroFactura":"38302529","Centro":"3069","PONumber":"450220312950102","EE":"0180170388","FECHA":"16-11-2017"}]	';
	//die();
	
	ini_set('memory_limit', "2048M");
	ini_set('max_execution_time', 600); //x saniye
	ini_set("display_errors", 1);
	ini_set("max_input_time ", 600);
	set_time_limit(600);
	
	function QuitarDecimales($numeroFloat,$decimales)
	{
		//Porcentaje solo con 2 decimales
		$NumeroString = (string) $numeroFloat;			
		$NumeroStringDividido=explode(".", $NumeroString);
			
		//Decimales
		$SoloDecimales=$NumeroStringDividido[1];
		$DecimalesNumeroString=substr($SoloDecimales,0,2);
				
		$NuevoNumero=$NumeroStringDividido[0].".".$DecimalesNumeroString;
				
		$NuevoNumeroFormateado=floatval($NuevoNumero);
		
		return $NuevoNumeroFormateado;
	}
	
	$dataSession = array();
	
	
	if (!isset($_SESSION["email"]) || $_SESSION["email"]==""){
			$item=array("TYPE"=>"E",
							"CODE"=>"1",
							"MESSAGE"=>"Su sesión ha expirado, por favor inicie sesión nuevamente.",
							"LOG_NO"=>"MSG",
							"LOG_MSG_NO"=>"000000",
							"MESSAGE_V1"=>"",
							"MESSAGE_V2"=>"",
							"MESSAGE_V3"=>"",
							"MESSAGE_V4"=>""
							);
			array_push($dataSession,$item);
		$data=json_encode($dataSession);

		echo $data;
		die();			
	}
	
	//echo "Verificacion : ".$VeriHeader;
	//die();
	//Variables recibidas
	$CantidadBulto=$_POST["CantidadBulto"];
	$CartaPorte=$_POST["CartaPorte"];
	$ETA=$_POST["ETA"];
	$Ruta=$_POST["Ruta"];
	$Sociedad=$_POST["Sociedad"];
		
	$jsonCrearEE=$_POST["JSON_ArrayDeGEE"];
	$CrearEE=json_decode($jsonCrearEE);
	$vueltasEnBD=0;
	$vueltasControlEE=0;
	$afectadas=0;
	//print_r($CrearEE);
	//die();
	$data = array();
	$ArrayEECreadas = array();
	$ArrayFacturasProcesadas = array();
	 
	$numeroActualFactura="";
	 
	$xmlStringCompleto="";
	 
	//Formateando fecha
	$remplazar = array("/");
	$ETA = str_replace($remplazar, "-", $ETA);
	
	$fechaNueva = strtotime($ETA);

	$EtaFormateada = date('Ymd',$fechaNueva);
	//echo $newformat;
	//die();
	$XMLstring = '<urn:creaee_REQ_MT xmlns:urn="urn:creaee.kcl.cl">
							 <IHEADER>
								<DELIV_DATE>'.$EtaFormateada.'</DELIV_DATE>
								<ROUTE>'.$Ruta.'</ROUTE>
								<BILLOFLAD>'.$CartaPorte.'</BILLOFLAD>
								<NOSHPUNITS>'.$CantidadBulto.'</NOSHPUNITS>
								<CAMPO1></CAMPO1>
								<CAMPO2></CAMPO2>
								<CAMPO3></CAMPO3>
								<CAMPO4></CAMPO4>
								<CAMPO5></CAMPO5>
								<USUARIO>'.$_SESSION["email"].'</USUARIO>
							  </IHEADER>';
	 //print_r($CrearEE);
	 //die();
	 
	$Total=0;
	 
	foreach($CrearEE->ArrayDeGEE as $EE)
	{
		$numeroActualFactura=$EE->InvoiceNumber;
		$sqlDato="SELECT DISTINCT 
				detalle.InvoiceNumber,
				detalle.InvoicePosition,
				detalle.PONumber,
				detalle.POPosition,
				detalle.ProductID,
				detalle.ProductDesciption,
				detalle.ProductMeasure,
				detalle.ProductQuantity,
				detalle.PorductPrice,
				detalle.EntregaEntrante,
				detalle.PaisOrigen,
				CONVERT(VARCHAR(10),cabecera.InvoiceDate, 111) AS InvoiceDate,
				cabecera.InvoiceNetValue,
				cabecera.InvoiceGrossValue,
				cabecera.InvoiceGastos 
				FROM [InvoiceDetail] detalle
				INNER JOIN InvoiceHeader cabecera ON
				cabecera.InvoiceNumber=detalle.InvoiceNumber
				INNER JOIN [856DETAIL] detalle856 ON
				detalle856.segm_Id=detalle.InvoiceNumber
				AND detalle856.it_prodid=detalle.ProductID
				AND detalle856.it_po=detalle.PONumber
				AND detalle856.it_packingsleep='".$EE->Bulto."'
				WHERE detalle.InvoiceNumber='".$EE->InvoiceNumber."' AND detalle.PONumber='".$EE->PONumber."';";
		
		$conexion = conectar_srvdev();
		
		$resultadoSQL = sqlsrv_query($sqlDato, $conexion);	
		$BdArray = array();
		while( $row = sqlsrv_fetch_array($resultadoSQL) )
		{
			array_push($BdArray,$row);
		}
		
		sqlsrv_close($conexion);
		//print_r($BdArray);
		//die();
		$envio=0;
		

		foreach($BdArray as $rowBD)
		{
			$Multiplo=1;
			//Formateando fecha
			$remplazar = array("/");
			$FechaDetalle = str_replace($remplazar, "-", $rowBD[InvoiceDate]);
			
			//NetValue
			$HNetvalue=floatval($rowBD[InvoiceNetValue]);
			
			//Gastos
			$HGastos=floatval($rowBD[InvoiceGastos]);
			//Gastos Para envio
			$HGastosEnv=floatval($rowBD[InvoiceGastos]);
			
			if($HGastos<0)
			{
				$HGastos=$HGastos*-1;
				$Multiplo=-1;
			}
			
			//Calcular total precio
			$PQuantity=floatval($rowBD[ProductQuantity]);
			$PPrice=floatval($rowBD[PorductPrice]);
			//Total Product
			$PTotal=floatval($PQuantity*$PPrice);
			
			/** CALCULAR EL PORCENTAJE DEL PRODUCTO */
			$PPorcentaje=floatval($PTotal*100);
			$PPorcentaje=floatval($PPorcentaje/$HNetvalue);
			/** #CALCULAR EL PORCENTAJE DEL PRODUCTO# */
			$PorcentajeFormateado=$PPorcentaje;
			 
			/** CALCULAR EL VALOR ABSOLUTO DEL PORCENTAJE */
			$PorcentajeDiv100=floatval($PorcentajeFormateado/100);
			
			$PValorAbsoluto=floatval($PorcentajeDiv100*$HGastos);
			$PValorAbsoluto=$PValorAbsoluto*$Multiplo;
			$Total=$Total+$PValorAbsoluto;
			$PValorAbsoluto=round($PValorAbsoluto,2);
			
			$XMLstring .='<T_DETAIL>
					<CAMPO3>'.$EE->Centro.'</CAMPO3>
					<MATERIAL>'.$rowBD[ProductID].'</MATERIAL>
					<DELIV_QTY>'.$rowBD[ProductQuantity].'</DELIV_QTY>
					<UNIT>'.$rowBD[ProductMeasure].'</UNIT>
					<PO_NUMBER>'.substr($EE->PONumber, 0, 10).'</PO_NUMBER>
					<PO_ITEM>'.$rowBD[POPosition].'</PO_ITEM>
					<FA_NUMERO>'.$EE->InvoiceNumber.'</FA_NUMERO>
					<FA_FECHA>'.$FechaDetalle.'</FA_FECHA>
					<PAIS_ORIG>'.$rowBD[PaisOrigen].'</PAIS_ORIG>
					<CAMPO1>'.$EE->InvoiceNumber.'</CAMPO1>
					<CAMPO2>'.$EE->PONumber.'</CAMPO2>
					<CAMPO4>'.$HGastosEnv.'</CAMPO4>
					<CAMPO5>'.$EE->PONumber.'</CAMPO5>
					<GASTOS>'.$PValorAbsoluto.'</GASTOS>
					<PRIO_URG>'.substr($EE->PONumber,10,2).'</PRIO_URG>
					<IDBULTO>'.$EE->Bulto.'</IDBULTO>
				 </T_DETAIL>';
			$vueltasEnBD++;
		}//FIN FOREACH BD ARRAY
	}//FIN FOREACH ARRAY GEE
	
	//Ingresando Bultos Bulto
	foreach($CrearEE->ArrayDeGEE as $EE)
	{
		//Unidades de vulto
		
		$ValPeso=0;
		$ValVolumen=0;
		$ValAlto=0;
		$ValLargo=0;
		$ValAncho=0;
		
		
		try {$ValPeso=floatval($EE->Peso);}catch(Exception $e) {}
		try {$ValVolumen=floatval($EE->Volumen);}catch(Exception $e) {}
		try {$ValAlto=floatval($EE->Alto);}catch(Exception $e) {}
		try {$ValLargo=floatval($EE->Largo);}catch(Exception $e) {}
		try {$ValAncho=floatval($EE->Ancho);}catch(Exception $e) {}
		
		$XMLstring .='<T_BULTO>
					   <IDBULTO>'.$EE->Bulto.'</IDBULTO>
					   <PESO>'.$ValPeso.'</PESO>
					   <UMPESO>KG</UMPESO>
					   <VOLUMEN>'.$ValVolumen.'</VOLUMEN>
					   <UMVOL>MT3</UMVOL>
					   <ALTO>'.$ValAlto.'</ALTO>
					   <LARGO>'.$ValLargo.'</LARGO>
					   <ANCHO>'.$ValAncho.'</ANCHO>
					   <UMMED>CM</UMMED>
				 </T_BULTO>';
	}

	
    //Se cierra el XML
		$XMLstring .='</urn:creaee_REQ_MT>';
		
		//echo $XMLstring;
		//die();
		
		$file = fopen("wsCrearEE_RESULTADO.txt","a+");
		fwrite($file,"\n\r ".$XMLstring);
		fclose($file);
		
		$soapaction = "http://sap.com/xi/WebService/soap1.1"; 
		
		$wdsl="http://$UrlWSDespi:$PuertoWSDespi/XISOAPAdapter/MessageServlet?channel=*:BC_EDI:creaee_SOAP_CC&version=3.0";
		$wdsl="http://$UrlWSDespi:$PuertoWSDespi/XISOAPAdapter/MessageServlet?senderParty=&senderService=BC_EDI&receiverParty=&receiverService=&interface=os_creaee_SI&interfaceNamespace=urn:creaee.kcl.cl";
	
		$client = new nusoap_client($wdsl,false);
		$client->setCredentials($usuarioWSDespi,$contrasenaWSDespi);
		$client->soap_defencoding = 'UTF-8';
		$client->decode_utf8 = false;
		$err = $client->getError();
		if ($err) {
		 echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
		}
		
		$mysoapmsg = $client->serializeEnvelope($XMLstring,'',array(),'document', 'literal');
		$resultWSResp = $client->send($mysoapmsg,$soapaction);

		//echo $XMLstring;
		//$envio++;
		//print_r($result);
		//die();
		
		if ($client->fault) {
		 echo '<h2>Fault</h2><pre>';
		 print_r($result);
		 echo '</pre>';
		}
		else
		{
		 // Check for errors
		 $err = $client->getError();
		 if ($err) {
		  // Display the error
		  echo '<h2>Error</h2><pre>' . $err . '</pre>';
		 } 
		else
		{
		  // Display the result
		  //echo '<h2>Result</h2><pre>';
		  
			$resultWS=json_encode($resultWSResp);
			
			$file = fopen("wsCrearEE_RESULTADO.txt","a+");
			fwrite($file,"\n\r ".$resultWS);
			fclose($file);

			//$ResultadoManualWS='{"T_RETORNO":{"VBELN":"0180195709","CENTRO":"3149","CAMPO1":"S","CAMPO2":"Se han creado 0001 suministros del n\u00famero 0180195709","CAMPO3":"E09-809070","CAMPO4":"00030","CAMPO5":"450223290620101"},"E_VBELN":""}';
			//$result=json_decode($ResultadoManualWS);

			$result=$resultWSResp;	
			
			//echo $XMLstring;
			$resultadoReturn  = count($result['T_RETURN']);
			$resultadoRetorno = count($result['T_RETORNO']);
			//echo 'Valor return: '.$resultadoReturn.'<br />';
			//echo 'Valor retorno: '.$resultadoRetorno.'<br />';
			//echo 'vueltas en BD: '.$vueltasEnBD.'<br />';
			if($resultadoReturn<1&&$resultadoRetorno<1)
			{
				$ResultadoError=print_r($result, TRUE);
				$MensajeErrorWS='<strong>WS: </strong><br />';			
				$MensajeErrorWS.='<br /><strong>Text: </strong><br /><textarea style="width:100%;min-height: 120px" >'.$ResultadoError.'</textarea>';
				//echo $afectadas;
				$item=array("TYPE"=>"E",
							"CODE"=>"",
							"MESSAGE"=>$MensajeErrorWS,
							"LOG_NO"=>"MSG",
							"LOG_MSG_NO"=>"",
							"MESSAGE_V1"=>"",
							"MESSAGE_V2"=>"",
							"MESSAGE_V3"=>"",
							"MESSAGE_V4"=>""
							);
				array_push($data,$item);
			}
			//die();$result->T_RETURN
			//T_RETURN

			//Viene un error
			if($resultadoReturn>0)
			{
				
				if(isset($result['T_RETURN'][0]))
				{
					$resultReturn=$result['T_RETURN'];
				}
				else
				{
					$resultReturn=$result;
				}
				
				foreach($resultReturn as $Resp)
				{
					if((string)$Resp[TYPE]!="")
					{
						$item=array("TYPE"=>$Resp[TYPE],
									"CODE"=>$Resp[CODE],
									"MESSAGE"=>$Resp[MESSAGE],
									"LOG_NO"=>"MSG",
									"LOG_MSG_NO"=>$Resp[LOG_MSG_NO],
									"MESSAGE_V1"=>$Resp[MESSAGE_V1],
									"MESSAGE_V2"=>$Resp[MESSAGE_V2],
									"MESSAGE_V3"=>$Resp[MESSAGE_V3],
									"MESSAGE_V4"=>$Resp[MESSAGE_V4]
									);
						array_push($data,$item);
					}
				}
			}
				
			//No viene error				
			if($resultadoRetorno>0)
			{
				if(isset($result['T_RETORNO'][0]))
				{
					$result=$result['T_RETORNO'];
				}
					
				foreach($result as $Resp)
				{
					if((string)$Resp[CAMPO1]=="S")
					{
						$InvoiceNumberRecibido =(string)$Resp[CAMPO3];						
						$PONumberRecibido =(string)$Resp[CAMPO5];
						$CentroRecibido=(string)$Resp[CENTRO];
						$POPositionRecibido =(string)$Resp[CAMPO4];
						$EERecibido=(string)$Resp[VBELN];
						
						foreach($CrearEE->ArrayDeGEE as $CrEE)
						{
							$InvoiceNumberEnviado=(string)$CrEE->InvoiceNumber;													
							$PONumberEnviado=(string)$CrEE->PONumber;
							$CentroEnviado=(string)$CrEE->Centro;
							
							if($InvoiceNumberRecibido==$InvoiceNumberEnviado && 
							   $PONumberRecibido==$PONumberEnviado)
							{
								$numeroActualFactura=$CrEE->InvoiceNumber;
										
								$sqlUpdateDetail="UPDATE [InvoiceDetail] SET [EntregaEntrante]='".intval($Resp[VBELN])."' 
										WHERE [InvoiceNumber]='".$numeroActualFactura."'
										AND ([EntregaEntrante]='0'
										OR [EntregaEntrante] is null
										OR rtrim(ltrim([EntregaEntrante])) = '')
										AND PONumber='".$PONumberEnviado."'
										AND CAST([POPosition] AS INT)= CAST('".$POPositionRecibido."' AS INT);";
								
								ActualizarBD($sqlUpdateDetail);
								/**
								$conexion = conectar_srvdev();
								$result = sqlsrv_query($sqlUpdateDetail, $conexion);
								$afectadas=sqlsrv_rows_affected($conexion);
								sqlsrv_close($conexion);
								*/
								//echo $afectadas;		
								
								//Verificar si no existe lo agregara al arreglo
								if(!isset($ArrayFacturasProcesadas[$numeroActualFactura]))
								{
									$ArrayFacturasProcesadas[$numeroActualFactura]=$numeroActualFactura;
								}
									
								$MensajeCorrecto=$numeroActualFactura."-";
								$MensajeCorrecto.=$Resp[CENTRO];
								$MensajeCorrecto.=" ";
								$MensajeCorrecto.=$Resp[CAMPO2];
								//echo $afectadas;
								
								$EncontradaEE=false;
								
								//Saber si existe 
								$SecuenciaEEBuscada=$InvoiceNumberRecibido."_".$PONumberRecibido."_".$CentroRecibido."_".$EERecibido;
								if(!isset($ArrayEECreadas[$SecuenciaEEBuscada]))
								{
									//Se agrega
									$ArrayEECreadas[$SecuenciaEEBuscada]=$SecuenciaEEBuscada;
								}
								else
								{
									//Encontrada
									$EncontradaEE=true;
								}
								
								//echo $SecuenciaEECreada." VS ".$SecuenciaEEBuscada;
								
								if(!$EncontradaEE)
								{	
									//Ingresando datos de respuesta del ws
									$item=array("TYPE"=>"S",
													"CODE"=>$Resp[CODE],
													"MESSAGE"=>$MensajeCorrecto,
													"LOG_NO"=>"EE",
													"LOG_MSG_NO"=>$Resp[LOG_MSG_NO],
													"MESSAGE_V1"=>$Resp[MESSAGE_V1],
													"MESSAGE_V2"=>$Resp[MESSAGE_V2],
													"MESSAGE_V3"=>$Resp[MESSAGE_V3],
													"MESSAGE_V4"=>$Resp[MESSAGE_V4],
													"NumeroFactura"=>$numeroActualFactura,
													"Centro"=>$Resp[CENTRO],
													"PONumber"=>$PONumberEnviado,
													"EE"=>$Resp[VBELN],
													"FECHA"=>date("d-m-Y")
													);
									array_push($data,$item);
								}
							}//Fin EE Centro encontrado
						}//Fin foreach CrearEE
					}//Fin campo 'S'
				}//Fin foreach
			}//Fin resultado retorno
				
		}//Fin else correcto
			
		}
		//echo '</pre>';
		//print_r($ArrayEECreadas);
		//echo "<br /> FIN FOREACH";
		
		//Actualizando las facturas que fueron OK
		foreach($ArrayFacturasProcesadas as $Factura)
		{
			//Estado de la factura
			
			$sqlUpdateHeader="EXEC [SP_EE_FACTURA] '".$numeroActualFactura."'";
			
			ActualizarBD($sqlUpdateHeader);
			
		}
		CerrarConeccionUnica();
		$data=json_encode($data);

		echo $data;
	?>
	