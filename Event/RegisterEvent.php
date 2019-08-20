<?php
namespace Uknight\SteamAuthenticationBundle\Event;

use Acme\Store\Order;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * The order.placed event is dispatched each time an order is created
 * in the system.
 */
class RegisterEvent extends Event
{
    public const NAME = 'uknight.steam.register';

    protected $user;

    public function __construct(mixed $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
