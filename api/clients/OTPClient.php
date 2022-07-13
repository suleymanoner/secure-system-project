<?php

use OTPHP\TOTP;

class OTPClient {

    public function createQRCode() {

        $otp = TOTP::create();

        $secret = $otp->getSecret();

        $otp = TOTP::create($secret);
        
        $otp->setLabel('sssd-project@ibu');

        $current_otp = $otp->now();

        $qrCodeUri = $otp->getQrCodeUri(
            'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M',
            '[DATA]'
        );

        return ["otp" => $current_otp, "qrcode" => $qrCodeUri];
    }

    public function checkIsOTPCorrect($user_otp, $otp_array) {

        $otp = $otp_array["otp"];

        if($user_otp !== $otp) {
            print_r("not correct!");
        } else {
            print_r("correct!");
        }
    }

}
