<?php
// On place cette classe dans le namespace Framework\Router.
// Cela veut dire qu’elle fait partie du "module" de routage de ton framework.
namespace Framework\Router;

/**
 * Classe Route
 * Cette classe représente une route qui a été trouvée (matchée) par le Router.
 * Par exemple : pour l’URL "/blog/mon-article", une Route correspondante va être créée.
 */
class Route
{

    /**
     * Nom de la route.
     * Exemple : "blog.show"
     * @var string
     */
    private $name;

    /**
     * Fonction (callback) à exécuter si la route est utilisée.
     * Généralement, c’est une méthode de contrôleur.
     * @var callable
     */
    private $callback;

    /**
     * Paramètres extraits de l’URL.
     * Exemple : ['slug' => 'mon-article']
     * @var array
     */
    private $parameters;

    /**
     * Constructeur de la route.
     * Il permet d’enregistrer le nom, le callback, et les paramètres associés à une URL.
     *
     * @param string $name Le nom de la route (utilisé pour la génération d’URL)
     * @param callable $callback La fonction à appeler quand la route est utilisée
     * @param array $parameters Les paramètres extraits de l’URL (ex: slug, id…)
     */
    public function __construct(string $name, callable $callback, array $parameters)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->parameters = $parameters;
    }

    /**
     * Retourne le nom de la route.
     * Ce nom est utile pour retrouver une route plus tard (ex: pour générer une URL)
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Retourne la fonction à appeler quand cette route est utilisée.
     * Ce callback sera appelé dans App.php avec la requête en paramètre.
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * Retourne les paramètres extraits de l’URL dynamique.
     * Exemple : pour "/blog/mon-slug", si l’URL correspond à "/blog/{slug}", ça retourne ['slug' => 'mon-slug']
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
}
