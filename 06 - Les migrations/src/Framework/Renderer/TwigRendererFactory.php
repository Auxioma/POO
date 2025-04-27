<?php
namespace Framework\Renderer;

// ----------------------------------------------------------
// Cette classe est une "Factory" (fabrique) :
// Elle est utilisée pour construire une instance de TwigRenderer,
// en récupérant automatiquement toutes ses dépendances grâce au conteneur.
//
// On sépare ainsi la logique de construction (factory) de la logique d’utilisation (TwigRenderer).
// Cela rend le code plus propre, modulaire et facile à tester.
// ----------------------------------------------------------

use Psr\Container\ContainerInterface; // Interface standard pour tous les conteneurs DI (interopérabilité PSR-11)

class TwigRendererFactory
{
    /**
     * Cette méthode magique "__invoke" permet à la classe d'être utilisée comme une fonction.
     * Elle est appelée automatiquement par le conteneur (ex: PHP-DI) lorsqu’il a besoin de créer un TwigRenderer.
     *
     * @param ContainerInterface $container - Le conteneur d’injection (ex: PHP-DI), qui contient tous les services.
     * @return TwigRenderer - Une instance complètement configurée du moteur Twig personnalisé.
     */
    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        // ------------------------------------------------------------------------
        // On commence par récupérer le chemin où sont stockées les vues Twig.
        // Cette valeur (ex: "/chemin/vers/views") doit avoir été définie dans le fichier config.php.
        // ------------------------------------------------------------------------
        $viewPath = $container->get('views.path');

        // ------------------------------------------------------------------------
        // On crée un "loader" pour Twig qui sait où chercher les fichiers .twig.
        // C’est un peu comme dire à Twig : "Voici ton dossier de vues".
        // ------------------------------------------------------------------------
        $loader = new \Twig\Loader\FilesystemLoader($viewPath);

        // ------------------------------------------------------------------------
        // On crée ensuite le moteur de rendu Twig lui-même (l’environnement).
        // C’est cet objet qui va transformer les fichiers .twig en HTML réel.
        // ------------------------------------------------------------------------
        $twig = new \Twig\Environment($loader);

        // ------------------------------------------------------------------------
        // On vérifie si le conteneur connaît des "extensions" Twig personnalisées.
        // Par exemple : une extension pour gérer les routes, la date, la sécurité…
        // On les ajoute une par une à l’environnement Twig.
        // ------------------------------------------------------------------------
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }

        // ------------------------------------------------------------------------
        // On retourne enfin une instance de notre classe TwigRenderer personnalisée.
        // Elle est maintenant prête à rendre les vues HTML Twig dans notre app.
        // ------------------------------------------------------------------------
        return new TwigRenderer($loader, $twig);
    }
}
