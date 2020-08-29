<!--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
-->

@if (env('APP_ENV') === 'production')
    @if (env('GA_TOKEN', null) !== null)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GA_TOKEN') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ env('GA_TOKEN') }}');
        </script>
    @endif
@endif
