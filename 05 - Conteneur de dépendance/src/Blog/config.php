<?php

// Importe la classe du module Blog et les fonctions du conteneur d'injection de dépendances
use App\Blog\BlogModule;  // Le module Blog à configurer
use function \Di\autowire;  // Fonction helper pour créer des objets via le conteneur DI
use function \Di\get;     // Fonction helper pour récupérer une valeur déjà définie dans le conteneur

/**
* Fichier de configuration pour le module Blog
* Retourne un tableau de définitions pour le conteneur d'injection de dépendances
*/
return [
   // Définit le préfixe d'URL pour toutes les routes du module Blog
   // Cette valeur pourra être modifiée facilement sans changer le code du module
   'blog.prefix' => '/blog',
   
   // Configure l'instanciation du module BlogModule
   // - object() indique au conteneur qu'il doit créer une instance de BlogModule
   // - constructorParameter() permet de spécifier la valeur d'un paramètre du constructeur
   // - get('blog.prefix') récupère la valeur de 'blog.prefix' définie juste au-dessus
   BlogModule::class => autowire()->constructorParameter('prefix', get('blog.prefix'))
];