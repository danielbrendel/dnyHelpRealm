<!--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
-->

@extends('layouts.layout_clep')

@section('content')
    <div>
        <h1 class="clep-headline">
            <?php
                $first = substr(env('APP_NAME'), 0, 4);
                $second = substr(env('APP_NAME'), 4);
            ?>
            <center>{{ $first }}<strong>{{ $second }}</strong></center>
        </h1>

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

            <div>
                <span>
                    <button class="button is-success" onclick="document.getElementById('loginform').submit();">{{ __('app.login') }}</button>
                </span>

                <span class="is-right clep-recover-top">
                    <div class="recover-pw">
                        <center><a href="javascript:void(0)" onclick="vue.bShowRecover = true;">{{ __('app.recover_password') }}</a></center>
                    </div>
                </span>
            </div>

            <div class="clep-border clep-signup">
                <center><a href="javascript:void(0)" onclick="vue.bShowRegister = true;">{{ __('app.register') }}</a></center>
            </div>
        </form>
    </div>
@endsection
