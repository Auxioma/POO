<?php

// On définit l'espace de nom de la classe Router. Cela permet d'organiser le code
// et d'éviter les conflits de noms avec d'autres classes portant le même nom ailleurs.
namespace Framework;

// On importe des classes nécessaires à notre routeur :
// - Route de notre propre framework
// - L'interface de la requête HTTP PSR-7
// - FastRouteRouter pour la gestion des routes
// - ZendRoute qui représente une route spécifique
use Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

/**
 * La classe Router gère toutes les routes de l'application.
 * Elle permet d'enregistrer des routes, de faire correspondre une URL à une action,
 * et de générer des URLs à partir de noms de routes.
 */
class Router
{
    /**
     * Le routeur principal basé sur FastRoute (via Zend Expressive).
     * C’est lui qui va vraiment s’occuper d’analyser les URLs et leurs paramètres.
     *
     * @var FastRouteRouter
     */
    private $router;

    /**
     * Le constructeur initialise le routeur.
     * On utilise ici le système FastRoute qui est rapide et simple à configurer.
     */
    public function __construct()
    {
        $this->router = new FastRouteRouter();
    }

    /**
     * Enregistre une nouvelle route HTTP de type GET dans le routeur.
     * Une route est définie par son chemin, une fonction à exécuter, et un nom unique.
     *
     * @param string   $path      L’URL à intercepter (ex: '/blog')
     * @param callable $callable  La fonction ou méthode à appeler si l'URL correspond
     * @param string   $name      Le nom unique donné à cette route
     */
    public function get(string $path, callable $callable, string $name)
    {
        // On crée une nouvelle route en précisant :
        // - le chemin
        // - la fonction à exécuter
        // - la méthode HTTP acceptée (GET ici)
        // - le nom de la route pour la retrouver plus tard
        $this->router->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
    }

    /**
     * Tente de faire correspondre une requête HTTP avec une des routes enregistrées.
     * Si une correspondance est trouvée, on retourne une instance de notre propre classe Route.
     * Sinon, on retourne null.
     *
     * @param ServerRequestInterface $request La requête HTTP à analyser
     * @return Route|null                      L’objet Route correspondant ou null si rien ne correspond
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        // On demande à FastRoute s’il trouve une route correspondant à la requête
        $result = $this->router->match($request);

        // Si une route correspond bien à l’URL, on la transforme en objet Route (propre à notre framework)
        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),      // Nom de la route
                $result->getMatchedMiddleware(),     // Fonction/méthode à exécuter
                $result->getMatchedParams()          // Paramètres extraits de l'URL (ex : slug, id)
            );
        }

        // Si aucune route ne correspond à la requête, on retourne null (erreur 404 généralement)
        return null;
    }

    /**
     * Génère une URL à partir du nom d'une route et des paramètres nécessaires.
     * Cela permet de créer des liens dans les vues sans écrire les URLs en dur.
     *
     * @param string $name    Le nom de la route (défini lors de l’enregistrement)
     * @param array  $params  Les paramètres dynamiques à insérer dans l’URL
     * @return string|null    L’URL générée ou null si la route n’existe pas
     */
    public function generateUri(string $name, array $params): ?string
    {
        // On utilise FastRoute pour construire l'URL complète à partir du nom et des paramètres.
        return $this->router->generateUri($name, $params);
    }
}
