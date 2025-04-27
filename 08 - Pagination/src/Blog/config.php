<?php

// Importation du module Blog et des fonctions d'injection de dépendances (DI).
use App\Blog\BlogModule;
use function \Di\autowire;
use function \Di\get;

/**
 * Fichier de configuration pour le module Blog.
 * 
 * Ce fichier retourne un tableau associatif contenant les paramètres nécessaires
 * à la configuration du module Blog, comme le préfixe des routes.
 */
return [
    // Définition du préfixe pour les routes du module Blog.
    // Par défaut, toutes les routes du blog commenceront par "/blog".
    'blog.prefix' => '/blog',

    // Configuration de l'injection de dépendances pour le BlogModule.
    // On utilise la fonction `autowire` pour créer une instance automatique
    // et on lui injecte le paramètre `prefix` en récupérant la valeur de `blog.prefix`.
    BlogModule::class => autowire()->constructorParameter('prefix', get('blog.prefix'))
];