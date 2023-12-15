{{--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">
    <head>
        @include('layouts/layout_ga')

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ env('APP_NAME') }} - {{ env('APP_DESCRIPTION') }}</title>

        <meta name="author" content="{{ env('APP_AUTHOR') }}">
        <meta name="description" content="{{ env('APP_METADESC') }}">
        <meta name="keywords" content="{{ env('APP_METATAGS') }}">

        <link rel="icon" type="image/png" href="{{ asset('gfx/logo.png') }}">

        <link rel="stylesheet" type="text/css" href="{{ asset('css/bulma.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/metro-all.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">

        @if (env('APP_ENV') == 'local')
            <script src="{{ asset('js/vue.js') }}"></script>
        @elseif (env('APP_ENV') == 'production')
            <script src="{{ asset('js/vue.min.js') }}"></script>
        @endif
        <script src="{{ asset('js/fontawesome.js') }}"></script>
        <script src="{{ asset('js/metro.min.js') }}"></script>
        <script src="{{ asset('js/push.min.js') }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>
    </head>

    <body class="clep-outer" style="background-image: url('{{ asset('gfx/home_bg.jpg') }}');">
        <div class="cookie-consent-bottombox-outer" id="cookie-consent">
            <div class="cookie-consent-bottombox-inner">
                <div class="cookie-consent-text">
                    {!! __('app.cookie_consent') !!}

                    @if (env('GA_TOKEN') !== null)
                        <br/>

                        {!! __('app.cookie_tracking') !!}
                    @endif
                </div>

                <div class="cookie-consent-button">
                    <button type="button" onclick="vue.clickedCookieConsentButton()">{{ __('app.ok') }}</button>
                </div>
            </div>
        </div>

        <div id="clep">
            <div class="clep-content">
                @if ($errors->any())
                    <div id="error-message-1">
                        <article class="message is-danger">
                            <div class="message-header">
                                <p>{{ __('app.error') }}</p>
                                <button class="delete" aria-label="delete" onclick="document.getElementById('error-message-1').style.display = 'none';"></button>
                            </div>
                            <div class="message-body">
                                @foreach ($errors->all() as $error)
                                    {{ $error }}<br/>
                                @endforeach
                            </div>
                        </article>
                    </div>
                    <br/>
                @endif

                @if (Session::has('error'))
                    <div id="error-message-2">
                        <article class="message is-danger">
                            <div class="message-header">
                                <p>{{ __('app.error') }}</p>
                                <button class="delete" aria-label="delete" onclick="document.getElementById('error-message-2').style.display = 'none';"></button>
                            </div>
                            <div class="message-body">
                                {{ Session::get('error') }}
                            </div>
                        </article>
                    </div>
                    <br/>
                @endif

                @if (Session::has('success'))
                    <div id="success-message">
                        <article class="message is-success">
                            <div class="message-header">
                                <p>{{ __('app.success') }}</p>
                                <button class="delete" aria-label="delete" onclick="document.getElementById('success-message').style.display = 'none';"></button>
                            </div>
                            <div class="message-body">
                                {{ Session::get('success') }}
                            </div>
                        </article>
                    </div>
                    <br/>
                @endif

                @yield('content')
            </div>

            <div class="modal" :class="{'is-active': bShowRegister}">
                <div class="modal-background"></div>
                <div class="modal-card">
                    <header class="modal-card-head is-stretched">
                        <p class="modal-card-title">{{ __('app.register') }}</p>
                        <button class="delete" aria-label="close" onclick="vue.bShowRegister = false;"></button>
                    </header>
                    <section class="modal-card-body is-stretched">
                        <form id="regform" method="POST" action="{{ url('/register') }}">
                            @csrf

                            <div class="field">
                                <label class="label">{{ __('app.register_company') }}</label>
                                <div class="control">
                                    <input class="input" type="text" name="company" required>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.register_name') }}</label>
                                <div class="control">
                                    <input class="input" type="text" name="fullname" required>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.register_email') }}</label>
                                <div class="control">
                                    <input class="input" type="email" name="email" required>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.register_password') }}</label>
                                <div class="control">
                                    <input class="input" type="password" name="password" required>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.register_password_confirmation') }}</label>
                                <div class="control">
                                    <input class="input" type="password" name="password_confirmation" required>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Captcha: {{ $captchadata[0] }} + {{ $captchadata[1] }} = ?</label>
                                <div class="control">
                                    <input class="input" type="text" name="captcha" required>
                                </div>
                            </div>

                            <div class="field">
                                {!! __('app.register_agreement', ['tac' => url('/tac')]) !!}
                            </div>
                        </form>
                    </section>
                    <footer class="modal-card-foot is-stretched">
                            <span>
                                <button class="button is-success" onclick="document.getElementById('regform').submit();">{{ __('app.register') }}</button>
                            </span>
                    </footer>
                </div>
            </div>

            <div class="modal" :class="{'is-active': bShowRecover}">
                <div class="modal-background"></div>
                <div class="modal-card">
                    <header class="modal-card-head is-stretched">
                        <p class="modal-card-title">{{ __('app.recover_password') }}</p>
                        <button class="delete" aria-label="close" onclick="vue.bShowRecover = false;"></button>
                    </header>
                    <section class="modal-card-body is-stretched">
                        <form method="POST" action="/recover" id="formResetPw">
                            @csrf

                            <div class="field">
                                <label class="label">{{ __('app.email') }}</label>
                                <div class="control">
                                    <input type="email" onkeyup="javascript:invalidRecoverEmail()" onchange="javascript:invalidRecoverEmail()" onkeydown="if (event.keyCode === 13) { document.getElementById('formResetPw').submit(); }" class="input" name="email" id="recoveremail" required>
                                </div>
                            </div>

                            <input type="submit" id="recoverpwsubmit" class="is-hidden">
                        </form>
                    </section>
                    <footer class="modal-card-foot is-stretched">
                        <button class="button is-success" onclick="document.getElementById('recoverpwsubmit').click();">{{ __('app.recover_password') }}</button>
                        <button class="button" onclick="vue.bShowRecover = false;">{{ __('app.cancel') }}</button>
                    </footer>
                </div>
            </div>
        </div>
    </body>

    <script>
        var vue = new Vue({
            el: '#clep',

            data: {
                bShowRecover: false,
                bShowRegister: false,
            },

            methods: {
                invalidLoginEmail: function() {
                    var el = document.getElementById("loginemail");

                    if ((el.value.length == 0) || (el.value.indexOf('@') == -1) || (el.value.indexOf('.') == -1)) {
                        el.classList.add('is-danger');
                    } else {
                        el.classList.remove('is-danger');
                    }
                },

                invalidRecoverEmail: function() {
                    var el = document.getElementById("recoveremail");

                    if ((el.value.length == 0) || (el.value.indexOf('@') == -1) || (el.value.indexOf('.') == -1)) {
                        el.classList.add('is-danger');
                    } else {
                        el.classList.remove('is-danger');
                    }
                },

                invalidLoginPassword: function() {
                    var el = document.getElementById("loginpw");

                    if (el.value.length == 0) {
                        el.classList.add('is-danger');
                    } else {
                        el.classList.remove('is-danger');
                    }
                },

                setclepFlag: function() {
                    let futureDate = new Date(Date.now() + 1000 * 60 * 60 * 24 * 365);
                    document.cookie = 'clep=1; expires=' + futureDate.toUTCString() + '; path=/;';
                },

                clickedCookieConsentButton: function() {
                    //Client clicked on Ok-button so set cookie to not show consent anymore

                    let futureDate = new Date(Date.now() + 1000 * 60 * 60 * 24 * 365);
                    document.cookie = 'cookieconsent=1; expires=' + futureDate.toUTCString() + ';';

                    document.getElementById('cookie-consent').style.display = 'none';
                },

                handleCookieConsent: function() {
                    //Show cookie consent if not already for this client

                    var cookies = document.cookie.split(';');
                    var foundCookie = false;
                    for (i = 0; i < cookies.length; i++) {
                        if (cookies[i].indexOf('cookieconsent') !== -1) {
                            foundCookie = true;
                            break;
                        }
                    }

                    if (foundCookie === false) {
                        document.getElementById('cookie-consent').style.display = 'unset';
                    }
                },
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            vue.setclepFlag();
            vue.handleCookieConsent();
        });
    </script>
</html>
