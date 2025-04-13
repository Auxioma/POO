<?php
namespace Framework\Renderer;

// On définit la classe TwigRenderer qui implémente l'interface RendererInterface
// Cette classe est un moteur de rendu utilisant le système de templates Twig
class TwigRenderer implements RendererInterface
{
    // Les propriétés pour stocker l'instance du moteur Twig et du chargeur de fichiers Twig
    private $twig;
    private $loader;

    /**
     * Le constructeur initialise l'objet TwigRenderer avec un loader et un environnement Twig.
     * Le loader est utilisé pour trouver les fichiers de vue, et l'environnement est responsable du rendu des vues.
     * 
     * @param \Twig_Loader_Filesystem $loader - Le chargeur de fichiers de vues Twig
     * @param \Twig_Environment $twig - L'environnement de rendu Twig
     */
    public function __construct(\Twig_Loader_Filesystem $loader, \Twig_Environment $twig)
    {
        // On stocke les objets Twig dans les propriétés de la classe
        $this->loader = $loader;
        $this->twig = $twig;
    }

    /**
     * Permet d'ajouter un chemin pour charger les vues.
     * Cette méthode associe un namespace à un chemin, ce qui permet de gérer des vues par groupes (ex. '@blog')
     * 
     * @param string $namespace - Le namespace de la vue (ex: 'blog', 'admin')
     * @param null|string $path - Le chemin du dossier contenant les vues
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        // Utilisation du loader Twig pour associer un chemin à un namespace
        $this->loader->addPath($path, $namespace);
    }

    /**
     * Permet de rendre une vue en utilisant Twig.
     * La vue est chargée et les paramètres sont passés pour générer le contenu final.
     * 
     * Exemple d'utilisation :
     * $this->render('@blog/view', ['title' => 'Mon Blog']); 
     * 
     * @param string $view - Le nom de la vue à rendre (ex: '@blog/view')
     * @param array $params - Les variables à passer à la vue (par exemple, ['title' => 'Blog'])
     * @return string - Le contenu généré de la vue, généralement du HTML
     */
    public function render(string $view, array $params = []): string
    {
        // Utilisation de l'environnement Twig pour rendre la vue avec les paramètres donnés
        return $this->twig->render($view . '.twig', $params);
    }

    /**
     * Permet d'ajouter des variables globales qui seront disponibles dans toutes les vues.
     * Cela permet de rendre certaines informations accessibles dans toutes les vues sans avoir à les passer explicitement à chaque fois.
     * 
     * Exemple d'utilisation :
     * $this->addGlobal('site_name', 'Mon Super Site');
     * 
     * @param string $key - Le nom de la variable globale
     * @param mixed $value - La valeur de la variable globale
     */
    public function addGlobal(string $key, $value): void
    {
        // Utilisation de la méthode de Twig pour ajouter une variable globale
        $this->twig->addGlobal($key, $value);
    }
}
