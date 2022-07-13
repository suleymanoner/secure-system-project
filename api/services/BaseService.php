<?php


class BaseService {

    public function getResponse($status, $message) {
        return Flight::json(array(
                'status' => $status,
                'response' => $message
            ));
    }

    public function checkPasswordPwned($password) {

        $hash = strtoupper(hash("sha1", $password));    
        $hash_prefix = substr($hash, 0, 5);
        $hash_remaining = substr($hash, 5);
        
        $response = file_get_contents('https://api.pwnedpasswords.com/range/'.$hash_prefix);
        
        if (strpos($response, $hash_remaining) !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function createJWT($user) {
        $key = 'SECRET_JWT';
        $payload = [
            'id' => $user[0]['id'],
            'name' => $user[0]['username']
        ];

        $jwt = \Firebase\JWT\JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }

    public function checkDomainExists($email, $record = 'MX'){
        $array = explode('@', $email);
        return checkdnsrr($array[1], $record);
    }

    public function checkForPassword($db_user, $password, $two_factor = "") {
        $db_password = $db_user[0]['password'];
    
        if(password_verify($password, $db_password)) {
            if($two_factor !== "") {
                return;
            } else {
                $jwt = $this->createJWT($db_user);
                $this->getResponse('ok', $jwt);
            }
        } else {
            $this->getResponse('error', 'User or password is not valid! Please try again!');
            die();
        }
    }

}