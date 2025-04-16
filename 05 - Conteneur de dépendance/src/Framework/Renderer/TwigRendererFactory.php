<?php
// On déclare un espace de nom pour organiser le code et éviter les conflits de noms.
namespace Framework\Renderer;

// On importe l'interface ContainerInterface, utilisée pour accéder aux services de la container.
use Psr\Container\ContainerInterface;

// Définition de la classe TwigRendererFactory.
// C’est une "factory" : une classe qui a pour rôle de créer et configurer un objet.
class TwigRendererFactory
{
    // Méthode spéciale __invoke, qui permet d'utiliser un objet de cette classe comme une fonction.
    // Elle reçoit un conteneur de services qui permet d'accéder aux dépendances de l'application.
    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        // On récupère le chemin vers les fichiers de vue depuis le conteneur.
        $viewPath = $container->get('views.path');

        // On crée un "loader" Twig, qui sait où aller chercher les fichiers de templates.
        $loader = new \Twig\Loader\FilesystemLoader($viewPath);

        // On initialise l'environnement Twig avec ce loader.
        $twig = new \Twig\Environment($loader);

        // Si des extensions Twig sont définies dans le conteneur, on les ajoute à l'environnement.
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }

        // On retourne un nouvel objet TwigRenderer prêt à être utilisé.
        return new TwigRenderer($loader, $twig);
    }
}
