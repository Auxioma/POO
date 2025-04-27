<?php
namespace Framework\Renderer;

use Twig\Loader\FilesystemLoader;

/**
 * Classe TwigRenderer
 * 
 * Cette classe implémente le moteur de rendu basé sur Twig. Elle permet
 * de gérer les chemins des vues, d'ajouter des variables globales et de
 * rendre des templates Twig avec des paramètres.
 */
class TwigRenderer implements RendererInterface
{
    /**
     * @var \Twig\Environment
     * Instance de l'environnement Twig pour rendre les templates.
     */
    private $twig;

    /**
     * @var FilesystemLoader
     * Chargeur de fichiers pour spécifier les chemins des templates.
     */
    private $loader;

    /**
     * Constructeur de la classe TwigRenderer.
     * 
     * @param FilesystemLoader $loader Chargeur des fichiers Twig.
     * @param \Twig\Environment $twig Instance de l'environnement Twig.
     */
    public function __construct(FilesystemLoader $loader, \Twig\Environment $twig)
    {
        $this->loader = $loader;
        $this->twig = $twig;
    }

    /**
     * Permet d'ajouter un chemin pour charger les vues.
     * 
     * Cette méthode ajoute un nouveau chemin pour charger les templates,
     * avec la possibilité d'utiliser un namespace pour organiser les vues.
     * 
     * @param string $namespace Le namespace pour organiser les vues.
     * @param null|string $path Le chemin des vues associé au namespace.
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->loader->addPath($path, $namespace);
    }

    /**
     * Permet de rendre une vue.
     * 
     * Cette méthode génère et retourne le contenu HTML d'une vue Twig.
     * Le chemin de la vue peut inclure un namespace ajouté via `addPath`.
     * Par exemple :
     * - $this->render('@blog/view', ['param1' => 'value']);
     * - $this->render('view', ['param2' => 'value']);
     * 
     * @param string $view Chemin ou namespace de la vue.
     * @param array $params Paramètres à passer à la vue.
     * @return string Contenu HTML rendu de la vue.
     */
    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.twig', $params);
    }

    /**
     * Permet d'ajouter des variables globales accessibles à toutes les vues.
     * 
     * Les variables globales sont disponibles dans toutes les vues Twig
     * et permettent d'ajouter des données communes comme un titre, un utilisateur
     * connecté, ou d'autres informations récurrentes.
     * 
     * @param string $key Nom de la variable.
     * @param mixed $value Valeur de la variable.
     */
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }
}