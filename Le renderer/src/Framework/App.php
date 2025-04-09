<?php
// Définition du namespace pour cette classe
namespace Framework;

// Importation des classes nécessaires
use GuzzleHttp\Psr7\Response;                   // Classe pour créer des réponses HTTP
use Psr\Http\Message\ResponseInterface;         // Interface PSR-7 pour les réponses HTTP
use Psr\Http\Message\ServerRequestInterface;    // Interface PSR-7 pour les requêtes HTTP

/**
 * Classe App - Point d'entrée principal de l'application
 * Cette classe coordonne les différents modules et gère le cycle de vie de la requête
 */
class App
{
    /**
     * Liste des modules chargés dans l'application
     * @var array
     */
    private $modules = [];
    
    /**
     * Instance du routeur qui gère les routes de l'application
     * @var Router
     */
    private $router;
    
    /**
     * Constructeur de l'application
     * 
     * @param string[] $modules - Liste des classes de modules à instancier
     * @param array $dependencies - Dépendances à injecter dans les modules (comme le renderer)
     */
    public function __construct(array $modules = [], array $dependencies = [])
    {
        // Création d'une instance du routeur
        $this->router = new Router();
        
        // Si un renderer est fourni dans les dépendances, on y ajoute le routeur
        // comme variable globale pour qu'il soit accessible dans toutes les vues
        if (array_key_exists('renderer', $dependencies)) {
            $dependencies['renderer']->addGlobal('router', $this->router);
        }
        
        // Instanciation de chaque module en lui passant le routeur et le renderer
        foreach ($modules as $module) {
            // Crée dynamiquement une instance de la classe du module (ex: new BlogModule())
            // et passe le routeur et le renderer en paramètres du constructeur
            $this->modules[] = new $module($this->router, $dependencies['renderer']);
        }
    }
    
    /**
     * Exécute l'application pour une requête donnée
     * 
     * @param ServerRequestInterface $request - La requête HTTP à traiter
     * @return ResponseInterface - La réponse HTTP à renvoyer au client
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        // Récupère le chemin de l'URL (ex: /blog/article)
        $uri = $request->getUri()->getPath();
        
        // Si l'URI se termine par un slash, on redirige vers l'URI sans le slash final
        // C'est une bonne pratique pour éviter les duplications de contenu
        if (!empty($uri) && $uri[-1] === "/") {
            return (new Response())
                ->withStatus(301)                                // Code HTTP 301 (redirection permanente)
                ->withHeader('Location', substr($uri, 0, -1));   // Redirection vers l'URI sans le slash
        }
        
        // Recherche d'une route correspondant à la requête
        $route = $this->router->match($request);
        
        // Si aucune route n'a été trouvée, on renvoie une erreur 404
        if (is_null($route)) {
            return new Response(404, [], '<h1>Erreur 404</h1>');
        }
        
        // Récupération des paramètres de la route (ex: {id} => 42)
        $params = $route->getParams();
        
        // Ajout des paramètres comme attributs de la requête
        // Cela permet d'accéder aux paramètres via $request->getAttribute('nom_param')
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        
        // Appel de la fonction/méthode associée à la route avec la requête en paramètre
        $response = call_user_func_array($route->getCallback(), [$request]);
       
        // Gestion de différents types de réponses
        if (is_string($response)) {
            // Si la réponse est une chaîne, on la transforme en objet Response avec le statut 200 (OK)
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            // Si la réponse est déjà un objet Response, on le retourne tel quel
            return $response;
        } else {
            // Si le type de réponse n'est pas géré, on lance une exception
            throw new \Exception('The response is not a string or an instance of ResponseInterface');
        }
    }
}