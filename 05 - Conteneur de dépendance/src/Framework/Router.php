<?php

// Déclare l’espace de noms de la classe, ici sous le framework.
namespace Framework;

// On importe les classes nécessaires : Route pour définir les routes de l'application,
// ServerRequestInterface pour manipuler les requêtes HTTP et Zend\Expressive\Router pour le routage rapide.
use Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

/**
 * La classe Router est responsable de l'enregistrement et de la correspondance des routes.
 * Elle utilise le routeur FastRoute pour trouver les correspondances de routes.
 */
class Router
{
    /**
     * @var FastRouteRouter
     * Le routeur utilisé pour ajouter et faire correspondre les routes.
     * FastRoute est une bibliothèque rapide de routage HTTP.
     */
    private $router;

    /**
     * Le constructeur de la classe Router.
     * Il initialise un nouvel objet FastRouteRouter.
     * FastRoute permet d’ajouter des routes et de faire correspondre les requêtes HTTP à ces routes.
     */
    public function __construct()
    {
        // Crée une instance de FastRouteRouter, qui sera utilisée pour gérer les routes.
        $this->router = new FastRouteRouter(); 
    }

    /**
     * Enregistre une route de type GET dans le routeur.
     * Cette méthode ajoute une route à l'application en spécifiant le chemin, 
     * le callback (fonction ou contrôleur) et le nom de la route.
     *
     * @param string $path Le chemin de la route (ex : '/home', '/user/{id}').
     * @param string|callable $callable La fonction ou le contrôleur à exécuter lorsque la route est trouvée.
     * @param string $name Le nom de la route, utilisé pour générer des liens vers cette route.
     */
    public function get(string $path, $callable, string $name)
    {
        // Ajoute une nouvelle route GET au routeur avec le chemin, le callback et le nom.
        $this->router->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
    }

    /**
     * Essaie de faire correspondre une requête à une route enregistrée.
     * Si une correspondance est trouvée, elle retourne un objet Route, sinon null.
     *
     * @param ServerRequestInterface $request L'objet requête qui contient l'URL demandée.
     * @return Route|null L'objet Route qui correspond à la requête, ou null si aucune correspondance n’est trouvée.
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        // Appelle la méthode match de FastRouteRouter pour tenter de trouver une correspondance.
        $result = $this->router->match($request);

        // Si la correspondance est réussie (isSuccess() retourne true), on crée et retourne une nouvelle Route.
        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(), // Nom de la route correspondante.
                $result->getMatchedMiddleware(), // Le middleware ou callback associé à la route.
                $result->getMatchedParams() // Les paramètres de la route extraits de l'URL.
            );
        }

        // Si aucune correspondance n'a été trouvée, retourne null.
        return null;
    }

    /**
     * Génère une URL pour une route donnée à partir de son nom et des paramètres.
     * Cette méthode est utilisée pour créer des liens vers des routes spécifiques dans l'application.
     *
     * @param string $name Le nom de la route pour laquelle générer l'URL.
     * @param array $params Les paramètres à inclure dans l'URL (ex : ['id' => 123]).
     * @return string|null L'URL générée ou null si l'URL ne peut pas être générée.
     */
    public function generateUri(string $name, array $params): ?string
    {
        // Utilise la méthode generateUri de FastRouteRouter pour générer l'URL à partir du nom et des paramètres.
        return $this->router->generateUri($name, $params);
    }
}
