<?php

namespace TransformStudios\MagicLink\Mail;

use Facades\Statamic\Routing\ResolveRedirect;
use Grosv\LaravelPasswordlessLogin\LoginUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Statamic\Auth\User;
use TransformStudios\MagicLink\Exceptions\MissingRedirectException;

class MagicLink extends Mailable
{
    use Queueable, SerializesModels;

    private User $user;
    private ?string $redirect = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, ?string $redirect)
    {
        $this->user = $user;
        $this->redirect = $redirect;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $generator = tap(new LoginUrl($this->user), function (LoginUrl $generator) {
            $generator->setRedirectUrl($this->redirect());
        });

        return $this
            ->subject(config('magic-link.email_subject', 'Your magic link is here...'))
            ->view('magic-link::mail.login-link')
            ->with(['url' => $generator->generate()]);
    }

    private function redirect()
    {
        if (! $redirect = $this->redirect ?? config('magic-link.redirect')) {
            throw new MissingRedirectException();
        }

        return ResolveRedirect::resolve($redirect);
    }
}
