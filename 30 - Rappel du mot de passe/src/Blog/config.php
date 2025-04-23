<?php

use App\Blog\BlogModule;
use function \Di\autowire;
use function \Di\get;

return [
    'blog.prefix' => '/blog',
    'admin.widgets' => \DI\add([
        get(\App\Blog\BlogWidget::class)
    ])
];
