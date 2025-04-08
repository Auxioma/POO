<?php
// Déclaration d'un espace de noms. Cela permet d’organiser le code PHP en groupes logiques.
namespace Framework;

// Importation de classes externes nécessaires à notre classe Router.
use Framework\Router\Route; // Notre propre classe Route
use Psr\Http\Message\ServerRequestInterface; // Interface standard PSR-7 pour une requête HTTP
use Laminas\Router\Http\TreeRouteStack; // Routeur fourni par Laminas
use Laminas\Router\Http\Literal; // Type de route "fixe"
use Laminas\Router\Http\Segment; // Type de route "avec paramètres"
use Laminas\Http\PhpEnvironment\Request as LaminasRequest; // Représente une requête HTTP pour Laminas
use Laminas\Router\RouteMatch; // Résultat d'une tentative de correspondance avec une route

/**
 * Classe Router : elle permet d’enregistrer des routes et de retrouver celle correspondant à une requête HTTP.
 */
class Router
{
    /**
     * @var TreeRouteStack
     * Le routeur principal qui contient toutes les routes définies.
     */
    private TreeRouteStack $router;
    
    /**
     * @var array
     * Tableau qui contient les routes définies avec leurs informations (chemin, fonction à exécuter, etc.)
     */
    private array $routes = [];

    /**
     * Constructeur appelé automatiquement quand on crée un objet Router.
     * Il initialise l'objet routeur de Laminas.
     */
    public function __construct()
    {
        $this->router = new TreeRouteStack();
    }

    /**
     * Enregistre une nouvelle route HTTP de type GET.
     *
     * @param string $path Le chemin de l’URL (ex : "/contact")
     * @param callable $callable La fonction à exécuter quand cette URL est appelée
     * @param string $name Le nom de la route (utile pour la retrouver ou générer des URLs)
     */
    public function get(string $path, callable $callable, string $name): void
    {
        // On sauvegarde la route dans notre tableau interne
        $this->routes[$name] = [
            'path' => $path,
            'callback' => $callable,
            'method' => 'GET'
        ];

        // Si l’URL contient des paramètres dynamiques (comme {id})
        if (strpos($path, '{') !== false) {
            // On transforme le format {param} en :param (format attendu par Laminas)
            $laminasPath = preg_replace('/{([^}]+)}/', ':$1', $path);
            
            // On enregistre la route comme un "Segment" (route dynamique)
            $this->router->addRoute($name, [
                'type' => Segment::class,
                'options' => [
                    'route' => $laminasPath,
                    'defaults' => [
                        'method' => 'GET'
                    ],
                ],
            ]);
        } else {
            // Si l’URL ne contient pas de paramètres, on la considère comme une route "fixe"
            $this->router->addRoute($name, [
                'type' => Literal::class,
                'options' => [
                    'route' => $path,
                    'defaults' => [
                        'method' => 'GET'
                    ],
                ],
            ]);
        }
    }

    /**
     * Permet de trouver la route correspondant à la requête HTTP reçue.
     *
     * @param ServerRequestInterface $request L’objet représentant la requête HTTP reçue (ex: un clic utilisateur)
     * @return Route|null Retourne une route correspondante ou null si aucune n’est trouvée.
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        // Convertit la requête au format Laminas (car notre routeur utilise Laminas)
        $laminasRequest = new LaminasRequest();
        $laminasRequest->setUri($request->getUri()->getPath()); // Chemin de l’URL
        $laminasRequest->setMethod($request->getMethod()); // Méthode HTTP (GET, POST, etc.)
       
        // Recherche d’une route qui correspond à cette requête
        $result = $this->router->match($laminasRequest);
    
        // Si une correspondance est trouvée
        if ($result instanceof RouteMatch) {
            // Récupère le nom de la route trouvée
            $routeName = $result->getMatchedRouteName();
            
            // Récupère la fonction associée à cette route
            $callback = $this->routes[$routeName]['callback'] ?? null;
            
            // Si aucun callback n’a été défini, on retourne null
            if (!$callback) {
                return null;
            }
            
            // Récupère les paramètres passés dans l’URL
            $params = $result->getParams();
            
            // On enlève certains paramètres ajoutés automatiquement par Laminas
            unset($params['controller'], $params['action'], $params['method']);
           
            // On crée et retourne une nouvelle instance de Route
            return new Route(
                $routeName,
                $callback,
                $params
            );
        }
       
        // Si aucune route ne correspond à la requête, on retourne null
        return null;
    }

    /**
     * Génère une URL à partir du nom d'une route et de ses paramètres éventuels.
     *
     * @param string $name Le nom de la route (défini dans get())
     * @param array $params Les paramètres dynamiques à insérer dans l’URL
     * @return string|null Retourne l’URL générée, ou null si la route n’existe pas
     */
    public function generateUri(string $name, array $params = []): ?string
    {
        try {
            // Utilise le routeur Laminas pour générer automatiquement l’URL
            return $this->router->assemble($params, ['name' => $name]);
        } catch (\Exception $e) {
            // Si l’URL ne peut pas être générée automatiquement, on essaie manuellement
            if (!isset($this->routes[$name])) {
                return null;
            }

            $path = $this->routes[$name]['path'];

            // Remplace chaque paramètre dynamique dans l’URL
            foreach ($params as $paramName => $paramValue) {
                $path = str_replace("{{$paramName}}", $paramValue, $path);
            }

            return $path;
        }
    }
}