<?php
// On inclut automatiquement tous les fichiers nécessaires grâce à Composer (autoload)
require '../vendor/autoload.php';

// On crée un moteur de rendu basé sur Twig, pour afficher les vues HTML
// dirname(__DIR__) permet de remonter d'un dossier et d'aller dans /views
$renderer = new \Framework\Renderer\TwigRenderer(dirname(__DIR__) . '/views');

// On instancie l'application principale en lui passant :
// - une liste de modules à charger (ici, BlogModule)
// - un tableau de dépendances (ici, on lui passe le renderer Twig)
$app = new \Framework\App([
    \App\Blog\BlogModule::class  // Module de blog
], [
    'renderer' => $renderer       // Injection du moteur de rendu dans l'application
]);

// On récupère la requête HTTP actuelle via Guzzle (bibliothèque PSR-7)
// fromGlobals() crée un objet ServerRequest à partir des superglobales PHP ($_GET, $_POST, etc.)
$response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());

// On envoie la réponse HTTP générée par l'application au navigateur du client
\Http\Response\send($response);
