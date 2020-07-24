<?php

namespace App\Api;

class DataEngine {

    private $url = "https://backoffice.dataengine.com.br/v2";
    private $key = "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzZXJ2aWNlX2F1dGhfcmVxdWVzdF92YWxpZGF0aW9uIiwiaWF0IjoxNTQ0ODA3NDA0fQ.bGVo6g4rnCZwvSU9kzzmSgT4ehoWZvtbBlAW1Z9112gB7o0defbBzWIGZsH8R9FeqU5mazG3xq66BQwQKK_pMiXH_CFY8LBHVhKPTpp-FRnleTTIIxG9z7ZNkOLRst9iuOOg0IHqywFE8XjbPFRVLMa81oNq6N1OWb28F9jwkmpzVxKS1mil34UIlWY6ksnpfOG6sRRzg4gadW3FqAUgZzPVOsVkovXm8c61CCqm8XKp-GM_0JLHck2o1W_60W_V_-E3h8WIDKrm4NVhTF8MbKbHV_I3Xf7QobC3iQeklAgR-iPFIm6ogJAHH7F2hqtNfg8Z-ZVyS-KaTSY5q5M6czc5o0pfe6L12qnzeJ9tigZaOYcTjEZg0fXR2gB1UORpm4i7W0DkNnTfFbBMl28ZRDa_aHhbd6nYG_33SV7RJSn0y1FAyTIhlJB_qcZXVJf9Eno7JWEKEchcXkULZpvNMbEwQcbiRh99tSSKK_jD2KVt2Lssxs1ro-dO3ttrdlwRRzmS-Kp6TuA3PsOt2U-gJy_g1vlhw9vHXqTP8Sd8KOpFnIuSnwtBOBJbQsv0NykRlpTYgHvkC6c5LD7EOUrlosytWT9xwzK2cVDVg12LqHYVHbdVejk0CQD0Q20hgOvbgNMnovUFzZRiaAHdvuEqQC5mgVDkFME7E83NH0UKdcc";
    private $email = "gustavo@mg2imob.com.br";
    private $pass = "Gu#160388";
    private $token;
    private $refresh;
    private $flow;
    private $call;

    public function converte_base64(){
        return base64_encode($this->email.":".$this->pass);
    }

    public function auth() {
        // $headers = [
        //     'content-type' => 'application/json',
        //     'x-api-key' => $this->key,
        //     'authorization' => 'Basic '.$this->converte_base64()
        // ];

        $headers = [
            'content-type: application/json',
            'x-api-key: '.$this->key,
            'Authorization: Basic '.$this->converte_base64()
        ];

        return $this->curl($this->url.'/user/auth', 'get', $headers);

        //return \Unirest\Request::get($this->url.'/user/auth', $headers);
    }

    public function check($token) {
        $this->token = $token;

        $headers = array(
            'content-type: application/json',
            'wx-grant-type: password',
            'x-api-key: '.$this->key,
            'x-access-token: '.$this->token
        );

        // $headers = array(
        //     'content-type' => 'application/json',
        //     'wx-grant-type' => 'password',
        //     'x-api-key' => $this->key,
        //     'x-access-token' => $this->token
        // );

        return $this->curl($this->url.'/user/validate-token', 'get', $headers);

        //return \Unirest\Request::get($this->url.'/user/validate-token', $headers);
    }

    public function renew($token, $refresh) {
        $this->token = $token;
        $this->refresh = $refresh;

        // $headers = array(
        //     'content-type' => 'application/json',
        //     'wx-grant-type' => 'refresh_token',
        //     'x-api-key' => $this->key,
        //     'x-access-token' => $this->token,
        //     'x-refresh_token' => $this->refresh
        // );

        $headers = array(
            'content-type: application/json',
            'wx-grant-type: refresh_token',
            'x-api-key: '.$this->key,
            'x-access-token: '.$this->token,
            'x-refresh_token: '.$this->refresh
        );

        return $this->curl($this->url.'/user/renew-token', 'get', $headers);

        //return \Unirest\Request::get($this->url.'/user/renew-token', $headers);
    }

    public function call($flow, $field, $value){
        // $this->token = $token;
        $this->flow = $flow;

        // $headers = array(
        //     'content-type' => 'application/json',
        //     'x-api-key' => $this->key,
        //     'x-access-token' => $this->token
        // );

        // $headers = array(
        //     'content-type: application/json',
        //     'x-api-key: '.$this->key,
        //     'x-access-token: '.$this->token
        // );

        $headers = array(
            'content-type: application/json',
            'x-api-key: '.$this->key
        );

        $data = array(
            'IdProviderFlow' => $this->flow,
            'Fields' => [
                ['Field' => $field, 'Value' => $value]
            ]
        );

        \Log::info('HEADERS: '.serialize($headers));
        \Log::info('DATA: '.serialize($data));

        //$corpo = \Unirest\Request\Body::json($data);

        $retorno = $this->curl($this->url.'/api/callmanager', 'post', $headers, $data);
        //$retorno =  \Unirest\Request::post($this->url.'/api/callmanager', $headers, $corpo);

        \Log::info(serialize($retorno));
        return $retorno;
    }

    public function status($flow, $call) {
        // $this->token = $token;
        $this->flow = $flow;
        $this->call = $call;

        // $headers = array(
        //     'content-type' => 'application/json',
        //     'x-api-key' => $this->key,
        //     'x-access-token' => $this->token
        // );

        // $headers = array(
        //     'content-type: application/json',
        //     'x-api-key: '.$this->key,
        //     'x-access-token: '.$this->token
        // );

        $headers = array(
            'content-type: application/json',
            'x-api-key: '.$this->key
        );

        return $this->curl($this->url.'/api/callmanager/'.$this->flow.'/'.$this->call, 'get', $headers);

        // return \Unirest\Request::get($this->url.'/api/callmanager/'.$this->flow.'/'.$this->call, $headers);
    }

    public function curl($url, $action, $headers, $post = array()){
        switch ($action) {
            case 'get':  $opt = array( CURLOPT_URL => $url, CURLOPT_HTTPHEADER => $headers, CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_VERBOSE => true ); break;
            case 'post': $opt = array( CURLOPT_URL => $url, CURLOPT_HTTPHEADER => $headers, CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_CONNECTTIMEOUT => 60, CURLOPT_POST => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_VERBOSE => true, CURLOPT_POSTFIELDS => json_encode($post) ); break;
        }

        $curl = curl_init();
        curl_setopt_array($curl, $opt);
        $xml = curl_exec($curl);
        curl_close($curl);

        return json_decode($xml);
    }
}
