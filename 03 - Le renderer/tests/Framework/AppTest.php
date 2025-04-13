<?php
// Ligne 1 : Ouvre un bloc de code PHP

// Ligne 2 : Déclaration du namespace (espace de noms) du fichier de test
// Cela permet d'organiser ton code et d'éviter les conflits entre classes ayant le même nom
namespace Tests\Framework;

// Les lignes suivantes importent les classes dont on aura besoin dans ce fichier :

use App\Blog\BlogModule; // On utilise le module Blog pour tester les routes de blog
use Framework\App; // La classe principale de ton application (le noyau du framework)
use GuzzleHttp\Psr7\ServerRequest; // Classe qui permet de créer des requêtes HTTP factices pour les tests
use PHPUnit\Framework\TestCase; // Classe de base pour tous les tests avec PHPUnit
use Psr\Http\Message\ResponseInterface; // Interface pour représenter les réponses HTTP
use Tests\Framework\Modules\ErroredModule; // Module qui simule une erreur pour tester la gestion des exceptions
use Tests\Framework\Modules\StringModule; // Module qui retourne simplement une chaîne, pour tester la conversion en réponse

// Déclaration de la classe de test, qui hérite de TestCase (classe fournie par PHPUnit)
class AppTest extends TestCase {

    /**
     * Test : Redirection si une URL se termine par un slash (ex : /demoslash/)
     * On attend que l'application redirige vers /demoslash sans slash à la fin
     */
    public function testRedirectTrailingSlash() {
        $app = new App(); // On crée une instance de l'application (sans modules ici)
        $request = new ServerRequest('GET', '/demoslash/'); // On simule une requête GET sur l'URL "/demoslash/"
        $response = $app->run($request); // On exécute l'application avec cette requête

        // On vérifie que l'en-tête "Location" contient bien "/demoslash"
        $this->assertContains('/demoslash', $response->getHeader('Location'));
        // On vérifie que le code de réponse HTTP est bien 301 (redirection permanente)
        $this->assertEquals(301, $response->getStatusCode());
    }

    /**
     * Test : Vérifie que les routes du module Blog fonctionnent
     */
    public function testBlog() {
        // On instancie l'application avec le module Blog
        $app = new App([
            BlogModule::class
        ]);

        // Test de la page principale du blog
        $request = new ServerRequest('GET', '/blog'); // Simule une requête sur "/blog"
        $response = $app->run($request); // Exécute l'application

        // On vérifie que le contenu HTML attendu est présent dans la réponse
        $this->assertContains('<h1>Bienvenue sur le blog</h1>', (string)$response->getBody());
        // Le code HTTP attendu est 200 (OK)
        $this->assertEquals(200, $response->getStatusCode());

        // Test d’un article de blog spécifique
        $requestSingle = new ServerRequest('GET', '/blog/article-de-test');
        $responseSingle = $app->run($requestSingle);
        // Vérifie que le contenu de l’article est bien présent
        $this->assertContains('<h1>Bienvenue sur l\'article article-de-test</h1>', (string)$responseSingle->getBody());
    }

    /**
     * Test : Lève une exception si une route ne retourne rien
     */
    public function testThrowExceptionIfNoResponseSent () {
        // On crée une application avec un module qui provoque une erreur
        $app = new App([
            ErroredModule::class
        ]);

        $request = new ServerRequest('GET', '/demo');
        // On s'attend à ce qu'une exception de type Exception soit levée
        $this->expectException(\Exception::class);
        $app->run($request); // Exécution de la requête qui déclenche l'exception
    }

    /**
     * Test : Si une route retourne une chaîne de caractères, elle doit être convertie en réponse HTTP
     */
    public function testConvertStringToResponse () {
        // On crée l'application avec le module StringModule
        $app = new App([
            StringModule::class
        ]);

        $request = new ServerRequest('GET', '/demo');
        $response = $app->run($request);

        // On vérifie que la réponse est bien une instance de ResponseInterface (objet réponse)
        $this->assertInstanceOf(ResponseInterface::class, $response);
        // On vérifie que le corps de la réponse contient bien la chaîne "DEMO"
        $this->assertEquals('DEMO', (string)$response->getBody());
    }

    /**
     * Test : Vérifie qu'une requête vers une route inexistante retourne une erreur 404
     */
    public function testError404() {
        $app = new App(); // Application sans modules

        $request = new ServerRequest('GET', '/aze'); // URL qui n’existe pas
        $response = $app->run($request);

        // On vérifie que le corps de la réponse contient bien l’erreur 404
        $this->assertContains('<h1>Erreur 404</h1>', (string)$response->getBody());
        // On vérifie que le code de réponse HTTP est bien 404
        $this->assertEquals(404, $response->getStatusCode());
    }

}
