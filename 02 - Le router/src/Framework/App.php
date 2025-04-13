<?php

// On déclare un espace de noms (namespace), c’est comme un dossier logique pour organiser ton code.
// Cela permet aussi d’éviter les conflits de noms entre différentes classes portant le même nom.
namespace Framework;

// On importe les classes externes dont on a besoin pour gérer les requêtes et les réponses HTTP.
// Ces classes sont fournies par des bibliothèques conformes aux standards PSR (interopérabilité PHP).
use GuzzleHttp\Psr7\Response; // Cette classe permet de créer des objets Response facilement.
use Psr\Http\Message\ResponseInterface; // Interface pour toutes les réponses HTTP
use Psr\Http\Message\ServerRequestInterface; // Interface pour les requêtes HTTP entrantes (URL, méthode, etc.)

/**
 * La classe App est **le noyau de ton application web**.
 * C’est elle qui :
 * - charge tous les modules actifs de l’application,
 * - installe les routes dans le routeur,
 * - traite chaque requête HTTP (URL) reçue,
 * - exécute la bonne fonction en réponse à cette requête,
 * - et retourne une réponse HTTP (HTML, JSON, etc.).
 */
class App
{
    /**
     * Tableau qui va contenir tous les modules de l'application.
     * Un module est une “brique” de ton site web (ex : BlogModule, AuthModule, AdminModule…).
     * Chaque module peut enregistrer ses propres routes.
     * @var array
     */
    private $modules = [];

    /**
     * Le routeur permet d'associer une URL à une fonction (souvent un contrôleur).
     * C’est un élément essentiel dans tout framework web.
     * Exemple : "/blog/mon-article" → BlogController::show()
     * @var Router
     */
    private $router;

    /**
     * Le constructeur est exécuté automatiquement quand on crée un objet de cette classe.
     * Exemple : $app = new App([BlogModule::class, AuthModule::class]);
     *
     * @param string[] $modules Liste des modules (noms de classes) à charger automatiquement.
     */
    public function __construct(array $modules = [])
    {
        // On crée une instance du routeur pour pouvoir enregistrer des routes.
        $this->router = new Router();

        // On parcourt tous les modules passés en paramètre du constructeur.
        foreach ($modules as $module) {
            // On instancie chaque module, en lui donnant le routeur.
            // Chaque module peut ainsi appeler $router->get(...) pour enregistrer ses routes.
            $this->modules[] = new $module($this->router);
        }
    }

    /**
     * Cette méthode exécute le cœur du framework.
     * Elle reçoit une requête HTTP (généralement automatiquement via un serveur comme Apache ou Nginx)
     * et doit retourner une réponse HTTP adaptée.
     *
     * @param ServerRequestInterface $request L’objet représentant la requête HTTP du client.
     * @return ResponseInterface L’objet réponse à envoyer au client (navigateur ou API).
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        // On récupère le chemin de l’URL demandée, par exemple "/blog/mon-article"
        $uri = $request->getUri()->getPath();

        // Vérification : si l’URL se termine par un slash ("/"), on redirige vers la version sans slash.
        // Exemple : "/blog/" redirigé vers "/blog"
        // C’est une bonne pratique SEO pour éviter le contenu dupliqué.
        if (!empty($uri) && $uri[-1] === "/") {
            // Création d'une nouvelle réponse HTTP avec un statut 301 (redirection permanente).
            // On ajoute un en-tête "Location" pour indiquer la nouvelle URL.
            return (new Response())
                ->withStatus(301) // 301 = redirection permanente
                ->withHeader('Location', substr($uri, 0, -1)); // On enlève le "/" final
        }

        // On demande au routeur de trouver une route qui correspond à la requête actuelle.
        // Il va comparer la méthode HTTP (GET, POST...) et l’URL ("/blog/article-12", etc.)
        $route = $this->router->match($request);

        // Si aucune route ne correspond, cela signifie que l’URL demandée n’existe pas.
        // On retourne alors une réponse HTTP avec le statut 404 (Not Found).
        if (is_null($route)) {
            return new Response(404, [], '<h1>Erreur 404 : Page non trouvée</h1>');
        }

        // Si une route est trouvée, elle peut contenir des **paramètres** extraits de l’URL.
        // Exemple : /blog/article-mon-slug-15 → ['slug' => 'mon-slug', 'id' => '15']
        $params = $route->getParams();

        // Ces paramètres doivent être rendus accessibles dans le contrôleur.
        // Pour cela, on les ajoute à la requête via `withAttribute`.
        // array_reduce permet de parcourir chaque clé et valeur du tableau $params.
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            // On ajoute chaque paramètre à l’objet $request
            return $request->withAttribute($key, $params[$key]);
        }, $request);

        // Maintenant qu'on a une requête bien préparée, on peut exécuter le contrôleur associé.
        // Le contrôleur est généralement une fonction ou une méthode.
        // On l’exécute en lui passant la requête en paramètre.
        $response = call_user_func_array($route->getCallback(), [$request]);

        // 🧠 Le contrôleur peut retourner plusieurs types de résultats.

        // Si le résultat est une chaîne de caractères (souvent du HTML),
        // on crée une réponse HTTP avec un statut 200 (OK) et ce contenu.
        if (is_string($response)) {
            return new Response(200, [], $response);
        }

        // Si le résultat est déjà un objet de type ResponseInterface (ex : JSON, HTML complet),
        // on le retourne tel quel.
        elseif ($response instanceof ResponseInterface) {
            return $response;
        }

        // Si le résultat n’est ni une chaîne ni une réponse HTTP valide,
        // c’est une erreur de développement. On lance une exception.
        else {
            throw new \Exception('La réponse retournée n’est pas valide. Elle doit être une chaîne ou une instance de ResponseInterface.');
        }
    }
}
