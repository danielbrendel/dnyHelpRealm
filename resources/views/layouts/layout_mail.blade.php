<!--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 0.1
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
-->

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">
    <head>
        <meta charset="utf-8">

        <style>
            html, body {
                width: 100%;
                height: 100%;
                margin: 0;
            }

            .mail-wrapper {
                position: relative;
                width: 280px;
                min-height: 120px;
                top: 50%;
                left: 50%;
                -webkit-transform: translate(-50%,-50%);
                transform: translate(-50%,-50%);
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

            .mail-action {
                position: relative;
                margin-top: 25px;
            }

            .mail-footer {
                position: relative;
                margin-top: 25px;
                font-size: 0.7em;
                color: rgb(150, 150, 150);
            }

            .button {
                display: block;
                min-width: 115px;
                height: 25px;
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
            <div class="mail-footer">{{ __('app.mail_footer') }}</div>
        </div>
    </body>
</html>