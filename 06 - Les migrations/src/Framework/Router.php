<?php

namespace Framework;

use Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

/**
 * Register and match routes
 * Cette classe gère les routes pour l'application, c'est-à-dire qu'elle permet de définir des routes (URL)
 * et d'associer des actions à ces routes. Elle utilise le routeur FastRoute de Zend pour faire correspondre
 * les requêtes HTTP aux bonnes actions ou contrôleurs.
 */
class Router
{
    /**
     * @var FastRouteRouter
     * La propriété `$router` est une instance du routeur FastRoute de Zend,
     * utilisé pour ajouter et faire correspondre les routes.
     */
    private $router;

    public function __construct()
    {
        // Crée une nouvelle instance du routeur FastRoute
        $this->router = new FastRouteRouter();
    }

    /**
     * Enregistre une route de type GET dans le routeur.
     *
     * @param string $path Le chemin de l'URL pour la route (ex : '/blog/{slug}').
     * @param string|callable $callable L'action ou le contrôleur à exécuter pour cette route.
     * @param string $name Le nom unique de la route pour l'identifier plus tard (ex : 'blog.show').
     */
    public function get(string $path, $callable, string $name)
    {
        // Ajoute une nouvelle route au routeur. Cette route sera utilisée pour gérer les requêtes GET.
        // Le nom de la route est défini pour permettre de la retrouver facilement plus tard.
        $this->router->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
    }

    /**
     * Effectue la correspondance d'une requête HTTP avec les routes définies.
     *
     * @param ServerRequestInterface $request L'objet de la requête HTTP.
     * @return Route|null Retourne un objet `Route` si la correspondance est trouvée, sinon `null`.
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        // Le résultat de la correspondance de la requête avec les routes
        $result = $this->router->match($request);

        // Si la correspondance est un succès, on crée et retourne un objet `Route`.
        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),  // Le nom de la route correspondante
                $result->getMatchedMiddleware(),  // Le middleware (action ou contrôleur)
                $result->getMatchedParams()      // Les paramètres de la route
            );
        }

        // Si aucune correspondance n'est trouvée, on retourne null
        return null;
    }

    /**
     * Génère une URL à partir du nom d'une route et des paramètres associés.
     *
     * @param string $name Le nom de la route pour laquelle générer l'URL.
     * @param array $params Les paramètres de la route (ex : ['slug' => 'mon-article']).
     * @return string|null Retourne l'URL générée ou null si la génération échoue.
     */
    public function generateUri(string $name, array $params): ?string
    {
        // Utilise le générateur d'URL du routeur pour générer une URL en fonction du nom de la route et des paramètres.
        return $this->router->generateUri($name, $params);
    }
}
