# klear-starter
klear-starter


## Klear en Packagist  https://packagist.org/packages/

¿que es packagist? es un gestor de paquetes, que se pueden instalar con composer.
Por las limitaciones de mayúsculas que tiene este gestor no se puede instalar klear por individual. Pero hay algo mejor y es que con "**composer create-project  irontec/klear-starter my-proyect**" crea un skeleton de un proyecto zend framework 1 con un klear basico funcionando (skeleton https://github.com/irontec/klear-starter). 


## Como empezar un nuevo proyecto

Para este ejemplo la base de datos y el namespace serán “Testing”.

En el directorio de desarrollo ejecutamos el siguiente comando:
```bash
composer create-project irontec/klear-starter Testing
```

Lo que crea un proyecto de Zend Framework 1 con el sistema de layout’s activo, el modulo klear, los generadores y la librería Iron.

Al terminar la instalación composer, deja el siguiente mensaje "ahora entra a tu proyecto y ejecuta  php cli/install.php" el cual pide la información que necesaria en el application.ini, crea la tabla “KlearUsers” y ejecutará los generadores klear-models-mappers-generator.php, klear-db-generator.php y klear-yaml-generator.php

Si todo se ejecuta correctamente queda funcionando una web limpia y un klear, con la tabla de usuarios y con datos de accesos admin:1234


## Requisitos:
* Zend-framework 1.12
* MySQL 5.5

* PHP 5.5
* php5-imagick
* php5-curl
* php5-mysql
* php5-mcrypt
* php-apc
* php-pear
* php5-dev
* php5-readline

* pecl install yaml

## Configuraciones de Apache:
```apache
<Directory /path/>
    Options -Indexes FollowSymLinks -MultiViews
    AllowOverride All
    Order allow,deny
    allow from all
</Directory>
```

