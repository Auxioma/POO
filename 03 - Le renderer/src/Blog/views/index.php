<?= $renderer->render('header') ?>

<h1>Bienvenue sur le blog</h1>

<ul>
    <li><a href="<?= $router->generateUri('blog.show', ['slug' => 'zaeaze0-7aze']); ?>">Article 1</a></li>
    <li>Article 1</li>
    <li>Article 1</li>
    <li>Article 1</li>
    <li>Article 1</li>
    <li>Article 1</li>
</ul>

<?= $renderer->render('footer') ?>