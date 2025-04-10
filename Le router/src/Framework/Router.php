<?php
// Déclaration d'un espace de noms (namespace).
// EXPLICATION POUR DÉBUTANTS : Un namespace est comme un dossier virtuel qui permet d'organiser 
// le code et d'éviter les conflits de noms entre différentes classes. 
// C'est comme si on rangeait cette classe Router dans un dossier nommé "Framework".
namespace Framework;

// Importation de classes que notre Router va utiliser.
// EXPLICATION POUR DÉBUTANTS : Avec "use", on dit à PHP quelles classes on veut utiliser.
// C'est comme si on préparait nos outils avant de commencer à travailler.

// Notre propre classe Route qui sera utilisée pour représenter une route trouvée
use Framework\Router\Route;

// Interface PSR-7 pour représenter une requête HTTP standardisée
// EXPLICATION POUR DÉBUTANTS : PSR-7 est une norme qui définit comment les requêtes HTTP 
// doivent être représentées en PHP. C'est comme un contrat que différentes bibliothèques respectent.
use Psr\Http\Message\ServerRequestInterface;

// Classes du framework Laminas (anciennement Zend) qu'on va utiliser
// EXPLICATION POUR DÉBUTANTS : Laminas est un framework PHP qui fournit des composants réutilisables.
// Ici, on utilise leur système de routage car il est robuste et bien testé.
use Laminas\Router\Http\TreeRouteStack; // Le routeur principal qui gère l'arborescence des routes
use Laminas\Router\Http\Literal;        // Pour les routes fixes comme "/accueil"
use Laminas\Router\Http\Segment;         // Pour les routes avec paramètres comme "/article/{id}"
use Laminas\Http\PhpEnvironment\Request as LaminasRequest; // Représentation d'une requête HTTP pour Laminas
use Laminas\Router\RouteMatch;           // Résultat obtenu quand une route correspond à une requête

/**
 * Classe Router : Permet de gérer les routes de notre application web.
 * 
 * EXPLICATION POUR DÉBUTANTS : En POO, une classe est un modèle qui définit 
 * la structure et le comportement d'un objet. Cette classe Router est responsable
 * de gérer les URLs de notre site web et de décider quelle fonction exécuter
 * quand un utilisateur visite une page.
 */
class Router 
{
    /**
     * @var TreeRouteStack
     * Le routeur Laminas qui fait le gros du travail.
     * 
     * EXPLICATION POUR DÉBUTANTS : Une propriété (ou attribut) est une variable 
     * appartenant à une classe. Ce $router est un objet Laminas qui va nous aider
     * à gérer nos routes. Le "private" signifie que seules les méthodes de cette
     * classe peuvent y accéder.
     */
    private TreeRouteStack $router;
    
    /**
     * @var array
     * Tableau associatif qui stocke toutes nos routes avec leurs informations.
     * 
     * EXPLICATION POUR DÉBUTANTS : Ce tableau va contenir toutes les routes
     * que nous définirons. Chaque route aura un nom (clé du tableau) et des
     * informations associées (chemin, fonction à exécuter, etc.).
     */
    private array $routes = []; 

    /**
     * Constructeur de la classe Router.
     * 
     * EXPLICATION POUR DÉBUTANTS : Le constructeur est une méthode spéciale qui 
     * est appelée automatiquement quand on crée un nouvel objet avec "new Router()".
     * Il permet d'initialiser les propriétés de l'objet.
     */
    public function __construct()
    {
        // On crée une nouvelle instance du routeur Laminas
        // EXPLICATION POUR DÉBUTANTS : Avec "new", on crée un nouvel objet.
        // C'est comme fabriquer un outil à partir d'un plan (la classe).
        $this->router = new TreeRouteStack();
    }

