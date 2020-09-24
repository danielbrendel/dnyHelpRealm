<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ env('APP_NAME') }} | Error 404</title>

        <meta name="author" content="{{ env('APP_AUTHOR') }}">
        <meta name="description" content="{{ env('APP_DESCRIPTION') }}">
        <meta name="tags" content="dnyHelpRealm, HelpRealm, ticket, ticket system, support ticket system, support, system, agent, client, helpdesk">

        <link rel="shortcut icon" href="{{ asset('gfx/logo.png') }}">

        <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bulma.css') }}">
    </head>
    <body>
        <div class="error-404">
            <article class="message is-warning">
                <div class="message-header">
                    <p>Error 404</p>
                </div>
                <div class="message-body">
                    {{ __('app.error_404') }}<br/><br/>
                    <a href="{{ url('/') }}">Home</a>
                </div>
            </article>
        </div>
    </body>
</html>
