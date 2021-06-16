<?php

namespace TransformStudios\MagicLink\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Statamic\Facades\User as UserAPI;
use TransformStudios\MagicLink\Mail\MagicLink;

class EmailMagicLinkController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        /* @var \Statamic\Auth\User */
        if (! $user = UserAPI::findByEmail($request->email)) {
            return back()->with(
                [
                    'not_found' => true,
                ]
            );
        }

        Mail::to($user->email())->send(
            new MagicLink($user, $request->input('redirect')
        ));

        return back()->with(
            [
                'success' => true,
                'email' => $user->email(),
            ]
        );
    }
}