    /**
     * Méthode pour enregistrer une route de type GET.
     * 
     * EXPLICATION POUR DÉBUTANTS : Une méthode est une fonction appartenant à une classe.
     * Celle-ci permet d'ajouter une nouvelle route à notre routeur. GET est la méthode HTTP
     * utilisée quand on visite simplement une page dans le navigateur.
     *
     * @param string $path Le chemin de l'URL (ex : "/contact")
     * @param callable $callable La fonction à exécuter quand cette URL est visitée
     * @param string $name Le nom unique donné à cette route
     * 
     * EXPLICATION POUR DÉBUTANTS : Les paramètres sont les informations qu'on doit fournir
     * quand on appelle cette méthode. "void" signifie que la méthode ne renvoie rien.
     */
    public function get(string $path, callable $callable, string $name): void
    {
        // On stocke les informations de la route dans notre tableau
        // EXPLICATION POUR DÉBUTANTS : $this fait référence à l'objet courant.
        // $this->routes est le tableau que nous avons défini plus haut.
        $this->routes[$name] = [
            'path' => $path,         // Le chemin de l'URL
            'callback' => $callable, // La fonction à exécuter
            'method' => 'GET'        // La méthode HTTP
        ];

        // On vérifie si la route contient des paramètres variables
        // EXPLICATION POUR DÉBUTANTS : strpos cherche la position d'un caractère dans une chaîne.
        // Si on trouve '{' dans le chemin, c'est qu'il y a un paramètre comme dans "/articles/{id}".
        if (strpos($path, '{') !== false) {
            // On convertit notre format {param} au format :param utilisé par Laminas
            // EXPLICATION POUR DÉBUTANTS : preg_replace est une fonction qui remplace du texte
            // selon un modèle (expression régulière). Ici, on remplace {texte} par :texte
            $laminasPath = preg_replace('/{([^}]+)}/', ':$1', $path);
            
            // On ajoute une route dynamique au routeur Laminas
            // EXPLICATION POUR DÉBUTANTS : On enregistre la route dans le routeur Laminas
            // en précisant qu'il s'agit d'un segment (route avec parties variables).
            $this->router->addRoute($name, [
                'type' => Segment::class,  // Type de route : avec paramètres
                'options' => [
                    'route' => $laminasPath,  // Le chemin converti au format Laminas
                    'defaults' => [
                        'method' => 'GET'     // Méthode HTTP par défaut
                    ],
                ],
            ]);
        } else {
            // Si l'URL ne contient pas de paramètres, on ajoute une route fixe
            // EXPLICATION POUR DÉBUTANTS : Une route "Literal" est une route fixe,
            // sans parties variables (comme "/contact" ou "/accueil").
            $this->router->addRoute($name, [
                'type' => Literal::class,  // Type de route : fixe
                'options' => [
                    'route' => $path,      // Le chemin tel quel
                    'defaults' => [
                        'method' => 'GET'  // Méthode HTTP par défaut
                    ],
                ],
            ]);
        }
    }

