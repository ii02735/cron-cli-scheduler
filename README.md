##Planificateur de tâches CRON

Cette dépendance vous permet de définir vos tâches CRON depuis une interface de commande en PHP.

__Attention__ : Il vous est nécessaire de définir une tâche CRON qui s'exécutera toutes les minutes.


```
* * * * * php <chemin de votre projet PHP>/vendor/ii02735/cron.php
```

##

###Installation

Ajouter la dépendance à votre projet :

```
composer require ii02735/cronScheduler
```

####Configuration de la base de données

Renseigner les informations d'accès à la base de données dans le fichier __credentials.php__.

