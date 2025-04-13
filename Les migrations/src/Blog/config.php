<?php

// On importe la classe BlogModule qui représente le module du blog
use App\Blog\BlogModule;

// On importe deux fonctions utilitaires du conteneur PHP-DI
use function \Di\object;
use function \Di\get;

// Ce fichier retourne un tableau de configuration
return [

    // On définit une clé 'blog.prefix' avec la valeur '/blog'
    // Elle représente le préfixe que toutes les routes du blog vont utiliser
    'blog.prefix' => '/blog',

    // On explique au conteneur comment construire le BlogModule
    // On utilise la fonction object() pour dire "instancie cet objet"
    // Puis on utilise constructorParameter() pour lui dire :
    // "le paramètre 'prefix' du constructeur doit être rempli avec la valeur de 'blog.prefix'"
    BlogModule::class => object()
        ->constructorParameter('prefix', get('blog.prefix'))
];
