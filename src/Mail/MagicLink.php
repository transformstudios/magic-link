<?php

namespace TransformStudios\MagicLink\Mail;

use Grosv\LaravelPasswordlessLogin\PasswordlessLogin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Statamic\Auth\User;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Site;
use Statamic\Facades\User as UserFacade;
use Statamic\Routing\ResolveRedirect;
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
    public function __construct(User|Authenticatable $user, ?string $redirect)
    {
        $this->redirect = $redirect;
        $this->user = UserFacade::fromUser($user);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->to($this->user->email())
            ->subject(config('magic-link.email_subject', 'Your magic link is here...'))
            ->view('magic-link::mail.login-link')
            ->addData();
    }

    private function addData(): self
    {
        return $this->with(array_merge(
            $this->getGlobalsData(),
            [
                'url' => PasswordlessLogin::forUser($this->getUser())
                    ->setRedirectUrl($this->redirect())
                    ->generate(),
            ]
        ));
    }

    private function getUser(): Authenticatable|User
    {
        return $this->user instanceof \Statamic\Auth\Eloquent\User ? $this->user->model() : $this->user;
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

    private function redirect()
    {
        if (! $redirect = $this->redirect ?? config('magic-link.redirect')) {
            throw new MissingRedirectException();
        }

        return (new ResolveRedirect)($redirect);
    }
}
