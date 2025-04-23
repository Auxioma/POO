<?php
namespace App\Auth;

use Framework\Auth;

class AuthTwigExtension extends \Twig\Extension\AbstractExtension
{

    /**
     * @var Auth
     */
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function getFunctions()
    {
        return [
            new \Twig\TwigFunction('current_user', [$this->auth, 'getUser'])
        ];
    }
}
