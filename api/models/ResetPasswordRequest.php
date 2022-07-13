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
 *     title="ResetPasswordRequest",
 *     description="ResetPasswordRequest",
 * )
 */
class ResetPasswordRequest 
{
    /**
     * @OA\Property(
     *     format="int64",
     *     description="New Password",
     *     title="Password",
     *     maximum=255
     * )
     *
     * @var string
     */
    private $new_password;

    /**
     * @OA\Property(
     *     format="int64",
     *     description="Confirm Password",
     *     title="Password",
     *     maximum=255
     * )
     *
     * @var string
     */
    private $confirm_password;

    /**
     * @OA\Property(
     *     format="email",
     *     description="Email",
     *     title="Email",
     * )
     *
     * @var string
     */
    private $email;
}