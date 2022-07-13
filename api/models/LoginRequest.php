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
 *     title="Login model",
 *     description="Login model",
 * )
 */
class LoginRequest
{
    /**
     * @OA\Property(
     *     description="Username",
     *     title="Username",
     * )
     *
     * @var string
     */
    private $username;

    /**
     * @OA\Property(
     *     format="int64",
     *     description="Password",
     *     title="Password",
     *     maximum=255
     * )
     *
     * @var string
     */
    private $password;

}