<?php
require '../vendor/autoload.php';

use App\Blog\BlogModule;

// On crée une instance de notre application principale (App.php)
// On lui passe un tableau contenant la liste des modules à charger
// Ici, on charge uniquement le module "Blog" (BlogModule)
$app = new \Framework\App([
    \App\Blog\BlogModule::class
]);


$response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());
\Http\Response\send($response);
