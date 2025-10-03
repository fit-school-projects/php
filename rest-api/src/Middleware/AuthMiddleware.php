<?php

namespace Books\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Psr7Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        if (!$request->hasHeader('Authorization')) {
            $response = new Psr7Response();
            return $response->withStatus(401);
        }

        $username = 'admin';
        $password = 'pas$word';

        if ($username != 'admin' || $password != 'pas$word') {
            $response = new Psr7Response();
            return $response->withStatus(401);
        }

        return $handler->handle($request);
    }
}