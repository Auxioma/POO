<?php
// Déclare que ce fichier appartient à l'espace de noms Tests\Framework
// Cela permet d’organiser ton code proprement.
namespace Tests\Framework;

// On importe les classes nécessaires :

use Framework\Router; // C’est la classe que l’on veut tester (le routeur de notre mini-framework)
use GuzzleHttp\Psr7\ServerRequest; // Permet de créer des requêtes HTTP simulées
use PHPUnit\Framework\TestCase; // Classe de base de PHPUnit pour écrire des tests automatisés

// On crée une nouvelle classe de test qui va contenir tous nos tests pour la classe Router
// Elle hérite de TestCase, ce qui permet à PHPUnit de reconnaître cette classe comme un fichier de test.
class RouterTest extends TestCase {

    /**
     * Cette propriété va contenir notre objet Router à tester.
     * Elle est déclarée ici pour qu’on puisse y accéder dans toutes les fonctions de test.
     * @var Router
     */
    private $router;

    /**
     * La méthode setUp() est appelée automatiquement avant chaque test.
     * Elle sert à initialiser tout ce dont on a besoin pour les tests.
     * Ici, on crée une nouvelle instance du routeur avant chaque test.
     */
    public function setUp(): void
    {
        // On instancie le routeur
        $this->router = new Router();
    }

    /**
     * Ce test vérifie qu’une route GET simple fonctionne bien.
     */
    public function testGetMethod()
    {
        // On simule une requête HTTP GET vers l'URL "/blog"
        $request = new ServerRequest('GET', '/blog');

        // On ajoute une route GET dans notre routeur :
        // - l’URL à reconnaître est "/blog"
        // - la fonction (callback) exécutée si la route est trouvée retourne "hello"
        // - le nom donné à cette route est "blog"
        $this->router->get('/blog', function () { return 'hello'; }, 'blog');

        // On demande au routeur de "matcher" (trouver une route qui correspond) à la requête
        $route = $this->router->match($request);

        // On vérifie que le nom de la route trouvée est bien "blog"
        $this->assertEquals('blog', $route->getName());

        // On exécute la fonction de la route et on vérifie qu’elle retourne "hello"
        $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));
    }

    /**
     * Ce test vérifie qu’une route non définie n’est pas trouvée.
     */
    public function testGetMethodIfURLDoesNotExists()
    {
        // On simule une requête GET vers "/blog"
        $request = new ServerRequest('GET', '/blog');

        // On ajoute une route différente, "/blogaze"
        $this->router->get('/blogaze', function () { return 'hello'; }, 'blog');

        // Le routeur ne doit pas trouver de correspondance avec la requête
        $route = $this->router->match($request);

        // Comme aucune route ne correspond, le résultat doit être null
        $this->assertEquals(null, $route);
    }

    /**
     * Ce test vérifie que le routeur peut gérer des routes avec des paramètres dynamiques (comme /blog/mon-slug-8).
     */
    public function testGetMethodWithParameters()
    {
        // On simule une requête vers "/blog/mon-slug-8"
        $request = new ServerRequest('GET', '/blog/mon-slug-8');

        // On ajoute une première route simple, pour vérifier que ce n'est pas celle-ci qui est choisie
        $this->router->get('/blog', function () { return 'azezea'; }, 'posts');

        // On ajoute une deuxième route avec des paramètres dynamiques :
        // - {slug:[a-z0-9\-]+} signifie qu’on attend un mot (lettres minuscules, chiffres, tirets)
        // - {id:\d+} signifie qu’on attend un ou plusieurs chiffres
        // Ex : /blog/mon-slug-8 => slug = "mon-slug", id = 8
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () { return 'hello'; }, 'post.show');

        // Le routeur doit reconnaître la bonne route
        $route = $this->router->match($request);

        // On vérifie que le nom de la route trouvée est "post.show"
        $this->assertEquals('post.show', $route->getName());

        // On vérifie que la fonction retourne bien "hello"
        $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));

        // On vérifie que les bons paramètres ont été extraits de l’URL
        $this->assertEquals(['slug' => 'mon-slug', 'id' => '8'], $route->getParams());

        // Maintenant, on teste une URL invalide : le slug contient un underscore (non autorisé par la regex)
        $route = $this->router->match(new ServerRequest('GET', '/blog/mon_slug-8'));

        // Cette URL ne doit pas matcher, donc $route doit être null
        $this->assertEquals(null, $route);
    }

    /**
     * Ce test vérifie que le routeur peut générer une URL automatiquement à partir du nom d’une route.
     */
    public function testGenerateUri()
    {
        // On déclare deux routes : une simple et une avec paramètres
        $this->router->get('/blog', function () { return 'azezea'; }, 'posts');
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () { return 'hello'; }, 'post.show');

        // On demande au routeur de générer une URL pour la route "post.show" en remplaçant les paramètres
        $uri = $this->router->generateUri('post.show', [
            'slug' => 'mon-article',
            'id' => 18
        ]);

        // On vérifie que l’URL générée est correcte
        $this->assertEquals('/blog/mon-article-18', $uri);
    }

}
