<?php

namespace TransformStudios\MagicLink;

use Edalzell\Forma\Forma;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $publishAfterInstall = false;

    protected $routes = [
        'web' => __DIR__.'/../routes/web.php',
    ];

    protected $tags = [
        MagicLinkTags::class,
    ];

    public function boot()
    {
        parent::boot();

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/magic-link'),
        ]);

        Forma::add('transformstudios/magic-link');
    }
}
