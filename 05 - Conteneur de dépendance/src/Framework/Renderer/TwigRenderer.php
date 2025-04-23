<?php
namespace Framework\Renderer;

/**
* Implémentation de RendererInterface utilisant le moteur de template Twig
* Twig est un moteur de template plus puissant que PHP avec une syntaxe plus élégante
* et des fonctionnalités avancées (héritage, inclusion, macros, etc.)
*/
class TwigRenderer implements RendererInterface
{
   /**
    * Instance de l'environnement Twig qui gère le rendu des templates
    * @var \Twig\Environment
    */
    private $twig;

   /**
    * Chargeur de fichiers Twig qui gère les chemins vers les templates
    * @var FilesystemLoader
    */
    private $loader;

   /**
    * Constructeur qui initialise le renderer avec les composants Twig nécessaires
    *
    * @param FilesystemLoader $loader Le chargeur de fichiers qui gère les chemins des templates
    * @param \Twig\Environment $twig L'environnement Twig configuré
    */
    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        $this->loader = $loader;
        $this->twig = $twig;
    }

   /**
    * Permet d'ajouter un chemin pour charger les vues
    * Délègue l'opération au chargeur de fichiers Twig
    *
    * @param string $namespace Identifiant pour ce groupe de vues (ex: 'blog', 'admin')
    * @param string|null $path Chemin vers le dossier contenant les vues
    */
    public function addPath(string $namespace, ?string $path = null): void
    {
        // Ajoute le chemin au loader Twig avec le namespace spécifié
        $this->loader->addPath($path, $namespace);
    }

   /**
    * Permet de rendre une vue avec Twig
    * Délègue le rendu à l'environnement Twig
    *
    * @param string $view Nom de la vue à rendre (avec ou sans namespace)
    * @param array $params Variables à passer à la vue pour le rendu
    * @return string Le contenu HTML/texte généré par la vue
    */
    public function render(string $view, array $params = []): string
    {
        // Ajoute l'extension .twig au nom de la vue et délègue le rendu à Twig
        return $this->twig->render($view . '.twig', $params);
    }

   /**
    * Permet d'ajouter des variables globales accessibles à toutes les vues
    * Délègue l'opération à l'environnement Twig
    *
    * @param string $key Nom de la variable
    * @param mixed $value Valeur de la variable (peut être de tout type)
    */
    public function addGlobal(string $key, $value): void
    {
        // Ajoute la variable globale à l'environnement Twig
        $this->twig->addGlobal($key, $value);
    }
}
