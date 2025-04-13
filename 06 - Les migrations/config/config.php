<?php
// On commence le fichier PHP

// On importe les classes dont on a besoin depuis le framework
use Framework\Renderer\RendererInterface; // Interface pour gérer l'affichage des vues (HTML)
use Framework\Renderer\TwigRendererFactory; // Fabrique qui crée un moteur de rendu Twig
use Framework\Router\RouterTwigExtension; // Extension pour utiliser le routeur dans les templates Twig

// On retourne un tableau de configuration utilisé par le conteneur de dépendances
return [

    // Informations pour se connecter à la base de données
    'database.host' => 'localhost',          // Adresse du serveur de base de données (ici sur l’ordinateur local)
    'database.username' => 'root',           // Nom d’utilisateur pour se connecter à la base de données
    'database.password' => 'root',           // Mot de passe de la base de données
    'database.name' => 'monsupersite',       // Nom de la base de données qu’on va utiliser

    // Chemin vers les fichiers de vues (templates HTML)
    'views.path' => dirname(__DIR__) . '/views', // On prend le dossier parent (..) et on ajoute /views

    // Liste des extensions utilisées par le moteur de template Twig
    'twig.extensions' => [
        \DI\get(RouterTwigExtension::class) // On demande au conteneur de fournir l’extension du routeur
    ],

    // Configuration du routeur
    \Framework\Router::class => \DI\object(), // On demande au conteneur de créer un objet de la classe Router

    // Configuration du moteur de rendu : on demande au conteneur de créer un moteur Twig via une fabrique
    RendererInterface::class => \DI\factory(TwigRendererFactory::class)
];
