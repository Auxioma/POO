<?php
namespace Framework;

class Module
{
    // Constantes qui peuvent être définies dans les sous-classes.
    // Ces constantes servent de points d'extension pour configurer différents aspects du module.

    /**
     * DEFINITIONS : Un emplacement pour les définitions de configuration du module.
     * Il peut s'agir de fichiers ou d'autres ressources de configuration nécessaires à ce module.
     * Par défaut, cette constante est définie sur `null`, mais chaque module peut la redéfinir pour spécifier où se trouvent ses configurations.
     */
    const DEFINITIONS = null;

    /**
     * MIGRATIONS : Un emplacement pour les migrations de base de données associées à ce module.
     * Les migrations sont utilisées pour modifier la structure de la base de données (ajouter des tables, des colonnes, etc.).
     * Cette constante est également définie sur `null` par défaut et peut être redéfinie par chaque module.
     */
    const MIGRATIONS = null;

    /**
     * SEEDS : Un emplacement pour les données de peuplement initiales (seeds) pour ce module.
     * Les seeds permettent d'insérer des données de base dans la base de données, utiles lors du développement ou de la mise en place initiale.
     * Par défaut, cette constante est aussi définie sur `null`.
     */
    const SEEDS = null;
}
