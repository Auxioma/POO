<?php
// On commence le script PHP

// On inclut l'autoloader de Composer pour charger automatiquement toutes les classes
require dirname(__DIR__) . '/vendor/autoload.php';

// Liste des modules de l'application que l'on souhaite charger
$modules = [
    \App\Blog\BlogModule::class // Ici, on charge uniquement le module "Blog"
];

// On crée un constructeur de conteneur d'injection de dépendances avec PHP-DI
$builder = new \DI\ContainerBuilder();

// On ajoute les définitions de services générales à partir du fichier config/config.php
$builder->addDefinitions(dirname(__DIR__) . '/config/config.php');

// Pour chaque module de l'application, on vérifie s’il possède une constante DEFINITIONS
// Si oui, on ajoute aussi ces définitions au conteneur (permet à chaque module de se configurer lui-même)
foreach ($modules as $module) {
    if ($module::DEFINITIONS) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}

// On ajoute un autre fichier de configuration général (config.php à la racine)
$builder->addDefinitions(dirname(__DIR__) . '/config.php');

// On construit le conteneur avec toutes les définitions collectées
$container = $builder->build();

// On instancie l'application principale en lui passant le conteneur et la liste des modules
$app = new \Framework\App($container, $modules);

// On vérifie si le script est lancé depuis le navigateur (et non en ligne de commande CLI)
if (php_sapi_name() !== "cli") {
    // On récupère la requête HTTP depuis les variables globales ($_GET, $_POST, etc.)
    $response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());

    // On envoie la réponse HTTP au navigateur (HTML, JSON, etc.)
    \Http\Response\send($response);
}
