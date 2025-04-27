<?php
namespace Framework\Actions;

// Importation des classes nécessaires pour gérer une réponse HTTP.
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Trait RouterAwareAction
 * 
 * Ce trait ajoute des méthodes liées à l'utilisation du routeur.
 * Il est conçu pour être utilisé dans des classes qui nécessitent
 * des fonctionnalités de routage, comme la redirection.
 * 
 * @package Framework\Actions
 */
trait RouterAwareAction
{
    /**
     * Renvoie une réponse de redirection.
     * 
     * Cette méthode permet de rediriger l'utilisateur vers une autre route.
     * Elle génère une URL en utilisant le routeur, puis configure une réponse
     * HTTP avec un code de statut 301 (redirection permanente) et un en-tête `location`.
     * 
     * @param string $path Nom de la route vers laquelle rediriger.
     * @param array $params Paramètres pour la génération de l'URL (par exemple, ID, slug).
     * @return ResponseInterface Instance de la réponse HTTP correspondant à la redirection.
     */
    public function redirect(string $path, array $params = []): ResponseInterface
    {
        // Génère l'URL de redirection en utilisant le routeur et les paramètres fournis.
        $redirectUri = $this->router->generateUri($path, $params);

        // Crée une nouvelle réponse HTTP avec un statut 301 et ajoute l'en-tête `location`.
        return (new Response())
            ->withStatus(301) // Redirection permanente
            ->withHeader('location', $redirectUri);
    }
}