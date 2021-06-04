<?php

return [
    'email_subject' => env('MAGIC_LINK_EMAIL_SUBJECT', 'Magic Login Link'),
    'redirect' => env('MAGIC_LINK_REDIRECT', env('APP_URL')),
];
