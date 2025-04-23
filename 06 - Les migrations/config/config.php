<?php
// On commence le fichier PHP
// Tous les fichiers contenant du code PHP doivent commencer par <?php

// -----------------------------------------------------
// IMPORTATION DES CLASSES NÉCESSAIRES
// -----------------------------------------------------

// On "importe" ici des classes venant d'un framework (probablement un framework maison ou basé sur un framework PHP existant)
// Cela permet d’utiliser ces classes sans écrire leur chemin complet à chaque fois

use Framework\Renderer\RendererInterface;       // Une interface = un contrat : ici, elle définit ce que doit faire un moteur de rendu (affichage HTML)
use Framework\Renderer\TwigRendererFactory;     // Une fabrique = une classe spéciale qui construit d'autres objets (ici un moteur Twig)
use Framework\Router\RouterTwigExtension;       // Extension Twig qui permet d’utiliser le routeur (générer des URLs) dans les templates HTML

// -----------------------------------------------------
// RETOUR D'UN TABLEAU DE CONFIGURATION
// -----------------------------------------------------

// On retourne ici un tableau associatif PHP, qui contient des paramètres de configuration
// Ce tableau va être utilisé par un conteneur de dépendances (souvent appelé "DI Container")
// Le conteneur sert à centraliser la création des objets et à injecter les dépendances automatiquement

return [

    // -------------------------------------------------
    // CONFIGURATION DE LA BASE DE DONNÉES
    // -------------------------------------------------

    'database.host' => 'localhost',          // Adresse du serveur MySQL (localhost = l’ordinateur actuel)
    'database.username' => 'root',           // Identifiant pour se connecter à la base de données
    'database.password' => 'root',           // Mot de passe associé à l’utilisateur ci-dessus
    'database.name' => 'monsupersite',       // Nom de la base de données à utiliser

    // -------------------------------------------------
    // CHEMIN VERS LES VUES (templates HTML)
    // -------------------------------------------------

    // Les vues sont les fichiers qui contiennent le HTML affiché à l'utilisateur
    // On indique ici à quel endroit du projet on peut les trouver
    'views.path' => dirname(__DIR__) . '/views',
    // __DIR__ = le dossier courant (celui du fichier actuel)
    // dirname(__DIR__) = le dossier parent (..)
    // On ajoute ensuite /views pour pointer vers le bon dossier

    // -------------------------------------------------
    // EXTENSIONS POUR LE MOTEUR DE TEMPLATE TWIG
    // -------------------------------------------------

    'twig.extensions' => [
        \DI\get(RouterTwigExtension::class)
        // \DI\get(...) signifie : "demande au conteneur de fournir une instance de cette classe"
        // Ici, on dit qu'on veut que Twig utilise une extension spéciale pour générer des URLs facilement dans les templates
    ],

    // -------------------------------------------------
    // CONFIGURATION DU ROUTEUR
    // -------------------------------------------------

    \Framework\Router::class => \DI\autowire(),
    // On demande ici à notre conteneur de créer un objet de la classe Router
    // Le routeur est l'outil qui relie une URL (ex: /contact) à un contrôleur PHP (ex: ContactController)

    // -------------------------------------------------
    // CONFIGURATION DU MOTEUR DE RENDU (TWIG)
    // -------------------------------------------------

    RendererInterface::class => \DI\factory(TwigRendererFactory::class)
    // On dit ici : "quand on demande un RendererInterface, utilise la fabrique TwigRendererFactory pour le créer"
    // Cela permet d’afficher nos vues en utilisant Twig, un moteur de template moderne et puissant
];
