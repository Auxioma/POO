<?php
// Définition du namespace pour cette classe
namespace Framework;

/**
 * Classe Renderer - Moteur de rendu de vues (templates)
 * Cette classe est responsable de charger et d'afficher les fichiers de vues PHP
 */
class Renderer {
    // Constante définissant le namespace par défaut pour les vues
    // Cette constante permet d'utiliser les vues sans préfixe de namespace
    const DEFAULT_NAMESPACE = '__MAIN';
    
    /**
     * Tableau associatif qui contient tous les chemins de dossiers des vues
     * Format: [namespace => chemin_du_dossier]
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
     * Permet d'ajouter un chemin pour charger les vues
     * 
     * @param string $namespace - Identifiant du dossier de vues (ou chemin direct si $path est null)
     * @param null|string $path - Chemin du dossier contenant les vues
     */
    public function addPath(string $namespace, ?string $path = null): void {
        // Si $path est null, on considère que $namespace est directement le chemin
        // et on l'associe au namespace par défaut
        if (is_null($path)) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            // Sinon, on associe le chemin $path au namespace spécifié
            $this->paths[$namespace] = $path;
        }
    }
    
    /**
     * Permet de rendre une vue (générer le HTML)
     * Le chemin peut être précisé avec des namespaces ajoutés via addPath()
     * Exemples d'utilisation:
     * $this->render('@blog/view');  // Utilise le namespace 'blog'
     * $this->render('view');        // Utilise le namespace par défaut
     * 
     * @param string $view - Nom de la vue à rendre (avec ou sans namespace)
     * @param array $params - Variables à passer à la vue
     * @return string - Le contenu HTML généré par la vue
     */
    public function render(string $view, array $params = []): string {
        // Détermination du chemin complet du fichier de vue
        if ($this->hasNamespace($view)) {
            // Si la vue commence par @, on remplace le namespace par son chemin
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            // Sinon, on utilise le namespace par défaut
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }
        
        // Démarre la mise en mémoire tampon de la sortie
        // Cela permet de capturer tout le contenu généré au lieu de l'envoyer directement au navigateur
        ob_start();
        
        // Rend l'instance de Renderer disponible dans la vue sous le nom $renderer
        $renderer = $this;
        
        // Extraction des variables globales dans la portée locale
        // Chaque clé du tableau devient une variable disponible dans la vue
        extract($this->globals);
        
        // Extraction des variables spécifiques à cette vue
        extract($params);
        
        // Inclusion du fichier de vue
        // Toutes les variables extraites sont maintenant disponibles dans ce fichier
        require($path);
        
        // Récupère le contenu généré et vide la mémoire tampon
        return ob_get_clean();
    }
    
    /**
     * Permet d'ajouter des variables globales accessibles à toutes les vues
     *
     * @param string $key - Nom de la variable
     * @param mixed $value - Valeur de la variable
     */
    public function addGlobal(string $key, $value): void {
        $this->globals[$key] = $value;
    }
    
    /**
     * Vérifie si une vue utilise un namespace (commence par @)
     * 
     * @param string $view - Nom de la vue
     * @return bool - True si la vue utilise un namespace
     */
    private function hasNamespace(string $view): bool {
        return $view[0] === '@';
    }
    
    /**
     * Extrait le nom du namespace d'une vue
     * Par exemple, pour '@blog/article', retourne 'blog'
     * 
     * @param string $view - Nom de la vue avec namespace
     * @return string - Le nom du namespace
     */
    private function getNamespace(string $view): string {
        // Extrait la partie entre @ et le premier /
        return substr($view, 1, strpos($view, '/') - 1);
    }
    
    /**
     * Remplace le préfixe du namespace (@namespace) par le chemin correspondant
     * 
     * @param string $view - Nom de la vue avec namespace
     * @return string - Le chemin complet vers le fichier de vue (sans extension)
     */
    private function replaceNamespace(string $view): string {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }
}