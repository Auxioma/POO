<?php
// Charge l'autoloader de Composer qui permet d'utiliser toutes les bibliothèques installées et nos propres classes
// sans avoir à faire des require/include manuels pour chaque fichier
require '../vendor/autoload.php';

// Définition de la liste des modules qui constituent notre application
// Les modules sont des classes qui encapsulent une fonctionnalité spécifique de l'application
$modules = [
    \App\Blog\BlogModule::class  // Module de blog qui gère les articles, catégories, etc.
    // D'autres modules pourraient être ajoutés ici (Auth, Admin, API, etc.)
];

// Création d'un constructeur de conteneur d'injection de dépendances (DI)
// Le conteneur DI permet de gérer les dépendances entre les différentes classes de l'application
// et facilite les tests unitaires et la maintenance du code
$builder = new \DI\ContainerBuilder();

// Chargement du fichier de configuration principal qui contient les paramètres de base
// dirname(__DIR__) remonte d'un niveau dans l'arborescence par rapport au script actuel
$builder->addDefinitions(dirname(__DIR__) . '/config/config.php');

// Parcours de tous les modules déclarés pour charger leurs définitions spécifiques
// Chaque module peut définir ses propres services et paramètres de configuration
foreach ($modules as $module) {
    // Vérifie si le module possède des définitions de dépendances (constante DEFINITIONS)
    if ($module::DEFINITIONS) {
        // Ajoute ces définitions au constructeur du conteneur
        // Ces définitions peuvent être un tableau ou un chemin vers un fichier PHP
        $builder->addDefinitions($module::DEFINITIONS);
    }
}

// Charge un fichier de configuration supplémentaire qui peut surcharger les configurations précédentes
// Cela permet d'avoir des configurations spécifiques à l'environnement (dev, prod, test)
$builder->addDefinitions(dirname(__DIR__) . '/config.php');

// Construit effectivement le conteneur avec toutes les définitions chargées
// À partir de maintenant, le conteneur peut résoudre les dépendances automatiquement
$container = $builder->build();

// Crée l'instance principale de l'application en lui fournissant:
// - le conteneur d'injection de dépendances pour résoudre les services
// - la liste des modules à initialiser et exécuter
$app = new \Framework\App($container, $modules);

// Exécute l'application avec la requête HTTP actuelle:
// - ServerRequest::fromGlobals() crée un objet Request à partir des variables globales ($_GET, $_POST, etc.)
// - app->run() traite cette requête et retourne un objet Response conforme au standard PSR-7
$response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());

// Envoie la réponse HTTP au navigateur:
// - envoie les en-têtes HTTP
// - envoie le corps de la réponse
// - termine l'exécution du script
\Http\Response\send($response);
