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
 *     title="CheckTokenTimeRequest model",
 *     description="CheckTokenTimeRequest model",
 * )
 */
class CheckTokenTimeRequest
{
    
    /**
     * @OA\Property(
     *     format="int64",
     *     description="Email",
     *     title="email",
     *     maximum=255
     * )
     *
     * @var string
     */
    private $email;
}
