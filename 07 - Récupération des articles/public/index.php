<?php

// Chargement automatique des classes via Composer (autoloader PSR-4)
require dirname(__DIR__) . '/vendor/autoload.php';

// Liste des modules utilisés par l'application (ici, uniquement le module Blog)
$modules = [
    \App\Blog\BlogModule::class
];

// Création d'un container d'injection de dépendances avec PHP-DI
$builder = new \DI\ContainerBuilder();

// Ajout d’un premier fichier de configuration général
$builder->addDefinitions(dirname(__DIR__) . '/config/config.php');

// Pour chaque module, on ajoute ses définitions (si elles existent)
foreach ($modules as $module) {
    if ($module::DEFINITIONS) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}

// Ajout d’un autre fichier de config (peut contenir des surcharges ou définitions spécifiques)
$builder->addDefinitions(dirname(__DIR__) . '/config.php');

// Construction finale du container après avoir ajouté toutes les définitions
$container = $builder->build();

// Création de l’application en lui passant le container et la liste des modules à charger
$app = new \Framework\App($container, $modules);

// Si le script n’est pas lancé depuis le terminal (ligne de commande)
if (php_sapi_name() !== "cli") {
    // On génère une requête HTTP à partir des variables globales ($_GET, $_POST, etc.)
    $response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());

    // Envoi de la réponse HTTP au client (navigateur)
    \Http\Response\send($response);
}
