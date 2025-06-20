# API de Hoteles
- Creado con PHP 8.4
- Laravel 12
- Base de dato PostgreSQL
- Librería de Test PHPUnit

## Instalación:
- Tener PHP 8.4 pueden usar Laragon para facilitar esta tarea (si están en windows)
- Tener un servidor de PostgreSQL la version 17 de preferencia.
- Instalar las dependencias con ```composer install```
- Configurar el .env, colocar los datos de la base de datos.
- ejecutar las migraciones con ```php artisan migrate```
- ejecutar los datos semillas con ```php artisan db:seed``` inserta datos base,acomodaciones, tipos de habitaciones, reglas y el usuario admin para conectarse via API.
- ejecutar ```php artisan key:generate```
- ejecutar ```php artisan serve``` si tienes la instalación en Linux o conectarte al dominio con el que haya quedado en la carpeta de Laragon.


### Usuario Admin:
- email: admin@panel.com
- password: esTheAdmin.25*

