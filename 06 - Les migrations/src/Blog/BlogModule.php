<?php
// Début du fichier PHP

namespace App\Blog;
// ------------------------------------------------------------
// Le namespace indique que cette classe fait partie du module "Blog".
// Cela fonctionne comme un dossier virtuel pour organiser les fichiers PHP.
// ------------------------------------------------------------

use App\Blog\Actions\BlogAction;
// On importe la classe "BlogAction" qui contient la logique métier pour le blog (affichage d’articles, etc.)

use Framework\Module;
// On importe une classe de base appelée "Module", qui aide à structurer un module de l'application.
// Elle peut contenir des comportements communs à tous les modules.

use Framework\Renderer\RendererInterface;
// Interface pour afficher des vues HTML à l’aide d’un moteur de rendu comme Twig.

use Framework\Router;
// Classe utilisée pour définir les routes de l’application web (ex: quelles URL déclenchent quelle action ?)

// ------------------------------------------------------------
// DÉCLARATION DE LA CLASSE BlogModule
// Cette classe représente le "module Blog", une partie indépendante de l'application.
// Elle est chargée automatiquement à l’initialisation de l’application.
// ------------------------------------------------------------

class BlogModule extends Module
{
    // Emplacement du fichier qui contient les définitions pour le conteneur d'injection (services spécifiques au module)
    const DEFINITIONS = __DIR__ . '/config.php';

    // Emplacement des scripts de migration (création de la base de données pour ce module)
    const MIGRATIONS =  __DIR__ . '/db/migrations';

    // Emplacement des "seeders" = scripts pour insérer de fausses données (ex: faux articles de blog)
    const SEEDS =  __DIR__ . '/db/seeds';

    /**
     * Constructeur du module.
     *
     * Il permet :
     * - de définir les chemins vers les vues
     * - de définir les routes HTTP propres à ce module (ex: /blog, /blog/article-slug)
     *
     * @param string $prefix : préfixe pour toutes les routes (ex: "/blog")
     * @param Router $router : objet routeur (gère les correspondances URL → action PHP)
     * @param RendererInterface $renderer : moteur de rendu (permet d'afficher du HTML via Twig par exemple)
     */
    public function __construct(string $prefix, Router $router, RendererInterface $renderer)
    {
        // --------------------------------------------------------------------
        // 1. Ajouter le chemin vers les vues (templates HTML) du blog
        // Cela permet d’utiliser l’alias @blog dans les vues Twig
        // Exemple dans une autre classe : $renderer->render('@blog/index');
        // --------------------------------------------------------------------
        $renderer->addPath('blog', __DIR__ . '/views');

        // --------------------------------------------------------------------
        // 2. Définir les routes du module
        // --------------------------------------------------------------------

        // Route pour la page d’accueil du blog
        // Exemple : /blog → exécute BlogAction::__invoke() sans slug
        $router->get($prefix, BlogAction::class, 'blog.index');

        // Route pour un article du blog
        // Exemple : /blog/mon-article → exécute BlogAction::__invoke() avec un slug
        // La partie entre {} est une variable d'URL (slug) capturée avec une regex : [a-z\-0-9]+
        $router->get($prefix . '/{slug:[a-z\-0-9]+}', BlogAction::class, 'blog.show');
    }
}
