<?php
	error_reporting(0);
    //require_once "lib/nusoap.php";
	
	
		
    include("config.php");
        function datosUsuario($rut, $key1, $key2) {
			
			if ($key1 <> KEY1 or $key2 <> KEY2) {
				return "[ERR1]";
			}
			$findme   = '-';
			$pos = strpos($rut, $findme);
			if ($pos === false) {
				$rut = substr($rut,0,strlen($rut)-1)."-".substr($rut,strlen($rut)-1,strlen($rut));
			} 
			
			$url = "http://".SERVERDU."/contactos/kcc/_search?pretty=true&q=rut:%22".$rut."%22";
			echo $url = str_replace("%22",'"',$url);
			$result =  file_get_contents($url);

			$array_result = json_decode($result);
			
			$data = $array_result->hits->hits;
			$arr_data = $data[0];
			print_r($arr_data);
			/*$Resultado = array();
							$tmpArray = array('nombre' => $arr_data->{'_source'}->nombre,
								  'email' => $arr_data->{'_source'}->email,
								  'userid' => $arr_data->{'_source'}->userid);
				array_push($Resultado, $tmpArray);*/
			if ($arr_data->{'_source'}->userid == "")
			{
				return "[ERR2]";
			} else
			{
				return $arr_data->{'_source'}->nombre."|".$arr_data->{'_source'}->email."|".$arr_data->{'_source'}->userid;
			}
		}
     
	 

/*    $server = new soap_server();
    $server->configureWSDL("usuario", "urn:usuario");
	$server->soap_defencoding = 'UTF-8';

		$server->register("datosUsuario",
						array("uname" => "xsd:string",
								"key1" => "xsd:string",
								"key2" => "xsd:string"),
						array("retorno" => "xsd:string"),
						"urn:usuario",
						"urn:usuario#datosUsuario",
						"rpc",
						"encoded",
						"Extrae datos de un usuario");
      

    $server->service($HTTP_RAW_POST_DATA);*/

	$rut = "13093470-6";
	$key1 = "a85pYlpoqsrrQ==";
	$key2 = "bds548wapqXgRos8725z";
	echo datosUsuario($rut, $key1, $key2);
?>