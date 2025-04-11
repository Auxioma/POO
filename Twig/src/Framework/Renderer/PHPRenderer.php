<?php

// On déclare l’espace de nom du moteur de rendu
namespace Framework\Renderer;

// Déclaration de la classe PHPRenderer qui implémente l’interface RendererInterface
class PHPRenderer implements RendererInterface
{
    // Constante qui définit le namespace par défaut pour les vues
    const DEFAULT_NAMESPACE = '__MAIN';

    // Tableau contenant les chemins d’accès pour chaque namespace
    private $paths = [];

    /**
     * Variables globales disponibles dans toutes les vues (ex: utilisateur connecté, titre du site...)
     * @var array
     */
    private $globals = [];

    // Constructeur : on peut définir un chemin par défaut dès la création du moteur
    public function __construct(?string $defaultPath = null)
    {
        if (!is_null($defaultPath)) {
            // Si un chemin est fourni, on l’ajoute au namespace par défaut
            $this->addPath($defaultPath);
        }
    }

    /**
     * Permet d’ajouter un chemin pour charger les vues
     * @param string $namespace Le nom du namespace ou bien le chemin si $path est null
     * @param null|string $path Le chemin des vues
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        if (is_null($path)) {
            // Si aucun chemin n’est précisé, le namespace devient le chemin par défaut
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            // Sinon on associe le namespace à son chemin
            $this->paths[$namespace] = $path;
        }
    }

    /**
     * Méthode principale pour afficher une vue
     * @param string $view Le nom de la vue (ex: '@blog/index' ou 'home')
     * @param array $params Les variables à passer à la vue
     * @return string Le contenu HTML rendu
     */
    public function render(string $view, array $params = []): string
    {
        // Si la vue utilise un namespace (ex: @blog/view)
        if ($this->hasNamespace($view)) {
            // On remplace le namespace par son vrai chemin
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            // Sinon, on utilise le chemin par défaut
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }

        // On active la mise en mémoire tampon (tout ce qui est affiché est stocké)
        ob_start();

        // On peut utiliser $renderer dans la vue si besoin
        $renderer = $this;

        // On extrait les variables globales (elles deviennent accessibles par leur nom directement dans la vue)
        extract($this->globals);

        // On extrait aussi les variables spécifiques à cette vue
        extract($params);

        // On inclut le fichier PHP correspondant à la vue
        require($path);

        // On récupère tout ce qui a été affiché et on le retourne sous forme de string
        return ob_get_clean();
    }

    /**
     * Ajoute une variable globale accessible dans toutes les vues
     * @param string $key Nom de la variable
     * @param mixed $value Valeur de la variable
     */
    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    // Vérifie si la vue commence par un '@' (donc si elle a un namespace)
    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    // Récupère le nom du namespace à partir de la vue (ex: @blog/index → blog)
    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }

    // Remplace le namespace par son vrai chemin
    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }
}
