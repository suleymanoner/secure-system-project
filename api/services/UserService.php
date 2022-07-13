<?php
require_once dirname(__FILE__)."/../dao/UserDao.php";
require_once dirname(__FILE__)."/BaseService.php";
require_once dirname(__FILE__)."/../clients/SMTPClient.php";
require_once dirname(__FILE__)."/../clients/NexmoClient.php";
require_once dirname(__FILE__)."/../clients/OTPClient.php";


class UserService extends BaseService {

    private $smtpClient;
    private $nexmoClient;
    private $otpClient;

    public function __construct() {
        $this->dao = new UserDao();
        $this->smtpClient = new SMTPClient();
        $this->nexmoClient = new NexmoClient();
        $this->otpClient = new OTPClient();
    }

    public function get_user_by_id($id) {
        return $this->dao->get_user_by_id($id);
    }

    public function check_username($username) {
        return $this->dao->check_username($username);
    }

    public function get_user_by_email($email) {
        return $this->dao->get_user_by_email($email);
    }

    public function register($username, $email, $password, $password_again, $phone, $auth_way) {

        $db_user_by_email = $this->dao->get_user_by_email($email);

        if($this->dao->check_username($username)[0]['exist'] == 1) {
            $this->getResponse('error', 'There is already an account with this username!');
            die();
        }

        if(isset($db_user_by_email[0]['email'])) {
            $this->getResponse('error', 'There is already an account with this email address!');
            die();
        }

        if(!ctype_alnum($username)){
            $this->getResponse('error', 'Username can only contain alphanumeric characters!');
            die();
        }
        
        if(strlen($username) < 3) {
            $this->getResponse('error', 'Username should contain more than 3 character!');
            die();
        }
    
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->getResponse('error', 'Your email is not valid!');
            die();
        }

        if(!$this->checkDomainExists($email)) {
            $this->getResponse('error', 'Your email domain is not valid!');
            die();
        }

        if(strlen($password) < 8) {
            $this->getResponse('error', 'Password should contain at least 8 character!');
            die();
        }

        if($password !== $password_again) {
            $this->getResponse('error', 'Your passwords not matched!');
            die();
        }

        if(!isset($auth_way)) {
            $this->getResponse('error', 'Please choose 2 Factor Authentication way!');
            die();
        }

