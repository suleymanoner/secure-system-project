<?php

/**
 * @OA\Get(path="/user", tags={"user"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Response(response="200", description="Get user based on username.")
 * )
 */
Flight::route('GET /user', function(){
    $token = @getallheaders()['Authentication'];

    try {
        $decoded = (array)\Firebase\JWT\JWT::decode($token, 'SECRET_JWT', ['HS256']);
        Flight::json(Flight::userService()->get_user_by_id($decoded['id']));
    } catch (\Exception $e) {
        Flight::json(["error" => $e->getMessage()]);
    }
});

/**
 * @OA\Post(
 *     path="/register",
 *     tags={"user"},
 *     summary="Create user",
 *     description="User registration.",
 *     operationId="register",
 *     @OA\Response(
 *         response="default",
 *         description="successful operation"
 *     ),
 *     @OA\RequestBody(
 *         description="Create user object",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
 *     )
 * )
 */
Flight::route('POST /register', function(){
    $username = Flight::request()->data->username;
    $email = Flight::request()->data->email;
    $password = Flight::request()->data->password;
    $password_again = Flight::request()->data->password_again;
    $phone = Flight::request()->data->phone;
    $auth_way = Flight::request()->data->auth_way;

    Flight::userService()->register($username, $email, $password, $password_again, $phone, $auth_way);
});

/**
 * @OA\Post(
 *     path="/confirm",
 *     tags={"user"},
 *     summary="Confirm account",
 *     description="Confirm account.",
 *     operationId="confirm",
 *     @OA\Response(
 *         response="default",
 *         description="successful operation"
 *     ),
 *     @OA\RequestBody(
 *         description="Confirm account.",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ConfirmationRequest")
 *     )
 * )
 */
Flight::route('POST /confirm', function(){
    $link = Flight::request()->data->link;
    $random = Flight::request()->data->random;
    Flight::userService()->confirm($link, $random);  
});

/**
 * @OA\Post(
 *     path="/login",
 *     tags={"user"},
 *     summary="Login user",
 *     description="This can only be done by the registered users.",
 *     operationId="login",
 *     @OA\Response(
 *         response="default",
 *         description="successful operation"
 *     ),
 *     @OA\RequestBody(
 *         description="Login user",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
 *     )
 * )
 */  
Flight::route('POST /login', function(){
    $username = Flight::request()->data->username;
    $password = Flight::request()->data->password;
    Flight::userService()->login($username, $password);
});

/**
 * @OA\Post(
 *     path="/handle_two_factor_auth",
 *     tags={"user"},
 *     security={{"ApiKeyAuth": {}}},
 *     summary="Open 2 factor authentication",
 *     description="This can only be done by the registered users.",
 *     operationId="handle_two_factor_auth",
 *     @OA\Response(
 *         response="default",
 *         description="successful operation"
 *     ),
 *     @OA\RequestBody(
 *         description="Handle Two Factor",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/HandleTwoFactorRequest")
 *     )
 * )
 */  
Flight::route('POST /handle_two_factor_auth', function(){
    $action = Flight::request()->data->action;
    $token = @getallheaders()['Authentication'];

    try {
        $decoded = (array)\Firebase\JWT\JWT::decode($token, 'SECRET_JWT', ['HS256']);
        Flight::userService()->handle_two_factor_auth($decoded['id'], $action);
    } catch (\Exception $e) {
        Flight::json(["error" => $e->getMessage()]);
    }
});

/**
 * @OA\Post(
 *     path="/change_two_factor_way",
 *     tags={"user"},
 *     security={{"ApiKeyAuth": {}}},
 *     summary="Change 2 factor authentication way",
 *     description="This can only be done by the registered users.",
 *     operationId="change_two_factor_way",
 *     @OA\Response(
 *         response="default",
 *         description="successful operation"
 *     ),
 *     @OA\RequestBody(
 *         description="Change Two Factor",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/HandleTwoFactorRequest")
 *     )
 * )
 */  
Flight::route('POST /change_two_factor_way', function(){
    $action = Flight::request()->data->action;
    $token = @getallheaders()['Authentication'];

    try {
        $decoded = (array)\Firebase\JWT\JWT::decode($token, 'SECRET_JWT', ['HS256']);
        Flight::userService()->change_two_factor_way($decoded['id'], $action);
    } catch (\Exception $e) {
        Flight::json(["error" => $e->getMessage()]);
    }
});

/**
 * @OA\Get(
 *     path="/get_remember_cookie",
 *     tags={"user"},
 *     security={{"ApiKeyAuth": {}}},
 *     summary="Get remember cookie",
 *     description="This can only be done by the registered users.",
 *     operationId="get_remember_cookie",
 *     @OA\Response(
 *         response="default",
 *         description="successful operation"
 *     ),
 * )
 */  
