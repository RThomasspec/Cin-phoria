

## Lien vers le MCD
https://lucid.app/lucidchart/f69aaed3-31ed-4a09-9321-550a07ef9006/edit?viewport_loc=-2508%2C-307%2C2428%2C1342%2C0_0&invitationId=inv_d753cf06-3d3a-49c0-90ee-375953dc35da


## Lien vers le Diagramme d’utilisation
https://lucid.app/lucidchart/b720c13c-e8e5-4645-bc24-4f341dd9b9c0/edit?beaconFlowId=C9E81DE598663CB4&invitationId=inv_7bf4cc1f-1ebe-4010-b103-7803df32261b&page=0_0#

## Lien vers le Diagramme de séquance (Utilisateur)
https://lucid.app/lucidchart/b720c13c-e8e5-4645-bc24-4f341dd9b9c0/edit?viewport_loc=715%2C89%2C4376%2C2419%2C0_0&invitationId=inv_7bf4cc1f-1ebe-4010-b103-7803df32261b

## Lien vers le Diagramme de séquance (Employe)

## Lien vers le Diagramme de séquance (Visiteur)

## Lien vers le Diagramme de séquance (Administrateur)



Guide de Déploiement Local

## Prérequis

Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre machine :

- [PHP 8.1+](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Symfony CLI](https://symfony.com/download)
- [MySQL](https://www.mysql.com/) ou tout autre SGBD compatible

## Installation

Clonez le dépôt sur votre machine locale à l'aide de la commande suivante :

```bash
git clone https://github.com/RThomasspec/Cin-phoria
cd Cin-phoria

composer install

Configurer l'environnement
cp .env .env.local


DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"

Importez les données du fichier Cinephoria(6).sql dans la bdd via phpmyadmin

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

## Environement de test

installer php unit : composer require --dev phpunit/phpunit

-- Pour éxécuter un test spécifique : ./vendor/bin/phpunit tests/DirectoryName/TestFileName.php


