<?php
namespace Framework\Renderer;

use Twig\Loader\FilesystemLoader;
use Psr\Container\ContainerInterface;

/**
 * Classe TwigRendererFactory
 * 
 * Cette classe est une fabrique (factory) pour créer une instance de `TwigRenderer`.
 * Elle utilise un conteneur d'injection de dépendances pour configurer et fournir
 * les chemins des vues et les extensions Twig.
 */
class TwigRendererFactory
{
    /**
     * Permet de créer une instance de TwigRenderer.
     * 
     * @param ContainerInterface $container Conteneur d'injection de dépendances.
     * @return TwigRenderer Instance configurée de TwigRenderer.
     */
    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        // Récupération du chemin des vues à partir du conteneur
        $viewPath = $container->get('views.path');

        // Initialisation du chargeur de fichiers pour Twig
        $loader = new \Twig\Loader\FilesystemLoader($viewPath);

        // Création de l'environnement Twig
        $twig = new \Twig\Environment($loader);

        // Ajout des extensions Twig, si elles sont configurées dans le conteneur
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }

        // Retourne une instance de TwigRenderer avec le chargeur et l'environnement Twig
        return new TwigRenderer($loader, $twig);
    }
}