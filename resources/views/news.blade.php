{{--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('title', __('app.home_news'))

@section('content')
    <div class="columns is-centered is-vcentered">
        <div class="column is-three-fifths">
            <div class="home-padding">
                <h1>{{ __('app.home_news') }}</h1>
                <br/><br/>

                <a class="twitter-timeline" href="{{ env('TWITTER_LINK') }}">Tweets by {{ env('TWITTER_IDENT') }}</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script> 
            </div>
        </div>
    </div>
@endsection