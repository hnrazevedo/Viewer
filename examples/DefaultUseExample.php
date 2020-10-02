
<?php

use HnrAzevedo\Viewer\Viewer;

/* NOTE: in case of error an exception is thrown */

try{

    Viewer::path(__DIR__.'/Views/')->render('default', $data);

}catch(Exception $er){

    die("Code Error: {$er->getCode()}, Line: {$er->getLine()}, File: {$er->getFile()}, Message: {$er->getMessage()}.");

}
