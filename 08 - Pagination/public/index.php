<?php
// Inclusion de l'autoloader de Composer pour charger automatiquement les classes nécessaires.
// dirname(__DIR__) retourne le chemin du dossier parent pour inclure le fichier 'vendor/autoload.php'.
require dirname(__DIR__) . '/vendor/autoload.php';

// Définition des modules de l'application.
// Chaque module représente une fonctionnalité ou une partie spécifique de l'application.
// Par exemple : ici on charge le module "Blog" via sa classe \App\Blog\BlogModule.
$modules = [
    \App\Blog\BlogModule::class
];

// Création d'un conteneur d'injection de dépendances.
// Le conteneur est utilisé pour gérer les dépendances des différentes classes de l'application.
$builder = new \DI\ContainerBuilder();

// Ajout des définitions de configuration au conteneur.
// Ces définitions permettent de configurer les services (exemple : base de données, routes, etc.).
$builder->addDefinitions(dirname(__DIR__) . '/config/config.php');

// Chargement des définitions spécifiques pour chaque module.
// Chaque module peut contenir ses propres définitions de dépendances ou de services.
foreach ($modules as $module) {
    // Vérifie si le module a des définitions à ajouter (via la constante DEFINITIONS).
    if ($module::DEFINITIONS) {
        // Ajoute les définitions propres au module dans le conteneur.
        $builder->addDefinitions($module::DEFINITIONS);
    }
}

// Ajout d'une dernière définition globale pour configurer l'application.
// Ce fichier peut contenir des paramètres supplémentaires.
$builder->addDefinitions(dirname(__DIR__) . '/config.php');

// Construction du conteneur d'injection de dépendances.
// Une fois construit, ce conteneur est prêt à être utilisé par l'application.
$container = $builder->build();

// Création d'une instance de l'application principale.
// L'application utilise le conteneur pour récupérer les services nécessaires et les modules pour structurer ses fonctionnalités.
$app = new \Framework\App($container, $modules);

// Vérification si le script est exécuté dans un environnement CLI (ligne de commande).
// Si ce n'est pas le cas, cela signifie que le script est appelé via un serveur HTTP.
if (php_sapi_name() !== "cli") {
    // Création de la requête HTTP à partir des variables globales PHP.
    // GuzzleHttp\Psr7\ServerRequest::fromGlobals() permet de traduire les superglobales ($_GET, $_POST, etc.) en un objet PSR-7.
    $response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());
    
    // Envoi de la réponse HTTP au client (navigateur ou autre).
    // Http\Response\send() s'assure que la réponse est correctement envoyée avec les en-têtes appropriés.
    \Http\Response\send($response);
}