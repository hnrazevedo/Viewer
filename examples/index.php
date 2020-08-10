<?php

session_start();

require __DIR__.'/../vendor/autoload.php';

use HnrAzevedo\Viewer\Viewer;

/* NOTE: in case of error an exception is thrown */

try{
    
    $data = ['parameter'=>
        [
            'param1' => 'param1Value',
            'param2' => 'param2Value'  
        ]    
    ];

    Viewer::create(__DIR__.'/Views/')
          ->render('default', $data);

}catch(Exception $er){

    die("Code Error: {$er->getCode()}, Line: {$er->getLine()}, File: {$er->getFile()}, Message: {$er->getMessage()}.");

}