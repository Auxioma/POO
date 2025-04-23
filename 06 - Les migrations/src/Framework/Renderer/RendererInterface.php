<?php

namespace Framework\Renderer;

// L'interface RendererInterface définit les méthodes que doit implémenter toute classe de rendu de vues
// Elle permet d'assurer que toutes les classes de rendu de vues respectent une certaine structure.

interface RendererInterface
{
    /**
     * Permet de rajouter un chemin pour charger les vues
     * Cette méthode permet d'associer un "namespace" à un chemin de fichiers de vue.
     * Le "namespace" permet de regrouper les vues sous une certaine catégorie.
     * Exemple : ajouter un chemin pour les vues du blog avec un namespace spécifique.
     *
     * @param string $namespace - Le nom du namespace (ex: 'blog', 'admin')
     * @param null|string $path - Le chemin vers le dossier où se trouvent les vues
     */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * Permet de rendre une vue
     * Cette méthode va charger le fichier de vue spécifié et y injecter des paramètres.
     * Si la vue utilise un namespace, le chemin sera calculé en fonction de celui-ci.
     * Exemple : $this->render('@blog/view'); ou $this->render('view');
     *
     * @param string $view - Le nom de la vue à rendre
     * @param array $params - Les variables à envoyer à la vue
     * @return string - Le contenu généré de la vue (par exemple le HTML)
     */
    public function render(string $view, array $params = []): string;

    /**
     * Permet de rajouter des variables globales à toutes les vues
     * Cette méthode permet d'ajouter des variables qui seront disponibles dans toutes les vues,
     * sans avoir à les passer explicitement à chaque fois que la vue est rendue.
     * Exemple : Ajouter une variable globale qui contient les informations sur l'utilisateur.
     *
     * @param string $key - Le nom de la variable globale
     * @param mixed $value - La valeur de la variable globale
     */
    public function addGlobal(string $key, $value): void;
}
