<?php

namespace Framework\Renderer;

/**
 * Classe PHPRenderer
 * 
 * Cette classe implémente le moteur de rendu pour afficher des vues PHP.
 * Elle permet de définir des chemins pour charger les vues, ajouter des variables
 * globales et rendre des fichiers de vue avec des paramètres spécifiques.
 */
class PHPRenderer implements RendererInterface
{
    /**
     * Namespace par défaut pour les vues sans namespace explicite.
     */
    const DEFAULT_NAMESPACE = '__MAIN';

    /**
     * Tableau des chemins vers les vues, organisés par namespace.
     * @var array
     */
    private $paths = [];

    /**
     * Variables globalement accessibles pour toutes les vues.
     * @var array
     */
    private $globals = [];

    /**
     * Constructeur de la classe PHPRenderer.
     * 
     * @param string|null $defaultPath Chemin par défaut pour les vues (optionnel).
     */
    public function __construct(?string $defaultPath = null)
    {
        if (!is_null($defaultPath)) {
            $this->addPath($defaultPath);
        }
    }

    /**
     * Permet d'ajouter un chemin pour charger les vues.
     * 
     * Si $path est null, $namespace est utilisé comme chemin par défaut.
     * 
     * @param string $namespace Le namespace pour organiser les vues.
     * @param string|null $path Le chemin des vues associé au namespace.
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        if (is_null($path)) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            $this->paths[$namespace] = $path;
        }
    }

    /**
     * Permet de rendre une vue.
     * 
     * Le chemin peut être précisé avec des namespaces ajoutés via addPath().
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
        // Détermine le chemin complet de la vue
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }

        // Mise en tampon de sortie pour capturer le rendu
        ob_start();
        $renderer = $this; // Permet d'accéder à $this dans les vues
        extract($this->globals); // Variables globales
        extract($params); // Variables spécifiques à la vue
        require($path);
        return ob_get_clean();
    }

    /**
     * Permet d'ajouter des variables globales accessibles à toutes les vues.
     * 
     * @param string $key Nom de la variable.
     * @param mixed $value Valeur de la variable.
     */
    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    /**
     * Vérifie si la vue utilise un namespace.
     * 
     * @param string $view Chemin ou namespace de la vue.
     * @return bool True si un namespace est utilisé.
     */
    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    /**
     * Récupère le namespace d'une vue.
     * 
     * @param string $view Chemin ou namespace de la vue.
     * @return string Le namespace de la vue.
     */
    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }

    /**
     * Remplace le namespace par le chemin correspondant.
     * 
     * @param string $view Chemin ou namespace de la vue.
     * @return string Chemin complet de la vue.
     */
    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }
}