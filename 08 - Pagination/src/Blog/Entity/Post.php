<?php
namespace App\Blog\Entity;

/**
 * Classe représentant un article de blog (Post).
 * 
 * Cette classe est utilisée pour manipuler les données d'un article
 * de blog sous forme d'objet. Elle inclut des propriétés comme l'identifiant,
 * le nom, le slug, le contenu ainsi que les dates de création et de mise à jour.
 */
class Post
{
    /**
     * @var int|null $id
     * Identifiant unique de l'article.
     */
    public $id;

    /**
     * @var string|null $name
     * Titre ou nom de l'article.
     */
    public $name;

    /**
     * @var string|null $slug
     * Slug de l'article (utilisé pour les URL conviviales).
     */
    public $slug;

    /**
     * @var string|null $content
     * Contenu principal de l'article.
     */
    public $content;

    /**
     * @var string|\DateTime|null $created_at
     * Date de création de l'article (sous forme de chaîne ou d'instance DateTime).
     */
    public $created_at;

    /**
     * @var string|\DateTime|null $updated_at
     * Date de mise à jour de l'article (sous forme de chaîne ou d'instance DateTime).
     */
    public $updated_at;

    /**
     * Constructeur de la classe Post.
     * 
     * Si les propriétés `created_at` et `updated_at` sont définies sous
     * forme de chaînes de caractères, elles sont automatiquement converties
     * en objets DateTime pour simplifier leur manipulation.
     */
    public function __construct()
    {
        // Si la propriété `created_at` est définie, on la transforme en objet DateTime.
        if ($this->created_at) {
            $this->created_at = new \DateTime($this->created_at);
        }

        // Si la propriété `updated_at` est définie, on la transforme en objet DateTime.
        if ($this->updated_at) {
            $this->updated_at = new \DateTime($this->updated_at);
        }
    }
}