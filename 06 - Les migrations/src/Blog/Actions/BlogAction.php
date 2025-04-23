<?php
// On déclare le début du fichier PHP

namespace App\Blog\Actions;
// ------------------------------------------------------------
// Le namespace permet de ranger les classes comme dans des dossiers.
// Ici, la classe BlogAction fait partie du module "Blog", dans le sous-dossier "Actions".
// Cela évite aussi les conflits de noms entre différentes classes du projet.
// ------------------------------------------------------------

use Framework\Renderer\RendererInterface;
// On importe une interface qui permet d’afficher des vues HTML (ou autre).
// Une "interface" est un contrat : elle dit quelles méthodes une classe doit implémenter.

use Psr\Http\Message\ServerRequestInterface as Request;
// On importe une interface normalisée représentant une requête HTTP.
// Elle fait partie du standard PSR-7 (utilisé par beaucoup de frameworks modernes).

// ------------------------------------------------------------
// DÉCLARATION DE LA CLASSE BlogAction
// ------------------------------------------------------------

class BlogAction
{
    /**
     * @var RendererInterface
     * Déclaration d'une propriété privée : $renderer
     * Ce sera un objet qui sait "rendre" (afficher) des vues.
     * On suit ici le principe d'injection de dépendance : on ne crée pas l'objet dans la classe, on le reçoit.
     */
    private $renderer;

    /**
     * Constructeur de la classe
     *
     * @param RendererInterface $renderer : le moteur de rendu (par exemple Twig)
     */
    public function __construct(RendererInterface $renderer)
    {
        // Le conteneur d'injection (DI Container) va passer automatiquement le bon objet ici.
        $this->renderer = $renderer;
    }

    /**
     * Méthode spéciale "__invoke" : rend l'objet "appelable" comme une fonction
     *
     * @param Request $request : l'objet représentant la requête HTTP (avec l’URL, les données GET/POST, etc.)
     * @return mixed : une réponse générée (souvent une chaîne HTML)
     */
    public function __invoke(Request $request)
    {
        // On récupère un paramètre "slug" depuis l'URL (ex: /blog/article-mon-titre)
        $slug = $request->getAttribute('slug');

        // Si un "slug" est présent dans l'URL, on veut afficher l’article correspondant
        if ($slug) {
            return $this->show($slug); // appel de la méthode show()
        }

        // Sinon, on affiche la liste des articles du blog
        return $this->index(); // appel de la méthode index()
    }

    /**
     * Affiche la page d’accueil du blog (liste des articles)
     *
     * @return string : HTML généré par Twig
     */
    public function index(): string
    {
        // On demande au moteur de rendu d'afficher la vue "@blog/index"
        // Le "@" est souvent utilisé pour indiquer le nom du module (ici : blog)
        return $this->renderer->render('@blog/index');
    }

    /**
     * Affiche un article spécifique selon son "slug"
     *
     * @param string $slug : identifiant unique de l’article dans l’URL
     * @return string : HTML généré par la vue Twig
     */
    public function show(string $slug): string
    {
        // On passe le slug à la vue pour pouvoir l'afficher ou s’en servir (ex: charger l'article depuis la BDD)
        return $this->renderer->render('@blog/show', [
            'slug' => $slug // Ce tableau représente les variables accessibles dans la vue Twig
        ]);
    }
}
