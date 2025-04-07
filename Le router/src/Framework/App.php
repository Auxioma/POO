<?php

// Le namespace permet de structurer ton code PHP et éviter les conflits entre classes.
namespace Framework;

// On importe les classes dont on a besoin.
use GuzzleHttp\Psr7\Response; // Pour créer des réponses HTTP (statut, corps, en-têtes)
use Psr\Http\Message\ResponseInterface; // Interface standard que nos réponses doivent respecter
use Psr\Http\Message\ServerRequestInterface; // Interface représentant une requête HTTP (GET, POST, etc.)

/**
 * La classe App est le cœur de ton application web.
 * Elle s’occupe de :
 * - charger les modules,
 * - gérer les routes,
 * - exécuter les contrôleurs,
 * - retourner une réponse HTTP.
 */
class App
{

    /**
     * Liste des modules chargés dans l'application (chaque module peut définir ses propres routes).
     * @var array
     */
    private $modules = [];

    /**
     * Le routeur de l'application, chargé de faire correspondre les URLs aux bonnes actions.
     * @var Router
     */
    private $router;

    /**
     * Constructeur de l'application
     * @param string[] $modules Liste des modules à charger dynamiquement (ex: BlogModule, AuthModule…)
     */
    public function __construct(array $modules = [])
    {
        // On initialise le routeur
        $this->router = new Router();

        // On parcourt la liste des modules passés en paramètre
        foreach ($modules as $module) {
            // On instancie chaque module avec le routeur (pour qu’il puisse enregistrer ses routes)
            $this->modules[] = new $module($this->router);
        }
    }

    /**
     * Fonction principale qui exécute l'application
     * @param ServerRequestInterface $request La requête HTTP reçue (ex: GET /blog)
     * @return ResponseInterface La réponse HTTP à envoyer au client
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        // On récupère l'URL demandée (ex: "/blog/mon-article")
        $uri = $request->getUri()->getPath();

        // Si l’URL se termine par un slash ("/"), on redirige sans le slash (bonne pratique SEO)
        if (!empty($uri) && $uri[-1] === "/") {
            // Redirection 301 vers l'URL sans le slash final
            return (new Response())
                ->withStatus(301) // Code de redirection permanente
                ->withHeader('Location', substr($uri, 0, -1)); // Nouvelle URL
        }

        // On demande au routeur de trouver une route qui correspond à la requête
        $route = $this->router->match($request);

        // Si aucune route ne correspond, on retourne une erreur 404
        if (is_null($route)) {
            return new Response(404, [], '<h1>Erreur 404</h1>');
        }

        // On récupère les paramètres extraits de l’URL (ex: slug, id…)
        $params = $route->getParams();

        // On ajoute ces paramètres à la requête sous forme d’attributs accessibles dans les contrôleurs
        // array_reduce va ajouter chaque paramètre un par un dans la requête
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);

        // On exécute la fonction (callback) associée à la route
        // Généralement, c’est un contrôleur qui prend la requête en paramètre
        $response = call_user_func_array($route->getCallback(), [$request]);

        // Si la réponse est une chaîne de caractères, on crée une réponse HTTP avec code 200
        if (is_string($response)) {
            return new Response(200, [], $response);
        }
        // Si c’est déjà une réponse HTTP valide, on la retourne telle quelle
        elseif ($response instanceof ResponseInterface) {
            return $response;
        }
        // Si le type de retour est inconnu, on lève une exception
        else {
            throw new \Exception('The response is not a string or an instance of ResponseInterface');
        }
    }
}