    /**
     * Méthode pour trouver la route correspondant à une requête HTTP.
     * 
     * EXPLICATION POUR DÉBUTANTS : Cette méthode permet de déterminer quelle route
     * correspond à l'URL actuelle demandée par l'utilisateur.
     *
     * @param ServerRequestInterface $request La requête HTTP à analyser
     * @return Route|null Une route si trouvée, null sinon
     * 
     * EXPLICATION POUR DÉBUTANTS : Cette méthode reçoit un objet requête et renvoie
     * soit un objet Route, soit null. Le caractère "?" dans "?Route" indique que
     * la valeur peut être null.
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        // On crée une requête au format attendu par Laminas
        // EXPLICATION POUR DÉBUTANTS : On doit "traduire" la requête PSR-7 en
        // une requête comprise par Laminas.
        $laminasRequest = new LaminasRequest();
        
        // On définit l'URI (le chemin de l'URL) de la requête
        // EXPLICATION POUR DÉBUTANTS : getUri()->getPath() récupère le chemin de l'URL
        // (ex: "/contact" dans "http://monsite.com/contact")
        $laminasRequest->setUri($request->getUri()->getPath());
        
        // On définit la méthode HTTP (GET, POST, etc.)
        // EXPLICATION POUR DÉBUTANTS : La méthode HTTP indique l'action demandée
        // (GET pour récupérer, POST pour envoyer des données, etc.)
        $laminasRequest->setMethod($request->getMethod());
       
        // On essaie de faire correspondre la requête à une route
        // EXPLICATION POUR DÉBUTANTS : On demande au routeur Laminas de trouver
        // une route qui correspond à notre requête.
        $result = $this->router->match($laminasRequest);
    
        // Si une correspondance est trouvée
        // EXPLICATION POUR DÉBUTANTS : instanceof vérifie si $result est bien
        // un objet de type RouteMatch. Si oui, c'est qu'une route a été trouvée.
        if ($result instanceof RouteMatch) {
            // On récupère le nom de la route qui correspond
            // EXPLICATION POUR DÉBUTANTS : On obtient le nom unique de la route
            // que nous avons défini lors de l'enregistrement.
            $routeName = $result->getMatchedRouteName();
            
            // On récupère la fonction associée à cette route
            // EXPLICATION POUR DÉBUTANTS : Le "?? null" est un opérateur de coalescence
            // qui retourne null si $this->routes[$routeName]['callback'] n'existe pas.
            $callback = $this->routes[$routeName]['callback'] ?? null;
            
            // Si aucune fonction n'est définie, on retourne null
            // EXPLICATION POUR DÉBUTANTS : Si on n'a pas de fonction à exécuter,
            // il n'y a pas de sens à retourner une route.
            if (!$callback) {
                return null;
            }
            
            // On récupère les paramètres de l'URL
            // EXPLICATION POUR DÉBUTANTS : Par exemple, dans "/articles/5",
            // on récupérerait ['id' => 5] si la route est "/articles/{id}".
            $params = $result->getParams();
            
            // On supprime les paramètres internes ajoutés par Laminas
            // EXPLICATION POUR DÉBUTANTS : unset supprime des éléments d'un tableau.
            // Ces clés sont ajoutées automatiquement par Laminas mais ne nous sont pas utiles.
            unset($params['controller'], $params['action'], $params['method']);
           
            // On crée et retourne un nouvel objet Route avec les informations trouvées
            // EXPLICATION POUR DÉBUTANTS : On encapsule toutes les informations de la route
            // dans un objet Route qu'on va pouvoir utiliser facilement ailleurs.
            return new Route(
                $routeName,  // Le nom de la route
                $callback,   // La fonction à exécuter
                $params      // Les paramètres extraits de l'URL
            );
        }
       
        // Si aucune route ne correspond, on retourne null
        // EXPLICATION POUR DÉBUTANTS : Le mot-clé "return" termine la fonction
        // et renvoie la valeur indiquée (ici, null).
        return null;
    }

    /**
     * Génère une URL à partir du nom d'une route et de ses paramètres.
     * 
     * EXPLICATION POUR DÉBUTANTS : Cette méthode fait l'inverse du routage :
     * au lieu de trouver une route à partir d'une URL, elle crée une URL
     * à partir d'une route. C'est utile pour créer des liens dans les vues.
     *
     * @param string $name Le nom de la route
     * @param array $params Les paramètres à insérer dans l'URL (par défaut un tableau vide)
     * @return string|null L'URL générée ou null si la route n'existe pas
     * 
     * EXPLICATION POUR DÉBUTANTS : Cette méthode renvoie soit une chaîne (l'URL),
     * soit null. Le tableau $params a une valeur par défaut (tableau vide).
     */
    public function generateUri(string $name, array $params = []): ?string
    {
        try {
            // On essaie d'utiliser le générateur d'URL de Laminas
            // EXPLICATION POUR DÉBUTANTS : try/catch permet de gérer les erreurs.
            // Si le code dans le bloc try génère une erreur, le bloc catch sera exécuté.
            return $this->router->assemble($params, ['name' => $name]);
        } catch (\Exception $e) {
            // Si Laminas ne peut pas générer l'URL, on essaie notre propre méthode
            // EXPLICATION POUR DÉBUTANTS : \Exception est la classe de base pour
            // toutes les erreurs en PHP. $e contient les détails de l'erreur.
            
            // Vérifie si la route existe dans notre tableau
            if (!isset($this->routes[$name])) {
                return null;  // Si la route n'existe pas, on retourne null
            }

            // On récupère le chemin de la route
            $path = $this->routes[$name]['path'];

            // Pour chaque paramètre, on remplace son placeholder dans l'URL
            // EXPLICATION POUR DÉBUTANTS : foreach parcourt chaque élément du tableau.
            // Ici, on remplace {paramName} par sa valeur dans le chemin.
            foreach ($params as $paramName => $paramValue) {
                $path = str_replace("{{$paramName}}", $paramValue, $path);
            }

            // On retourne l'URL générée
            return $path;
        }
    }
}