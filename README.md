# Viewer @HnrAzevedo

[![Maintainer](https://img.shields.io/badge/maintainer-@hnrazevedo-blue?style=flat-square)](https://github.com/hnrazevedo)
[![Latest Version](https://img.shields.io/github/v/tag/hnrazevedo/Viewer?label=version&style=flat-square)](Release)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/quality/g/hnrazevedo/Viewer?style=flat-square)](https://scrutinizer-ci.com/g/hnrazevedo/Viewer/?branch=master)
[![Build Status](https://img.shields.io/scrutinizer/build/g/hnrazevedo/Viewer?style=flat-square)](https://scrutinizer-ci.com/g/hnrazevedo/Viewer/build-status/master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/hnrazevedo/Viewer?style=flat-square)](https://packagist.org/packages/hnrazevedo/Viewer)
[![Total Downloads](https://img.shields.io/packagist/dt/hnrazevedo/Viewer?style=flat-square)](https://packagist.org/packages/hnrazevedo/Viewer)

###### The Viewer is a simple component of managing the MVC project visualization layer. Its author is not a professional in the development area, just someone in the Technology area who is improving his knowledge.

O Viewer é um simples componente de administração da camada de visualização de projetos MVC. Seu autor não é profissional da área de desenvolvimento, apenas alguem da área de Tecnologia que está aperfeiçoando seus conhecimentos.

### Highlights

- Easy to set up (Fácil de configurar)
- Simple controller interface (Interface de controlador simples)
- Composer ready (Pronto para o composer)

## Installation

Viewer is available via Composer:

```bash 
"hnrazevedo/Viewer": "^1.0"
```

or run

```bash
composer require hnrazevedo/Viewer
```

## Methods

### Extensions

- View files: view.php
- Imported files: tpl.php 

### Basic use
```php
require __DIR__.'/../vendor/autoload.php';

use HnrAzevedo\Viewer\Viewer;

Viewer::create(__DIR__.'/Views/') /* View file path */
      ->render('default');    
```

### Data transfer between the view and the controller
```php
require __DIR__.'/../vendor/autoload.php';

use HnrAzevedo\Viewer\Viewer;

$data = [
    'parameter'=>
        [
            'param1' => 1,
            'param2' => 'param2Value'  
        ]    
    ];

Viewer::create(__DIR__.'/Views/')
      ->render('default', $data);
```
### Returning data in the view
```
<html>
    <?= $parameter['param1'] ?>
    <?= $parameter['param2'] ?>
</html>
```
#### Or
```
<html>
    {{parameter}}
    {{parameter.param2}}
</html>
```
#### Note: If there is no variable to replace the value defined in the view, the text will be visible
#### Result:
```
<html>
    {{parameter}}
    param2Value 
</html>
```

### Import content within the view. 
### NOTE: File extension tpl.php
### NOTE: File path is from the defined view path in question
```
<html>
    <body>
        <?php $this->include('../Imports/header'); ?>
        <main>
            ...
        </main>
        <?php $this->include('../Imports/footer'); ?>
    </body>
</html>
```

## Support

###### Security: If you discover any security related issues, please email hnrazevedo@gmail.com instead of using the issue tracker.

Se você descobrir algum problema relacionado à segurança, envie um e-mail para hnrazevedo@gmail.com em vez de usar o rastreador de problemas.

## Credits

- [Henri Azevedo](https://github.com/hnrazevedo) (Developer)
- [Robson V. Leite](https://github.com/robsonvleite) (Readme based on your datalayer design)

## License

The MIT License (MIT). Please see [License File](https://github.com/hnrazevedo/Viewer/blob/master/LICENSE.md) for more information.