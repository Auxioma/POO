<?php

namespace Framework\Renderer;

/**
* Interface pour les moteurs de rendu de vues
* Cette interface définit les méthodes que tout moteur de rendu doit implémenter
* Permet d'avoir plusieurs implémentations interchangeables (PHP, Twig, etc.)
*/
interface RendererInterface
{
   /**
    * Permet d'ajouter un chemin pour charger les vues
    *
    * @param string $namespace Identifiant pour ce groupe de vues (ex: 'blog', 'admin')
    * @param string|null $path Chemin vers le dossier contenant les vues
    *                          Si null, $namespace est considéré comme le chemin par défaut
    */
    public function addPath(string $namespace, ?string $path = null): void;

   /**
    * Permet de rendre une vue et retourner son contenu sous forme de chaîne
    *
    * Le chemin peut être précisé avec des namespaces ajoutés via addPath()
    * Exemples d'utilisation:
    *   $this->render('@blog/view');  // Utilise le namespace 'blog'
    *   $this->render('view');        // Utilise le namespace par défaut
    *
    * @param string $view Nom de la vue à rendre (avec ou sans namespace)
    * @param array $params Variables à passer à la vue pour le rendu
    * @return string Le contenu HTML/texte généré par la vue
    */
    public function render(string $view, array $params = []): string;

   /**
    * Permet d'ajouter des variables globales accessibles à toutes les vues
    * Ces variables seront disponibles dans tous les templates rendus par ce moteur
    *
    * @param string $key Nom de la variable
    * @param mixed $value Valeur de la variable (peut être de tout type)
    */
    public function addGlobal(string $key, $value): void;
}
