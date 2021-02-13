<?
error_reporting(1);
    include("../../../conect/conect_componentes.php");
include ("conf_sap.php");
require_once('lib/nusoap.php');

	$data = array();
	parse_str($_REQUEST['form'], $data);
	extract($data);

 $useCURL = isset($_POST["usecurl"]) ? $_POST["usecurl"] : "0";
						//http://despi.kcl.cl:50000/XISOAPAdapter/MessageServlet?channel=*:BC_DETCOMPONENTES:detcomponentes_SOAP_CC&version=3.0&Sender.Service=BC_DETCOMPONENTES&Interface=urn:detallecomponentes.komatsu.cl^detcomponentes_os_SI
 $client = new nusoap_client("http://$host:$port/XISOAPAdapter/MessageServlet?channel=*:BC_DETCOMPONENTES:detcomponentes_SOAP_CC&version=3.0", false);
$err = $client->getError();
if ($err) {
	echo "<h2>Constructor error</h2><pre>" . $err . "</pre>";
	echo "<h2>Debug</h2><pre>" . htmlspecialchars($client->getDebug(), ENT_QUOTES) . "</pre>";
	exit();
}

$client->setCredentials($piuser,$pipass); 
$client->setUseCurl($useCURL);

 
	$XMLstring = '<urn:detcomponentes_REQ_MT xmlns:urn="urn:detallecomponentes.komatsu.cl">';
	$XMLstring =$XMLstring ."<faena></faena>";
	$XMLstring =$XMLstring ."<modelo></modelo>";
	$XMLstring =$XMLstring ."<equipo></equipo>";
	$XMLstring =$XMLstring ."<tipoequipo></tipoequipo>";
	$XMLstring =$XMLstring ."<numeroparte>".$material."</numeroparte>";
	$XMLstring =$XMLstring ."<numeroserie>".$serie."</numeroserie>";
	$XMLstring =$XMLstring ."</urn:detcomponentes_REQ_MT>";

	$soapaction = "http://sap.com/xi/WebService/soap1.1"; 
	
	$mysoapmsg = $client->serializeEnvelope($XMLstring,'',array(),'document', 'literal');
	$result = $client->send($mysoapmsg,$soapaction);
	if ($client->fault) {
	echo "<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>"; print_r($result); echo "</pre>";
} else {
	$err = $client->getError();
	if ($err) {
		echo "<h2>Error</h2><pre>" . $err . "</pre>";
	} else {
		//echo "<h2>Result</h2><pre>"; print_r($result); echo "</pre>";
	}
}

	if ($select == "detalle")
	{
		$DATOS = $result["datos"];

		if	($DATOS[0] == "")
			$DATOS=&$result;
		else
			$DATOS = $result["datos"];
		$id=0;
		$entre = "";
		$retorno = array();
		foreach($DATOS as $key => $valor) 
		{
			$valor["horasacumuladas"] = number_format($valor["horasacumuladas"],0,",",".");
			$valor["horasultimavuelta"] = number_format($valor["horasultimavuelta"],0,",",".");
			$valor["ordenservicio"] = number_format($valor["ordenservicio"],0,"","");
			array_push($retorno,$valor);
		}
		//print_r($retorno);
		echo json_encode($retorno);
	}
	$result = "";
	?>
    
