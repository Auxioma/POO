<?php
// On déclare le namespace du renderer Twig
namespace Framework\Renderer;

// Classe TwigRenderer qui implémente RendererInterface
class TwigRenderer implements RendererInterface
{
    // Variable qui contient l’objet principal de Twig
    private $twig;

    // Variable qui gère les chemins vers les vues (templates)
    private $loader;

    // Constructeur : on initialise Twig avec un chemin par défaut
    public function __construct(string $path)
    {
        // On crée un loader qui va chercher les fichiers twig dans le dossier donné
        $this->loader = new \Twig_Loader_Filesystem($path);

        // On crée l'environnement Twig à partir du loader
        $this->twig = new \Twig_Environment($this->loader, []);
    }

    /**
     * Ajoute un chemin supplémentaire pour un namespace spécifique
     * Cela permet d’organiser les vues par modules (ex: @blog)
     * 
     * @param string $namespace Nom du namespace (ex: 'blog')
     * @param null|string $path Chemin vers les vues correspondantes
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        // On ajoute le chemin au loader sous le nom du namespace
        $this->loader->addPath($path, $namespace);
    }

    /**
     * Affiche une vue en utilisant Twig
     * Le nom du fichier `.twig` est automatiquement ajouté
     * 
     * @param string $view Nom de la vue (ex: '@blog/index')
     * @param array $params Variables passées à la vue
     * @return string Code HTML généré par Twig
     */
    public function render(string $view, array $params = []): string
    {
        // Twig ajoute automatiquement l’extension `.twig`
        return $this->twig->render($view . '.twig', $params);
    }

    /**
     * Ajoute une variable globale accessible dans toutes les vues Twig
     * 
     * @param string $key Nom de la variable
     * @param mixed $value Valeur de la variable
     */
    public function addGlobal(string $key, $value): void
    {
        // Cette variable sera disponible dans toutes les vues
        $this->twig->addGlobal($key, $value);
    }
}
