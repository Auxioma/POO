<?php

// Ce fichier retourne un tableau associatif qui contient des configurations pour l'application.

// Le tableau associatif est une structure de données où chaque clé (à gauche) est associée à une valeur (à droite).
// Dans ce cas, nous avons une clé 'blog.prefix' et une valeur '/news', qui définit un préfixe d'URL pour la section blog.

// Cela signifie que toute URL liée à la section blog aura ce préfixe '/news'.
// Par exemple, si vous avez une route pour un article de blog, au lieu de '/article',
// l'URL complète pourrait être '/news/article' grâce à ce préfixe.

// Vous pouvez ajouter d'autres configurations dans ce tableau si nécessaire, en suivant le même format.

return [
    'blog.prefix' => '/news' // Préfixe utilisé pour les URLs de la section blog.
];
