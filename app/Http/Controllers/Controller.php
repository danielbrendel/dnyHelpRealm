<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Auth;
use \App\User;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Set language for current execution
     *
     * @return Closure
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::guest()) {
                $user = User::get(auth()->id());
                if ($user) {
                    \App::setLocale($user->language);
                }
            } else {
                \App::setLocale(env('APP_LANG', 'en'));
            }

            return $next($request);
        });
    }

    /**
     * Get headers for mail() function call
     * @return string
     */
    public static function getMailHeaders()
    {
        return "Content-type: text/html; charset=utf-8\r\n"
            . "From: " . env('APP_NAME') . " <" . env('MAILSERV_EMAILADDR') . ">\r\n"
            . "Reply-To: " . env('APP_NAME') . " <" . env('MAILSERV_EMAILADDR') . ">\r\n"
            . "Return-Path: " . env('APP_NAME') . " <" . env('MAILSERV_EMAILADDR') . ">\r\n"
            . "Organization: " . env('APP_NAME') . "\r\n"
            . "MIME-Version: 1.0\r\n"
            . "X-Mailer: PHP". phpversion() . "\r\n";
    }
}
