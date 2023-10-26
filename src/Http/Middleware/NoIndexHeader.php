<?php

namespace FoF\Sitemap\Http\Middleware;

use Flarum\Foundation\Config;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class NoIndexHeader implements Middleware
{
    protected $policy = '';

    public function __construct(protected LoggerInterface $logger)
    {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $this->logger->info('NoIndexMiddleware', ['response', $response]);

        return $response->withAddedHeader('Referrer-Policy', $this->policy);
    }
}
