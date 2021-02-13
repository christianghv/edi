<?php
	require_once("../../plugins/nusoap/nusoap.php");
	require_once("../../conf_sap.php");
	include_once("SegAjax.php");
	//$wdsl="http://$UrlWSDespi:$PuertoWSDespi/XISOAPAdapter/MessageServlet?channel=*:BC_EDI:extraerrutas_SOAP_CC&version=3.0";
	$wdsl="http://$UrlWSDespi:$PuertoWSDespi/XISOAPAdapter/MessageServlet?senderParty=&senderService=BC_EDI&receiverParty=&receiverService=&interface=os_extraerrutas_SI&interfaceNamespace=urn:extraerrutas.kcl.cl";
	//echo $wdsl;die();
	
	$client = new nusoap_client($wdsl,false);
	$client->setCredentials($usuarioWSDespi,$contrasenaWSDespi);
	$client->soap_defencoding = 'UTF-8';
	$client->decode_utf8 = false;
	$err = $client->getError();
	if ($err) {
	 echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
	}
	//XML de envio de datos
	$XMLEnvio.='<urn:extraerrutas_REQ_MT xmlns:urn="urn:extraerrutas.kcl.cl">
				<IRoute></IRoute>
				</urn:extraerrutas_REQ_MT>';
	
	$soapaction = "http://sap.com/xi/WebService/soap1.1";
	
	$mysoapmsg = $client->serializeEnvelope($XMLEnvio,'',array(),'document', 'literal');
	$result = $client->send($mysoapmsg,$soapaction);

	if ($client->fault) {
	// echo '<h2>Fault</h2><pre>';
	// print_r($result);
	// echo '</pre>';
	} else {
	 // Check for errors
	 $err = $client->getError();
	 if ($err) {
	   //Display the error
	 // echo '<h2>Error</h2><pre>' . $err . '</pre>';
	 } else {
	  // Display the result
	 //echo '<h2>Resultado WS:</h2><pre>';	 
	 //print_r($result);
	 //die();
	 $result=json_encode($result);
	 $result=json_decode($result);
	 //print_r($result);
	 //die();
	 $data = array();		
		
	 foreach($result->T_Rutas as $ruta)
	 {
		$item=array("BEZEI"=>$ruta->BEZEI,"ROUTE"=>$ruta->ROUTE);
		array_push($data,$item);			
	 }
	  		
	 //print_r($data);
	 //die();
	 $data=json_encode($data);
	 //print_r($data);
	 echo $data;
	  
	 // echo '</pre>';
	 }
	}
	?>
	