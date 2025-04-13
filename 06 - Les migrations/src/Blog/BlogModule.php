<?php
// Début du fichier PHP

namespace App\Blog;
// On déclare que cette classe se trouve dans le namespace App\Blog
// Cela aide à organiser le code comme dans des dossiers virtuels

use App\Blog\Actions\BlogAction;
// On importe la classe qui va gérer les actions (contrôleur) du blog

use Framework\Module;
// On importe la classe Module, une classe de base pour structurer un "module"

use Framework\Renderer\RendererInterface;
// On importe l'interface du moteur de rendu, pour afficher des vues (ex : Twig)

use Framework\Router;
// On importe la classe Router, utilisée pour définir les routes de l’application

class BlogModule extends Module
{
    // Fichier contenant les définitions des services propres au module (ex: config du container DI)
    const DEFINITIONS = __DIR__ . '/config.php';

    // Dossier contenant les fichiers de migration de base de données pour le blog
    const MIGRATIONS =  __DIR__ . '/db/migrations';

    // Dossier contenant les seeders (faux articles pour remplir la base de données)
    const SEEDS =  __DIR__ . '/db/seeds';

    /**
     * Le constructeur de notre module.
     *
     * @param string $prefix Le préfixe d’URL pour les routes du blog (ex : "/blog")
     * @param Router $router Le routeur de l'application
     * @param RendererInterface $renderer Le moteur de rendu (ex: Twig)
     */
    public function __construct(string $prefix, Router $router, RendererInterface $renderer)
    {
        // On indique au moteur de rendu où trouver les vues Twig de ce module
        // 'blog' est un alias qu’on pourra utiliser comme ceci : @blog/nomDuTemplate
        $renderer->addPath('blog', __DIR__ . '/views');

        // On enregistre une route pour afficher la page d’accueil du blog
        // Exemple : /blog → va déclencher BlogAction
        $router->get($prefix, BlogAction::class, 'blog.index');

        // On enregistre une route pour afficher un article selon son "slug"
        // Exemple : /blog/mon-article → va aussi déclencher BlogAction
        // Le slug est capturé avec une expression régulière [a-z\-0-9]+
        $router->get($prefix . '/{slug:[a-z\-0-9]+}', BlogAction::class, 'blog.show');
    }
}
