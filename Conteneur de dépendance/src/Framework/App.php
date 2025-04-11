<?php

// Déclare l’espace de noms de l'application, ici tout ce qui concerne le framework se trouve sous "Framework".
namespace Framework;

// On importe des classes externes nécessaires pour gérer les requêtes et réponses HTTP.
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * La classe principale de l'application. Elle gère l'exécution des requêtes et la génération des réponses.
 * Elle contient toute la logique de traitement des requêtes HTTP et de gestion des modules.
 */
class App
{
    /**
     * Liste des modules que l'application charge et utilise.
     * Les modules peuvent être des fonctionnalités supplémentaires, comme l'authentification ou un blog.
     * @var array
     */
    private $modules = [];

    /**
     * Le conteneur de services, utilisé pour récupérer les services (ou objets) nécessaires à l'application.
     * Par exemple, ici le routeur ou les modules.
     * @var ContainerInterface
     */
    private $container;

    /**
     * Le constructeur de la classe. Il est appelé quand l'application est lancée.
     * Il reçoit un conteneur d'objets et une liste de modules à charger.
     *
     * @param ContainerInterface $container Le conteneur d'objets.
     * @param string[] $modules Liste des modules à charger.
     */
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        // On initialise le conteneur de services de l'application.
        $this->container = $container;

        // On parcourt tous les modules que l'on souhaite charger.
        // Chaque module est récupéré du conteneur et ajouté à la liste des modules de l'application.
        foreach ($modules as $module) {
            $this->modules[] = $container->get($module);
        }
    }

    /**
     * La méthode principale qui lance l'exécution de l'application pour traiter une requête.
     * Elle reçoit une requête HTTP et renvoie une réponse HTTP.
     *
     * @param ServerRequestInterface $request La requête HTTP envoyée par le client.
     * @return ResponseInterface La réponse HTTP à renvoyer au client.
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        // On récupère le chemin de l'URL demandée par le client.
        $uri = $request->getUri()->getPath();

        // Si l'URL se termine par un slash ("/"), on effectue une redirection vers la même URL sans le slash.
        // Cela permet d'uniformiser les URLs.
        if (!empty($uri) && $uri[-1] === "/") {
            return (new Response())
                ->withStatus(301) // Redirection permanente (301).
                ->withHeader('Location', substr($uri, 0, -1)); // On enlève le slash final de l'URL
        }

        // On récupère le routeur depuis le conteneur de services (c'est ce qui gère les routes de l'application).
        $router = $this->container->get(Router::class);

        // Le routeur tente de faire correspondre la requête à une route existante définie dans l'application.
        // Si aucune route ne correspond, la fonction renverra null.
        $route = $router->match($request);

        // Si aucune route n'a été trouvée, on retourne une erreur 404 (page non trouvée).
        if (is_null($route)) {
            return new Response(404, [], '<h1>Erreur 404</h1>');
        }

        // Une fois qu'on a trouvé une route, on récupère ses paramètres (comme un identifiant, un slug, etc.).
        $params = $route->getParams();

        // On ajoute chaque paramètre de la route à l'objet de la requête sous forme d'attributs.
        // Cela permet de les récupérer facilement dans le contrôleur.
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);

        // On récupère le callback associé à cette route.
        // Un callback peut être un contrôleur ou une fonction à exécuter.
        $callback = $route->getCallback();

        // Si le callback est une chaîne (nom d'une classe ou d'un contrôleur), on le transforme en objet via le conteneur.
        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }

        // On appelle le callback avec la requête en paramètre.
        // La réponse peut être soit une chaîne de texte (HTML), soit un objet de type ResponseInterface.
        $response = call_user_func_array($callback, [$request]);

        // Si la réponse est une chaîne de texte, on crée une réponse HTTP avec ce contenu.
        if (is_string($response)) {
            return new Response(200, [], $response);
        }
        // Si la réponse est déjà un objet de type ResponseInterface, on la retourne telle quelle.
        elseif ($response instanceof ResponseInterface) {
            return $response;
        }
        // Si la réponse n'est ni une chaîne ni un objet ResponseInterface, une erreur est lancée.
        else {
            throw new \Exception('The response is not a string or an instance of ResponseInterface');
        }
    }
}
