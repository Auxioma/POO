<?php
// On définit l'espace de nom (namespace) de cette classe, pour bien organiser le code
namespace App\Blog;

// On importe les classes dont on aura besoin
use Framework\Renderer\RendererInterface; // Interface pour le moteur de rendu
use Framework\Router;                     // Classe qui gère les routes
use Psr\Http\Message\ServerRequestInterface as Request; // Interface standard pour les requêtes HTTP

// Déclaration de la classe BlogModule
class BlogModule
{
    // Propriété privée pour stocker le moteur de rendu
    private $renderer;

    // Le constructeur est appelé automatiquement à la création de l'objet
    public function __construct(Router $router, RendererInterface $renderer)
    {
        // On stocke le moteur de rendu dans une propriété pour pouvoir l'utiliser dans d'autres méthodes
        $this->renderer = $renderer;

        // On ajoute un chemin de vue appelé "blog" qui pointe vers le dossier des vues du blog
        $this->renderer->addPath('blog', __DIR__ . '/views');

        // On déclare une route GET qui correspond à l'URL /blog
        // Quand cette URL est appelée, la méthode index() sera exécutée
        $router->get('/blog', [$this, 'index'], 'blog.index');

        // On déclare une route GET avec un paramètre {slug} (texte autorisé : lettres, chiffres, tirets)
        // Quand cette URL est appelée, la méthode show() sera exécutée
        $router->get('/blog/{slug:[a-z\-0-9]+}', [$this, 'show'], 'blog.show');
    }

    // Méthode qui affiche la liste des articles du blog
    public function index(Request $request): string
    {
        // On utilise le moteur de rendu pour afficher la vue index du blog
        return $this->renderer->render('@blog/index');
    }

    // Méthode qui affiche un article en particulier (détail d’un article)
    public function show(Request $request): string
    {
        // On utilise le moteur de rendu pour afficher la vue show
        // On passe à la vue la valeur du paramètre "slug" récupéré depuis l’URL
        return $this->renderer->render('@blog/show', [
            'slug' => $request->getAttribute('slug')
        ]);
    }
}
