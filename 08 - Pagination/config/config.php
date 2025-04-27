<?php

// On utilise des classes provenant de plusieurs namespaces du framework pour configurer notre application.
// Ces classes gèrent le rendu des vues, le routage et les extensions Twig.
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router\RouterTwigExtension;
use Framework\Twig\{
    PagerFantaExtension, TextExtension, TimeExtension
};

// On retourne un tableau associatif qui contient la configuration de l'application.
return [
    // Configuration de la base de données
    // 'database.host' : l'adresse du serveur de base de données (ici "localhost" pour un serveur local)
    'database.host' => 'localhost',
    // 'database.username' : le nom d'utilisateur pour se connecter à la base de données (ici "root")
    'database.username' => 'root',
    // 'database.password' : le mot de passe pour se connecter à la base de données (ici vide, ce qui est fréquent en local mais déconseillé en production)
    'database.password' => '',
    // 'database.name' : le nom de la base de données (ici "blog")
    'database.name' => 'blog',
    
    // Configuration des vues
    // 'views.path' : le chemin du dossier contenant les fichiers de vues pour le rendu HTML
    'views.path' => dirname(__DIR__) . '/views',
    
    // Extensions Twig
    // 'twig.extensions' : une liste d'extensions Twig pour ajouter des fonctionnalités au moteur de templates
    'twig.extensions' => [
        // Ajout d'une extension pour le routage (RouterTwigExtension)
        \DI\get(RouterTwigExtension::class),
        // Ajout d'une extension pour la pagination (PagerFantaExtension)
        \DI\get(PagerFantaExtension::class),
        // Ajout d'une extension pour manipuler du texte (TextExtension)
        \DI\get(TextExtension::class),
        // Ajout d'une extension pour gérer des dates/temps (TimeExtension)
        \DI\get(TimeExtension::class)
    ],
    
    // Configuration du routeur
    // Le routeur est configuré automatiquement avec \DI\autowire.
    \Framework\Router::class => \DI\autowire(),
    
    // Configuration du moteur de rendu
    // On utilise une fabrique (factory) pour créer une instance de TwigRenderer.
    RendererInterface::class => \DI\factory(TwigRendererFactory::class),
    
    // Configuration de la connexion à la base de données avec PDO
    // On utilise une fonction qui retourne une instance de PDO configurée avec les paramètres de la base de données.
    \PDO::class => function (\Psr\Container\ContainerInterface $c) {
        return new PDO(
            // Construction de la chaîne de connexion à la base de données (DSN)
            'mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name'),
            // Nom d'utilisateur et mot de passe pour se connecter
            $c->get('database.username'),
            $c->get('database.password'),
            [
                // Mode de récupération par défaut : les résultats seront retournés sous forme d'objets
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                // On active les exceptions pour gérer les erreurs de manière plus robuste
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
];