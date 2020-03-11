<!--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 0.1
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
-->

@extends('layouts.layout_customer', ['bgimage' => $bgimage])

@section('content')
    <div class="app-install" id="app-install">
        <div class="header-image">
            <img src="{{ asset('gfx/header.png') }}" title="{{ env('APP_NAME') }}" alt="Header"/>
        </div>

        <div>
            {{ __('app.install_description') }}<br/><br/>
        </div>

        <div class="install-form">
            <form method="POST" action="{{ url('/install') }}">
                @csrf 

                <div class="field">
                    <label class="label">{{ __('app.install_database') }}</label>
                    <div class="control">
                        <input class="input" type="text" name="database" value="pqs" required>
                    </div>
                </div>
                    
                <div class="field">
                    <label class="label">{{ __('app.install_dbuser') }}</label>
                    <div class="control">
                        <input class="input" type="text" name="dbuser" value="root" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">{{ __('app.install_dbpw') }}</label>
                    <div class="control">
                        <input class="input" type="password" name="dbpw">
                    </div>
                </div>

                <div class="field">
                    <label class="label">{{ __('app.install_dbhost') }}</label>
                    <div class="control">
                        <input class="input" type="text" name="dbhost" value="127.0.0.1" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">{{ __('app.install_dbport') }}</label>
                    <div class="control">
                        <input class="input" type="text" name="dbport" value="3306" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">{{ __('app.install_lang') }}</label>
                    <div class="control">
                        <select name="lang">
                            @foreach ($langs as $lng)
                                <option value="{{ $lng }}">{{ $lng }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field">
                    <input class="button is-stretched is-info" type="submit" value="{{ __('app.install_submit') }}" onclick="doProcessing();">
                </div>

                <div class="field" id="processing"></div>
            </form>

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
                @endif
        </div>
    </div>
@endsection

@section('javascript')
    var gf = document.getElementById('guest-frame');
    gf.classList.add('app-install-resolution');
    gf.style.overflowY = 'auto';

    var processing = "Processing";
    var curDot = "";

    function doProcessing()
    {
        curDot += ".";
        if (curDot === "....") {
            curDot = ".";
        }

        document.getElementById("processing").innerHTML = "<center>" + processing + curDot + "</center>";

        setTimeout(function(){doProcessing();}, 350);
    }
@endsection