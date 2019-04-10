<?php

namespace Uknight\SteamAuthenticationBundle\Exception;

/**
 * @author Knojector <dev@knojector.xyz>
 */
class InvalidApiResponseException extends \Exception
{
    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct($message, 500);
    }
}