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
"hnrazevedo/Viewer": "^1.3"
```

or run

```bash
composer require hnrazevedo/Viewer
```

## Methods

### Extensions

- View files: view.php
- Imported files: inc.php 

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
            'param3' => '<a href="#">Parameter3</a>'  
        ]    
    ];

Viewer::create(__DIR__.'/Views/')
      ->render('default', $data);
```

## Returning data in the view

#### The htmlspecialchars function is used by default as an escape to prevent XSS attacks.
É utilizado de forma padrão a função htmlspecialchars como escape para evitar ataques XSS.  

#### Sintax:
Sintaxe:
```
{{ $var }} htmlspecialchars
```

#### To display information without space use the syntax:
Para exibir informações sem espace utilize a sintaxe: 
```
{{!! $var !!}} NO htmlspecialchars
```

#### HTML file example
```html
<html>
    {{ $parameter }}
    {{ $parameter.param2 }}
    {{ $parameter.param3 }}
    {{!! $parameter.param3 !!}}
</html>
```
#### Note: If there is no variable to replace the value defined in the view, the text will be visible
#### HTML file result example:
```html
<html>
    {{ $parameter }}
    param2Value 
    <a href="#">Parameter3</a> 
    <a tag>Parameter</a tag>
</html>
```

### NOTE: to return any object, it must have implemented the "__get" method, returning the desired value.
```php
$user = new Model\User();

Viewer::create(__DIR__.'/Views/')
      ->render('default', ['user'=>$user]);
```
```html
{{ $user.name }} -> return $user->$name
```

### Import content within the view. 
### NOTE: File extension inc.php
### NOTE: File path is from the defined view path in question
```html
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
### If the file is not found, in order to avoid a page break, a div results with an error message instead of include.
Caso o arquivo não seja encontrado, para não haver quebra de página, é resultado uma div com a mensagem de erro no lugar do include.
```html
<html>
    <body>
        <div class='view error'>Component error: Impotation file does not exist: header.inc.php .</div>
        <main>
            ...
        </main>
        <footer>
            ...
        </footer>
    </body>
</html>
```

## HTML compression
### All code returned from a view or include is compressed. Thus, to avoid code problems, all comments are ignored when rendering the content.
Todo código retornado de uma view ou include é compressado. Com isto, para evitar problemas de código, todos os comentários são ignorados na renderização do conteúdo.

#### Source code
```html
<html>
    <body>
        <main>
            <!-- Multi-line 
                             comment --> ...
        </main>
        <footer>
            ...
        </footer>
    </body>
    <script>
        ...; // Single line comment
        /* comments */ ...; /* comments */
        /* 
            Multi-line
            commnets
        */
        ...;
    </script>
</html>
```
#### Rendered code
```html
<html><body><main>...</main><footer>...</footer></body><script>...;...;...;</script></html>
```

## Support

###### Security: If you discover any security related issues, please email hnr.azevedo@gmail.com instead of using the issue tracker.

Se você descobrir algum problema relacionado à segurança, envie um e-mail para hnr.azevedo@gmail.com em vez de usar o rastreador de problemas.

## Credits

- [Henri Azevedo](https://github.com/hnrazevedo) (Developer)
- [Robson V. Leite](https://github.com/robsonvleite) (Readme based on your datalayer design)

## License

The MIT License (MIT). Please see [License File](https://github.com/hnrazevedo/Viewer/blob/master/LICENSE.md) for more information.
