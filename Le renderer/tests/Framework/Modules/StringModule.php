<?php
// Définition du namespace pour cette classe de test
namespace Tests\Framework\Modules;

/**
 * Classe StringModule - Module de test qui retourne une chaîne de caractères simple
 * Ce module est utilisé pour tester le cas où une route retourne une simple chaîne
 * Le framework devrait alors convertir cette chaîne en objet Response
 */
class StringModule {
    
    /**
     * Constructeur du module de test
     * 
     * @param \Framework\Router $router - Le routeur de l'application injecté par le framework
     */
    public function __construct(\Framework\Router $router)
    {
        // Définition d'une route qui retourne simplement la chaîne "DEMO"
        // Cette route est nommée "demo" et répond à l'URL "/demo"
        $router->get('/demo', function () {
            // Retourne une simple chaîne de caractères
            // Dans la méthode run() de App, cette chaîne sera convertie en objet Response
            return 'DEMO';
        }, 'demo');
    }
}