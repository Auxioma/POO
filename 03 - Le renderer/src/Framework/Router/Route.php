<?php
// Définition du namespace pour cette classe
// Les namespaces permettent d'organiser le code et d'éviter les conflits de noms
namespace Framework\Router;

/**
 * Class Route
 * Représente une route trouvée (matched) par le router
 * Cette classe est utilisée pour stocker les informations d'une route après qu'elle ait été identifiée
 * comme correspondant à l'URL demandée
 */
class Route
{
    /**
     * Le nom de la route
     * Ce nom sert d'identifiant pour pouvoir générer des URLs à partir des routes
     * @var string
     */
    private $name;
    
    /**
     * La fonction ou méthode à appeler lorsque cette route est utilisée
     * Un callable peut être une fonction anonyme, une méthode de classe ou une fonction nommée
     * @var callable
     */
    private $callback;
    
    /**
     * Les paramètres extraits de l'URL
     * Par exemple, pour une route /blog/{slug}, si l'URL est /blog/mon-article,
     * les paramètres contiendront ['slug' => 'mon-article']
     * @var array
     */
    private $parameters;
    
    /**
     * Constructeur de la classe Route
     * 
     * @param string $name - Le nom de la route
     * @param callable $callback - La fonction à appeler pour cette route
     * @param array $parameters - Les paramètres extraits de l'URL
     */
    public function __construct(string $name, callable $callback, array $parameters)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->parameters = $parameters;
    }
    
    /**
     * Récupère le nom de la route
     * 
     * @return string - Le nom de la route
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Récupère la fonction/méthode associée à cette route
     * Cette fonction sera appelée par le dispatcher du router pour traiter la requête
     * 
     * @return callable - La fonction/méthode à appeler
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }
    
    /**
     * Récupère les paramètres extraits de l'URL
     * Ces paramètres sont généralement passés à la fonction de callback
     * 
     * @return array - Tableau associatif des paramètres (nom => valeur)
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
}