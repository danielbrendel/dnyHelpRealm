{{--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2024 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('title', __('app.faq'))

@section('content')
    <div class="columns is-centered is-vcentered">
        <div class="column is-three-fifths">
            <div class="home-padding">
                <div class="home-headline">
                    <center><h1>{{ __('app.faq') }}</h1></center>
                </div>

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
            </div>
        </div>
    </div>
@endsection