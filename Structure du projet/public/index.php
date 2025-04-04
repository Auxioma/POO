<?php

// Cette ligne charge automatiquement toutes les dépendances définies dans le fichier composer.json.
// Elle permet d'inclure des bibliothèques comme Guzzle, qui est utilisé pour les requêtes HTTP, et le Framework utilisé dans le projet.
require '../vendor/autoload.php';

// Création d'une instance de la classe \Framework\App(), qui représente l'application du framework.
// Cette classe gère les routes, la gestion des requêtes et des réponses dans le cadre du framework utilisé.
// L'instance de $app permettra ensuite de gérer l'exécution de l'application.
$app = new \Framework\App();

// Déclaration d'un tableau vide $demo.
// Ici, $demo est une variable qui pourrait être utilisée pour stocker des données ou des résultats,
// mais dans cet exemple, elle est laissée vide. Son utilisation peut dépendre du contexte du projet.
$demo = array();

// Appel à la méthode 'run' de l'objet $app. Cette méthode exécute l'application.
// Elle prend en paramètre un objet de type \GuzzleHttp\Psr7\ServerRequest (une requête HTTP),
// qui est créé en utilisant la méthode fromGlobals(). Cette méthode extrait les informations de la requête
// HTTP à partir des variables globales PHP ($_GET, $_POST, $_SERVER, etc.).
$response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());

// Envoi de la réponse HTTP au client.
// \Http\Response\send() est une fonction qui envoie l'objet $response généré par l'application vers le navigateur du client.
// Cela finalise le processus de traitement de la requête en envoyant la réponse appropriée au client.
\Http\Response\send($response);
