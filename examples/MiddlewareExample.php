<?php

use HnrAzevedo\Http\Factory;
use HnrAzevedo\Http\Uri;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

use HnrAzevedo\Viewer\Viewer;

try{
    $serverRequest = (new Factory())->createServerRequest(
        $_SERVER['REQUEST_METHOD'], 
        new Uri($_SERVER['REQUEST_URI'])
    );

    $serverRequest = $serverRequest->withAttribute('viewer',[
        'path' => 'Views',
        'file' => 'default',
        'data' => $data
    ]);
    
    class App implements MiddlewareInterface{
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
            echo $request->getBody()->getContents();
            return (new Factory())->createResponse(200);
        }
    }

    define('GLOBAL_MIDDLEWARES',[
        Viewer::class,
        App::class
    ]);

    function nextExample(RequestHandlerInterface $defaultHandler): RequestHandlerInterface
    {
        return new class (GLOBAL_MIDDLEWARES, $defaultHandler) implements RequestHandlerInterface {
            private RequestHandlerInterface $handler;
            private array $pipeline;

            public function __construct(array $pipeline, RequestHandlerInterface $handler)
            {
                $this->handler = $handler;
                $this->pipeline = $pipeline;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                if (!$middleware = array_shift($this->pipeline)) {
                    return $this->handler->handle($request);
                }

                $next = clone $this;
                $this->pipeline = [];

                $response = (new $middleware())->process($request, $next);

                return $response;
            }
        };
    }


    function runMiddlewares($serverRequest)
    {
        nextExample(new class implements RequestHandlerInterface{
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return (new Factory())->createResponse(200);
            }
        })->handle($serverRequest);
    }

    runMiddlewares($serverRequest);

}catch(Exception $er){

    die("Code Error: {$er->getCode()}<br>Line: {$er->getLine()}<br>File: {$er->getFile()}<br>Message: {$er->getMessage()}.");

}
