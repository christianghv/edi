<?php
	require_once($_SERVER['DOCUMENT_ROOT']."/plugins/nusoap/nusoap.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/conf_sap.php");
	//error_reporting(0);
	
	class WS_CabeceraOC
	{
		// Declaración de una propiedad
		private $wdsl;
		private $client;
		private $err;
		
		function __construct($UrlWSDespi,$PuertoWSDespi,$usuarioWSDespi,$contrasenaWSDespi) {
			$this->wdsl='http://'.$UrlWSDespi.':'.$PuertoWSDespi.'/XISOAPAdapter/MessageServlet?senderParty=&senderService=BC_EDI&receiverParty=&receiverService=&interface=os_cabeceraoc_SI&interfaceNamespace=urn:cabeceraoc.kcl.cl';
		  	$this->client = new nusoap_client($this->wdsl,false);
			$this->client->setCredentials($usuarioWSDespi,$contrasenaWSDespi);
			$this->client->soap_defencoding = 'UTF-8';
			$this->client->decode_utf8 = false;
		}
		
		public function WS_cabeceraordencompra($sociedad,$proveedor,$FInicioFormateada,$FTerminoFormateada,$NroOC)
		{
			$data = array();
			
			//XML de envio de datos
			$XMLEnvio='<urn:cabeceraoc_REQ_MT xmlns:urn="urn:cabeceraoc.kcl.cl">
								 <Sociedad>'.$sociedad.'</Sociedad>
								 <Proveedor>'.$proveedor.'</Proveedor>
								 <FeInicio>'.$FInicioFormateada.'</FeInicio>
								 <FeTermino>'.$FTerminoFormateada.'</FeTermino>
								 <NroOC>'.trim($NroOC).'</NroOC>
								 <Campo1></Campo1>
								 <Campo2></Campo2>
								 <Campo3></Campo3>
								 <Campo4></Campo4>
								 <Campo5></Campo5>
							  </urn:cabeceraoc_REQ_MT>';
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
				//die();
				 
				 $data = array();
				 
				 $MensajeServidor="";
					 
				 try{
					 $MensajeServidor=$result['faultstring'];
					 $Context=$result[detail][SystemError][context];
					 $Code=$result[detail][SystemError][code];
					 $Texto=$result[detail][SystemError][text];
					 
				} catch (Exception $e) 
				{
					//NAda
				}
				 
				 if($MensajeServidor=="Server Error")
				 {
					$item=array(	"Resultado"=>"E",
									"Context"=>$Context,
									"Code"=>$Code,
									"Texto"=>$Texto
									);
					array_push($data,$item);
				}
				else
				{
					$resultado  = count($result);
					$resultadoSalida = count($result['salida'][0]);
					
					if($resultado==1 && $resultadoSalida==0)
					{
						foreach($result as $cabecera)
						{
							$fechaNueva = strtotime($cabecera['Fecha']);
							$FechaOC = date('d-m-Y',$fechaNueva);
							
							$item=array(
										"Resultado"=>"S",
										"NroOC"=>$cabecera['NroOC'],
										"Fecha"=>$FechaOC,
										"Centro"=>$cabecera['Centro'],
										"DescCentro"=>$cabecera['DescCentro'],
										"Monto"=>$cabecera['Monto']
										);
							array_push($data,$item);			
						}
					}
					else
					{
						foreach($result['salida'] as $cabecera)
						{
							$fechaNueva = strtotime($cabecera['Fecha']);
							$FechaOC = date('d-m-Y',$fechaNueva);
							
							$item=array(
										"Resultado"=>"S",
										"NroOC"=>$cabecera['NroOC'],
										"Fecha"=>$FechaOC,
										"Centro"=>$cabecera['Centro'],
										"DescCentro"=>$cabecera['DescCentro'],
										"Monto"=>$cabecera['Monto']
										);
							array_push($data,$item);		
						}
					}
				 }
			 }
			
			return $data;
		}
		}
	}
		
		//Variables recibidas
		if($_POST["sociedad"] !="" && $_POST["proveedor"] !="")
		{
			$sociedad = $_POST["sociedad"];
			$proveedor = $_POST["proveedor"];
			$fecha_inicio = $_POST["fecha_inicio"];
			$fecha_termino = $_POST["fecha_termino"];
			$NroOC = $_POST["NroOC"];
			
			//Formateando fecha inicio
			$remplazar = array("/");
			$FInicio = str_replace($remplazar, "-", $fecha_inicio);	
			$fechaNueva = strtotime($FInicio);
			$FInicioFormateada = date('Ymd',$fechaNueva);
			
			//Formateando fecha termino
			$remplazar = array("/");
			$FTermino = str_replace($remplazar, "-", $fecha_termino);	
			$fechaNueva = strtotime($FTermino);
			$FTerminoFormateada = date('Ymd',$fechaNueva);
			
			require_once($_SERVER['DOCUMENT_ROOT']."/EDI/ajax/SegAjax.php");
			global $UrlWSDespi,$PuertoWSDespi,$usuarioWSDespi,$contrasenaWSDespi;
			
			$WS_CabeceraOC = new WS_CabeceraOC($UrlWSDespi,$PuertoWSDespi,$usuarioWSDespi,$contrasenaWSDespi);
			$data=$WS_CabeceraOC->WS_cabeceraordencompra($sociedad,$proveedor,$FInicioFormateada,$FTerminoFormateada,$NroOC);
			echo json_encode($data);
		}
		
		
		/**
		//$wdsl="http://$UrlWSDespi:$PuertoWSDespi/XISOAPAdapter/MessageServlet?channel=*:BC_EDI:cabeceraoc_SOAP_CC&version=3.0&Sender.Service=BC_EDI&Interface=urn:cabeceraoc.kcl.cl^os_cabeceraoc_SI";
		$wdsl="http://$UrlWSDespi:$PuertoWSDespi/XISOAPAdapter/MessageServlet?senderParty=&senderService=BC_EDI&receiverParty=&receiverService=&interface=os_cabeceraoc_SI&interfaceNamespace=urn:cabeceraoc.kcl.cl";
		//echo $wdsl;
		
		$client = new nusoap_client($wdsl,false);
		$client->setCredentials($usuarioWSDespi,$contrasenaWSDespi);
		$client->soap_defencoding = 'UTF-8';
		$client->decode_utf8 = false;
		$err = $client->getError();
		if ($err) {
		 echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
		}
		
		$sociedad = $_POST["sociedad"];
		$proveedor = $_POST["proveedor"];
		$fecha_inicio = $_POST["fecha_inicio"];
		$fecha_termino = $_POST["fecha_termino"];
		$NroOC = $_POST["NroOC"];
		
		//Formateando fecha inicio
		$remplazar = array("/");
		$FInicio = str_replace($remplazar, "-", $fecha_inicio);	
		$fechaNueva = strtotime($FInicio);
		$FInicioFormateada = date('Ymd',$fechaNueva);
		
		//Formateando fecha termino
		$remplazar = array("/");
		$FTermino = str_replace($remplazar, "-", $fecha_termino);	
		$fechaNueva = strtotime($FTermino);
		$FTerminoFormateada = date('Ymd',$fechaNueva);
					  
	
		//XML de envio de datos
	$XMLEnvio='<urn:cabeceraoc_REQ_MT xmlns:urn="urn:cabeceraoc.kcl.cl">
						 <Sociedad>'.$sociedad.'</Sociedad>
						 <Proveedor>'.$proveedor.'</Proveedor>
						 <FeInicio>'.$FInicioFormateada.'</FeInicio>
						 <FeTermino>'.$FTerminoFormateada.'</FeTermino>
						 <NroOC>'.trim($NroOC).'</NroOC>
						 <Campo1></Campo1>
						 <Campo2></Campo2>
						 <Campo3></Campo3>
						 <Campo4></Campo4>
						 <Campo5></Campo5>
					  </urn:cabeceraoc_REQ_MT>';
	
	$soapaction = "http://sap.com/xi/WebService/soap1.1";
	
	//echo $XMLEnvio;
	//die();
	
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

	 $result=json_encode($result);
	 $result=json_decode($result);
	 //echo "El resultado es: ";
	 //print_r($result);
	 //die();
	 
	 $data = array();
	 
	 $MensajeServidor="";
	 	 
	 try{
		 $MensajeServidor=$result->faultstring;
    	 $Context=$result->detail->SystemError->context;
		 $Code=$result->detail->SystemError->code;
		 $Texto=$result->detail->SystemError->text;
		 
	} catch (Exception $e) 
	{
		//NAda
	}
	 
	 if($MensajeServidor=="Server Error")
	 {
		$item=array(	"Resultado"=>"E",
						"Context"=>$Context,
						"Code"=>$Code,
						"Texto"=>$Texto
						);
		array_push($data,$item);
	 }
	 else{ 
	 
		 $ResultadoSimple=count($result);
		 $ResultadoComplejo=count($result->salida);
		 

		//echo "S: ".$ResultadoSimple."<br /> C:".$ResultadoComplejo;
		//die();
		
		if($ResultadoComplejo>1)
		{
			 foreach($result->salida as $cabecera)
			 {
				$fechaNueva = strtotime($cabecera->Fecha);
				$FechaOC = date('d-m-Y',$fechaNueva);
				
				$item=array(
							"Resultado"=>"S",
							"NroOC"=>$cabecera->NroOC,
							"Fecha"=>$FechaOC,
							"Centro"=>$cabecera->Centro,
							"DescCentro"=>$cabecera->DescCentro,
							"Monto"=>$cabecera->Monto
							);
				array_push($data,$item);			
			 }
		 }
		 else
		 {
			foreach($result as $cabecera)
			{
				$fechaNueva = strtotime($cabecera->Fecha);
				$FechaOC = date('d-m-Y',$fechaNueva);
				
				$item=array(
							"Resultado"=>"S",
							"NroOC"=>$cabecera->NroOC,
							"Fecha"=>$FechaOC,
							"Centro"=>$cabecera->Centro,
							"DescCentro"=>$cabecera->DescCentro,
							"Monto"=>$cabecera->Monto
							);
				array_push($data,$item);			
			}
		}
	 }
	 //print_r($data);
	 //die();
	 $data=json_encode($data);
	 //print_r($data);
	 echo $data;
	  
	 // echo '</pre>';
	 }
	}
	*/
	?>
	