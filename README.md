# Viewer @HnrAzevedo

[![Maintainer](https://img.shields.io/badge/maintainer-@hnrazevedo-blue?style=flat-square)](https://github.com/hnrazevedo)
[![Latest Version](https://img.shields.io/github/v/tag/hnrazevedo/Viewer?label=version&style=flat-square)](Release)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/quality/g/hnrazevedo/Viewer?style=flat-square)](https://scrutinizer-ci.com/g/hnrazevedo/Viewer/?branch=master)
[![Build Status](https://img.shields.io/scrutinizer/build/g/hnrazevedo/Viewer?style=flat-square)](https://scrutinizer-ci.com/g/hnrazevedo/Viewer/build-status/master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/hnrazevedo/Viewer?style=flat-square)](https://packagist.org/packages/hnrazevedo/Viewer)
[![Total Downloads](https://img.shields.io/packagist/dt/hnrazevedo/Viewer?style=flat-square)](https://packagist.org/packages/hnrazevedo/Viewer)

###### Viewer is a simple friendly URL abstractor. Its author is not a professional in the development area, just someone in the Technology area who is improving his knowledge.

O Viewer é um simples abstrator de URL amigável. Seu autor não é profissional da área de desenvolvimento, apenas alguem da área de Tecnologia que está aperfeiçoando seus conhecimentos.

### Highlights

- Easy to set up (Fácil de configurar)
- Simple controller interface (Interface de controlador simples)
- Composer ready (Pronto para o composer)

## Installation

Viewer is available via Composer:

```bash 
"hnrazevedo/Vviewer": "^1.0"
```

or run

```bash
composer require hnrazevedo/Viewer
```

## Configure server

### Nginx

#### nginx.conf
```
location / {
    index index.php;
    try_files $uri  /index.php$is_args$args;
}
```
### Apache

#### .htaccess
```
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews
        Options -Indexes
    </IfModule>

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]

    <FilesMatch "index.php">
        allow from all
    </FilesMatch>

</IfModule>
```

## Documentation

###### For details on how to use the Viewer, see the sample folder with details in the component directory

Para mais detalhes sobre como usar o Viewer, veja a pasta de exemplos com detalhes no diretório do componente

#### Configure

#### It is necessary to configure the storage directory of the routes
É necessário configurar o diretório de armazenamento das rotas

```php
define("Viewer_CONFIG", [
    "path" => "/Routes/", //Directory where PHP files with routes are stored
    "path.filters" => __DIR__."/Filters/", // Directory where the extended HnrAzevedo\Filter objects are stored with the filters
    "controller.namespace" => "Example\\Controllers" // Namespace of your project's controller
]);
```

#### errors

#### In cases of configuration errors or nonexistent pages, the Viewer will throw an Exception.
Em casos de erros de configuração ou páginas inexistentes, o Viewer disparara uma Exception.

#### Viewer methods

#### Available methods

- get: URL access or get method
- post: post method
- ajax: called fetch or XMLHttpRequest
- form*: to be implemented globally

#### The routes must be set in a flat file without classes, as they will be imported when creating the object
As rotas devem ser setadas num arquivo simples sem classes, pois seram importadas na criação do objeto

```php
use HnrAzevedo\Viewer\Viewer;

/* Standard route definition mode */
Viewer::get('/','Application:index');

/* Set route name to be called by identification */
Viewer::get('/','Application:index')->name('index');

/* Set filter for route */
Viewer::get('/logout','User:logout')->filter('user_in');
/* OR */
Viewer::get('/logout','User:logout')->filter(['user_in']);

/* Pass parameters to controller and method */
Viewer::post('/{controller}/{method}','{controller}:{method}');

/* Ajax example */
Viewer::ajax('/userList/','User:listme');

/* Group only serves to add filters for all its members and a prefix in their URL */
Viewer::group('/administrator/', function(){
    /* POST: /administrator/controller */
    Viewer::post('/controller/','Administrator:execute');
    /* GET: /administrador/pages/index */
    Viewer::get('/pages/index','Administrator:view');
})->filter('admin');

/* Perform anonymous function directly via the route */
Viewer::get('/{parameter}', function($data){
    echo $data['parameter'];
});

/* Filter definition */
Viewer::get('/my-account','User:my_account')->filter('user_in');
```

#### Route definition orders

##### Correct way of defining routes
```php
/* Access via anything except /1 and /3 */
Viewer::get('/{teste}',function(){
    echo 'teste';
});
/* Acess via /1 */
Viewer::get('/1',function(){
    echo 1;
});
/* Acess via /3 */
Viewer::get('/3',function(){
    echo 3;
});
```

##### Incorrect way of defining routes
```php
/* It will never be accessed */
Viewer::get('/1',function(){
    echo 1;
});

/* It will never be accessed */
Viewer::get('/3',function(){
    echo 3;
});

/* Access via anything */
Viewer::get('/{teste}',function(){
    echo 'teste';
});
```

#### run route

```php
use HnrAzevedo\Viewer\Viewer;

/* NOTE: in case of error an exception is thrown */
/* Fires from the URL accessed */
Viewer::create()->dispatch();

/* Shoot by name */
Viewer::create()->dispatch('index');
```

## Controller
```php
namespace Example\Controllers;

class User{

    public function my_account(array $data): void
    {
        // Returning values ​​in the controller
        var_dump($data['GET']);
        var_dump($data['POST']);
        var_dump($data['FILES']);
    }

}
```

## Route Filter

#### To create filters for your routes, see https://github.com/hnrazevedo/Filter.
Para criar filtros para suas rotas, consulte https://github.com/hnrazevedo/Filter.


## Support

###### Security: If you discover any security related issues, please email hnrazevedo@gmail.com instead of using the issue tracker.

Se você descobrir algum problema relacionado à segurança, envie um e-mail para hnrazevedo@gmail.com em vez de usar o rastreador de problemas.

## Credits

- [Henri Azevedo](https://github.com/hnrazevedo) (Developer)
- [Robson V. Leite](https://github.com/robsonvleite) (Readme based on your datalayer design)

## License

The MIT License (MIT). Please see [License File](https://github.com/hnrazevedo/Viewer/blob/master/LICENSE.md) for more information.