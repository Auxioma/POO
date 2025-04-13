<?php

// Définition du namespace Framework. Cela permet de regrouper les classes de l'application dans un espace spécifique
// pour éviter les conflits de noms avec d'autres classes dans le projet.
namespace Framework;

// Importation de la classe Response de GuzzleHttp\Psr7, qui est utilisée pour créer des réponses HTTP.
use GuzzleHttp\Psr7\Response;

// Importation de l'interface ResponseInterface de Psr\Http\Message, qui définit les méthodes de base pour gérer une réponse HTTP.
use Psr\Http\Message\ResponseInterface;

// Importation de l'interface ServerRequestInterface de Psr\Http\Message, qui définit les méthodes de base pour traiter une requête HTTP.
use Psr\Http\Message\ServerRequestInterface;

// Définition de la classe App, qui représente l'application principale du framework.
// Cette classe gère le traitement des requêtes HTTP et l'envoi des réponses.
class App
{
    // La méthode run() reçoit une requête HTTP et retourne une réponse HTTP.
    // Elle prend en paramètre un objet ServerRequestInterface $request, qui contient toutes les informations sur la requête entrante.
    // Elle retourne un objet ResponseInterface, qui représente la réponse HTTP que l'application enverra au client.
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        // On récupère le chemin (path) de l'URL de la requête, par exemple "/blog" ou "/articles".
        $uri = $request->getUri()->getPath();

        // Vérification si l'URL se termine par un slash ("/").
        // Si c'est le cas, on redirige l'utilisateur vers la même URL sans le slash final.
        if (!empty($uri) && $uri[-1] === "/") {
            return (new Response()) // Crée une nouvelle instance de Response.
                ->withStatus(301) // Définit le code de statut HTTP à 301 (mouvement permanent), indiquant que l'URL a été redirigée.
                ->withHeader('Location', substr($uri, 0, -1)); // Envoie un en-tête de redirection Location pour retirer le slash.
        }

        // Si l'URL correspond à "/blog", on génère une réponse avec le message "Bienvenue sur le blog".
        if ($uri === '/blog') {
            return new Response(200, [], '<h1>Bienvenue sur le blog</h1>'); // Retourne une réponse 200 OK avec du HTML.
        }

        // Si l'URL ne correspond à aucune route définie, on retourne une réponse d'erreur 404.
        return new Response(404, [], '<h1>Erreur 404</h1>'); // Retourne une réponse 404 Not Found avec un message d'erreur.
    }
}
