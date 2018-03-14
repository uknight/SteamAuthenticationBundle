
# SteamAuthenticationBundle
A Symfony4 Bundle that provides authentication via Steam for your application.
For now there is no Flex recipe so you have to create the config by hand.

## Installation & Configuration
At first, require the bundle via Composer and add it to your `config/bundles.php` file.
`composer require knojector/steam-authentication-bundle`

----------

Create the file `knojector_steam_authentication` in the `config/packages` directory and add the following content to it.
```yml
knojector_steam_auth:
    api_key: ReplaceWithYouKey # https://steamcommunity.com/dev/apikey
    login_route: RouteName # The route the user is redirected to after Steam Login
    login_redirect: RouteName # The route the user is redirected to if the login was successfull
    user_class: App\Entity\User # Classname of your User Entity
```
----------
Make your User Entity extend from the `Knojector\SteamAuthenticationBundle\User\AbstractSteamUser` class
```php
<?php

namespace App\Entity;

use Knojector\SteamAuthenticationBundle\User\AbstractSteamUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Knojector <dev@404-labs.xyz>
 *
 * @ORM\Entity()
 */
class User extends AbstractSteamUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->roles = [];
    }
}
```


----------

Finally you just have to configure your firewall. A working example might look like this
```yaml
security:
    providers:
        steam_user_provider:
            id: Knojector\SteamAuthenticationBundle\Security\User\SteamUserProvider
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            anonymous: ~
            pattern: ^/
            provider: steam_user_provider
            steam: true
            logout:
                path:   /logout
                target: /

```

----------

To display the "Login via Steam" button simply include this snippet in your template
```twig
{% include '@KnojectorSteamAuthentication/login.html.twig' %}
```
