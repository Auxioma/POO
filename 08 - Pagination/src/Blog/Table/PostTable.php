<?php

namespace App\Blog\Table;

// Importation des classes nécessaires pour interagir avec les articles de blog.
use App\Blog\Entity\Post;
use Framework\Database\PaginatedQuery;
use Pagerfanta\Pagerfanta;

/**
 * Classe PostTable
 * 
 * Cette classe est responsable de l'interaction avec la base de données
 * pour les articles de blog. Elle fournit des méthodes pour paginer les articles
 * et récupérer un article spécifique.
 */
class PostTable
{
    /**
     * @var \PDO
     * Instance de PDO pour interagir avec la base de données.
     */
    private $pdo;

    /**
     * Constructeur de la classe PostTable.
     * 
     * @param \PDO $pdo Instance de PDO pour gérer les connexions à la base de données.
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère les articles paginés.
     * 
     * Cette méthode retourne une liste paginée des articles de blog,
     * triés par date de création (les plus récents en premier).
     * 
     * @param int $perPage Nombre d'articles par page.
     * @param int $currentPage Numéro de la page actuelle.
     * @return Pagerfanta Objet de pagination contenant les articles.
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        // Création d'une requête paginée à l'aide de PaginatedQuery.
        // La requête principale récupère tous les articles triés par `created_at` dans l'ordre décroissant.
        // La requête secondaire compte le nombre total d'articles pour la pagination.
        $query = new PaginatedQuery(
            $this->pdo,
            'SELECT * FROM posts ORDER BY created_at DESC',
            'SELECT COUNT(id) FROM posts',
            Post::class
        );

        // Retourne un objet Pagerfanta configuré avec le nombre d'articles par page et la page actuelle.
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * Récupère un article à partir de son ID.
     * 
     * Cette méthode permet de récupérer un article spécifique
     * en utilisant son identifiant unique.
     * 
     * @param int $id Identifiant de l'article.
     * @return Post Instance de la classe Post représentant l'article.
     */
    public function find(int $id): Post
    {
        // Préparation d'une requête SQL pour récupérer un article par son ID.
        $query = $this->pdo
            ->prepare('SELECT * FROM posts WHERE id = ?');

        // Exécution de la requête avec l'identifiant de l'article.
        $query->execute([$id]);

        // Configuration du mode de récupération pour que les résultats soient des instances de la classe Post.
        $query->setFetchMode(\PDO::FETCH_CLASS, Post::class);

        // Retourne le résultat de la requête (l'article récupéré).
        return $query->fetch();
    }
}