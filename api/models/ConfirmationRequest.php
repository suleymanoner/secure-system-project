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
 *     title="ConfirmationRequest model",
 *     description="ConfirmationRequest model",
 * )
 */
class ConfirmationRequest
{

    /**
     * @OA\Property(
     *     description="Link",
     *     title="Link",
     * )
     *
     * @var string
     */
    private $link;

    /**
     * @OA\Property(
     *     description="random",
     *     title="random",
     * )
     *
     * @var string
     */
    private $random;
}
