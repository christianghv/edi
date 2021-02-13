<?php
	error_reporting(0);
    require_once "lib/nusoap.php";
    include("config.php");
        function validaUsuario($uname, $pass, $key1, $key2) {
		

			$Resultado = array();
			
			if (empty($uname) or empty($pass)) {
				return "[ERR1]";
			}
			
			if ($key1 <> KEY1 or $key2 <> KEY2) {
				return "[ERR2]";
			}
			$ldapconn = ldap_connect(SERVERAD);
			if ($ldapconn) {

				// binding to ldap server
				$ldapbind = ldap_bind($ldapconn, $uname."@".DOMINIO, $pass);

				// verify binding
				if ($ldapbind) {
					return "[OK]";
				}	
				else {
					return "[ERR3]";
				}

			}
			else
				return "[ERR4]";
			
		}
     
	 

    $server = new soap_server();
    $server->configureWSDL("usuario", "urn:usuario");
	$server->soap_defencoding = 'UTF-8';
	/*
	$server->wsdl->addComplexType('Result',
		'complexType',
		'array',
		'',
		'SOAP-ENC:Array',
		array('Retorno' => array('name' => 'Retorno', 'type' => 'xsd:string'),
			  'Mensaje' => array('name' => 'Mensaje', 'type' => 'xsd:string')
			  )
    );
*/
		$server->register("validaUsuario",
						array("uname" => "xsd:string",
								"pass" => "xsd:string",
								"key1" => "xsd:string",
								"key2" => "xsd:string"),
						array("retorno" => "xsd:string"),
						"urn:usuario",
						"urn:usuario#validaUsuario",
						"rpc",
						"encoded",
						"Valida usuario en el active directory");
      

    $server->service($HTTP_RAW_POST_DATA);
?>