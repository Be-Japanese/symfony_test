# Symfony Football Project

A Symfony project for creating and managing football teams and there players

## Getting Started

1. If not already done, install php , database server "mysql , postgresql "
2. Clone this repository to your php server location or wsl using docker
2. Run `composer install` to install all needed packages
3. Edit `.env` with your database credentials , or use `.env.local` for docker
3. Run `symfony console doctrine:database:create` to create database 
4. Run `symfony console doctrine:migration:migrate` to migrate database  
4. Run `symfony console doctrine:fixtures:load` to seed dummy data 
5. Run `symfony server:start` to serve or use docker compose.

## Features
![plot](/relations.png)

* Each Team can have multiple players 
* Each Player can belong to only one Team
* Each Team can have multiple transactions 
* Each transaction can belong to only one Team
* Team current balance "sum of transactions"
* Each Offer must belong to only one Player
* Each Player can have only one offer


**thanks!**


## Credits

Created by [Ahmed Mansour](https://mansour.ly).