<?php

/**
 * @license Apache 2.0
 */

/**
 * Class User.
 *
 * @author  Suleyman Oner
 *
 * @OA\Schema(
 *     title="CheckTwoFactorRequest model",
 *     description="CheckTwoFactorRequest model",
 * )
 */
class CheckTwoFactorRequest
{
    /**
     * @OA\Property(
     *     description="code",
     *     title="code",
     * )
     *
     * @var string
     */
    private $code;

    /**
     * @OA\Property(
     *     description="username",
     *     title="username",
     * )
     *
     * @var string
     */
    private $username;

     /**
     * @OA\Property(
     *     description="Remember otp",
     *     title="remember",
     * )
     *
     * @var string
     */
    private $remember;

}