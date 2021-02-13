<?php
	require_once("../../plugins/nusoap/nusoap.php");
	require_once("../../conf_sap.php");
	include_once("SegAjax.php");
	//$wdsl="http://$UrlWSDespi:$PuertoWSDespi/XISOAPAdapter/MessageServlet?channel=*:BC_EDI:extraerpaises_SOAP_CC&version=3.0";
	$wdsl="http://$UrlWSDespi:$PuertoWSDespi/XISOAPAdapter/MessageServlet?senderParty=&senderService=BC_EDI&receiverParty=&receiverService=&interface=os_extraerpaises_SI&interfaceNamespace=urn:extraerpaises.kcl.cl";
	
	$client = new nusoap_client($wdsl,false);
	$client->setCredentials($usuarioWSDespi,$contrasenaWSDespi);
	$client->soap_defencoding = 'UTF-8';
	$client->decode_utf8 = false;
	$err = $client->getError();
	if ($err) {
	 echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
	}
	//XML de envio de datos
	$XMLEnvio.='<urn:extraerpaises_REQ_MT xmlns:urn="urn:extraerpaises.kcl.cl">
				<Pais></Pais>
				</urn:extraerpaises_REQ_MT>';
	
	$soapaction = "http://sap.com/xi/WebService/soap1.1";
	
	$mysoapmsg = $client->serializeEnvelope($XMLEnvio,'',array(),'document', 'literal');
	$result = $client->send($mysoapmsg,$soapaction);
	//print_r($result);
	//die();
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
	 //echo '<h2>Result</h2><pre>';
	 
	function orderMultiDimensionalArray ($arreglo) {
		foreach ($arreglo as $key => $row) {
			$aux[$key] = $row['Landx50'];
		}
		array_multisort($aux, SORT_ASC, $arreglo);
		return $arreglo;
    }  
	 
	 //print_r($result);
	 //die();
	 $result=json_encode($result);
	 $result=json_decode($result);
	 //print_r($result);
	 $data = array();		

	foreach($result->T_Paises as $pais)
	{
		$paises[] = array('Land1' => $pais->Land1,'Landx50' => $pais->Landx50);
		/**
		$file = fopen("paisescsv.txt",'a+');
		fwrite($file,$pais->Land1.";".$pais->Landx50.PHP_EOL);
		fclose($file);
		*/
	}
		
		$PaisesOrdenados=orderMultiDimensionalArray($paises);
	  		
		$result=json_encode($PaisesOrdenados);
		echo $result;
	 }
	}
	?>
	