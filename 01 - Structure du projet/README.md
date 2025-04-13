# Guillaume/Guillaume

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Application web PHP minimaliste avec gestion des requêtes HTTP et tests unitaires.

## Table des matières

- [Prérequis](#prérequis)
- [Installation](#installation)
- [Structure du projet](#structure-du-projet)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [Tests](#tests)
- [Dépendances](#dépendances)
- [Contribuer](#contribuer)
- [Licence](#licence)

## Prérequis

- PHP 7.4+
- Composer 2.0+
- PHPUnit 9.0+ (installé automatiquement via Composer)

## Installation

1. Cloner le dépôt :
```bash
git clone https://github.com/votre-utilisateur/guillaume.git
cd guillaume

2. Installer les dépendances :
composer install

3 .Vérifier l'installation :
php -v
composer --version
vendor/bin/phpunit --version

Structure du projet
guillaume/
├── src/
│   └── Framework/         # Code source principal
│       ├── App.php        # Classe principale
│       └── ...            # Autres classes
├── tests/                 # Tests unitaires
│   └── Framework/         # Tests des classes Framework
│       ├── AppTest.php    # Tests de la classe App
│       └── ...            # Autres tests
├── vendor/                # Dépendances Composer
├── .gitignore             # Fichiers ignorés par Git
├── composer.json          # Configuration Composer
└── README.md              # Ce fichier

Configuration

L'autoloading PSR-4 est configuré dans composer.json. Les classes sous le namespace Framework sont automatiquement chargées depuis src/Framework/.

Exemple d'utilisation :
php
Copy

require_once __DIR__ . '/vendor/autoload.php';
use Framework\App;

Utilisation

Exemple de base :
php
Copy

<?php
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Psr7\ServerRequest;
use Framework\App;

// Création de l'application
$app = new App();

// Exécution d'une requête
$request = new ServerRequest('GET', '/blog');
$response = $app->run($request);

// Affichage de la réponse
echo (string)$response->getBody();

Tests

Pour lancer les tests unitaires :
bash
Copy

vendor/bin/phpunit

Tests disponibles :

    Redirection d'URL (slash final)

    Route /blog

    Erreurs 404

    ... (ajouter d'autres tests ici)

Dépendances
Principales

    guzzlehttp/psr7 : Implémentation PSR-7 pour HTTP

    http-interop/response-sender : Envoi de réponses HTTP

Développement

    phpunit/phpunit : Framework de tests

    squizlabs/php_codesniffer : Analyse de code

Voir le fichier composer.json pour la liste complète.
