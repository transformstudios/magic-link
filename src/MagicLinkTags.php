<?php

namespace TransformStudios\MagicLink;

use Statamic\Tags\Tags;

class MagicLinkTags extends Tags
{
    protected static $handle = 'magic_link';

    public function redirect(): string
    {
        return request('redirect', config('magic-link.redirect'));
    }

    public function url(): string
    {
        return route('magic-link.send-email');
    }
}
