<?php

namespace FoF\Sitemap\Http\Middleware;

use Flarum\Discussion\Discussion;
use Illuminate\Support\Str;
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

        $this->logger->info('URI: '.$request->getRequestTarget());
        if(Str::startsWith($request->getUri(), '/d/')){
            $this->logger->info('ID: '.Str::between($request->getUri(), '/d/', '-'));
            
            $discussion = Discussion::whereHas('tags', function($query){
                $query->whereIn('id', [38, 33, 35, 36]);
            })->find(Str::between($request->getUri(), '/d/', '-'));

            if($discussion){
                return $response->withAddedHeader('X-Robots-Tag', 'noindex');
            }
        }

        return $response;
    }
}
