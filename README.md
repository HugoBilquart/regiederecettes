# Projet regie de recettes

Module de régie de recettes créé pendant un stage de BTS

## Extraits du cahier des charges :

### 1 - Contexte et définition du projet :

Actuellement les paiements effectués à la
régie de recettes de l’établissement sont enregistrés manuellement sur des liasses papier. L’idée est
de développer un module qui pourra être intégré à l’intranet de l’établissement afin d’assurer une
gestion numérique de cette régie.

### 2 - Objectif du projet

Le module devra être lié à l’annuaire LDAP de l’établissement. Les
enregistrements devront disposer de tous les champs actuellement proposés sous forme papier. Une
validation finale par l’agent comptable sera nécessaire. Le projet devra respecter la charte graphique
de l’établissement et pouvoir être intégré facilement à l’intranet.

## Recapitulatif des versions

### V1 : Formulaire, page des resultats, recapitulatif

### V2 : Menu, séparation des rubriques (Formulaire nouvelle transactions, résultats, recapitulatif)

### V3 : Changement de la charte graphique pour s'adapter à l'intranet

### V4 : Création des reçus de transaction

### V5 :
#### V5.0 : Stage formation ALIPTIC
- Changement de la charte graphique pour s'adapter au nouvel intranet (bootstrap, contrastes)
- Accès a certaines fonctionnalités selon la fonction de l'utilisateur
- L'utilisateur ne peut saisir que certains types de transactions (objet) selon sa fonction (indiqué sur la page d'accueil)
- Création de récapitulatif différent selon le moyen de paiement
- Utilisation d'un fichier texte pour les étudiants à la place de la base LDAP

#### V5.2 :
- Utilisation d'une base de données SQLite au lieu de phpMyAdmin pour la mise en ligne
- Isolation de l'application pour la mise en ligne (l'application est normalement intégrée à l'intranet)

## Installation
- Necessite les extensions SQLite pour PHP
chown -R www-data:www-data ../regiederecettes
chmod -R 777 db/regie.sqlite

## Notes
- Génération de nom/prenom aleatoire pour de faux étudiants : https://fr.fakenamegenerator.com/

- Noms : Indiqués dans la base
- MDP : regiederecettes87
