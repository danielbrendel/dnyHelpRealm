<!--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
-->

@extends('layouts.layout_customer', ['bgimage' => $bgimage])

@section('content')
    <div class="header-image">
        <img src="{{ asset('gfx/header.png') }}" title="{{ env('APP_NAME') }}" alt="Header"/>
    </div>

    <div class="form-wrapper">
        <form method="POST" action="{{ url('/reset?hash=' . $hash) }}">
            @csrf 

            <div class="field">
                <label class="label">{{ __('app.password') }}</label>
                <p class="control has-icons-left">
                    <input class="input" type="password" name="password" placeholder="{{ __('app.enterpassword') }}">
                    <span class="icon is-small is-left">
                    <i class="fas fa-lock"></i>
                    </span>
                </p>
            </div>

            <div class="field">
                <label class="label">{{ __('app.password_confirmation') }}</label>
                <p class="control has-icons-left">
                    <input class="input" type="password" name="password_confirm" placeholder="{{ __('app.enterpassword') }}">
                    <span class="icon is-small is-left">
                    <i class="fas fa-lock"></i>
                    </span>
                </p>
            </div>

            <div class="field">
                <input class="button is-stretched is-info" type="submit" value="{{ __('app.reset') }}">
            </div>
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
@endsection