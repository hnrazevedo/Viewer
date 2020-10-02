<?php

namespace HnrAzevedo\Viewer;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use HnrAzevedo\Http\Uri;

use HnrAzevedo\Http\Factory;

final class Viewer implements ViewerInterface
{
    use Helper;

    private static Viewer $instance;
    private static string $path = '';
    private static bool $middleware;

    public static function getInstance(): Viewer
    {
        self::$instance = (isset(self::$instance)) ? self::$instance : new self();
        return self::$instance;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(null === $request->getAttribute('viewer')){
            throw new \RuntimeException('The path and file parameters for viewing were not defined in the request');
        }

        self::$path = $request->getAttribute('viewer')['path'];

        $buffer = $this->getInstance()->render($request->getAttribute('viewer')['file'],
            (isset($request->getAttribute('viewer')['data'])) ? $request->getAttribute('viewer')['data'] : [] );

        $request = $request->withBody((new Factory())->createStream($buffer));

        return $handler->handle($request);
    }

    public static function render(string $file, ?array $data = []): string
    {
        self::getInstance()->data = $data;
        
        if(!isset(self::$middleware)){
            self::getInstance()->handle($file);
            return '';
        }

        return self::getInstance()->getBody($file.'.view.php');
    }

    private function handle(string $file): void
    {
        self::$middleware = false;
        
        $serverRequest = (new Factory())->createServerRequest(
            $_SERVER['REQUEST_METHOD'], 
            new Uri($_SERVER['REQUEST_URI'])
        );
    
        $serverRequest = $serverRequest->withAttribute('viewer',[
            'path' => self::$path,
            'file' => $file,
            'data' => self::getInstance()->data
        ]);


        self::getInstance()->process($serverRequest, new class implements RequestHandlerInterface{
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                echo $request->getBody()->getContents();
                return (new Factory())->createResponse(200);
            }
        });

    }

    public static function path(string $path): Viewer
    {
        self::$path = $path;
        return self::getInstance();
    }

    public static function import(string $file): void
    {
        try{
            echo self::getInstance()->getBody($file.'.inc.php');
        }catch(\Exception $er){
            echo "<div class='view error'>Component error: {$er->getMessage()}</div>";
        }
    }

    private function getBody(string $file)
    {
        $buffer = $this->getInstance()->getOB(self::$path . DIRECTORY_SEPARATOR . $file);
        $buffer = $this->getInstance()->getVars($buffer);
        $buffer = $this->getInstance()->getVars($buffer, false);
        $buffer = $this->getInstance()->removeComments($buffer);
        return $buffer;
    }

}
