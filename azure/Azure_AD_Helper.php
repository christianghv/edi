<?php

class AzureADHelper {

    private $clientId;

    private $clientSecret;

    private $redirectUri;

    public function setClientId($clientId) {
        $this->clientId = $clientId;
    }

    public function setClientSecret($clientSecret) {
        $this->clientSecret = $clientSecret;
    }

    public function setRedirectUri($redirectUri) {
        $this->redirectUri = $redirectUri;
    }

    public function getAutorizationUrl() {
        $authUrl = "https://login.microsoftonline.com/common/oauth2/authorize?";
        $authUrl .= "scope=offline_access%20User.Read";
        $authUrl .= "&response_type=code";
        $authUrl .= "&approval_prompt=auto";
        $authUrl .= "&redirect_uri=".urlencode($this->redirectUri);
        $authUrl .= "&client_id=".$this->clientId;
        return $authUrl;
    }

    public function getAccessToken($code) {
        error_log($code);
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_URL,"https://login.microsoftonline.com/common/oauth2/token");
        curl_setopt($ch, CURLOPT_POST, true);
        $postFields = "client_id=".$this->clientId;
        $postFields .= "&scope=offline_access%20User.Read";
        $postFields .= "&code=".$code;
        $postFields .= "&redirect_uri=".urlencode($this->redirectUri);
        $postFields .= "&grant_type=authorization_code";
        $postFields .= "&client_secret=".urlencode($this->clientSecret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        if (curl_errno ( $ch )) {
            error_log(curl_error ( $ch ));
            curl_close ( $ch );
            exit ();
        }
        curl_close ($ch);
        $jsonoutput = json_decode($server_output, true);
        return $jsonoutput['access_token'];
    }

    public function getUserFullname($token) {
        $payload = $this->getPayload($token);
        return $payload["ename"];
    }

    public function getGivenName($token) {
        $payload = $this->getPayload($token);
        return $payload["given_name"];
    }

    public function getFamilyName($token) {
        $payload = $this->getPayload($token);
        return $payload["family_name"];
    }

    public function getEmail($token) {
        $payload = $this->getPayload($token);
        //return $payload["email"];
        return isset($payload["email"]) ? $payload["email"] : $payload["upn"];
    }

    private function getPayload($token) {
        $pieces = explode(".", $token);
        $decoded = base64_decode($pieces[1]);
        return json_decode($decoded, true);
    }
}

?>