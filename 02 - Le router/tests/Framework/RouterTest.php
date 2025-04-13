<?php 
namespace Tests\Framework;

use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase {

    private $router;

    public function setUp(): void
    {
        $this->router = new Router();
    }

    public function testGetMethod()
    {
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blog', function () { return 'hello'; }, 'blog');
        $route = $this->router->match($request);

        $this->assertEquals('blog', $route->getName());
        $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));
    }

    public function testGetMethodIfURLDoesNotExists()
    {
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blogaze', function () { return 'hello'; }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals(null, $route);
    }

    public function testGetMethodWithParameters()
    {
        $request = new ServerRequest('GET', '/blog/mon-slug/8');

        // route dynamique avec paramètres
        $this->router->get('/blog/{slug}/{id}', function () { return 'hello'; }, 'post.show');

        $route = $this->router->match($request);

        $this->assertEquals('post.show', $route->getName());
        $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));
        $this->assertEquals(['slug' => 'mon-slug', 'id' => '8'], $route->getParams());

        // test URL invalide (manque un paramètre)
        $route = $this->router->match(new ServerRequest('GET', '/blog/mon-slug'));
        $this->assertEquals(null, $route);
    }

    public function testGenerateUri()
    {
        // Route avec paramètres dynamiques
        $this->router->get('/blog/{slug}/{id}', function () { return 'hello'; }, 'post.show');
    
        // Vérification que l'URL générée est correcte avec les paramètres
        $uri = $this->router->generateUri('post.show', [
            'slug' => 'mon-article',
            'id' => 108  // Assure-toi que l'ID ici correspond à celui attendu
        ]);
    
        // Vérification de l'URL générée
        $this->assertEquals('/blog/mon-article/108', $uri);  // Il faut vérifier que l'ID correspond bien à 108
    }
    
}
