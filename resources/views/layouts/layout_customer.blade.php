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
    <body @if ((bool)$wsobject->usebgcolor === false) style="background-image: url('{{ asset('gfx/backgrounds/' . $bgimage->file) }}');" @else style="background-color: {{ $wsobject->bgcolorcode }}" @endif>
        <div class="guest-bg" id="ga">
            @if (isset($faqs))
                <div class="faq-bg">
                    <div class="faq">
                        <div class="faq-header">
                            <div class="faq-header-title">{{ __('app.faq') }}</div>
                            <div class="faq-header-close"><i class="fas fa-times fa-lg" title="{{ __('app.close') }}" onclick="vue.toggleFaq();"></i></div>
                        </div>

                        <div class="faq-body">
                            <div data-role="accordion" data-one-frame="true" data-show-active="true">
                                @foreach ($faqs as $faq)
                                    <div class="frame">
                                        <div class="heading">{{ $faq->question }}</div>
                                        <div class="content">
                                            <div class="p-2">{{ $faq->answer }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <br/><br/>
                        </div>
                    </div>
                </div>
            @endif

            <div class="guest-frame" id="guest-frame">
                <div class="guest-content">
                    @yield('content')
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

            <div class="modal" :class="{'is-active': bShowOpenTicket}">
                <div class="modal-background"></div>
                <div class="modal-card">
                    <header class="modal-card-head is-stretched">
                    <p class="modal-card-title">{{ __('app.open_ticket') }}</p>
                    <button class="delete" aria-label="close" onclick="vue.bShowOpenTicket = false;"></button>
                    </header>
                    <section class="modal-card-body is-stretched">
                        <div class="field">
                            <label class="label">{{ __('app.ticket_enter_hash') }}</label>
                            <div class="control">
                                <input type="text" class="input" name="hash" id="ticketid" onkeydown="if (event.keyCode === 13) { document.getElementById('btnOpenTicket').click(); }" required>
                            </div>
                        </div>
                    </section>
                    <footer class="modal-card-foot is-stretched">
                    <button id="btnOpenTicket" class="button is-success" onclick="window.open('{{ url('/' . $workspace . '/ticket/show/') }}/' + document.getElementById('ticketid').value); vue.bShowOpenTicket = false;">{{ __('app.open_ticket') }}</button>
                    <button class="button" onclick="vue.bShowOpenTicket = false;">{{ __('app.cancel') }}</button>
                    </footer>
                </div>
            </div>
        </div>

        <script>
            var vue = new Vue({
                el: '#ga',

                data: {
                    bShowRecover: false,
                    bShowLogin: false,
                    bShowOpenTicket: false,
                    bShowFileDelete: false,
                    bShowCmtEdit: false,
                    bShowFaq: false,
                },

                methods: {
                    toggleFaq: function() {
                        this.bShowFaq = !this.bShowFaq;

                        if (this.bShowFaq) {
                            document.getElementsByClassName('faq-bg')[0].style.display = 'block';
                            document.getElementsByClassName('faq')[0].style.display = 'block';
                            
                            var iconElems = document.getElementsByClassName('ticket-form-icons');
                            for (i = 0; i < iconElems.length; i++) {
                                iconElems[i].style.visibility = 'hidden';
                            }
                        } else {
                            document.getElementsByClassName('faq-bg')[0].style.display = 'none';
                            document.getElementsByClassName('faq')[0].style.display = 'none';

                            var iconElems = document.getElementsByClassName('ticket-form-icons');
                            for (i = 0; i < iconElems.length; i++) {
                                iconElems[i].style.visibility = 'inherit';
                            }
                        }
                    },

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

                    invalidTicketName: function() {
                        var elInput = document.getElementById("ticketname");
                        var elHint = document.getElementById("help-ticket-name");
                        
                        if (elInput.value.length <= 3) {
                            elInput.classList.add('is-danger');
                            elHint.classList.remove('is-hidden');
                        } else {
                            elInput.classList.remove('is-danger');
                            elHint.classList.add('is-hidden');
                        }
                    },

                    invalidTicketEmail: function() {
                        var elInput = document.getElementById("ticketemail");
                        var elHint = document.getElementById("help-ticket-email");
                        
                        if ((elInput.value.length == 0) || (elInput.value.indexOf('@') == -1) || (elInput.value.indexOf('.') == -1)) {
                            elInput.classList.add('is-danger');
                            elHint.classList.remove('is-hidden');
                        } else {
                            elInput.classList.remove('is-danger');
                            elHint.classList.add('is-hidden');
                        }
                    },

                    invalidTicketSubject: function() {
                        var elInput = document.getElementById("ticketsubject");
                        var elHint = document.getElementById("help-ticket-subject");
                        
                        if (elInput.value.length <= 4) {
                            elInput.classList.add('is-danger');
                            elHint.classList.remove('is-hidden');
                        } else {
                            elInput.classList.remove('is-danger');
                            elHint.classList.add('is-hidden');
                        }
                    },

                    invalidTicketText: function() {
                        var el = document.getElementById("tickettext");
                        
                        if (el.value.length == 0) {
                            el.classList.add('is-danger');
                        } else {
                            el.classList.remove('is-danger');
                        }
                    },

                    invalidTicketCaptcha: function() {
                        var el = document.getElementById("ticketcaptcha");
                        
                        if (isNaN(el.value)) {
                            el.classList.add('is-danger');
                        } else {
                            el.classList.remove('is-danger');
                        }
                    },
                }
            });

            @yield('javascript')
        </script>
    </body>
</html>