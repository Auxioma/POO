<?php
// Définition du namespace pour cette classe
namespace Framework;

// Importation des classes nécessaires
use Framework\Router\Route;                 // Notre classe Route qui contient les informations d'une route correspondante
use Psr\Http\Message\ServerRequestInterface;    // Interface standardisée pour les requêtes HTTP
use Zend\Expressive\Router\FastRouteRouter;     // Implémentation du routeur FastRoute de Zend/Laminas
use Zend\Expressive\Router\Route as ZendRoute;  // La classe Route de Zend, renommée en ZendRoute pour éviter la confusion

/**
 * Classe Router - Enregistre et trouve les correspondances des routes
 * Cette classe est une façade (wrapper) autour du routeur FastRoute de Zend/Laminas
 * Elle simplifie l'utilisation du routeur et adapte son interface à notre application
 */
class Router
{
    /**
     * L'instance du routeur FastRoute de Zend/Laminas
     * @var FastRouteRouter
     */
    private $router;
    
    /**
     * Constructeur du Router
     * Initialise le routeur FastRoute de Zend/Laminas
     */
    public function __construct()
    {
        $this->router = new FastRouteRouter();
    }
    
    /**
     * Ajoute une route qui répond aux requêtes GET
     * 
     * @param string $path - Le chemin URL de la route (ex: /blog/{id})
     * @param callable $callable - La fonction/méthode à appeler quand cette route correspond
     * @param string $name - Un nom unique pour identifier cette route
     */
    public function get(string $path, callable $callable, string $name)
    {
        // Crée et ajoute une nouvelle route au routeur
        // Le tableau ['GET'] spécifie que cette route ne répond qu'aux requêtes GET
        $this->router->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
    }
    
    /**
     * Trouve une route correspondant à la requête HTTP
     * 
     * @param ServerRequestInterface $request - La requête HTTP
     * @return Route|null - Un objet Route si une correspondance est trouvée, null sinon
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        // Demande au routeur Zend de trouver une correspondance pour cette requête
        $result = $this->router->match($request);
        
        // Si une route correspondante a été trouvée
        if ($result->isSuccess()) {
            // Crée et retourne notre propre objet Route avec les informations de la route correspondante
            return new Route(
                $result->getMatchedRouteName(),       // Le nom de la route
                $result->getMatchedMiddleware(),      // La fonction/méthode à appeler
                $result->getMatchedParams()           // Les paramètres extraits de l'URL
            );
        }
        
        // Aucune route correspondante n'a été trouvée
        return null;
    }
    
    /**
     * Génère une URL à partir du nom d'une route et de paramètres
     * Utile pour créer des liens dans les vues sans coder en dur les URLs
     * 
     * @param string $name - Le nom de la route
     * @param array $params - Les paramètres à insérer dans l'URL
     * @return string|null - L'URL générée ou null si la route n'existe pas
     */
    public function generateUri(string $name, array $params): ?string
    {
        // Utilise le routeur Zend pour générer l'URL
        return $this->router->generateUri($name, $params);
    }
}