<?php
// Déclaration du namespace (espace de nommage)
// Cela permet d’organiser ton code dans un "dossier virtuel" App\Blog
namespace App\Blog;

// On importe les classes nécessaires
use Framework\Router; // Le routeur que l’on va utiliser pour enregistrer les routes de ce module
use Psr\Http\Message\ResponseInterface as Response; // Pas utilisé ici, mais tu pourras l’utiliser si un jour tu retournes un objet Response
use Psr\Http\Message\ServerRequestInterface as Request; // C’est la requête HTTP que l’on recevra dans nos méthodes

/**
 * Ce module représente la partie "blog" de ton site.
 * Il enregistre ses propres routes au moment de sa construction.
 */
class BlogModule
{

    /**
     * Le constructeur est appelé automatiquement quand on crée un objet BlogModule.
     * On lui passe le routeur pour qu’il puisse ajouter ses routes spécifiques.
     */
    public function __construct(Router $router)
    {
        // On ajoute une route GET vers "/blog"
        // Quand un utilisateur visite /blog, la méthode "index" sera appelée
        // Le nom de cette route est "blog.index"
        $router->get('/blog', [$this, 'index'], 'blog.index');

        // On ajoute une autre route GET avec un paramètre dynamique "slug"
        // Exemple : /blog/mon-article → appellera la méthode "show"
        // La regex [a-z\-]+ signifie qu’on accepte uniquement des lettres minuscules et des tirets
        // Le nom de cette route est "blog.show"
        $router->get('/blog/{slug:[a-z\-]+}', [$this, 'show'], 'blog.show');
    }

    /**
     * Méthode appelée quand un utilisateur visite /blog
     * @param Request $request La requête HTTP (elle peut contenir des infos comme l'utilisateur, les paramètres, etc.)
     * @return string Le HTML à afficher
     */
    public function index(Request $request): string
    {
        return '<h1>Bienvenue sur le blog</h1>';
    }

    /**
     * Méthode appelée quand un utilisateur visite /blog/{slug}
     * Exemple : /blog/mon-article → slug = "mon-article"
     * @param Request $request La requête contenant le paramètre slug
     * @return string Le HTML affiché
     */
    public function show(Request $request): string
    {
        // On récupère le slug transmis dans l'URL
        $slug = $request->getAttribute('slug');

        // On l’affiche dans un titre HTML
        return '<h1>Bienvenue sur l\'article ' . $slug . '</h1>';
    }
}
