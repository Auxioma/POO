<?php
namespace App\Blog\Actions;

// Importation des classes nécessaires pour gérer les actions du blog
use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Classe BlogAction
 * 
 * Cette classe gère les actions liées au blog, comme afficher la liste des articles
 * ou afficher un article en détail. Elle utilise des services comme le moteur de rendu,
 * le routeur et la table des articles.
 */
class BlogAction
{
    /**
     * @var RendererInterface
     * Service de rendu pour afficher les vues HTML.
     */
    private $renderer;

    /**
     * @var Router
     * Service de routage pour gérer les redirections ou la génération d'URL.
     */
    private $router;

    /**
     * @var PostTable
     * Accès à la table des articles pour interagir avec les données des articles.
     */
    private $postTable;

    /**
     * Inclusion du trait RouterAwareAction
     * 
     * Ce trait fournit des méthodes supplémentaires pour les actions liées au routage.
     */
    use RouterAwareAction;

    /**
     * Constructeur
     * 
     * @param RendererInterface $renderer Instance du moteur de rendu.
     * @param Router $router Instance du routeur.
     * @param PostTable $postTable Instance pour accéder aux données des articles.
     */
    public function __construct(RendererInterface $renderer, Router $router, PostTable $postTable)
    {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postTable = $postTable;
    }

    /**
     * Méthode principale appelée par le routeur
     * 
     * Cette méthode détermine quelle action exécuter en fonction de la requête.
     * Si un identifiant d'article est présent, elle appelle la méthode `show`.
     * Sinon, elle appelle la méthode `index`.
     * 
     * @param Request $request Requête HTTP.
     * @return ResponseInterface|string
     */
    public function __invoke(Request $request)
    {
        if ($request->getAttribute('id')) {
            // Si un ID est présent dans la requête, on affiche un article spécifique.
            return $this->show($request);
        }
        // Sinon, on affiche la liste des articles.
        return $this->index($request);
    }

    /**
     * Affiche la liste des articles du blog
     * 
     * Cette méthode récupère une liste paginée d'articles et les passe à la vue.
     * 
     * @param Request $request Requête HTTP.
     * @return string Contenu HTML de la page des articles.
     */
    public function index(Request $request): string
    {
        // Récupération des paramètres de la requête (comme la page `p`).
        $params = $request->getQueryParams();

        // Récupère les articles paginés (12 articles par page, numéro de page issu des paramètres).
        $posts = $this->postTable->findPaginated(12, $params['p'] ?? 1);

        // Rend la vue `index` avec la liste des articles.
        return $this->renderer->render('@blog/index', compact('posts'));
    }

    /**
     * Affiche un article spécifique
     * 
     * Cette méthode récupère un article en fonction de son ID, vérifie que le slug est correct,
     * et affiche la vue correspondante. Si le slug est incorrect, elle redirige vers l'URL correcte.
     * 
     * @param Request $request Requête HTTP.
     * @return ResponseInterface|string Contenu HTML ou redirection.
     */
    public function show(Request $request)
    {
        // Récupération du slug et de l'article à partir des attributs de la requête.
        $slug = $request->getAttribute('slug');
        $post = $this->postTable->find($request->getAttribute('id'));

        // Si le slug dans l'URL ne correspond pas à celui de l'article, on redirige.
        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }

        // Rend la vue `show` avec les détails de l'article.
        return $this->renderer->render('@blog/show', [
            'post' => $post
        ]);
    }
}