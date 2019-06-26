## Cron task scheduler

That dependency allows you to define your cron tasks for bash instructions from a PHP CLI (made with symfony/console)

__Warning__ : you must create a cron task that will run every minutes from Linux crontab

```
* * * * * php <chemin de votre projet PHP>/vendor/ii02735/cron.php
```

##

### Installation

Add the depedency to your project :
```
composer require ii02735/cron-scheduler
```

#### Database configuration

Fill database credentials in __config.php__ if you want to create an EntityManager instance.
Else you can load in __$entityManagerInstance__ your instance by complete its value with a PHP file path

Don't forget to also add paths where your Entites are stored in __$entities_paths__ if you intend to create en EntityManager.


### Usage

Three commands are available :

- To add a task : 
```
php ./vendor/ii02735/console cron:scheduler:add
```
It will look the list of bash commands in ``config/commands.yml``
If you want to add more, please respect the syntax below : 
```yaml
<task identifier>: ...
  cmd: <bash command>
  schedule: <cron expression>
  disable:  true  #(optional if you want to disable your task after its addition)
```

- To list the created tasks :
```yaml
php ./vendor/ii02735/console cron:scheduler:list
```
  - Options :
    - __-f__, __--filter=FILTER__   Sort tasks from their status (on|off)
    - __-d__, __--del=DEL__         Delete a task
    - __-t__, __--toggle=TOGGLE__   Enable/disable a task
- Load a YAML file as tasks to be loaded :
```yaml
php ./vendor/ii02735/console cron:scheduler:load <YAML file path>
```
## Thanks
- [_NoUseFreak_ et ses collaborateurs](https://github.com/Cron/Cron) for their PHP implementation for CRON jobs
- [_dragonmantank_](https://github.com/dragonmantank/cron-expression) for his CRON expression parser
##

## Planificateur de tâches CRON

Cette dépendance vous permet de définir vos tâches CRON pour des instructions bash depuis une interface de commande en PHP.

__Attention__ : Il vous est nécessaire de définir une tâche CRON qui s'exécutera toutes les minutes depuis Linux crontab


```
* * * * * php <chemin de votre projet PHP>/vendor/ii02735/cron.php
```

##

### Installation

Ajouter la dépendance à votre projet :

```
composer require ii02735/cron-scheduler
```

#### Configuration de la base de données

Renseigner les informations d'accès dans __config.php__ si vous souhaitez créer une instance d'EntityManager
Sinon vous pouvez en charger une depuis __$entityManagerInstance__ en renseignant le chemin du fichier PHP qui construit l'instance.

N'oubliez pas de renseigner vos chemins où sont stockés les Entités dans __$entities_paths__ si vous souhaitez créer votre instance d'EntityManager

### Utilisation

Trois commandes sont disponibles pour l'utilisation de la dépendance :

- Pour ajouter une tâche :
```
php ./vendor/ii02735/console cron:scheduler:add
```
La commande ira piocher la liste des instructions bash dans le fichier ``config/commands.yml``
Si vous souhaitez rajouter des instructions, veuillez respecter la syntaxe du fichier yml :
```yaml
<id de la tâche>: ...
  cmd: <commande bash>
  schedule: <expression cron>
  disable:  true  #(optionnel si vous désactiver la commande lors de son ajout en tâche CRON)
```

- Pour lister les tâches :
```yaml
php ./vendor/ii02735/console cron:scheduler:list
```
  - Options :
    - __-f__, __--filter=FILTER__   Trie les tâches selon leur état (on|off)
    - __-d__, __--del=DEL__         Supprimer une tâche
    - __-t__, __--toggle=TOGGLE__   Activer/désactiver une tâche

- Pour charger un fichier contenant des instructions bash :
```yaml
php ./vendor/ii02735/console cron:scheduler:load <chemin du fichier YAML>
```
## Remerciements
- [_NoUseFreak_ et ses collaborateurs](https://github.com/Cron/Cron) pour leur implémentation en PHP des tâches CRON
- [_dragonmantank_](https://github.com/dragonmantank/cron-expression) pour son parseur/analyseur d'expression CRON
