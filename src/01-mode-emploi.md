# Mode d'emploi

Voici un petit guide qui explique comment utiliser ce projet.

## Installation

C'est simple, et normalement à faire en quelques étapes :
1. Copier l'ensemble des fichiers dans un répertoire ;
2. Vous assurer le fichier `•htaccess` (ligne 2) pointe vers l'url de base de votre site ;
3. Vous assurer que le PHP est disponible sur le serveur (ou sur votre ordinateur pour 
une installation locale).

## Créer du contenu

Il suffit de rédiger vos pages dans le langage Markdown (vous pouvez aussi y inclure
du code HTML directement), et de placer tous ces documents (avec l'extension `.md`) 
dans le répertoire `src/`. 

## Modifier l'aspect du site

Pour cela, vous devez modifier le fichier `theme/struct.html` qui contient la
structure du document final. Ce qui est important à noter, c'est qu'il est prévu
que le contenu soit insérer dans la première balise de type `<section>` trouvé dans
ce fichier. De même, le menu est intégré dans la première balise de type `<nav>`(il
ne devrait y en avoir qu'une normalement).

Ensuite, vous devez modifier le fichier `theme/style.css` pour gérer les aspects de
présentation, couleurs et autres. Ici, c'est fait de manière très simpliste.

