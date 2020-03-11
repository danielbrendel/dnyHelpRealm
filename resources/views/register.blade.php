@extends('layouts.layout_home')

@section('content')
    <h1>{{ __('app.register') }}</h1>

    <br/>

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

<form method="POST" action="{{ url('/register') }}">
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
    
    <div class="field">
        <input class="button is-stretched is-info" type="submit" value="{{ __('app.register') }}" onclick="doProcessing();">
    </div>
</form>
@endsection