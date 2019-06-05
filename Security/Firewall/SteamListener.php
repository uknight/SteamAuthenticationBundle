<?php

namespace Uknight\SteamAuthenticationBundle\Security\Firewall;

use Uknight\SteamAuthenticationBundle\Security\Authentication\Token\SteamUserToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;


/**
 * @author Knojector <dev@knojector.xyz>
 */
class SteamListener implements ListenerInterface
{
    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * @var string
     */
    private $loginRedirect;

    /**
     * @var string
     */
    private $loginRoute;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var EventDispatcherInterface
     */
    private $ed;

    /**
     * @param AuthenticationManagerInterface $authenticationManager
     * @param RouterInterface                $router
     * @param string                         $loginRedirect
     * @param string                         $loginRoute
     * @param TokenStorageInterface          $tokenStorage
     */
    public function __construct(
        AuthenticationManagerInterface $authenticationManager,
        RouterInterface $router,
        string $loginRedirect,
        string $loginRoute,
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $ed
    )
    {
        $this->authenticationManager = $authenticationManager;
        $this->router = $router;
        $this->loginRedirect = $loginRedirect;
        $this->loginRoute = $loginRoute;
        $this->tokenStorage = $tokenStorage;
        $this->ed = $ed;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $returnTo = $request->query->get('openid_return_to');

        if ($returnTo !== $this->router->generate($this->loginRoute, [], UrlGeneratorInterface::ABSOLUTE_URL)) {
            return;
        }

        $claimedId = str_replace('https://steamcommunity.com/openid/id/', '', $request->query->get('openid_claimed_id'));

        $token = new SteamUserToken();
        $token->setUsername($claimedId);

        $authToken = $this->authenticationManager->authenticate($token);
        $this->tokenStorage->setToken($authToken);

        $event = new InteractiveLoginEvent($request, $authToken);
        $this->ed->dispatch("security.interactive_login", $event);

        $event->setResponse(new RedirectResponse(
            $this->router->generate($this->loginRedirect)
        ));
    }

}
