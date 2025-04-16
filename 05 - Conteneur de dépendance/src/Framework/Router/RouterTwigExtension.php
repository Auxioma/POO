<?php

// On déclare l’espace de noms : cette classe fait partie du module de routage du framework.
namespace Framework\Router;

// On importe la classe Router pour pouvoir l'utiliser dans cette classe.
use Framework\Router;

/**
 * Cette classe ajoute une extension personnalisée à Twig.
 * Elle permet d'utiliser une fonction appelée "path" directement dans les fichiers Twig.
 * Par exemple : {{ path('article.show', {'id': 12}) }}
 */
class RouterTwigExtension extends \Twig\Extension\AbstractExtension
{
    /**
     * @var Router
     * L’objet Router est utilisé pour générer des URLs à partir du nom d'une route.
     */
    private $router;

    /**
     * Constructeur de l'extension.
     * On injecte ici le routeur pour pouvoir l’utiliser dans Twig.
     *
     * @param Router $router Le routeur de l'application (permet de générer des URLs).
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Cette méthode informe Twig des nouvelles fonctions disponibles dans les vues.
     * Ici, on ajoute une fonction appelée "path", qui appelle notre méthode `pathFor`.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            // On déclare une nouvelle fonction Twig nommée "path" qui appelle la méthode pathFor().
            new \Twig\TwigFunction('path', [$this, 'pathFor'])
        ];
    }

    /**
     * Cette méthode est appelée dans les fichiers Twig quand on écrit {{ path('nom_de_route') }}
     * Elle utilise le routeur pour générer une URL à partir du nom de la route et des paramètres éventuels.
     *
     * @param string $path Le nom de la route à utiliser.
     * @param array $params Paramètres optionnels à inclure dans l'URL.
     * @return string L'URL générée.
     */
    public function pathFor(string $path, array $params = []): string
    {
        return $this->router->generateUri($path, $params);
    }
}
