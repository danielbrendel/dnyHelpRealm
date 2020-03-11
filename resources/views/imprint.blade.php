<!--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 0.1
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
-->

@extends('layouts.layout_home')

@section('content')
    <div class="columns is-centered is-vcentered">
        <div class="column is-three-fifths">
            <div class="home-padding">
                <h4>{{ __('app.imprint_headline') }}</h4>
                <br/><br/>
                <div class="imprint">
                    <div class="imprint-image">
                        <img src="{{ asset('/gfx/danielbrendel.jpg') }}" alt="Daniel Brendel" width="75" height="75">
                    </div>
                    <div class="imprint-info">
                        {{ __('app.author') }}: {{ env('APP_AUTHOR') }}<br/>
                        {{ __('app.contact') }}: <a href="mailto:{{ env('APP_CONTACT') }}">{{ env('APP_CONTACT') }}</a><br/>
                        {{ __('app.development') }}: <a href="https://github.com/danielbrendel/" target="_blank">GitHub</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection