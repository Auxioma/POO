<?php

// On déclare l’espace de nom du renderer
namespace Framework\Renderer;

// Déclaration de l’interface RendererInterface
interface RendererInterface
{
    /**
     * Méthode à implémenter pour ajouter un chemin de vues
     * Exemple : $renderer->addPath('blog', '/chemin/vers/vues/blog');
     * 
     * @param string $namespace Le nom du namespace (ex: 'blog')
     * @param null|string $path Le chemin des vues pour ce namespace
     */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * Méthode à implémenter pour afficher une vue
     * Exemple : $renderer->render('@blog/view', ['titre' => 'Bienvenue']);
     * 
     * @param string $view Le nom de la vue à afficher (ex: '@blog/index')
     * @param array $params Tableau associatif contenant les variables à passer à la vue
     * @return string Le contenu HTML généré par la vue
     */
    public function render(string $view, array $params = []): string;

    /**
     * Méthode à implémenter pour définir des variables accessibles globalement dans toutes les vues
     * Exemple : $renderer->addGlobal('user', $utilisateur);
     *
     * @param string $key Le nom de la variable
     * @param mixed $value La valeur de cette variable
     */
    public function addGlobal(string $key, $value): void;
}
