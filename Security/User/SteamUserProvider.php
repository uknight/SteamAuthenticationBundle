<?php

namespace Uknight\SteamAuthenticationBundle\Security\User;

use Uknight\SteamAuthenticationBundle\User\SteamUserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Uknight\SteamAuthenticationBundle\Factory\UserFactory;
use Uknight\SteamAuthenticationBundle\Http\SteamApiClient;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Uknight\SteamAuthenticationBundle\Event\RegisterEvent;

/**
 * @author Knojector <dev@knojector.xyz>
 */
class SteamUserProvider implements UserProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SteamApiClient
     */
    private $api;

    /**
     * @var string
     */
    private $userClass;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EntityManagerInterface $entityManager
     * @param SteamApiClient         $steamApiClient
     * @param string                 $userClass
     * @param UserFactory            $userFactory
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SteamApiClient $steamApiClient,
        string $userClass,
        UserFactory $userFactory,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->entityManager = $entityManager;
        $this->api = $steamApiClient;
        $this->userClass = $userClass;
        $this->userFactory = $userFactory;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username, $fos = true)
    {
        // ToDo: handle $fos parameter properly

        $user = $this->entityManager->getRepository($this->userClass)->findOneBy(['steamId' => $username]);
        $userData = $this->api->loadProfile($username);
        if (null === $user) {
            // * here we register user... and nee to do something with it
            $user = $this->userFactory->getFromSteamApiResponse($userData, $fos);

            $this->entityManager->persist($user);

            $event = new RegisterEvent($user);
            $this->dispatcher->dispatch($event, OrderPlacedEvent::NAME);
        } else {
            $user->update($userData);
        }

        $this->entityManager->flush();

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof SteamUserInterface) {
            throw new UnsupportedUserException();
        }

        return $this->entityManager->getRepository($this->userClass)->findOneBy(['steamId' => $user->getSteamId()]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === $this->userClass;
    }
}
