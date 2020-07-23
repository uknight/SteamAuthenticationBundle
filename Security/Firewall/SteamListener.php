<?php

namespace Uknight\SteamAuthenticationBundle\Security\Firewall;

use Uknight\SteamAuthenticationBundle\Security\Authentication\Token\SteamUserToken;
use Uknight\SteamAuthenticationBundle\Security\Authentication\Validator\RequestValidatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
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
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /** @var RequestValidatorInterface */
    private $requestValidator;

    /**
     * @var EventDispatcherInterface
     */
    private $ed;

    /**
     * @param AuthenticationManagerInterface $authenticationManager
     * @param RouterInterface $router
     * @param string $loginRedirect
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        AuthenticationManagerInterface $authenticationManager,
        RouterInterface $router,
        string $loginRedirect,
        string $loginRoute,
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $ed
        // TokenStorageInterface $tokenStorage,
        RequestValidatorInterface $requestValidator
    )
    {
        $this->authenticationManager = $authenticationManager;
        $this->router = $router;
        $this->loginRedirect = $loginRedirect;
        $this->tokenStorage = $tokenStorage;
        $this->ed = $ed;
        $this->requestValidator = $requestValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $this->requestValidator->setRequest($request);

        if (!$this->requestValidator->validate()) {
            return;
        }

        $claimedId = str_replace('https://steamcommunity.com/openid/id/', '', $request->query->get('openid_claimed_id'));

        $token = new SteamUserToken();
        $token->setUsername($claimedId);

        $authToken = $this->authenticationManager->authenticate($token);
        $this->tokenStorage->setToken($authToken);

        $loginEvent = new InteractiveLoginEvent($request, $authToken);
        $this->ed->dispatch("security.interactive_login", $loginEvent);

        $event->setResponse(new RedirectResponse(
            $this->router->generate($this->loginRedirect)
        ));
    }
}