        if($this->checkPasswordPwned($password)) {
            $this->getResponse('error', 'Your password has been breached! Please try another password!');
            die();
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                $random = base64_encode($username);

                $user = [
                    'username' => $username,
                    'email' => $email,
                    'password' => $hashed_password,
                    'phone' => $phone,
                    'two_factor_way' => $auth_way,
                    'confirmation_link' => $random
                ];

                $this->dao->register($user);
                $this->smtpClient->send_email($email, 'Confirm Your Account', 'Dear User, here is the confirmation link.', 'confirmation.html?rndm='.$random);
                $this->getResponse('ok', 'Please confirm your account!');
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    public function confirm($link, $random) {

        if($random) {
            $username = base64_decode($random);
            $db_user = $this->dao->get_user_by_username($username);

            if(!isset($db_user[0]['id'])) {
                $this->getResponse('error', "You don't have account! Please register first.");
                die();
            } else {
                if(str_contains($link, $db_user[0]['confirmation_link'])) {
                    $this->dao->update_user_status($db_user[0]['username']);
                    $this->dao->delete_confirm_link($db_user[0]['username']);
                    $jwt = $this->createJWT($db_user);
                    $this->getResponse('ok', $jwt);
                } else {
                    $this->getResponse('error', "Link invalid!");
                }
            }
        }
    }

    public function login($username, $password) {

        $db_user = $this->dao->login($username);

        if(!isset($db_user[0]['username'])) {
            $this->getResponse('error', 'User or password is not valid! Please try again!');
            die();
        }

        if($db_user[0]['two_factor_auth']) {

            if(isset($_COOKIE['PHPSESSID'])) {

                if(str_contains($_COOKIE['PHPSESSID'], $db_user[0]['username'])) {
                    $this->checkForPassword($db_user, $password);
                } else {

                    if($db_user[0]['two_factor_way'] == 'SMS') {
                        $this->checkForPassword($db_user, $password, "SMS");
                        $random_otp = random_int(100000, 999999);
                        $this->dao->save_otp($random_otp, $db_user[0]["username"]);
                        $this->nexmoClient->sendSms($db_user[0]['phone'], $random_otp);
                        $this->getResponse('ok', 'Code sent to your phone! Please check it!');
                    } else if($db_user[0]['two_factor_way'] == 'QRCODE') {
                        $this->checkForPassword($db_user, $password, "QRCODE");
                        $qr_array = $this->otpClient->createQRCode();
                        $this->dao->save_otp($qr_array["otp"], $db_user[0]["username"]);
                        $this->getResponse('qr', $qr_array['qrcode']);
                    }
                }            
            } else {

                if($db_user[0]['two_factor_way'] == 'SMS') {
                    $this->checkForPassword($db_user, $password, "SMS");
                    $random_otp = random_int(100000, 999999);
                    $this->dao->save_otp($random_otp, $db_user[0]["username"]);
                    $this->nexmoClient->sendSms($db_user[0]['phone'], $random_otp);
                    $this->getResponse('ok', 'Code sent to your phone! Please check it!');
                } else if($db_user[0]['two_factor_way'] == 'QRCODE') {
                    $this->checkForPassword($db_user, $password, "QRCODE");
                    $qr_array = $this->otpClient->createQRCode();
                    $this->dao->save_otp($qr_array["otp"], $db_user[0]["username"]);
                    $this->getResponse('qr', $qr_array['qrcode']);
                }
            }
        } else {
            $this->checkForPassword($db_user, $password);
        }
    }

    public function handle_two_factor_auth($id, $action) {
        $this->dao->handle_two_factor_auth($id, $action);

        if($action == "enable") {
            $this->getResponse('ok', 'Enabled!');
        } else if($action == "disable") {
            $this->getResponse('ok', 'Disabled!');
        }
    }

    public function change_two_factor_way($id, $action) {
        $this->dao->change_two_factor_way($id, $action);

        if($action == "SMS") {
            $this->getResponse('ok', 'SMS');
        } else if($action == "QRCODE") {
            $this->getResponse('ok', 'QRCODE');
        }
    }

    public function check_two_auth_code($code, $username, $remember) {

        $db_user = $this->dao->get_user_by_username($username);

        if($db_user[0]['otp'] != $code) {
            $this->getResponse('error', 'Your code not matched!');
            die();
        }

        if($remember == "true") {
            $cookie = $db_user[0]['username'] . md5("Cookie");
            setcookie('PHPSESSID', $cookie, time()+3600, '/');
        }

        $jwt = $this->createJWT($db_user);  
        $this->getResponse('ok', $jwt);
    }

    public function get_remember_cookie($id) {

        $db_user = $this->dao->get_user_by_id($id);

        if(isset($_COOKIE['PHPSESSID'])) {
            if(str_contains($_COOKIE['PHPSESSID'], $db_user[0]['username'])) {
                $this->getResponse('ok', "yes");
            } else {
                $this->getResponse('ok', "no");
            }
        } else {
            $this->getResponse('ok', "no");
        }
    }

    public function delete_remember_cookie() {
        if(isset($_COOKIE['PHPSESSID'])) {
            unset($_COOKIE['PHPSESSID']); 
            setcookie('PHPSESSID', "", -1, '/'); 
        } else {
            $this->getResponse('error', "No Cookie!");
        }
    }

    public function show_two_factor($id) {
        $two_factor = $this->dao->show_two_factor($id)[0]['two_factor_auth'];

        if($two_factor) {
            $this->getResponse('ok', '1');
        } else {
            $this->getResponse('ok', '0');
        }
    }

    public function change_password($old_pass, $new_password, $confirm_pass, $username) {

        $db_user = $this->dao->get_user_by_username($username);

        if(!password_verify($old_pass, $db_user[0]['password'])) {
            $this->getResponse('error', 'Your old password is not correct!');
            die();
        }

        if(strlen($new_password) < 8) {
            $this->getResponse('error', 'Password should contain at least 8 character!');
            die();
        }

        if($this->checkPasswordPwned($new_password)) {
            $this->getResponse('error', 'New password has been breached! Please try another password!');
            die();
        }

        if($new_password !== $confirm_pass) {
            $this->getResponse('error', 'Your passwords not matched!');
            die();
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        try {
            $this->dao->change_password($hashed_password, $username);
            $this->getResponse('ok', 'Your password changed!');
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function send_forgot_password_link($email) {

        $db_user = $this->dao->get_user_by_email($email);

        if(!isset($db_user[0]["id"])) {
            $this->getResponse('error', "You don't have account! Please register first.");
            die();
        } else {
            $this->dao->update_token($db_user[0]["username"]);
            $this->smtpClient->send_email($email, 'Forgot Your Password', 'Dear User, here is the recovery link.', 'views/forgot_password.html?email='.$email);
            $this->getResponse('ok', 'Reset password link sent your email!');
        }       
    }

    public function check_token_time($email) {

        $db_user = $this->dao->get_user_by_email($email);

        if($db_user[0]["token_created_at"] !== null) {
            if(strtotime(date("Y-m-d H:i:s")) - strtotime($db_user[0]["token_created_at"]) > 300) {
                $this->getResponse('error', 'Your reset link has expired!');
                die();
            } 
        } else {
            $this->getResponse('error', "You didn't send recovery email!");
            die();
        }
    }

    public function forgot_password($new_password, $confirm_pass, $user) {

        if($new_password !== $confirm_pass) {
            $this->getResponse('error', 'Your passwords not matched!');
            die();
        }

        if(strlen($new_password) < 8) {
            $this->getResponse('error', 'Password should contain at least 8 character!');
            die();
        }

        if($this->checkPasswordPwned($new_password)) {
            $this->getResponse('error', 'New password has been breached! Please try another password!');
            die();
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        try {
            if($user) {
                $this->dao->change_password($hashed_password, $user['username']);
                $this->smtpClient->send_email($user['email'], 'Password Changed', 'Dear User, You have successfully changed your password!');
                $this->dao->invalidate_token($user['username']);
                $this->getResponse('ok', 'Your password changed!');
            } else {
                $this->getResponse('error', "User could not found!");
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

}
