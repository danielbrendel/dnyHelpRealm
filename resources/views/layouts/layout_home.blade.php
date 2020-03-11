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
        @if (env('APP_ENV') === 'production')
            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=UA-160248599-1"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', 'UA-160248599-1');
            </script>
        @endif

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ env('APP_NAME') }} - {{ env('APP_DESCRIPTION') }}</title>

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

    <body style="background-image: url('{{ asset('/gfx/home_bg.jpg') }}'); overflow-y: auto;">
        <div id="home">
            <nav class="navbar" role="navigation" aria-label="main navigation">
                <div class="navbar-brand">
                <a class="navbar-item" href="{{ url('/') }}">
                    <?php
                        $first = substr(env('APP_NAME'), 0, 4);
                        $second = substr(env('APP_NAME'), 4);
                    ?>
                    {{ $first }}<strong>{{ $second }}</strong>
                </a>
            
                <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navMainMenu">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
                </div>
            
                <div id="navMainMenu" class="navbar-menu">
                <div class="navbar-start">
                    <a class="navbar-item" href="{{ url('/news') }}">
                        {{ __('app.home_news') }}
                    </a>

                    <a class="navbar-item" href="{{ url('/about') }}">
                        {{ __('app.home_about') }}
                    </a>

                    <a class="navbar-item" href="{{ url('/faq') }}">
                        {{ __('app.home_faq') }}
                    </a>
            
                    <a class="navbar-item" href="{{ asset('/data/documentation.pdf') }}">
                        {{ __('app.home_doc') }}
                    </a>
            
                    <a class="navbar-item" href="{{ url('/imprint') }}">
                        {{ __('app.home_imprint') }}
                    </a>

                    <a class="navbar-item" href="{{ url('/tac') }}">
                        {{ __('app.home_tac') }}
                    </a>
                </div>
            
                <div class="navbar-end">
                    <div class="navbar-item">
                    <div class="buttons">
                        <a class="button is-primary is-bold" href="javascript:void(0);" onclick="vue.bShowRegister = true;">
                            {{ __('app.register') }}
                        </a>
                        <a class="button is-light" href="javascript:void(0);" onclick="vue.bShowLogin = true;">
                            {{ __('app.login') }}
                        </a>
                    </div>
                    </div>
                </div>
                </div>
            </nav>

            <div class="home-content">
                <div class="container">
                    @if ($errors->any())
                        <div id="error-message-1">
                            <article class="message is-danger">
                            <div class="message-header">
                                <p>{{ __('error') }}</p>
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
                                <p>{{ __('error') }}</p>
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
                                <p>{{ __('success') }}</p>
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

                    <div class="cookie-consent-outer">
                        <div id="cookie-consent" class="cookie-consent-inner">
                            <div class="cookie-consent-text">
                                {{ __('app.cookie_consent') }}
                            </div>
        
                            <div class="cookie-consent-button">
                                <button type="button" onclick="vue.clickedCookieConsentButton()">{{ __('app.ok') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
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
                                    {{ __('app.register_agreement') }}
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

            <div class="modal" :class="{'is-active': bShowLogin}">
                <div class="modal-background"></div>
                <div class="modal-card">
                    <header class="modal-card-head is-stretched">
                        <p class="modal-card-title">{{ __('app.login') }}</p>
                        <button class="delete" aria-label="close" onclick="vue.bShowLogin = false;"></button>
                        </header>
                        <section class="modal-card-body is-stretched">
                            <div>
                                <form id="loginform" method="POST" action="{{ url('/login') }}">
                                    @csrf 
                        
                                    <div class="field">
                                        <label class="label">{{ __('app.email') }}</label>
                                        <p class="control has-icons-left has-icons-right">
                                            <input class="input" onkeyup="javascript:vue.invalidLoginEmail()" onchange="javascript:vue.invalidLoginEmail()" onkeydown="if (event.keyCode === 13) { document.getElementById('loginform').submit(); }" type="email" name="email" id="loginemail" placeholder="{{ __('app.enteremail') }}" required>
                                            <span class="icon is-small is-left">
                                            <i class="fas fa-envelope"></i>
                                            </span>
                                        </p>
                                    </div>
                                        
                                    <div class="field">
                                        <label class="label">{{ __('app.password') }}</label>
                                        <p class="control has-icons-left">
                                            <input class="input" onkeyup="javascript:vue.invalidLoginPassword()" onchange="javascript:vue.invalidLoginPassword()" onkeydown="if (event.keyCode === 13) { document.getElementById('loginform').submit(); }" type="password" name="password" id="loginpw" placeholder="{{ __('app.enterpassword') }}" required>
                                            <span class="icon is-small is-left">
                                            <i class="fas fa-lock"></i>
                                            </span>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </section>
                        <footer class="modal-card-foot is-stretched">
                        <span>
                            <button class="button is-success" onclick="document.getElementById('loginform').submit();">{{ __('app.login') }}</button>
                        </span>
                        <span class="is-right">
                            <div class="recover-pw">
                                <center><a href="javascript:void(0)" onclick="vue.bShowRecover = true; vue.bShowLogin = false;">{{ __('app.recover_password') }}</a></center>
                            </div>
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

        <div class="home-copyright"><center>Copyright &copy; 2019 - {{ date('Y') }} by Daniel Brendel</center></div>
    </body>

    <script>
var vue = new Vue({
                el: '#home',

                data: {
                    bShowRecover: false,
                    bShowLogin: false,
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

                    handleCookieConsent: function() {
						//Show cookie consent if not already for this client

						var cookies = document.cookie.split(';');
						var foundCookie = false;
						for (i = 0; i < cookies.length; i++) {
							if (cookies[i].indexOf('cookieconsent') === 1) {
                                foundCookie = true;
								break;
							}
						}

						if (foundCookie === false) {
							document.getElementById('cookie-consent').style.display = 'inline-block';
						}
					},

                    clickedCookieConsentButton: function() {
						//Client clicked on Ok-button so set cookie to not show consent anymore

						var curDate = new Date(Date.now() + 1000 * 60 * 60 * 24 * 365);
						document.cookie = 'cookieconsent=' + ((this.bShowDocLinks) ? '1' : '0') + '; expires=' + curDate.toUTCString() + ';';

						document.getElementById('cookie-consent').style.display = 'none';
					}
                }
            });

            @yield('javascript')

        document.addEventListener('DOMContentLoaded', () => {

        const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

        if ($navbarBurgers.length > 0) {

        $navbarBurgers.forEach( el => {
            el.addEventListener('click', () => {

            const target = el.dataset.target;
            const $target = document.getElementById(target);

            el.classList.toggle('is-active');
            $target.classList.toggle('is-active');

            });
        });
        }

        vue.handleCookieConsent();

        });
    </script>
</html>