Flight::route('GET /get_remember_cookie', function(){
    $token = @getallheaders()['Authentication'];

    try {
        $decoded = (array)\Firebase\JWT\JWT::decode($token, 'SECRET_JWT', ['HS256']);
        Flight::userService()->get_remember_cookie($decoded['id']);
    } catch (\Exception $e) {
        Flight::json(["error" => $e->getMessage()]);
    }
});

/**
 * @OA\Post(
 *     path="/delete_remember_cookie",
 *     tags={"user"},
 *     security={{"ApiKeyAuth": {}}},
 *     summary="Delete remember cookie",
 *     description="This can only be done by the registered users.",
 *     operationId="delete_remember_cookie",
 *     @OA\Response(
 *         response="default",
 *         description="successful operation"
 *     ),
 * )
 */  
Flight::route('POST /delete_remember_cookie', function(){
    Flight::userService()->delete_remember_cookie();
});

/**
 * @OA\Post(
 *     path="/check_two_auth_code",
 *     tags={"user"},
 *     security={{"ApiKeyAuth": {}}},
 *     summary="Check 2 factor auth code",
 *     description="This can only be done by the registered users.",
 *     operationId="check_two_auth_code",
 *     @OA\Response(
 *         response="default",
 *         description="successful operation"
 *     ),
 *     @OA\RequestBody(
 *         description="Check two auth code",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/CheckTwoFactorRequest")
 *     )
 * )
 */  
Flight::route('POST /check_two_auth_code', function(){
    $code = Flight::request()->data->code;
    $username = Flight::request()->data->username;
    $remember = Flight::request()->data->remember;
    Flight::userService()->check_two_auth_code($code, $username, $remember);
});

/**
 * @OA\Get(path="/show_two_factor", tags={"user"}, security={{"ApiKeyAuth": {}}},
 *     @OA\Response(response="200", description="Get if user enabled or disabled 2 factor auth.")
 * )
 */
Flight::route('GET /show_two_factor', function(){
    $token = @getallheaders()['Authentication'];

    try {
        $decoded = (array)\Firebase\JWT\JWT::decode($token, 'SECRET_JWT', ['HS256']);
        Flight::userService()->show_two_factor($decoded['id']);
    } catch (\Exception $e) {
        Flight::json(["error" => $e->getMessage()]);
    }
});

/**
 * @OA\Post(
 *     path="/changepassword",
 *     tags={"user"},
 *     security={{"ApiKeyAuth": {}}},
 *     summary="Change password",
 *     description="This can only be done by the registered users.",
 *     operationId="changepassword",
 *     @OA\Response(
 *         response="default",
 *         description="successful operation"
 *     ),
 *     @OA\RequestBody(
 *         description="Change password",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ChangePasswordRequest")
 *     )
 * )
 */  
Flight::route('POST /changepassword', function(){
    $old_password = Flight::request()->data->old_password;
    $new_password = Flight::request()->data->new_password;
    $confirm_password = Flight::request()->data->confirm_password;

    $token = @getallheaders()['Authentication'];

    $decoded = (array)\Firebase\JWT\JWT::decode($token, 'SECRET_JWT', ['HS256']);

    Flight::userService()->change_password($old_password, $new_password, $confirm_password, $decoded['name']);
});

/**
 * @OA\Post(
 *     path="/forgotpassword",
 *     tags={"user"},
 *     summary="Send forgot password email",
 *     description="This can only be done by the registered users.",
 *     operationId="forgotpassword",
 *     @OA\Response(
 *         response="default",
 *         description="successful operation"
 *     ),
 *     @OA\RequestBody(
 *         description="Send forgot password email",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ForgotPasswordRequest")
 *     )
 * )
 */  
Flight::route('POST /forgotpassword', function(){
    $email = Flight::request()->data->email;
    Flight::userService()->send_forgot_password_link($email);
});

/**
 * @OA\Post(
 *     path="/resetpassword",
 *     tags={"user"},
 *     summary="Update password",
 *     description="This can only be done by the registered users.",
 *     operationId="resetpassword",
 *     @OA\Response(
 *         response="default",
 *         description="successful operation"
 *     ),
 *     @OA\RequestBody(
 *         description="Update password",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ResetPasswordRequest")
 *     )
 * )
 */  
Flight::route('POST /resetpassword', function(){
    $new_password = Flight::request()->data->new_password;
    $confirm_password = Flight::request()->data->confirm_password;
    $email = Flight::request()->data->email;
    $user = Flight::userService()->get_user_by_email($email);
    Flight::userService()->forgot_password($new_password, $confirm_password, $user[0]);
});

/**
 * @OA\Post(
 *     path="/check_token",
 *     tags={"user"},
 *     summary="Check token time",
 *     description="This can only be done by the registered users.",
 *     operationId="check_token",
 *     @OA\Response(
 *         response="default",
 *         description="successful operation"
 *     ),
 *     @OA\RequestBody(
 *         description="Check token time",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/CheckTokenTimeRequest")
 *     )
 * )
 */  
Flight::route('POST /check_token', function(){
    $email = Flight::request()->data->email;
    Flight::userService()->check_token_time($email);
});
