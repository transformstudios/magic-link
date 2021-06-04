<?php

namespace TransformStudios\MagicLink\Mail;

use Facades\Statamic\Routing\ResolveRedirect;
use Grosv\LaravelPasswordlessLogin\LoginUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Statamic\Auth\User;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Site;
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
        $this->redirect = $redirect;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(config('magic-link.email_subject', 'Your magic link is here...'))
            ->view('magic-link::mail.login-link')
            ->addData();
    }

    private function redirect()
    {
        if (! $redirect = $this->redirect ?? config('magic-link.redirect')) {
            throw new MissingRedirectException();
        }

        return ResolveRedirect::resolve($redirect);
    }

    private function addData(): self
    {
        $generator = tap(new LoginUrl($this->user), function (LoginUrl $generator) {
            $generator->setRedirectUrl($this->redirect());
        });

        return $this->with(array_merge(
            $this->getGlobalsData(),
            ['url' => $generator->generate()]
        ));
    }

    private function getGlobalsData(): array
    {
        $data = [];
        $site = Site::current();

        foreach (GlobalSet::all() as $global) {
            if (! $global->existsIn($site->handle())) {
                continue;
            }

            $global = $global->in($site->handle());

            $data[$global->handle()] = $global->toAugmentedArray();
        }

        return $data;
    }
}
