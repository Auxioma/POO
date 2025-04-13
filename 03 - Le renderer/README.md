# Mini PHP Framework - Guide débutant

Bienvenue dans ce mini-framework PHP moderne, conçu pour vous aider à comprendre les concepts fondamentaux utilisés dans les frameworks professionnels comme Laravel, Symfony ou Slim.

## À propos du framework

Ce framework léger implémente une architecture MVC (Modèle-Vue-Contrôleur) avec une approche modulaire. Il offre:

- Un système de routage puissant
- Un moteur de templates simple mais efficace
- Une architecture orientée modules
- Une gestion moderne des requêtes/réponses HTTP (compatible PSR-7)

## Structure du projet

```
├── public/           # Point d'entrée de l'application
│   └── index.php     # Front controller
├── src/              # Code source
│   ├── Framework/    # Cœur du framework
│   │   ├── App.php           # Classe principale de l'application
│   │   ├── Renderer.php      # Moteur de templates
│   │   ├── Router.php        # Système de routage
│   │   └── Router/
│   │       └── Route.php     # Représentation d'une route
│   └── Blog/         # Module exemple
│       ├── BlogModule.php    # Définition du module Blog
│       └── views/            # Templates du module
├── tests/            # Tests unitaires
└── views/            # Templates globaux
    ├── footer.php    # Pied de page commun
    └── header.php    # En-tête commun
```

## Installation

1. Clonez ce dépôt
2. Exécutez `composer install` pour installer les dépendances
3. Configurez votre serveur web pour pointer sur le dossier `public/`

## Comment ça fonctionne?

### Le point d'entrée

Toute requête passe par `public/index.php` qui initialise l'application:

```php
// Charger l'autoloader
require '../vendor/autoload.php';

// Configurer le renderer
$renderer = new \Framework\Renderer();
$renderer->addPath(dirname(__DIR__) . '/views');

// Initialiser l'application avec ses modules
$app = new \Framework\App([
    \App\Blog\BlogModule::class
], [
    'renderer' => $renderer
]);

// Traiter la requête et envoyer la réponse
$response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());
\Http\Response\send($response);
```

### Définir un module

Chaque fonctionnalité est encapsulée dans un module:

```php
class BlogModule
{
    private $renderer;
    
    public function __construct(Router $router, Renderer $renderer)
    {
        $this->renderer = $renderer;
        
        // Définir le chemin des vues du module
        $this->renderer->addPath('blog', __DIR__ . '/views');
        
        // Définir les routes du module
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/{slug:[a-z\-0-9]+}', [$this, 'show'], 'blog.show');
    }
    
    // Contrôleur pour la page d'index
    public function index(Request $request): string
    {
        return $this->renderer->render('@blog/index');
    }
    
    // Contrôleur pour afficher un article
    public function show(Request $request): string
    {
        return $this->renderer->render('@blog/show', [
            'slug' => $request->getAttribute('slug')
        ]);
    }
}
```

### Créer des vues

Les vues sont de simples fichiers PHP qui reçoivent des variables:

```php
<!-- @blog/show.php -->
<?= $renderer->render('header', ['title' => $slug]) ?>

<h1>Bienvenue sur l'article <?= $slug ?></h1>

<?= $renderer->render('footer') ?>
```

## Concepts clés à comprendre

1. **Front Controller**: Un point d'entrée unique pour toutes les requêtes
2. **Routage**: Association d'URLs à des actions spécifiques
3. **Injection de dépendances**: Les modules reçoivent leurs dépendances au lieu de les créer
4. **Rendu de vues**: Système de templates pour séparer la logique de la présentation
5. **Architecture modulaire**: Organisation du code en modules réutilisables

## Exercices pratiques pour débutants

1. **Créer un nouveau module** 
   - Créez un `ContactModule` avec une page de formulaire de contact
   - Ajoutez les routes et vues nécessaires

2. **Étendre le `BlogModule`**
   - Ajoutez une route pour lister les articles par catégorie
   - Créez la vue correspondante

3. **Améliorer le `Renderer`**
   - Ajoutez une fonction pour inclure des fichiers CSS/JavaScript
   - Implémentez un système de layout plus avancé

## Tests

Exécutez les tests avec PHPUnit:

```
./vendor/bin/phpunit
```

## Ressources pour approfondir

- [Documentation PSR-7](https://www.php-fig.org/psr/psr-7/) - Standard pour les interfaces HTTP
- [Documentation PSR-4](https://www.php-fig.org/psr/psr-4/) - Standard pour l'autoloading
- [FastRoute](https://github.com/nikic/FastRoute) - Bibliothèque de routage utilisée en interne

---

Ce mini-framework est conçu à des fins éducatives pour comprendre les principes de base des frameworks PHP modernes. Il n'est pas recommandé pour une utilisation en production.
