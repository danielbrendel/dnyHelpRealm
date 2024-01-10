{{--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2024 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('title', __('app.home_imprint'))

@section('content')
    <div class="columns is-centered is-vcentered">
        <div class="column is-three-fifths">
            <div class="home-padding">
                <h4>{{ __('app.imprint_headline') }}</h4>
                
                <div class="imprint">
                    <div class="imprint-image">
                        <img src="{{ asset('/gfx/imprint-logo.png') }}" alt="Logo" width="75" height="75">
                    </div>
                    <div class="imprint-info">
                        {{ __('app.author') }}: {{ env('APP_AUTHOR') }}<br/>
                        {{ __('app.contact') }}: <a href="mailto:{{ env('APP_CONTACT') }}">{{ env('APP_CONTACT') }}</a><br/>

                        @if (env('APP_SHOWDEVINFO'))
                            GitHub: <a href="https://github.com/danielbrendel/" target="_blank">https://github.com/danielbrendel/</a>
                        @endif
                    </div>
                </div>
                
                <div class="imprint-additional-content">
                    <?php echo file_get_contents(public_path() . '/data/imprint.html'); ?>
                </div>
            </div>
        </div>
    </div>
@endsection