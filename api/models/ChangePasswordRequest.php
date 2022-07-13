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
 *     title="Change Password model",
 *     description="Change Password  model",
 * )
 */
class ChangePasswordRequest 
{
    /**
     * @OA\Property(
     *     format="int64",
     *     description="Old Password",
     *     title="Password",
     *     maximum=255
     * )
     *
     * @var string
     */
    private $old_password;

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
}