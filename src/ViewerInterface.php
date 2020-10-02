<?php

namespace HnrAzevedo\Viewer;

use Psr\Http\Server\MiddlewareInterface;

interface ViewerInterface extends MiddlewareInterface{
    
    public static function import(string $file): void;

    public static function render(string $file, ?array $data = []): string;

}