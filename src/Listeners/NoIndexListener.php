<?php

/*
 * This file is part of fof/sitemap.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace FoF\Sitemap\Listeners;

use Flarum\Frontend\Document;
use Flarum\Settings\SettingsRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Flarum\Extension\ExtensionManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class NoIndexListener
{
    public function __construct(protected ExtensionManager $extensionManager, protected LoggerInterface $logger)
    {}

    public function __invoke(Document $document, ServerRequestInterface $request)
    {
        $noIndexTags = [38, 33, 35, 36]; // Add tags here so their discussions include the no-index

        if( ! $this->extensionManager->isEnabled('flarum-tags')){
            return;
        }

        $this->logger->info('1');

        $type = Arr::get($document->getForumApiDocument(), 'data.type');

        $this->logger->info('Type:'.$type);

        if($type != 'discussions'){
            return;
        }
        
        $tags = Arr::get($document->getForumApiDocument(), 'data.relationships.tags.data');

        $this->logger->info('Tags', $tags);
        $this->logger->info('Head', $document->head);
        $this->logger->info('Meta', $document->meta);

        foreach($tags as $tag){
            if(in_array($tag['id'], $noIndexTags)){
                $document->head[] = '<meta name="robots" content="noindex">';
                return;
            }
        }
    }
}
