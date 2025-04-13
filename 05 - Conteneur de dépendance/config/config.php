<?php

// Importe les interfaces et classes nécessaires pour le système de rendu des vues
use Framework\Renderer\RendererInterface;  // Interface commune pour tous les moteurs de rendu
use Framework\Renderer\TwigRendererFactory; // Usine qui crée l'instance du moteur Twig
use Framework\Router\RouterTwigExtension;  // Extension qui permet d'utiliser le router dans les templates Twig

// Retourne un tableau de configuration pour le conteneur d'injection de dépendances (DI)
return [
    // Définit le chemin absolu vers le dossier contenant les templates/vues
    // dirname(__DIR__) remonte d'un niveau par rapport au fichier actuel
    'views.path' => dirname(__DIR__) . '/views',
    
    // Liste des extensions à charger pour le moteur de template Twig
    // Ces extensions ajoutent des fonctionnalités supplémentaires aux templates
    'twig.extensions' => [
      \DI\get(RouterTwigExtension::class)  // Récupère l'extension qui permet de générer des URLs dans les templates
    ],
    
    // Enregistre le service Router dans le conteneur d'injection de dépendances
    // \DI\object() crée une nouvelle instance de la classe Router
    \Framework\Router::class => \DI\object(),
    
    // Associe l'interface RendererInterface à l'implémentation TwigRenderer
    // \DI\factory() utilise une factory pour créer l'instance avec la configuration appropriée
    // Cela permet de changer facilement le moteur de rendu sans modifier le reste du code
    RendererInterface::class => \DI\factory(TwigRendererFactory::class)
];