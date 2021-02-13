<?php	
	require_once($_SERVER['DOCUMENT_ROOT']."/plugins/nusoap/nusoap.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/conf_sap.php");
		error_reporting(0);
	class WS_BuscarOC_855
	{
		// Declaración de una propiedad
		private $wdsl;
		private $client;
		private $err;

		function __construct($UrlWSDespi,$PuertoWSDespi,$usuarioWSDespi,$contrasenaWSDespi) {
			$this->wdsl='http://'.$UrlWSDespi.':'.$PuertoWSDespi.'/XISOAPAdapter/MessageServlet?senderParty=&senderService=BC_EDI&receiverParty=&receiverService=&interface=os_detalleordencompra_SI&interfaceNamespace=urn:detalleordencompra.kcl.cl';
		  	$this->client = new nusoap_client($this->wdsl,false);
			$this->client->setCredentials($usuarioWSDespi,$contrasenaWSDespi);
			$this->client->soap_defencoding = 'UTF-8';
			$this->client->decode_utf8 = false;
		}
		
		public function WS_detalleordencompra($NumeroOC)
		{
			$data = array();
			
			//XML de envio de datos
			$XMLEnvio='<urn:detalleordencompra_REQ_MT xmlns:urn="urn:detalleordencompra.kcl.cl">
						<NOC>'.$NumeroOC.'</NOC>
						<CONFIRMACIONES>X</CONFIRMACIONES>
						</urn:detalleordencompra_REQ_MT>';
			
			$soapaction = "http://sap.com/xi/WebService/soap1.1";
			
			$mysoapmsg = $this->client->serializeEnvelope($XMLEnvio,'',array(),'document', 'literal');
			$result = $this->client->send($mysoapmsg,$soapaction);
			
			if ($this->client->fault) {
			 //echo '<h2>Fault</h2><pre>';
			 //print_r($result);
			 //echo '</pre>';
			} else {
			 // Check for errors
			$this->err = $this->client->getError();
			 if ($this->err) {
			  // Display the error
			  //echo '<h2>Error despues de llamar</h2><pre>' . $this->err . '</pre>';
			  return $data;
			  
			 } else {
			  // Display the result
			  //print_r($result);
				$resultado  = count($result);
				$resultadoListado=0;
				
				if (isset($result['listado'])) 
				{
					$resultadoListado = count($result['listado'][0]);
				}
				
				
				if($resultado==1 && $resultadoListado==0)
				{
					foreach($result as $detalleOrden)
					{
						$item=array("CAMP1"=>$detalleOrden['CAMP1'],"CAMP2"=>$detalleOrden['CAMP2'],"CAMP3"=>$detalleOrden['CAMP3'],"CAMP4"=>$detalleOrden['CAMP4'],
						"CAMP5"=>$detalleOrden['CAMP5'],"EBELP"=>$detalleOrden['EBELP'],"MATNR"=>$detalleOrden['MATNR'],"MEINS"=>$detalleOrden['MEINS'],
						"MENGE"=>intval($detalleOrden['MENGE']),"NETPR"=>floatval($detalleOrden['NETPR']),"TXZ01"=>$detalleOrden['TXZ01'],"WAERS"=>$detalleOrden['WAERS']);
						array_push($data,$item);
					}
				}
				else
				{
					foreach($result['listado'] as $detalleOrden)
					{
						$item=array("CAMP1"=>$detalleOrden['CAMP1'],"CAMP2"=>$detalleOrden['CAMP2'],"CAMP3"=>$detalleOrden['CAMP3'],"CAMP4"=>$detalleOrden['CAMP4'],
						"CAMP5"=>$detalleOrden['CAMP5'],"EBELP"=>$detalleOrden['EBELP'],"MATNR"=>$detalleOrden['MATNR'],"MEINS"=>$detalleOrden['MEINS'],
						"MENGE"=>intval($detalleOrden['MENGE']),"NETPR"=>floatval($detalleOrden['NETPR']),"TXZ01"=>$detalleOrden['TXZ01'],"WAERS"=>$detalleOrden['WAERS']);
						array_push($data,$item);
					}
				}
			 }
			}
			return $data;
		}
	}
	
	
	//Variables recibidas
	if($_POST["NumeroOC"]!="")
	{
		$NumeroOC=$_POST["NumeroOC"];
		require_once($_SERVER['DOCUMENT_ROOT']."/EDI/ajax/SegAjax.php");
		global $UrlWSDespi,$PuertoWSDespi,$usuarioWSDespi,$contrasenaWSDespi;
		$WS_BuscarOC_855 = new WS_BuscarOC_855($UrlWSDespi,$PuertoWSDespi,$usuarioWSDespi,$contrasenaWSDespi);
		$data=$WS_BuscarOC_855->WS_detalleordencompra($NumeroOC);
		echo json_encode($data);
	}
	?>
	