<?php

use Illuminate\Support\Facades\Route;
use TransformStudios\MagicLink\Http\Controllers\EmailMagicLinkController;

Route::post('magic-link/email-login-link', [EmailMagicLinkController::class, '__invoke'])->name('magic-link.send-email');
