<?php
namespace Framework\Router;

/**
 * Classe représentant une route après la correspondance avec une requête
 */
class Route
{
    /**
     * @var string Le nom de la route
     */
    private string $name;
    
    /**
     * @var callable Le callback à exécuter
     */
    private $callback;
    
    /**
     * @var array Les paramètres extraits de l'URL
     */
    private array $parameters;
    
    /**
     * Constructeur de la route
     * 
     * @param string $name Nom de la route
     * @param callable $callback Fonction à exécuter
     * @param array $parameters Paramètres de l'URL
     */
    public function __construct(string $name, callable $callback, array $parameters = [])
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->parameters = $parameters;
    }
    
    /**
     * Obtenir le nom de la route
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Obtenir le callback
     * 
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }
    
    /**
     * Obtenir les paramètres
     * 
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
    
    /**
     * Alias de getParameters() pour compatibilité
     * 
     * @return array
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
    
    /**
     * Obtenir un paramètre spécifique
     * 
     * @param string $name Nom du paramètre
     * @param mixed $default Valeur par défaut si paramètre non trouvé
     * @return mixed
     */
    public function getParameter(string $name, $default = null)
    {
        return $this->parameters[$name] ?? $default;
    }
}