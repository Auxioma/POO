<?php
// Déclaration du namespace pour organiser cette classe
namespace Framework\Renderer;

// ---------------------------------------------------------------------
// Cette classe "TwigRenderer" implémente l'interface "RendererInterface".
// Cela veut dire qu'elle doit respecter un contrat : fournir certaines méthodes (ex: render()).
// Ce Renderer permet d'afficher des fichiers HTML en utilisant Twig, un moteur de template.
// ---------------------------------------------------------------------
class TwigRenderer implements RendererInterface
{
    // -----------------------------------------------------------------
    // $twig : représente l'objet principal de Twig (moteur de rendu).
    // $loader : c’est lui qui va chercher les fichiers de vues (templates).
    // -----------------------------------------------------------------
    private $twig;
    private $loader;

    /**
     * Constructeur de la classe.
     * Il reçoit deux objets :
     * - le chargeur Twig (permet de savoir où sont les fichiers de vues)
     * - l’environnement Twig (gère le rendu, les filtres, les fonctions Twig…)
     *
     * @param FilesystemLoader $loader → chargeur de fichiers Twig
     * @param \Twig\Environment $twig → moteur de rendu Twig
     */
    public function __construct(FilesystemLoader $loader, \Twig\Environment $twig)
    {
        $this->loader = $loader; // On sauvegarde le chargeur dans une propriété
        $this->twig = $twig;     // On sauvegarde l’environnement de rendu Twig
    }

    /**
     * Méthode pour ajouter un chemin personnalisé vers les vues.
     * Exemple : associer le namespace 'blog' au dossier "/views/blog"
     * Cela permet ensuite d'utiliser le chemin raccourci dans les vues : "@blog/article"
     *
     * @param string $namespace → un alias pour regrouper les vues (ex: blog, admin)
     * @param null|string $path → le chemin réel vers le dossier de vues
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        // Le loader de Twig ajoute ce chemin en lien avec un alias (namespace)
        $this->loader->addPath($path, $namespace);
    }

    /**
     * Méthode principale : elle permet d’afficher une vue HTML Twig avec des données dynamiques.
     * Exemple d’utilisation : render('@blog/index', ['title' => 'Mon super blog']);
     *
     * @param string $view → le nom du fichier de vue à afficher (sans ".twig")
     * @param array $params → les variables que l’on passe à la vue (disponibles dans Twig)
     * @return string → le code HTML généré à partir du template et des données
     */
    public function render(string $view, array $params = []): string
    {
        // Twig attend un nom de fichier avec l’extension .twig, donc on l’ajoute ici
        return $this->twig->render($view . '.twig', $params);
    }

    /**
     * Méthode pour ajouter une variable disponible **dans toutes** les vues Twig.
     * Cela évite de devoir la passer manuellement à chaque fois.
     *
     * Exemple :
     *   $this->addGlobal('site_name', 'Mon Super Site');
     * Ensuite, dans TOUTES les vues Twig, on peut utiliser {{ site_name }}
     *
     * @param string $key → nom de la variable (comme dans Twig)
     * @param mixed $value → la valeur associée
     */
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }
}
