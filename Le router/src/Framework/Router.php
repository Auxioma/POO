<?php

// Le namespace permet d'organiser ton code et d'éviter les conflits de noms entre classes.
namespace Framework;

// On importe des classes dont on a besoin (comme des outils externes).
use Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

/**
 * Cette classe permet d'enregistrer des routes (URLs) et de retrouver celle qui correspond à une requête.
 */
class Router
{
    /**
     * @var FastRouteRouter
     * On utilise un routeur rapide (de Zend) pour gérer les routes.
     */
    private $router;

    // Le constructeur initialise l'objet routeur (FastRouteRouter) dès que cette classe est utilisée.
    public function __construct()
    {
        $this->router = new FastRouteRouter();
    }

    /**
     * Méthode pour enregistrer une route de type GET.
     * @param string $path Le chemin de l'URL (ex: "/contact")
     * @param callable $callable Le code (fonction) à exécuter quand cette route est utilisée
     * @param string $name Un nom pour identifier cette route (ex: "contact_page")
     */
    public function get(string $path, callable $callable, string $name)
    {
        // On ajoute une route GET au routeur avec le chemin, le code à exécuter, et son nom.
        $this->router->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
    }

    /**
     * Méthode pour trouver la route correspondant à la requête HTTP reçue.
     * @param ServerRequestInterface $request La requête HTTP (ex: un utilisateur qui visite une page)
     * @return Route|null Retourne une instance de Route si une correspondance est trouvée, sinon null.
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        // On essaie de faire correspondre la requête à une route.
        $result = $this->router->match($request);

        // Si une route correspond, on retourne un objet Route avec les infos associées.
        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),      // nom de la route
                $result->getMatchedMiddleware(),     // le code (middleware) à exécuter
                $result->getMatchedParams()          // les paramètres récupérés dans l'URL
            );
        }

        // Si aucune route ne correspond, on retourne null.
        return null;
    }

    /**
     * Méthode pour générer une URL à partir du nom d'une route et de paramètres.
     * @param string $name Le nom de la route
     * @param array $params Les paramètres à insérer dans l'URL
     * @return string|null Retourne l'URL générée, ou null si la route n'existe pas
     */
    public function generateUri(string $name, array $params): ?string
    {
        // On utilise le routeur pour construire une URL basée sur le nom et les paramètres.
        return $this->router->generateUri($name, $params);
    }
}
