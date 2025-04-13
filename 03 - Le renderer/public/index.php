<?php
// Inclusion du fichier autoload.php fourni par Composer pour charger automatiquement toutes les classes nécessaires
// Cela évite d'avoir à utiliser des 'require' ou 'include' pour chaque fichier de classe
require '../vendor/autoload.php';

// Création d'une nouvelle instance de la classe Renderer du framework
// Cette classe est responsable de l'affichage des vues (templates) de notre application
$renderer = new \Framework\Renderer();

// Ajout d'un chemin où le renderer pourra trouver les fichiers de vues
// dirname(__DIR__) remonte d'un niveau dans l'arborescence des dossiers par rapport au fichier actuel
// puis on ajoute '/views' pour accéder au dossier contenant les templates
$renderer->addPath(dirname(__DIR__) . '/views');

// Création d'une nouvelle instance de la classe App qui représente notre application
// Premier paramètre : tableau des modules à charger (ici uniquement le BlogModule)
// Deuxième paramètre : tableau des dépendances à injecter dans l'application
// Ici on injecte le renderer que nous avons configuré précédemment
$app = new \Framework\App([
    \App\Blog\BlogModule::class
], [
    'renderer' => $renderer
]);

// Exécution de l'application avec la requête HTTP actuelle
// ServerRequest::fromGlobals() crée un objet Request à partir des variables globales ($_GET, $_POST, etc.)
// La méthode run() traite la requête et retourne un objet Response
$response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());

// Envoi de la réponse HTTP au navigateur
// Cette fonction prend l'objet Response généré et envoie les en-têtes et le contenu au client
\Http\Response\send($response);