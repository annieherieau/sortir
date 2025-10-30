# ENI - Projet Sortir

Projet PHP Symfony créé par [Yoann](https://github.com/Ahellys "‌") & [Annie](https://github.com/annieherieau "‌")

## 1. Présentation

La société ENI souhaite développer pour ses stagiaires actifs ainsi que ses anciens stagiaires une plateforme web leur permettant d’organiser des sorties.

La plateforme est une plateforme privée dont l’inscription sera gérée par le ou les administrateurs.

Les sorties ainsi que les participants sont rattachés à un campus pour permettre une organisation géographique des sorties.

## 2. Fonctionnalités réalisées (Itération 1)

- **Gestion des utilisateurs** : se connecter, se souvenir de moi, gérer son profil
- **Gestion des sorties** :
  - Tous : Afficher la liste des sorties par campus, afficher le détail d’une sortie, afficher le détail des participants,
  - Organisateur : créer une sortie, modifier / supprimer un sortie non publiée, publier une sortie, annuler une sortie
  - Participant : s’inscrire à une sortie, se désister
  - Application : clôturer les inscriptions, archiver les sortie

## 3. Stack

### 3.1. Base de données

- Base de Données SQL/MariaDB

### 3.2. Application Symfony

- PHP 8.1
- Symfony 6.4
- Starters:
  - Apache-pack
  - orm-Fixture
  - fakerPHP
  - Bootstrap

### 3.3. Front

- Boostrap 5.3.3

# Installation

**Pré-requis** : serveur Apache-MySQL-PHP (Wampp, Xampp,…)

1. Cloner le répertoire

```bash
git clone git@github.com:{repo}/{project}.git
```

2. Connecter la base de données

```bash
# exemple fichier .env.local
DATABASE_URL=mysql://root:@127.0.0.1:3306/projectName?server-version=10.4.28-MariaDB&charset=utf8mb4
```

3. Installer les packages

```
composer install
```

4. Executer la migration

```bash
# executer la migration
symfony console doctrine:migration:migrate
```

5. Serveur local

```bash
# démarrer le serveur
symfony serve -d
# arrêter le serveur
symfony server:stop
```
## Configuration
Fichier assets/controllers.json
- ux/turbo turbo-core : enable à false
