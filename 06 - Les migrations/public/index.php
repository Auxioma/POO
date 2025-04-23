<?php
// On commence le script PHP

// ------------------------------------------------------------------------
// AUTOLOADER DE COMPOSER
// ------------------------------------------------------------------------

// Composer est un gestionnaire de dépendances pour PHP
// Il permet de charger automatiquement les classes des bibliothèques externes
// Le fichier vendor/autoload.php est généré automatiquement par Composer
require dirname(__DIR__) . '/vendor/autoload.php';
// dirname(__DIR__) permet de remonter d’un dossier par rapport à l’emplacement actuel
// Cela revient à dire : "../vendor/autoload.php"

// ------------------------------------------------------------------------
// CHARGEMENT DES MODULES DE L’APPLICATION
// ------------------------------------------------------------------------

// On définit ici les "modules" qu’on veut charger dans l'application
// Un module est une partie indépendante de l’application (par exemple : Blog, Admin, Auth, etc.)
$modules = [
    \App\Blog\BlogModule::class // On ajoute ici uniquement le module Blog
];

// ------------------------------------------------------------------------
// CRÉATION DU CONTENEUR D’INJECTION DE DÉPENDANCES
// ------------------------------------------------------------------------

// On utilise la bibliothèque PHP-DI pour créer un conteneur de dépendances
// Ce conteneur permet d’injecter automatiquement les objets nécessaires dans d’autres objets
$builder = new \DI\ContainerBuilder();

// ------------------------------------------------------------------------
// AJOUT DES CONFIGURATIONS DE L’APPLICATION
// ------------------------------------------------------------------------

// On commence par ajouter un fichier de configuration principal situé dans /config/config.php
// Ce fichier retourne un tableau contenant des "définitions" pour le conteneur (ex: services, chemins, paramètres)
$builder->addDefinitions(dirname(__DIR__) . '/config/config.php');

// Ensuite, on boucle sur chaque module pour vérifier s’il possède une constante "DEFINITIONS"
// Cette constante doit pointer vers un fichier de config spécifique au module
foreach ($modules as $module) {
    if ($module::DEFINITIONS) {
        // Si la constante est définie, on l'ajoute aussi au conteneur
        $builder->addDefinitions($module::DEFINITIONS);
    }
}

// On ajoute une autre configuration globale (ex: paramètres généraux de l'app)
// Le fichier config.php peut contenir des choses comme l’environnement (dev/prod), les clés API, etc.
$builder->addDefinitions(dirname(__DIR__) . '/config.php');

// ------------------------------------------------------------------------
// CONSTRUCTION FINALE DU CONTENEUR
// ------------------------------------------------------------------------

// À ce stade, le builder connaît tous les services et paramètres
// On peut maintenant "construire" (build) le conteneur qui sera utilisé dans toute l’app
$container = $builder->build();

// ------------------------------------------------------------------------
// INITIALISATION DE L’APPLICATION
// ------------------------------------------------------------------------

// On crée une instance de l'application principale (Framework\App)
// On lui passe le conteneur (pour qu’elle puisse récupérer les services) et les modules (comme Blog)
$app = new \Framework\App($container, $modules);

// ------------------------------------------------------------------------
// LANCEMENT DE L’APPLICATION (hors mode console)
// ------------------------------------------------------------------------

// On vérifie ici si le script est lancé depuis un navigateur Web
// php_sapi_name() retourne "cli" si le script est lancé en ligne de commande
if (php_sapi_name() !== "cli") {

    // On crée une requête HTTP à partir des variables superglobales ($_GET, $_POST, etc.)
    // ServerRequest::fromGlobals() est une méthode qui transforme ces variables en un objet complet
    $response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());

    // Une fois la réponse générée, on l’envoie au client (navigateur)
    // Cela peut être une page HTML, une réponse JSON, une redirection, etc.
    \Http\Response\send($response);
}
