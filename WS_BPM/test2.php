<?
			$url = "http://cmwqbdsybase:50000/webdynpro/resources/demo.sap.com/estatustarea/AppEstTask?SAPtestId=7&j_username=U1001008&j_password=inicio01&idTarea=6e09c7bef7bf11e783ce000000355426#";
$post = null;
$host = parse_url($url, PHP_URL_HOST);
$headerArr = array();
$headerArr[] = 'Content-Type:application/xml';
$headerArr[] = 'cache-control: no-cache, no-store';
$headerArr[] = 'content-encoding: gzip';
$headerArr[] = 'content-type: text/html;charset=UTF-8';
$headerArr[] = 'Host:cmwqbdsybase:50000' ;
$headerArr[] = 'X-CSRF-Token:Fetch';
$headerArr[] = 'X-Requested-With:XMLHttpRequest';

$ch = curl_init();

curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "U1001008:inicio01");    
curl_setopt($ch, CURLOPT_SSLVERSION,3);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);

echo $result = curl_exec($ch);
if(curl_errno($ch)){
    echo 'Curl error: ' . curl_error($ch);
}
?>