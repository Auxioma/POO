<?php

// On place cette classe dans le namespace Framework
namespace Framework;

// On importe la classe Route personnalisée (celle que tu as créée dans Router\Route.php)
use Framework\Router\Route;

// On importe l’interface PSR-7 représentant une requête HTTP (utilisée dans match)
use Psr\Http\Message\ServerRequestInterface;

// On importe le routeur FastRoute de Zend Expressive (il gère le routage réel derrière les coulisses)
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

/**
 * Classe Router
 * Elle permet d’enregistrer des routes et de faire le lien entre une requête et la bonne action à exécuter.
 */
class Router
{
    /**
     * Le routeur utilisé en interne (basé sur FastRoute)
     * Il gère la logique de correspondance des routes
     * @var FastRouteRouter
     */ 
    private $router;

    /**
     * Constructeur de la classe Router
     * À l’instanciation, on crée un objet FastRouteRouter (c’est lui qui fait le "vrai" travail de routage)
     */
    public function __construct()
    {
        $this->router = new FastRouteRouter();
    }

    /**
     * Enregistre une route de type GET
     *
     * @param string $path Le chemin de l’URL (ex: "/blog/{slug}")
     * @param callable $callable La fonction ou méthode à exécuter quand l’URL est atteinte
     * @param string $name Le nom unique de la route (utile pour la génération d’URL)
     */
    public function get(string $path, callable $callable, string $name)
    {
        // On crée une nouvelle route avec la méthode GET grâce à la classe de Zend
        $this->router->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
    }

    /**
     * Permet de savoir si une requête HTTP correspond à une route enregistrée
     *
     * @param ServerRequestInterface $request La requête HTTP reçue (contenant l'URL, la méthode GET/POST, etc.)
     * @return Route|null Retourne un objet Route personnalisé si une route correspond, ou null sinon
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        // On demande à Zend de vérifier si la requête correspond à une route
        $result = $this->router->match($request);

        // Si oui, on retourne notre propre objet Route contenant les infos nécessaires
        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),      // Le nom de la route trouvée
                $result->getMatchedMiddleware(),     // La fonction à exécuter
                $result->getMatchedParams()          // Les paramètres extraits de l’URL (slug, id, etc.)
            );
        }

        // Si aucune route ne correspond, on retourne null (donc 404 plus tard dans App.php)
        return null;
    }

    /**
     * Génère une URL à partir du nom d’une route et de ses paramètres
     * Très utile pour les liens dans les vues : au lieu d’écrire manuellement l’URL, on la génère dynamiquement
     *
     * @param string $name Le nom de la route (ex: "post.show")
     * @param array $params Les paramètres à insérer dans l’URL (ex: ['slug' => 'mon-article', 'id' => 18])
     * @return string|null L’URL générée (ex: "/blog/mon-article-18")
     */
    public function generateUri(string $name, array $params): ?string
    {
        return $this->router->generateUri($name, $params);
    }
}
