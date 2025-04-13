<?php
// Ligne 1 : Démarre le bloc PHP

// Ligne 2 : Déclaration du namespace pour organiser ton code
namespace Tests\Framework;

// Lignes 4 à 6 : Importation des classes nécessaires

use Framework\Router; // Le routeur que tu veux tester
use GuzzleHttp\Psr7\Request; // Classe de requête HTTP (ici non utilisée mais importée)
use GuzzleHttp\Psr7\ServerRequest; // Classe pour simuler une requête serveur (GET, POST, etc.)
use PHPUnit\Framework\TestCase; // Classe de base de PHPUnit pour écrire des tests

// Ligne 8 : Déclaration de la classe de test
class RouterTest extends TestCase {

    /**
     * @var Router
     * Attribut privé pour contenir une instance du routeur à tester
     */
    private $router;

    /**
     * Méthode exécutée automatiquement avant chaque test
     * Elle instancie un nouvel objet Router
     */
    public function setUp():void
    {
        $this->router = new Router(); // Création d’un nouveau routeur pour chaque test
    }

    /**
     * Teste si une route GET simple fonctionne correctement
     */
    public function testGetMethod()
    {
        // Création d’une requête HTTP GET vers l’URL "/blog"
        $request = new ServerRequest('GET', '/blog');

        // On ajoute une route GET "/blog" avec une fonction de rappel (callback) qui retourne "hello"
        $this->router->get('/blog', function () { return 'hello'; }, 'blog');

        // On demande au routeur de trouver la route correspondant à la requête
        $route = $this->router->match($request);

        // Vérifie que le nom de la route correspond bien à "blog"
        $this->assertEquals('blog', $route->getName());

        // Vérifie que l'exécution du callback retourne bien "hello"
        $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));
    }

    /**
     * Teste le comportement si l'URL demandée ne correspond à aucune route
     */
    public function testGetMethodIfURLDoesNotExists()
    {
        // Requête vers "/blog"
        $request = new ServerRequest('GET', '/blog');

        // Route définie sur "/blogaze" donc différente
        $this->router->get('/blogaze', function () { return 'hello'; }, 'blog');

        // Recherche de correspondance
        $route = $this->router->match($request);

        // Doit retourner null car il n’y a pas de correspondance
        $this->assertEquals(null, $route);
    }

    /**
     * Teste une route avec des paramètres dynamiques dans l’URL
     * Exemple : /blog/mon-slug-8 => {slug} = mon-slug, {id} = 8
     */
    public function testGetMethodWithParameters()
    {
        // Requête vers une URL contenant un slug et un id
        $request = new ServerRequest('GET', '/blog/mon-slug-8');

        // Route simple sans paramètres (pour s'assurer qu'elle n'est pas choisie)
        $this->router->get('/blog', function () { return 'azezea'; }, 'posts');

        // Route avec paramètres dynamiques
        // {slug:[a-z0-9\-]+} signifie : slug peut contenir lettres minuscules, chiffres, tirets
        // {id:\d+} signifie : id est un entier
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () { return 'hello'; }, 'post.show');

        // On teste si le routeur choisit bien la route avec paramètres
        $route = $this->router->match($request);

        // Le nom de la route trouvée doit être "post.show"
        $this->assertEquals('post.show', $route->getName());

        // Le callback doit retourner "hello"
        $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));

        // Les paramètres extraits de l’URL doivent être corrects
        $this->assertEquals(['slug' => 'mon-slug', 'id' => '8'], $route->getParams());

        // Test supplémentaire : URL invalide (underscore au lieu de tiret) => ne doit pas correspondre
        $route = $this->router->match(new ServerRequest('GET', '/blog/mon_slug-8'));
        $this->assertEquals(null, $route);
    }

    /**
     * Teste la génération d'une URL à partir du nom d'une route et de ses paramètres
     */
    public function testGenerateUri()
    {
        // On ajoute deux routes : une sans paramètre et une avec des paramètres dynamiques
        $this->router->get('/blog', function () { return 'azezea'; }, 'posts');
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () { return 'hello'; }, 'post.show');

        // On génère une URL à partir du nom "post.show" avec les bons paramètres
        $uri = $this->router->generateUri('post.show', ['slug' => 'mon-article', 'id' => 18]);

        // On vérifie que l’URL générée correspond bien à ce qu’on attend
        $this->assertEquals('/blog/mon-article-18', $uri);
    }
}
