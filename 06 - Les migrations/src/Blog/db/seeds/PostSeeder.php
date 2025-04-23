<?php
// Début du fichier PHP

use Phinx\Seed\AbstractSeed;

// On importe la classe AbstractSeed qui permet de créer des "seeders" (données de test/remplissage)

class PostSeeder extends AbstractSeed
{
    /**
     * La méthode "run" est appelée quand on exécute la commande de seed.
     * Elle permet de remplir la base de données avec des données fictives (ex : pour les tests).
     */
    public function run()
    {
        $data = []; // On crée un tableau vide pour stocker les futures lignes à insérer

        $faker = \Faker\Factory::create('fr_FR');
        // On crée une instance de Faker pour générer des données aléatoires en français

        for ($i = 0; $i < 100; ++$i) {
            // On répète 100 fois pour créer 100 articles fictifs

            $date = $faker->unixTime('now');
            // On génère une date/heure aléatoire sous forme de timestamp UNIX

            $data[] = [
                'name' => $faker->catchPhrase,              // Un titre d’article accrocheur
                'slug' => $faker->slug,                     // Un slug (ex: titre-de-l-article)
                'content' => $faker->text(3000),            // Du texte de 3000 caractères environ
                'created_at' => date('Y-m-d H:i:s', $date), // Date de création formatée
                'updated_at' => date('Y-m-d H:i:s', $date)  // Date de mise à jour (identique ici)
            ];
        }

        // On insère toutes les données générées dans la table "posts"
        $this->table('posts')
            ->insert($data)
            ->save(); // On sauvegarde les données dans la base
    }
}
