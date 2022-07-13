<?php
require_once dirname(__FILE__).'/../config.php';

class HCaptchaClient {

    function takeCaptchaResponse() {

        $data = array(
            'secret' => CAPTCHA_SECRET,
            'response' => $_POST['h-captcha-response']
        );
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);
        $responseData = json_decode($response);

        if($responseData->success) {
            header("Location: ".BASE_URL."login.html");
        } 
        else {
            header("Location: ".BASE_URL."captcha.php");
        }
    }

}
