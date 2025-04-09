<?php
// Définition du namespace de cette classe
// Les namespaces permettent d'organiser le code et d'éviter les conflits de noms entre différentes classes
namespace App\Blog;

// Importation des classes externes utilisées dans ce fichier
// Les instructions "use" permettent d'utiliser ces classes sans avoir à spécifier leur namespace complet à chaque fois
use Framework\Renderer;    // Pour le rendu des vues
use Framework\Router;      // Pour la gestion des routes
use Psr\Http\Message\ServerRequestInterface as Request;  // Interface standard pour les requêtes HTTP (PSR-7)

/**
 * BlogModule - Classe qui gère le module Blog de l'application
 * Un module est une partie autonome de l'application qui gère une fonctionnalité spécifique
 */
class BlogModule
{
    /**
     * Instance du moteur de rendu (Renderer)
     * Cette propriété privée est accessible uniquement depuis l'intérieur de la classe
     */
    private $renderer;
    
    /**
     * Constructeur du module Blog
     * 
     * @param Router $router - Le routeur de l'application (injecté automatiquement)
     * @param Renderer $renderer - Le moteur de rendu (injecté automatiquement)
     */
    public function __construct(Router $router, Renderer $renderer)
    {
        // Stockage du moteur de rendu dans la propriété de la classe
        $this->renderer = $renderer;
        
        // Ajout du chemin des vues du module Blog
        // __DIR__ représente le répertoire du fichier actuel
        // 'blog' est l'alias qui permet d'accéder aux vues via @blog/...
        $this->renderer->addPath('blog', __DIR__ . '/views');
        
        // Définition des routes du module Blog
        // Chaque route associe une URL à une méthode de cette classe
        
        // Route pour la page d'index du blog (liste des articles)
        // Premier paramètre : URL de la route
        // Deuxième paramètre : callback à exécuter (ici, la méthode 'index' de cette classe)
        // Troisième paramètre : nom de la route pour pouvoir y faire référence ailleurs
        $router->get('/blog', [$this, 'index'], 'blog.index');
        
        // Route pour afficher un article spécifique
        // {slug:[a-z\-0-9]+} définit un paramètre 'slug' avec une expression régulière
        // qui accepte uniquement des lettres minuscules, des chiffres et des tirets
        $router->get('/blog/{slug:[a-z\-0-9]+}', [$this, 'show'], 'blog.show');
    }
    
    /**
     * Méthode pour afficher la page d'index du blog (liste des articles)
     * 
     * @param Request $request - La requête HTTP
     * @return string - Le HTML généré
     */
    public function index(Request $request): string
    {
        // Rendu de la vue 'index' du module Blog
        // @blog fait référence au chemin défini plus haut avec addPath()
        return $this->renderer->render('@blog/index');
    }
    
    /**
     * Méthode pour afficher un article spécifique
     * 
     * @param Request $request - La requête HTTP
     * @return string - Le HTML généré
     */
    public function show(Request $request): string
    {
        // Rendu de la vue 'show' du module Blog avec des paramètres
        // Le second paramètre est un tableau de variables à passer à la vue
        return $this->renderer->render('@blog/show', [
            // Récupération du paramètre 'slug' défini dans la route
            'slug' => $request->getAttribute('slug')
        ]);
    }
}