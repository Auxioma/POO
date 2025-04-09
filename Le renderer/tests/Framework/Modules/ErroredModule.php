<?php
// Définition du namespace pour cette classe de test
namespace Tests\Framework\Modules;

/**
 * Classe ErroredModule - Module spécialement conçu pour tester la gestion des erreurs
 * Ce module crée volontairement une situation d'erreur où une route retourne un objet
 * alors que le framework attend soit une chaîne de caractères, soit un objet ResponseInterface
 */
class ErroredModule {
    
    /**
     * Constructeur du module de test
     * 
     * @param \Framework\Router $router - Le routeur de l'application injecté par le framework
     */
    public function __construct(\Framework\Router $router)
    {
        // Définition d'une route qui génère intentionnellement une erreur
        // Cette route retourne un objet stdClass qui n'est ni une chaîne ni un ResponseInterface
        // Cela devrait provoquer une exception dans la méthode run() de la classe App
        $router->get('/demo', function () {
            // Retourne un objet standard vide, ce qui est un type de retour non supporté
            return new \stdClass();
        }, 'demo');
    }
}