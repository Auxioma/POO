<?php
namespace Framework\Database;

// Importation de l'interface AdapterInterface de Pagerfanta
use Pagerfanta\Adapter\AdapterInterface;

/**
 * Classe PaginatedQuery
 * 
 * Cette classe implémente l'interface AdapterInterface de Pagerfanta
 * pour fournir une pagination efficace des résultats d'une requête SQL.
 */
class PaginatedQuery implements AdapterInterface
{
    /**
     * @var \PDO
     * Instance de PDO pour interagir avec la base de données.
     */
    private $pdo;

    /**
     * @var string
     * Requête SQL permettant de récupérer les résultats paginés.
     */
    private $query;

    /**
     * @var string
     * Requête SQL permettant de compter le nombre total de résultats.
     */
    private $countQuery;

    /**
     * @var string
     * Nom de la classe d'entité dans laquelle les résultats seront mappés.
     */
    private $entity;

    /**
     * Constructeur de la classe PaginatedQuery.
     * 
     * @param \PDO $pdo Instance de PDO pour exécuter les requêtes.
     * @param string $query Requête SQL pour récupérer les résultats paginés.
     * @param string $countQuery Requête SQL pour compter le nombre total de résultats.
     * @param string $entity Classe d'entité pour mapper les résultats.
     */
    public function __construct(\PDO $pdo, string $query, string $countQuery, string $entity)
    {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $countQuery;
        $this->entity = $entity;
    }

    /**
     * Retourne le nombre total de résultats.
     * 
     * Cette méthode exécute la requête `$countQuery` pour compter
     * le nombre total de lignes correspondant à la pagination.
     * 
     * @return int Le nombre total de résultats.
     */
    public function getNbResults(): int
    {
        // Exécution de la requête pour compter les résultats
        return $this->pdo->query($this->countQuery)->fetchColumn();
    }

    /**
     * Retourne une tranche (slice) des résultats.
     * 
     * Cette méthode exécute la requête `$query` en ajoutant une clause `LIMIT`
     * pour récupérer une portion des résultats, selon les paramètres `offset` et `length`.
     * 
     * @param int $offset La position de départ pour les résultats.
     * @param int $length Le nombre de résultats à retourner.
     * 
     * @return array|\Traversable Une tranche des résultats sous forme de tableau.
     */
    public function getSlice($offset, $length): array
    {
        // Préparation de la requête avec la clause LIMIT
        $statement = $this->pdo->prepare($this->query . ' LIMIT :offset, :length');

        // Liaison des paramètres pour l'offset et la longueur
        $statement->bindParam('offset', $offset, \PDO::PARAM_INT);
        $statement->bindParam('length', $length, \PDO::PARAM_INT);

        // Configuration du mode de récupération pour mapper les résultats à des instances de la classe spécifiée
        $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);

        // Exécution de la requête
        $statement->execute();

        // Retourne tous les résultats sous forme de tableau
        return $statement->fetchAll();
    }
}