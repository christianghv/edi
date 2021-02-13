<?php

	function WS_BuscarCentros($Array_PoNumber)
	{	
		require_once($_SERVER['DOCUMENT_ROOT'].'/plugins/nusoap/nusoap.php');
		require_once($_SERVER['DOCUMENT_ROOT'].'/conf_sap.php');
		include_once("SegAjax.php");
		//$wdsl="http://$UrlWSDespi:$PuertoWSDespi/XISOAPAdapter/MessageServlet?channel=*:BC_EDI:extraecentro_SOAP_CC&version=3.0";
		$wdsl="http://$UrlWSDespi:$PuertoWSDespi/XISOAPAdapter/MessageServlet?senderParty=&senderService=BC_EDI&receiverParty=&receiverService=&interface=os_extraecentro_SI&interfaceNamespace=urn:extraecentro.kcl.cl";
		//echo $wdsl;die();
		
		$client = new nusoap_client($wdsl,false);
		$client->setCredentials($usuarioWSDespi,$contrasenaWSDespi);
		$client->soap_defencoding = 'UTF-8';
		$client->decode_utf8 = false;
		$err = $client->getError();
		if ($err) {
			return 'Constructor error '.$err;
		}

		//XML de envio de datos
		$XMLEnvio='<urn:extraecentro_REQ_MT xmlns:urn="urn:extraecentro.kcl.cl">';
		$vueltas=0;
		
		foreach($Array_PoNumber as $PoNumber)
		{
			$XMLEnvio.='<T_OC>
						<POGNUMBER>'.$PoNumber.'</POGNUMBER>
						<WERKS></WERKS>
						<INCOTERMS></INCOTERMS>
						</T_OC>';
			$vueltas++;
		}
		
		//Cierre de XML de envio
		$XMLEnvio.='</urn:extraecentro_REQ_MT>';

		$soapaction = "http://sap.com/xi/WebService/soap1.1";
		
		
		$mysoapmsg = $client->serializeEnvelope($XMLEnvio,'',array(),'document', 'literal');
		$result = $client->send($mysoapmsg,$soapaction);
		
		if ($client->fault) {
			return 'Fault '.json_encode($result);
		} else {
		 // Check for errors
		 $err = $client->getError();
		 if ($err) {
		  return 'Error '.$err;
		 } else {	
			/**
			//si las vueltas es 1 significa que solo hay un registro
			if($vueltas==1)
			{
				foreach($result as $centro)
				{
					$data[$centro->POGNUMBER]= array(
												"centro" => $centro->WERKS,
												"incoterms" => $centro->INCOTERMS);
				}
			}
			else
			{
				foreach($result->T_OC as $centro)
				{
					$data[$centro->POGNUMBER]= array(
												"centro" => $centro->WERKS,
												"incoterms" => $centro->INCOTERMS);
				}
			}
			*/
			$Resultado=array();
			
			if($vueltas==1)
			{
				$Resultado=$result;
			}
			else
			{
				$Resultado=$result['T_OC'];
			}
			
			return $Resultado;
		 }
		 
		}
	}
	
?>
	