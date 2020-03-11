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
                <div class="home-headline">
                    <center><h1>{{ env('APP_DESCRIPTION') }}</h1></center>
                </div>

                <div class="home-infotext">
                    <h3><center>{{ __('app.home_welcomemsg') }}</center></h3>
                </div>

                <div class="home-signup">
                    <center><button type="button" class="button is-outlined is-medium" onclick="vue.bShowRegister = true;">{{ __('app.register') }}</button></center>
                </div>

                <div class="home-screenshot">
                    <center><img src="{{ asset('/gfx/preview.png') }}" alt="preview_screenshot"/></center>
                </div>
            </div>
        </div>
    </div>
@endsection