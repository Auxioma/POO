<?php

namespace Framework\Renderer;

/**
 * Interface RendererInterface
 * 
 * Cette interface définit les méthodes nécessaires pour un moteur de rendu.
 * Les implémentations de cette interface permettent de gérer les vues,
 * les variables globales et les chemins pour les fichiers de vue.
 */
interface RendererInterface
{
    /**
     * Permet d'ajouter un chemin pour charger les vues.
     * 
     * Cette méthode permet de définir un chemin où les fichiers de vue
     * sont stockés, avec la possibilité d'utiliser des namespaces
     * pour organiser les vues par module ou fonctionnalité.
     * 
     * @param string $namespace Le namespace pour organiser les vues.
     * @param null|string $path Le chemin des vues associé au namespace.
     */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * Permet de rendre une vue.
     * 
     * Cette méthode génère et retourne le contenu HTML d'une vue.
     * Le chemin de la vue peut inclure un namespace ajouté via `addPath`.
     * Par exemple :
     * - $this->render('@blog/view', ['param1' => 'value']);
     * - $this->render('view', ['param2' => 'value']);
     * 
     * @param string $view Chemin ou namespace de la vue.
     * @param array $params Paramètres à passer à la vue.
     * @return string Contenu HTML rendu de la vue.
     */
    public function render(string $view, array $params = []): string;

    /**
     * Permet d'ajouter des variables globales accessibles à toutes les vues.
     * 
     * Les variables globales sont disponibles dans toutes les vues et
     * permettent d'ajouter des données communes comme un titre, un utilisateur
     * connecté, ou d'autres informations récurrentes.
     * 
     * @param string $key Nom de la variable.
     * @param mixed $value Valeur de la variable.
     */
    public function addGlobal(string $key, $value): void;
}