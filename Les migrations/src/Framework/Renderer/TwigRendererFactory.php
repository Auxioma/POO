<?php
namespace Framework\Renderer;

// La classe TwigRendererFactory est une fabrique (ou factory) qui crée et retourne une instance de TwigRenderer.
// Elle utilise le conteneur d'injection de dépendances (ContainerInterface) pour obtenir les dépendances nécessaires.

use Psr\Container\ContainerInterface;

class TwigRendererFactory
{
    /**
     * La méthode __invoke permet à l'objet d'être appelé comme une fonction.
     * Elle est utilisée par le conteneur d'injection de dépendances pour créer une instance de TwigRenderer.
     * 
     * @param ContainerInterface $container - Le conteneur d'injection de dépendances qui fournit les services nécessaires.
     * @return TwigRenderer - Une instance de la classe TwigRenderer prête à être utilisée.
     */
    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        // On récupère le chemin des vues depuis le conteneur, en utilisant la clé 'views.path'.
        // Cette valeur doit avoir été définie quelque part dans la configuration du conteneur.
        $viewPath = $container->get('views.path');
        
        // On crée un loader Twig qui indique à Twig où se trouvent les fichiers de vues.
        $loader = new \Twig_Loader_Filesystem($viewPath);

        // On crée un environnement Twig, qui est responsable du rendu des vues.
        $twig = new \Twig_Environment($loader);
        
        // Si le conteneur contient des extensions Twig (clés 'twig.extensions'), on les ajoute à l'environnement Twig.
        // Cela permet d'ajouter des fonctionnalités supplémentaires aux vues Twig (ex: extensions pour la gestion des routes, etc.).
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }
        
        // Enfin, on retourne une nouvelle instance de TwigRenderer, en passant le loader et l'environnement Twig créés.
        return new TwigRenderer($loader, $twig);
    }
}
