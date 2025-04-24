<?php

// On utilise des classes du framework, notamment pour le rendu de vues et le routage
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router\RouterTwigExtension;

// On retourne un tableau de configuration
return [

    // Configuration de la base de données
    'database.host' => 'localhost', // Adresse du serveur de base de données (ici, local)
    'database.username' => 'root', // Nom d’utilisateur pour se connecter à MySQL
    'database.password' => '', // Mot de passe de l’utilisateur MySQL
    'database.name' => 'blog', // Nom de la base de données utilisée

    // Chemin vers le dossier contenant les vues (fichiers Twig)
    'views.path' => dirname(__DIR__) . '/views',

    // Extensions Twig à enregistrer, ici une extension pour gérer les routes dans les vues
    'twig.extensions' => [
        \DI\get(RouterTwigExtension::class) // Récupère l’extension via le container
    ],

    // Enregistrement automatique de la classe Router via le container (Dependency Injection)
    \Framework\Router::class => \DI\autowire(),

    // Utilisation d’une fabrique (factory) pour générer l’implémentation de RendererInterface
    RendererInterface::class => \DI\factory(TwigRendererFactory::class),

    // Création manuelle d'une instance PDO pour gérer la connexion à la base de données
    \PDO::class => function (\Psr\Container\ContainerInterface $c) {
        return new PDO(
        // Chaîne de connexion DSN construite dynamiquement à partir des paramètres du container
            'mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name'),
            $c->get('database.username'), // Nom d’utilisateur
            $c->get('database.password'), // Mot de passe
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, // Les résultats seront des objets
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // Active les exceptions en cas d’erreur SQL
            ]
        );
    }
];
