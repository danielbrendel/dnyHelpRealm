{{--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2024 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>

        <style>
            @import url('https://fonts.googleapis.com/css?family=Nunito');

            body {
                background-color: rgb(246, 248, 242);
            }

            .mail-wrapper {
                position: relative;
                max-width: 420px;
                font-family: "Nunito", sans-serif;
            }

            .mail-title {
                position: relative;
                font-size: 1.5em;
                color: #1e204d;
                font-weight: bold;
                margin-bottom: 25px;
            }

            .mail-body {
                color: rgb(20, 20, 20);
            }

            .mail-body pre {
                white-space: pre-wrap;
                white-space: -moz-pre-wrap;
                white-space: -pre-wrap;
                white-space: -o-pre-wrap;
                word-wrap: break-word;
            }

            .mail-action {
                position: relative;
                margin-top: 25px;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .mail-footer {
                position: relative;
                margin-top: 25px;
                font-size: 0.7em;
                color: rgb(150, 150, 150);
            }

            .button {
                display: block;
                width: 115px;
                padding: 10px;
                text-align: center;
                text-decoration: none;
                border-radius: 5px;
                background-color: rgb(64, 173, 225);
                color: rgb(255, 255, 255);
                font-weight: bold;
            }
            .button:hover {
                background-color: rgb(48, 125, 242);
            }
        </style>
    </head>
    <body>
        <div class="mail-wrapper">
            <div class="mail-title">@yield('title')</div>
            <div class="mail-body">@yield('body')</div>
            <div class="mail-action">@yield('action')</div>
            <div class="mail-footer">{!! __('app.mail_footer', ['provider' => env('APP_NAME'), 'url' => url('/')]) !!}</div>
        </div>
    </body>
</html>
