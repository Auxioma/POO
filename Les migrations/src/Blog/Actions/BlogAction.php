<?php
// On déclare le début du fichier PHP

namespace App\Blog\Actions;
// On définit l'espace de noms (namespace) de cette classe. Cela permet d'organiser le code comme des dossiers virtuels.
// Ici, la classe se trouve dans le module "Blog", dossier "Actions".

use Framework\Renderer\RendererInterface;
// On importe l’interface RendererInterface, qui permet d’afficher des vues (fichiers HTML par exemple).

use Psr\Http\Message\ServerRequestInterface as Request;
// On importe l’interface Request, qui représente une requête HTTP (URL, paramètres, etc.).

class BlogAction
{
    /**
     * @var RendererInterface
     * On déclare une propriété privée $renderer, de type RendererInterface.
     * Ce sera le moteur de rendu qu’on utilise pour afficher les pages HTML.
     */
    private $renderer;

    public function __construct(RendererInterface $renderer)
    {
        // Le constructeur reçoit une instance du moteur de rendu (RendererInterface)
        // Cette instance est automatiquement injectée grâce au conteneur de dépendances.
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request)
    {
        // Cette méthode rend la classe "appelable" comme une fonction
        // Elle est appelée automatiquement quand une route utilise cette classe comme contrôleur

        $slug = $request->getAttribute('slug');
        // On essaie de récupérer l’attribut "slug" depuis l’URL (ex: /blog/mon-article)

        if ($slug) {
            // S’il y a un slug dans l’URL, on appelle la méthode show() pour afficher l’article
            return $this->show($slug);
        }

        // Sinon, on affiche la liste des articles (page d'accueil du blog)
        return $this->index();
    }

    public function index(): string
    {
        // Cette méthode affiche la page d'accueil du blog
        // Elle utilise le moteur de rendu pour afficher le template @blog/index
        return $this->renderer->render('@blog/index');
    }

    public function show(string $slug): string
    {
        // Cette méthode affiche un article en particulier, selon son "slug"
        // On passe le slug au template pour qu’il puisse l’utiliser dans la page HTML
        return $this->renderer->render('@blog/show', [
            'slug' => $slug
        ]);
    }
}
