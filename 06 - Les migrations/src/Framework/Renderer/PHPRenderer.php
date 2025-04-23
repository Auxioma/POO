<?php

namespace Framework\Renderer;

// On définit la classe PHPRenderer qui implémente l'interface RendererInterface
class PHPRenderer implements RendererInterface
{
    // Définition d'une constante pour le namespace par défaut, utilisé quand aucun namespace spécifique n'est donné
    const DEFAULT_NAMESPACE = '__MAIN';

    // Tableau pour stocker les chemins des vues par namespace
    private $paths = [];

    /**
     * Variables globalement accessibles dans toutes les vues
     * Ce tableau contient les variables qui seront disponibles dans toutes les vues
     * @var array
     */
    private $globals = [];

    /**
     * Le constructeur permet de spécifier un chemin par défaut lors de la création de l'objet.
     * Si un chemin est donné, il est ajouté avec la méthode addPath().
     *
     * @param string|null $defaultPath
     */
    public function __construct(?string $defaultPath = null)
    {
        // Si un chemin est spécifié, on l'ajoute à la liste des chemins disponibles
        if (!is_null($defaultPath)) {
            $this->addPath($defaultPath);
        }
    }

    /**
     * Ajoute un chemin pour charger les vues.
     * On peut associer un chemin spécifique à un namespace (comme @blog par exemple).
     *
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        // Si aucun chemin n'est spécifié, on ajoute un chemin pour le namespace par défaut
        if (is_null($path)) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            // Sinon, on associe le namespace donné avec le chemin spécifié
            $this->paths[$namespace] = $path;
        }
    }

    /**
     * Permet de rendre une vue en précisant un nom de vue et des paramètres.
     * Le chemin peut être précisé en utilisant des namespaces ajoutés avec addPath().
     *
     * Exemple d'utilisation :
     * $this->render('@blog/view'); // Pour une vue dans le namespace 'blog'
     * $this->render('view'); // Pour une vue dans le namespace par défaut
     *
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        // On vérifie si la vue spécifiée contient un namespace (commence par '@')
        if ($this->hasNamespace($view)) {
            // Si oui, on remplace le namespace par le chemin associé et on ajoute '.php'
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            // Sinon, on utilise le chemin par défaut et on ajoute '.php'
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }

        // Démarre la mise en tampon de sortie, c'est-à-dire la capture de tout ce qui est envoyé à l'écran
        ob_start();
        
        // Permet d'utiliser la variable $renderer dans la vue pour appeler des méthodes
        $renderer = $this;
        
        // On rend toutes les variables globales accessibles dans la vue
        extract($this->globals);
        
        // On rend les paramètres spécifiques à cette vue accessibles également
        extract($params);
        
        // On inclut la vue à l'emplacement calculé
        require($path);
        
        // On retourne le contenu généré (c'est-à-dire ce qui a été capturé par ob_start())
        return ob_get_clean();
    }

    /**
     * Permet de rajouter des variables globales qui seront disponibles dans toutes les vues.
     *
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void
    {
        // On ajoute la variable globale au tableau
        $this->globals[$key] = $value;
    }

    /**
     * Vérifie si le nom de la vue contient un namespace (indiqué par le signe @).
     *
     * @param string $view
     * @return bool
     */
    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    /**
     * Extrait le nom du namespace d'une vue, qui est la partie avant le premier '/'.
     *
     * @param string $view
     * @return string
     */
    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }

    /**
     * Remplace le namespace dans le nom de la vue par le chemin approprié
     * afin de localiser le fichier de la vue.
     *
     * @param string $view
     * @return string
     */
    private function replaceNamespace(string $view): string
    {
        // On extrait le namespace de la vue
        $namespace = $this->getNamespace($view);
        
        // On remplace le namespace par le chemin associé à ce namespace dans les chemins définis
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }
}
