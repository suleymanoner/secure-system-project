<?php
require_once dirname(__FILE__)."/BaseDao.php";


class UserDao extends BaseDao {

    public function get_user_by_id($id) {
        return $this->query('SELECT id, username, email, phone, two_factor_way FROM user WHERE id = :id', ["id" => $id]);
    }

    public function get_user_by_username($username) {
        return $this->query('SELECT * FROM user WHERE username = :username', ["username" => $username]);
    }

    public function get_user_by_email($email) {
        return $this->query('SELECT * FROM user WHERE email = :email', ["email" => $email]);
    }

    public function update_user_status($username) {
        return $this->query('UPDATE user SET status = "ACTIVE" WHERE username = :username', ["username" => $username]);
    }

    public function check_username($username) {
        return $this->query('SELECT COUNT(*) as exist FROM user WHERE username = :username', ["username" => $username]);
    }

    public function register($user) {
        return $this->insert('user', $user);
    }

    public function login($username) {
        return $this->query("SELECT * FROM user WHERE username = :username", ["username" => $username]);
    }

    public function change_password($new_password, $username) {
        return $this->query("UPDATE user SET password = :password WHERE username = :username",
                    ["password" => $new_password, "username" => $username]);
    }

    public function update_token($username) {
        return $this->query("UPDATE user SET token_created_at = NOW() WHERE username = :username",
                    ["username" => $username]);
    }

    public function invalidate_token($username) {
        return $this->query("UPDATE user SET token_created_at = (NOW() - INTERVAL 6 MINUTE) WHERE username = :username",
                    ["username" => $username]);
    }

    public function save_otp($otp, $username) {
        return $this->query("UPDATE user SET otp = :otp WHERE username = :username",
        ["username" => $username, "otp" => $otp]);
    }

    public function handle_two_factor_auth($id, $action) {
        if($action == "enable") {
            return $this->query("UPDATE user SET two_factor_auth = TRUE WHERE id = :id", ["id" => $id]);
        } else if($action == "disable") {
            return $this->query("UPDATE user SET two_factor_auth = FALSE WHERE id = :id", ["id" => $id]);
        }
    }

    public function show_two_factor($id) {
        return $this->query('SELECT two_factor_auth FROM user WHERE id = :id', ['id' => $id]);
    }

    public function change_two_factor_way($id, $type) {
        if($type == "SMS") {
            return $this->query("UPDATE user SET two_factor_way = 'SMS' WHERE id = :id", ["id" => $id]);
        } else if($type == "QRCODE") {
            return $this->query("UPDATE user SET two_factor_way = 'QRCODE' WHERE id = :id", ["id" => $id]);
        }
    }

    public function save_confirm_link($username, $link) {
        return $this->query("UPDATE user SET confirmation_link = :confirmation_link WHERE username = :username",
        ["username" => $username, "confirmation_link" => $link]);
    }

    public function delete_confirm_link($username) {
        return $this->query("UPDATE user SET confirmation_link = NULL WHERE username = :username",
        ["username" => $username]);
    }

}
