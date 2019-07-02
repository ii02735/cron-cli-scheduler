## Cron task scheduler

That dependency allows you to define your cron tasks for bash instructions from a PHP CLI (made with symfony/console)

__Warning__ : you must create a cron task that will run every minutes from Linux crontab

```
* * * * * php <PHP project path>/vendor/ii02735/cron.php
```

##

### Installation

Add the depedency to your project :
```
composer require ii02735/cron-scheduler
```

#### Configuration

You must provide an __environment .env file outside your project folder__ with the following environment variables in order to fill database's crendentials for Doctrine :

- __DB_DRIVER__ with your database driver
- __DB_NAME__ with your database's name
- __DB_HOST__ with your database's DSN
- __DB_USER__ with the database's user
- __DB_PASSWORD__ with the user's password

#### Important

- Don't forget to also add __the Entity's path__ in your Doctrine configuration in order to update your Database after with Doctrine's CLI.
- Don't also forget to provide your YAML file with your bash commands in __CRON_BASE_FILE__ variable (refer to syntax below).
- You can specify the language interface in the __CRON_SCHEDULER_LANG__ variable ("en" for English by default, "fr" for French).

### Usage

Three commands are available :

- To add a task : 
```
php ./vendor/ii02735/console cron:scheduler:add
```
It will look the list of bash commands in the file that you provided its path in __CRON_BASE_FILE__ variable.
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
- [_NoUseFreak_ and his collaborators](https://github.com/Cron/Cron) for their PHP implementation for CRON jobs
- [_peppeocchi_ and his collaborateurs](https://github.com/peppeocchi/php-cron-scheduler) for their advanced implementation for CRON jobs
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

#### Configuration

Vous devez fournir __un fichier d'environnement .env à l'extérieur de votre dossier de projet__ avec les variables d'environnement suivantes afin de donner accès à Doctrine à votre base de données :

- __DB_DRIVER__ avec le driver de votre base de données
- __DB_NAME__ avec le nom de votre base de données
- __DB_HOST__ avec le DSN de votre base de données
- __DB_USER__ avec l'utilisateur de la base de données
- __DB_PASSWORD__ avec le mot de passe de ce dernier

#### Important
- N'oubliez pas de préciser __le chemin de l'entité__ dans votre configuration de Doctrine afin de mettre à jour votre base de données avec le CLI de Doctrine
- N'oubliez pas de préciser le chemin du fichier YAML avec vos commandes bash dans la variable __CRON_BASE_FILE__ (vérifier la syntaxe ci-dessous) 
- Vous pouvez préciser la langue de l'interface dans la variable __CRON_SCHEDULER_LANG__ ("en" pour l'anglais par défaut, "fr" pour le français).

### Utilisation

Trois commandes sont disponibles pour l'utilisation de la dépendance :

- Pour ajouter une tâche :
```
php ./vendor/ii02735/console cron:scheduler:add
```
La commande ira piocher la liste des instructions bash dans le fichier que vous avez précisé dans __CRON_BASE_FILE__
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
- [_peppeocchi_ et ses collaborateurs](https://github.com/peppeocchi/php-cron-scheduler) pour leur implémentation avancée des tâches CRON
- [_dragonmantank_](https://github.com/dragonmantank/cron-expression) pour son parseur/analyseur d'expression CRON
