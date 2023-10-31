# NotaResto Application de notation de restaurant
  
## Comment installer le projet
    
### Environnement nécessaire
- PHP 8.2
- MySQL
- Symfony 5.4

### Installer le binaire de Symfony
https://symfony.com/download
  
### Dans le terminal, il faudra tapper plusieurs commandes
>Pour cloner le projet depuis gitlab, dans le dossier voulue :  
>- git clone git@gitlab.in2p3.fr:tommy.serain/notaresto.git  
>
>Pour instaler les vendors, à la racine du projet :  
>- symfony composer install
>
>>A la racine du projet, créer un fichier .env.local pour renseigner nos accès en bdd.  
>>Exemple pour ce fichier :  
>>DATABASE_URL="mysql://Identifiant:MotDePasse@127.0.0.1:3306/NomDeLaBaseDeDonnees?serverVersion=8&charset=utf8mb4"  
>
>Si besoin créer la base de données  
>- Symfony console d:d:c  
>
>Jouer la migration
>- symfony console d:m:m  
>
>Peupler l'application avec des fixtures  
>- symfony console d:f:l
  
  
  
  
## TP à réaliser pour monter en compétences.
Dépot git ou se trouve le projet :  
https://gist.github.com/tomsihap/e939150d64b25c7b4010c847a3e77d48
## Objectif :
Le but de ce TP va être de créer une application de notation de restaurant.  
Cette application sera réalisée avec Symfony 5.4 et twig.  
### Un utilisateur pourra :
- créer un compte et se connecter.
- rechercher les restaurants par code postal
- consulter la liste des restaurant les mieux notés sur la page d'accueil
- consulter la liste des avis clients sur la page d'un restaurant
### Une fois connecté
#### Un client pourra :
- noter un restaurant
- ajouter un avis sur la page d'un restaurant
- accéder à une page mon compte pour gérer ses avis.
#### Un restaurateur pourra :
- ajouter et gérer un ou plusieurs restaurants
- répondre aux avis clients sur ses restaurants
- accéder à une page mon compte pour gérer ses restaurants et ses avis
#### Un modérateur pourra :
- accéder à une administration pour gérer tous les éléments du site
