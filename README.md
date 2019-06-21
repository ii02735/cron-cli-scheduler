## Planificateur de tâches CRON

Cette dépendance vous permet de définir vos tâches CRON pour des instructions bash depuis une interface de commande en PHP.

__Attention__ : Il vous est nécessaire de définir une tâche CRON qui s'exécutera toutes les minutes.


```
* * * * * php <chemin de votre projet PHP>/vendor/ii02735/cron.php
```

##

### Installation

Ajouter la dépendance à votre projet :

```
composer require ii02735/cronScheduler
```

#### Configuration de la base de données

Renseigner les informations d'accès à la base de données dans le fichier __credentials.php__.

### Utilisation

Trois commandes sont disponibles pour l'utilisation de la dépendance :

- Pour ajouter une tâche :
```
php ./vendor/ii02735/console cron:scheduler:add
```
La commande ira piocher la liste des instructions bash dans le fichier ``config/commands.yml``
Si vous souhaitez rajouter des instructions, veuillez respecter la syntaxe du fichier yml :
```yaml
<id de la commande>: ...
  cmd: <commande bash>
  schedule: <expression cron>
  disable:  true  #(optionnel si vous désactiver la commande lors de son ajout en tâche CRON)
```

- Pour lister les tâches :
```yaml
php ./vendor/ii02735/console cron:scheduler:list
```
  - Options :
    - __-f__, __--filtre=FILTRE__   Trie les tâches selon leur état (on|off)
    - __-d__, __--del=DEL__         Supprimer une tâche
    - __-t__, __--toggle=TOGGLE__   Activer/désactiver une tâche

- Pour charger un fichier contenant des instructions bash :
```yaml
php ./vendor/ii02735/console cron:scheduler:load <chemin du fichier YAML>
```
## Remerciements
- [_NoUseFreak_ et ses collaborateurs](https://github.com/Cron/Cron) pour leur implémentation en PHP des tâches CRON
- [_dragonmantank_](https://github.com/dragonmantank/cron-expression) pour son parseur/analyseur d'expression CRON
