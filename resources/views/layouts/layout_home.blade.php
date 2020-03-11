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
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ env('APP_COMPANY') }} | {{ env('APP_NAME') }} - {{ env('APP_DESCRIPTION') }}</title>

        <meta name="author" content="{{ env('APP_AUTHOR') }}">
        <meta name="description" content="{{ env('APP_DESCRIPTION') }}">
        <meta name="tags" content="dnyHelpRealm, HelpRealm, ticket, ticket system, support ticket system, support, system, agent, client, helpdesk">

        <link rel="shortcut icon" href="{{ asset('gfx/logo.png') }}">
        
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bulma.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/metro-all.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">

        @if (env('APP_ENV') == 'local')
        <script src="{{ asset('js/vue.js') }}"></script>
        @elseif (env('APP_ENV') == 'production')
        <script src="{{ asset('js/vue.min.js') }}"></script>
        @endif
        <script src="https://kit.fontawesome.com/1ba6a6ae62.js"></script>
        <script src="{{ asset('js/metro.min.js') }}"></script>
        <script src="{{ asset('js/push.min.js') }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>
    </head>

    <body>
        @yield('content')
    </body>
</html>