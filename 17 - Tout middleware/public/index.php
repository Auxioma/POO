<?php

use App\Admin\AdminModule;
use App\Blog\BlogModule;
use Framework\Middleware\{
    DispatcherMiddleware,
    MethodMiddleware,
    RouterMiddleware,
    TrailingSlashMiddleware,
    NotFoundMiddleware
};
use GuzzleHttp\Psr7\ServerRequest;
use Middlewares\Whoops;

require dirname(__DIR__) . '/vendor/autoload.php';

$modules = [
    AdminModule::class,
    BlogModule::class
];

$app = (new \Framework\App(dirname(__DIR__) . '/config/config.php'))
    ->addModule(AdminModule::class)
    ->addModule(BlogModule::class)
    ->pipe(Whoops::class)
    ->pipe(TrailingSlashMiddleware::class)
    ->pipe(MethodMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)
    ->pipe(NotFoundMiddleware::class);

if (php_sapi_name() !== "cli") {
    $response = $app->run(ServerRequest::fromGlobals());
    \Http\Response\send($response);
}
