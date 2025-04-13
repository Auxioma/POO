<?php
// Le namespace permet d’organiser le code en "espaces de noms" pour éviter les conflits entre classes.
// Ici, on indique que cette classe fait partie du système de routage du framework.
namespace Framework\Router;

/**
 * La classe Route représente une route qui a été trouvée ("matchée") à partir d'une URL.
 * Elle contient trois informations : le nom de la route, le callback à exécuter, et les paramètres.
 */
class Route
{
    /**
     * @var string
     * Le nom de la route (utile pour générer des URLs ou faire des redirections).
     * Exemple : "post.show", "user.profile", etc.
     */
    private $name;

    /**
     * @var callable
     * Le callback (fonction, méthode ou contrôleur) à exécuter quand cette route est appelée.
     * Cela peut être une chaîne de type "App\Controller\BlogController::show"
     * ou une fonction anonyme.
     */
    private $callback;

    /**
     * @var array
     * Les paramètres extraits de l’URL lors du "match".
     * Exemple : si l’URL est /blog/42, le paramètre peut être ['id' => 42].
     */
    private $parameters;

    /**
     * Constructeur de la classe Route.
     * Il initialise les propriétés avec les valeurs passées en argument.
     *
     * @param string $name Le nom de la route.
     * @param string|callable $callback Le callback associé à la route (à exécuter si elle correspond).
     * @param array $parameters Les paramètres extraits de l'URL dynamique.
     */
    public function __construct(string $name, $callback, array $parameters)
    {
        // On stocke les valeurs dans les propriétés de la classe.
        $this->name = $name;
        $this->callback = $callback;
        $this->parameters = $parameters;
    }

    /**
     * Méthode pour récupérer le nom de la route.
     * Très utile pour identifier une route dans une application.
     *
     * @return string Le nom de la route.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Méthode pour récupérer le callback de la route.
     * Ce callback est ce qui sera exécuté lorsque la route est appelée.
     *
     * @return string|callable Le contrôleur ou la fonction à appeler.
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Méthode pour récupérer les paramètres extraits de l'URL.
     * Ces paramètres sont utiles pour transmettre des données au contrôleur.
     *
     * @return string[] Tableau associatif des paramètres d'URL (ex: ['id' => 42]).
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
}
