<?
			$url = "http://cmwqbdsybase:50000/webdynpro/resources/demo.sap.com/estatustarea/AppEstTask?SAPtestId=7&j_username=U1001008&j_password=inicio01&idTarea=6e09c7bef7bf11e783ce000000355426#";

$ch = curl_init($url);
$fp = fopen("localfile.html", "w");
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_exec($ch);
curl_close($ch);
fclose($fp);
?>
?>