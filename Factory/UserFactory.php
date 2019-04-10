<?php

namespace Uknight\SteamAuthenticationBundle\Factory;

use Uknight\SteamAuthenticationBundle\Exception\InvalidUserClassException;
use Uknight\SteamAuthenticationBundle\User\SteamUserInterface;

/**
 * @author Knojector <dev@knojector.xyz>
 * @patch U-Knight Team
 */
class UserFactory
{
    /**
     * @var string
     */
    private $userClass;

    /**
     * @param string $userClass
     */
    public function __construct(string $userClass)
    {
        $this->userClass = $userClass;
    }

    /**
     * @param array $userData
     *
     * @param bool $fos Do we use FOSUserBundle here?
     *
     * @return SteamUserInterface
     *
     * @throws InvalidUserClassException
     */
    public function getFromSteamApiResponse(array $userData, bool $fos)
    {
        $user = new $this->userClass;
        if (!$user instanceof SteamUserInterface) {
            throw new InvalidUserClassException($this->userClass);
        }

        $user->setSteamId($userData['steamid']);
        $user->setCommunityVisibilityState($userData['communityvisibilitystate']);
        if($fos == true)
        {
            $user->setUsername($userData['personaname'] . '_' . $userData['steamid']);
            $user->setEmail($userData['personaname'] . '@' . $userData['steamid'] . '.fake');
            $user->setPlainPassword($userData['personaname'] . '@' . $userData['steamid'] . '.fake');
            $user->setSlug($userData['steamid']);
            $user->setCreatedAt(new \DateTime("now"));
            $user->addRole('ROLE_USER');
            $user->setEnabled(true);
        }

//        $user->setProfileState(
//            isset($userData['profilestate']) ? $userData['profilestate'] : null
//        );
        $user->setProfileName($userData['personaname']);
        $user->setLastLogOff($userData['lastlogoff']);
//        $user->setCommentPermission(
//            isset($userData['commentpermission']) ? $userData['commentpermission'] : null
//        );
        $user->setProfileUrl($userData['profileurl']);
        $user->setAvatar($userData['avatarfull']);
        $user->setPersonaState($userData['personastate']);
        $user->setPrimaryClanId(
            isset($userData['primaryclanid']) ? $userData['primaryclanid'] : null
        );
        $user->setJoinDate(
            isset($userData['timecreated']) ? $userData['timecreated'] : null
        );
        $user->setCountryCode(
            isset($userData['loccountrycode']) ? $userData['loccountrycode'] : null
        );

        return $user;
    }
}
