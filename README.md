



## Lien vers le Diagramme d’utilisation
https://lucid.app/lucidspark/fdf934e8-8fd9-4137-bad4-1f37b4a40023/edit?viewport_loc=-10436%2C-633%2C9549%2C5255%2C0_0&invitationId=inv_0c73e5d2-58fc-4ddf-8194-532f273ec3e7



Guide de Déploiement Local

## Prérequis

Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre machine :

- [PHP 8.1+](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) et [npm](https://www.npmjs.com/)
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
