<?php

// Le namespace Tests\Framework permet de regrouper les tests dans un espace spécifique, ici sous 'Tests\Framework'.
// Cela permet de séparer les tests du code principal de l'application.
namespace Tests\Framework;

// Importation de la classe App du framework, qui contient la logique de l'application à tester.
use Framework\App;

// Importation de la classe ServerRequest de GuzzleHttp\Psr7, utilisée pour simuler une requête HTTP dans les tests.
use GuzzleHttp\Psr7\ServerRequest;

// Importation de la classe TestCase de PHPUnit, qui permet de créer des tests unitaires.
// TestCase contient des méthodes d'assertion pour vérifier le comportement du code testé.
use PHPUnit\Framework\TestCase;

// Définition de la classe de test AppTest qui étend TestCase de PHPUnit.
// Chaque méthode dans cette classe est un test qui vérifie une fonctionnalité de l'application.
class AppTest extends TestCase {

    // Test pour vérifier si l'application redirige correctement les URL avec un slash final.
    public function testRedirectTrailingSlash() {
        // Création d'une instance de l'application.
        $app = new App();
        
        // Création d'une requête HTTP GET vers l'URL '/demoslash/' (avec un slash final).
        $request = new ServerRequest('GET', '/demoslash/');
        
        // Exécution de la requête via l'application pour obtenir la réponse.
        $response = $app->run($request);
        
        // Vérification que l'en-tête 'Location' dans la réponse contient l'URL sans le slash final.
        // Cela valide que l'application redirige correctement l'URL.
        $this->assertContains('/demoslash', $response->getHeader('Location'));
        
        // Vérification que le code de statut HTTP est 301, ce qui indique une redirection permanente.
        $this->assertEquals(301, $response->getStatusCode());
    }

    // Test pour vérifier si la route "/blog" retourne la bonne réponse.
    public function testBlog() {
        // Création d'une instance de l'application.
        $app = new App();
        
        // Création d'une requête HTTP GET vers l'URL '/blog'.
        $request = new ServerRequest('GET', '/blog');
        
        // Exécution de la requête via l'application pour obtenir la réponse.
        $response = $app->run($request);
        
        // Vérification que le corps de la réponse contient le texte HTML attendu pour la page du blog.
        $this->assertStringContainsString('<h1>Bienvenue sur le blog</h1>', (string)$response->getBody());
        
        // Vérification que le code de statut HTTP est 200, ce qui indique une réponse réussie.
        $this->assertEquals(200, $response->getStatusCode());
    }

    // Test pour vérifier si la route non définie renvoie une erreur 404.
    public function testError404() {
        // Création d'une instance de l'application.
        $app = new App();
        
        // Création d'une requête HTTP GET vers une URL inexistante '/aze'.
        $request = new ServerRequest('GET', '/aze');
        
        // Exécution de la requête via l'application pour obtenir la réponse.
        $response = $app->run($request);
        
        // Vérification que le corps de la réponse contient le texte HTML attendu pour une erreur 404.
        $this->assertStringContainsString('<h1>Erreur 404</h1>', (string)$response->getBody());
        
        // Vérification que le code de statut HTTP est 404, ce qui indique que la page n'a pas été trouvée.
        $this->assertEquals(404, $response->getStatusCode());
    }
}