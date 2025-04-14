<?php
// Ligne 1 : Démarrage du code PHP

// Ligne 2 : Définition du namespace pour organiser les fichiers de test
namespace Tests\Framework;

// Ligne 4 : Import de la classe Renderer, qu’on veut tester
use Framework\Renderer;

// Ligne 5 : Import de TestCase, la classe de base de PHPUnit pour créer des tests unitaires
use PHPUnit\Framework\TestCase; 

// Définition de la classe de test qui hérite de TestCase
class RendererTest extends TestCase {

    // Ligne 8 : Déclaration d’un attribut privé qui contiendra une instance de Renderer
    private $renderer;

    /**
     * Cette méthode est appelée automatiquement avant chaque test
     * Elle permet d’instancier et configurer ce dont on a besoin
     */
    public function setUp(): void
    {
        // On crée une nouvelle instance de Renderer
        $this->renderer = new Renderer();

        // On ajoute un chemin par défaut vers les vues (fichiers HTML/PHP à rendre)
        // __DIR__ est une constante magique qui représente le chemin du dossier actuel
        // Donc ici on ajoute le dossier "views" qui est dans le même dossier que ce fichier
        $this->renderer->addPath(__DIR__ . '/views');
    }

    /**
     * Test : Vérifie que Renderer peut rendre un fichier à partir d’un namespace personnalisé
     */
    public function testRenderTheRightPath() {
        // On ajoute un "namespace" de vue nommé 'blog' qui pointe vers le même dossier views
        $this->renderer->addPath('blog', __DIR__ . '/views');

        // On appelle la méthode render avec la vue '@blog/demo'
        // Le "@" indique à Renderer d’utiliser le chemin du namespace 'blog'
        $content = $this->renderer->render('@blog/demo');

        // On vérifie que le rendu correspond exactement à 'Salut les gens'
        $this->assertEquals('Salut les gens', $content);
    }

    /**
     * Test : Vérifie qu’on peut rendre une vue depuis le chemin par défaut (sans namespace)
     */
    public function testRenderTheDefaultPath() {
        // Ici on rend simplement le fichier "demo" depuis le chemin par défaut
        $content = $this->renderer->render('demo');

        // On vérifie que le résultat est correct
        $this->assertEquals('Salut les gens', $content);
    }

    /**
     * Test : Vérifie qu’on peut passer des variables à une vue
     */
    public function testRenderWithParams() {
        // On rend la vue 'demoparams' en lui passant un tableau de variables
        // Ici, on passe ['nom' => 'Marc'] pour que la vue puisse afficher "Salut Marc"
        $content = $this->renderer->render('demoparams', ['nom' => 'Marc']);

        // On vérifie que la vue a bien affiché la variable
        $this->assertEquals('Salut Marc', $content);
    }

    /**
     * Test : Vérifie qu’on peut définir des variables globales valables pour toutes les vues
     */
    public function testGlobalParameters() {
        // On définit une variable globale 'nom' accessible dans toutes les vues
        $this->renderer->addGlobal('nom', 'Marc');

        // Même si on ne passe pas 'nom' directement à render, il est déjà connu globalement
        $content = $this->renderer->render('demoparams');

        // On vérifie que la variable globale a bien été utilisée dans la vue
        $this->assertEquals('Salut Marc', $content);
    }
}
