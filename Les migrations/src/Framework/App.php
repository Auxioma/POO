<?php

namespace Framework;

use GuzzleHttp\Psr7\Response;  // Utilisation de la classe Response de Guzzle pour gérer les réponses HTTP
use Psr\Container\ContainerInterface;  // Interface pour le conteneur de services (pour l'injection de dépendances)
use Psr\Http\Message\ResponseInterface;  // Interface représentant la réponse HTTP
use Psr\Http\Message\ServerRequestInterface;  // Interface représentant la requête HTTP

class App
{
    /**
     * Liste des modules à charger
     * @var array
     */
    private $modules = [];

    /**
     * Conteneur d'injection de dépendances
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructeur de l'application.
     * Ce constructeur prend un conteneur (pour l'injection de dépendances) et une liste de modules à charger.
     *
     * @param ContainerInterface $container - Le conteneur d'injection de dépendances.
     * @param string[] $modules - Liste des modules à charger.
     */
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->container = $container;  // Sauvegarde du conteneur pour l'utiliser plus tard

        // Boucle pour ajouter les modules dans l'application à partir du conteneur
        foreach ($modules as $module) {
            $this->modules[] = $container->get($module);  // Récupère le module depuis le conteneur
        }
    }

    /**
     * Exécute l'application et retourne une réponse.
     * Cette méthode gère le flux principal de l'application, y compris le routage et la génération de la réponse.
     *
     * @param ServerRequestInterface $request - La requête HTTP entrante
     * @return ResponseInterface - La réponse HTTP générée
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        // Récupère l'URI de la requête (c'est-à-dire le chemin de la demande)
        $uri = $request->getUri()->getPath();

        // Si l'URI se termine par un "/", on redirige vers la même URI sans le "/"
        if (!empty($uri) && $uri[-1] === "/") {
            return (new Response())  // Crée une nouvelle réponse
                ->withStatus(301)  // Statut 301 pour indiquer une redirection permanente
                ->withHeader('Location', substr($uri, 0, -1));  // Redirige sans le "/"
        }

        // Récupère le routeur à partir du conteneur
        $router = $this->container->get(Router::class);

        // Tente de faire correspondre la requête avec une route définie
        $route = $router->match($request);

        // Si aucune route n'est trouvée, retourne une réponse 404 (Page non trouvée)
        if (is_null($route)) {
            return new Response(404, [], '<h1>Erreur 404</h1>');
        }

        // Récupère les paramètres associés à la route
        $params = $route->getParams();

        // Ajoute chaque paramètre à la requête, en utilisant `withAttribute()` pour stocker les paramètres dans la requête
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);

        // Récupère la fonction de rappel (callback) associée à cette route
        $callback = $route->getCallback();

        // Si le callback est une chaîne de caractères (un nom de classe ou de fonction), récupère l'objet du conteneur
        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }

        // Exécute le callback avec la requête et récupère la réponse
        $response = call_user_func_array($callback, [$request]);

        // Si la réponse est une chaîne, crée une réponse HTTP 200 avec cette chaîne comme corps
        if (is_string($response)) {
            return new Response(200, [], $response);
        } 
        // Si la réponse est déjà une instance de `ResponseInterface`, la retourne directement
        elseif ($response instanceof ResponseInterface) {
            return $response;
        } 
        // Si la réponse n'est ni une chaîne ni un `ResponseInterface`, lance une exception
        else {
            throw new \Exception('The response is not a string or an instance of ResponseInterface');
        }
    }

    /**
     * Récupère le conteneur d'injection de dépendances
     *
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
