<?php
// Définit l'espace de noms pour cette classe
namespace App\Blog;

// Importe les classes nécessaires
use App\Blog\Actions\BlogAction;  // Contrôleur qui gère les actions du blog
use Framework\Module;  // Classe de base pour tous les modules de l'application
use Framework\Renderer\RendererInterface;  // Interface pour le système de rendu des templates
use Framework\Router;  // Gestionnaire de routes de l'application

/**
* Module principal du blog qui initialise les routes et les vues
* Cette classe étend la classe Module du framework
*/
class BlogModule extends Module
{
   /**
    * Constante qui définit le chemin vers le fichier de configuration du module
    * Ce fichier contient les définitions de dépendances spécifiques au module
    */
   const DEFINITIONS = __DIR__ . '/config.php';

   /**
    * Constructeur du module qui configure les chemins de vues et les routes
    * Les dépendances sont automatiquement injectées par le conteneur DI
    * 
    * @param string $prefix Préfixe URL pour toutes les routes du module (ex: /blog)
    * @param Router $router Gestionnaire de routes pour enregistrer les routes du module
    * @param RendererInterface $renderer Moteur de rendu pour enregistrer les chemins de vues
    */
   public function __construct(string $prefix, Router $router, RendererInterface $renderer)
   {
       // Ajoute un chemin vers les vues du module avec le namespace 'blog'
       // Cela permet d'utiliser @blog/xxx dans les appels de rendu
       $renderer->addPath('blog', __DIR__ . '/views');
       
       // Enregistre la route pour la page d'index du blog
       // Ex: /blog -> BlogAction (liste des articles)
       $router->get($prefix, BlogAction::class, 'blog.index');
       
       // Enregistre la route pour afficher un article spécifique
       // Ex: /blog/mon-article -> BlogAction (affiche l'article "mon-article")
       // Le pattern [a-z\-0-9]+ restreint les slugs aux lettres minuscules, chiffres et tirets
       $router->get($prefix . '/{slug:[a-z\-0-9]+}', BlogAction::class, 'blog.show');
   }
}