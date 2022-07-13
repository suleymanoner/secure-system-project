<?php
require_once dirname(__FILE__).'/../config.php';

class NexmoClient {

    function sendSms($phone, $code) {

        $data = array(
            'from' => 'Vonage APIs',
            'text' => 'Your code is : ' . $code,
            'to' => $phone,
            'api_key' => NEXMO_API_KEY,
            'api_secret' => NEXMO_API_SECRET
        );
        
        $post_data = json_encode($data);
        
        $crl = curl_init('https://rest.nexmo.com/sms/json');
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLINFO_HEADER_OUT, true);
        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
        
        curl_setopt($crl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json')
        );
                    
        curl_close($crl);
    }

}
