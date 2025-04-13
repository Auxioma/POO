<?php
// Début du fichier PHP
// Ce fichier est une migration : il sert à créer une table dans la base de données

use Phinx\Migration\AbstractMigration;
// On importe la classe de base AbstractMigration fournie par Phinx
// Elle contient des méthodes utiles pour modifier la base de données

class CreatePostsTable extends AbstractMigration
{
    // La méthode "change" est utilisée pour décrire les changements dans la base de données.
    // Phinx est capable de comprendre comment annuler automatiquement cette migration si besoin (rollback).
    public function change()
    {
        // On crée une nouvelle table appelée "posts"
        $this->table('posts')
            // On ajoute une colonne "name" de type chaîne de caractères (VARCHAR)
            ->addColumn('name', 'string')
            
            // On ajoute une colonne "slug" de type chaîne de caractères
            // Un "slug" est souvent une version lisible dans l’URL (ex: mon-super-article)
            ->addColumn('slug', 'string')
            
            // On ajoute une colonne "content" de type texte long
            // Elle contiendra le contenu complet de l’article (par exemple, du HTML)
            ->addColumn('content', 'text', [
                'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG
            ])
            
            // Colonne pour la date et l’heure de la dernière modification
            ->addColumn('updated_at', 'datetime')
            
            // Colonne pour la date et l’heure de création de l’article
            ->addColumn('created_at', 'datetime')
            
            // On crée réellement la table dans la base de données
            ->create();
    }
}
