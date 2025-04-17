<?php
// Déclaration du namespace pour l'organisation du code
namespace Framework\Router;

/**
 * Classe Route
 * Représente une route qui a été trouvée lors de la correspondance d'une URL
 */
class Route
{
    /**
     * Le nom de la route (ex: 'blog.index')
     * @var string
     */
    private $name;

    /**
     * Le callback associé à la route
     * Ce callback est la fonction à exécuter si la route est trouvée
     * En général, c’est un tableau contenant un contrôleur et une méthode
     * Exemple : [$this, 'index']
     * @var callable
     */
    private $callback;

    /**
     * Paramètres extraits de l’URL (ex: slug ou ID dans /blog/{slug})
     * @var array
     */
    private $parameters;

    /**
     * Constructeur : initialise la route avec ses propriétés
     *
     * @param string   $name       Le nom de la route
     * @param callable $callback   Le contrôleur associé
     * @param array    $parameters Les paramètres extraits de l'URL
     */
    public function __construct(string $name, callable $callback, array $parameters)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->parameters = $parameters;
    }

    /**
     * Retourne le nom de la route
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Retourne le callback (ex: une méthode d’un contrôleur)
     *
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * Retourne les paramètres capturés dans l’URL (ex: ['slug' => 'mon-article'])
     *
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
}
