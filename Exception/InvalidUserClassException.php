<?php

namespace UKnight\SteamAuthenticationBundle\Exception;

/**
 * @author Knojector <dev@knojector.xyz>
 */
class InvalidUserClassException extends \Exception
{
    /**
     * @param string $className
     */
    public function __construct(string $className)
    {
        parent::__construct(
            sprintf('The class "%s" can not be used for Steam authentication.', $className),
            500
        );
    }
}