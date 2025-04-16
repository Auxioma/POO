<?php
// Déclare l'espace de noms (namespace) de cette classe, qui permet d'organiser le code
// et d'éviter les conflits de nom avec d'autres bibliothèques
namespace App\Blog\Actions;

// Importe les classes nécessaires avec leur espace de noms complet
use Framework\Renderer\RendererInterface;  // Interface pour le moteur de rendu des vues
use Psr\Http\Message\ServerRequestInterface as Request;  // Interface standardisée PSR-7 pour les requêtes HTTP

/**
* Classe qui gère les actions/contrôleurs pour le blog
* Elle traite les requêtes HTTP et retourne les réponses appropriées
*/
class BlogAction
{
   /**
    * Instance du moteur de rendu qui sera utilisée pour générer les vues
    * @var RendererInterface
    */
    private $renderer;

   /**
    * Constructeur qui initialise la classe avec ses dépendances
    * L'injection de dépendances se fait automatiquement par le conteneur DI
    *
    * @param RendererInterface $renderer Le moteur de rendu des vues
    */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

   /**
    * Méthode magique qui permet d'utiliser un objet comme une fonction
    * Elle est appelée automatiquement quand la classe est utilisée comme un callable
    * Sert de point d'entrée principal qui détermine quelle action exécuter
    *
    * @param Request $request La requête HTTP entrante
    * @return string Le contenu HTML à afficher
    */
    public function __invoke(Request $request)
    {
        // Récupère le paramètre 'slug' de la requête (défini dans la route)
        $slug = $request->getAttribute('slug');
       
        // Si un slug est présent, on affiche un article spécifique
        if ($slug) {
            return $this->show($slug);
        }
       
        // Sinon, on affiche la liste des articles
        return $this->index();
    }

   /**
    * Affiche la liste des articles du blog
    *
    * @return string Le HTML généré pour la page d'index
    */
    public function index(): string
    {
        // Utilise le moteur de rendu pour générer la vue
        // '@blog/index' fait référence au template 'index' dans le namespace 'blog'
        return $this->renderer->render('@blog/index');
    }

   /**
    * Affiche un article spécifique en fonction de son slug
    *
    * @param string $slug L'identifiant unique de l'article dans l'URL
    * @return string Le HTML généré pour la page de l'article
    */
    public function show(string $slug): string
    {
        // Rend le template avec les données nécessaires (ici le slug)
        // Ces données seront accessibles dans le template
        return $this->renderer->render('@blog/show', [
           'slug' => $slug
        ]);
    }
}
