<?php


namespace App\Basket\Twig;

use App\Basket\Basket;

class BasketTwigExtension extends \Twig\Extension\AbstractExtension
{

    /**
     * @var Basket
     */
    private $basket;

    public function __construct(Basket $basket)
    {
        $this->basket = $basket;
    }

    public function getFunctions()
    {
        return [
          new \Twig\TwigFunction('basket_count', [$this->basket, 'count'])
        ];
    }
}
