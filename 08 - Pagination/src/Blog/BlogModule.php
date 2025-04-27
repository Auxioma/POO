<?php
namespace App\Blog;

// Importation des classes nécessaires pour configurer le module Blog.
use App\Blog\Actions\BlogAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;

/**
 * Classe BlogModule
 * 
 * Cette classe représente le module de blog. Elle configure les routes,
 * les chemins de vues et d'autres paramètres spécifiques au module.
 */
class BlogModule extends Module
{
    /**
     * Chemin vers le fichier de configuration du module.
     * 
     * Ce fichier contient les définitions nécessaires pour configurer
     * les services ou paramètres utilisés par ce module.
     */
    const DEFINITIONS = __DIR__ . '/config.php';

    /**
     * Chemin vers les fichiers de migration de la base de données.
     * 
     * Les migrations permettent de gérer les modifications appliquées
     * à la structure de la base de données.
     */
    const MIGRATIONS =  __DIR__ . '/db/migrations';

    /**
     * Chemin vers les fichiers de seeds de la base de données.
     * 
     * Les seeds permettent d'insérer des données initiales ou de test
     * dans la base de données.
     */
    const SEEDS =  __DIR__ . '/db/seeds';

    /**
     * Constructeur du module Blog.
     * 
     * @param string $prefix Préfixe des routes pour ce module (exemple : '/blog').
     * @param Router $router Instance du routeur pour enregistrer les routes.
     * @param RendererInterface $renderer Instance du moteur de rendu pour gérer les vues.
     */
    public function __construct(string $prefix, Router $router, RendererInterface $renderer)
    {
        // Ajout du chemin des vues pour le module blog.
        // Cela permet d'utiliser des fichiers de templates spécifiques à ce module.
        $renderer->addPath('blog', __DIR__ . '/views');

        // Enregistrement des routes pour le module blog.
        // Route pour afficher la liste des articles ('blog.index').
        $router->get($prefix, BlogAction::class, 'blog.index');

        // Route pour afficher un article spécifique ('blog.show').
        // Le slug (texte dans l'URL) doit correspondre à un format particulier ([a-z\-0-9]).
        // L'ID de l'article doit être un entier ([0-9]+).
        $router->get($prefix . '/{slug:[a-z\-0-9]+}-{id:[0-9]+}', BlogAction::class, 'blog.show');
    }
}