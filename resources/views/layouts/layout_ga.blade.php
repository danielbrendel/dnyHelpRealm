{{--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@if (env('APP_ENV') === 'production')
    @if (env('GA_TOKEN', null) !== null)
        <script>
            var gaProperty = '{{ env('GA_TOKEN') }}';
            var disableStr = 'ga-disable-' + gaProperty;
            if (document.cookie.indexOf(disableStr + '=true') > -1) {
                window[disableStr] = true;
            }
            window.gaOptOut = function() {
                let expires = new Date(Date.now() + 1000 * 60 * 60 * 24 * 365 * 100);
                document.cookie = disableStr + '=true; expires=' + expires.toUTCString() + '; path=/';
                window[disableStr] = true;
            }
        </script>

        <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GA_TOKEN') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ env('GA_TOKEN') }}');
            gtag('config', '{{ env('GA_TOKEN') }}', { 'anonymize_ip': true });
        </script>
    @endif
@endif
