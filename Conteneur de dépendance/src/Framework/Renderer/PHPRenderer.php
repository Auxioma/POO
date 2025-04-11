<?php

namespace Framework\Renderer;

/**
 * Moteur de rendu PHP simple
 * Permet de rendre des templates PHP en utilisant des namespaces pour organiser les vues
 */
class PHPRenderer implements RendererInterface
{
    /**
     * Namespace par défaut pour les templates qui n'en ont pas
     */
    const DEFAULT_NAMESPACE = '__MAIN';

    /**
     * Tableau associatif qui stocke les chemins vers les dossiers de vues
     * Format: ['namespace' => 'chemin/vers/dossier']
     * @var array
     */
    private $paths = [];

    /**
     * Variables globalement accessibles pour toutes les vues
     * Ces variables seront disponibles dans tous les templates
     * @var array
     */
    private $globals = [];

    /**
     * Constructeur du moteur de rendu
     * 
     * @param string|null $defaultPath Chemin par défaut vers le dossier principal des vues
     */
    public function __construct(?string $defaultPath = null)
    {
        // Si un chemin par défaut est fourni, on l'ajoute automatiquement
        if (!is_null($defaultPath)) {
            $this->addPath($defaultPath);
        }
    }

    /**
     * Permet d'ajouter un chemin pour charger les vues
     * 
     * @param string $namespace Le namespace ou le chemin si $path est null
     * @param string|null $path Le chemin vers le dossier des vues pour ce namespace
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        // Si path est null, alors on considère que namespace est en fait le chemin
        // et on l'associe au namespace par défaut
        if (is_null($path)) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            // Sinon on associe le namespace au chemin
            $this->paths[$namespace] = $path;
        }
    }

    /**
     * Permet de rendre une vue et retourner son contenu sous forme de chaîne
     * Le chemin peut être précisé avec des namespaces ajoutés via addPath()
     * Exemples:
     *   $this->render('@blog/view');  // Utilise le namespace 'blog'
     *   $this->render('view');        // Utilise le namespace par défaut
     * 
     * @param string $view Le nom de la vue à rendre (avec ou sans namespace)
     * @param array $params Variables à passer à la vue
     * @return string Le contenu rendu de la vue
     */
    public function render(string $view, array $params = []): string
    {
        // Détermine le chemin complet du fichier de vue
        if ($this->hasNamespace($view)) {
            // Si la vue contient un namespace (@namespace/vue)
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            // Sinon, utilise le namespace par défaut
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }

        // Démarre la mise en tampon de la sortie pour capturer le rendu
        ob_start();
        
        // Rend la variable $renderer accessible dans le template
        // pour permettre d'appeler render() de manière récursive
        $renderer = $this;
        
        // Extrait les variables globales et les paramètres pour les rendre
        // accessibles directement comme variables dans le template
        extract($this->globals);
        extract($params);
        
        // Inclut le fichier de template PHP
        require($path);
        
        // Récupère le contenu du tampon et le retourne
        return ob_get_clean();
    }

    /**
     * Permet d'ajouter des variables globales accessibles à toutes les vues
     *
     * @param string $key Nom de la variable
     * @param mixed $value Valeur de la variable
     */
    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    /**
     * Vérifie si la vue spécifiée contient un namespace (commence par @)
     * 
     * @param string $view Nom de la vue
     * @return bool True si la vue contient un namespace
     */
    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    /**
     * Extrait le namespace d'une vue
     * Exemple: "@blog/article" retourne "blog"
     * 
     * @param string $view Nom de la vue avec namespace
     * @return string Le namespace extrait
     */
    private function getNamespace(string $view): string
    {
        // Extrait la partie entre @ et /
        return substr($view, 1, strpos($view, '/') - 1);
    }

    /**
     * Remplace le namespace dans la vue par le chemin correspondant
     * Exemple: "@blog/article" devient "/path/to/blog/views/article"
     * 
     * @param string $view Nom de la vue avec namespace
     * @return string Le chemin complet sans l'extension
     */
    private function replaceNamespace(string $view): string
    {
        // Récupère le namespace de la vue
        $namespace = $this->getNamespace($view);
        
        // Remplace @namespace par le chemin réel associé à ce namespace
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }
}