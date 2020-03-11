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
                    <center><h1>{{ __('app.faq') }}</h1></center>
                </div>

                <div data-role="accordion" data-one-frame="true" data-show-active="true">
                    <div class="frame">
                        <div class="heading">Is this service free of charge?</div>
                        <div class="content">
                            <div class="p-2">Yes, this service is free of charge. You can just sign up and start using it!</div>
                        </div>
                    </div>
                    <div class="frame">
                        <div class="heading">Are you working on this fulltime?</div>
                        <div class="content">
                            <div class="p-2">I am considering this service as a hobby.</div>
                        </div>
                    </div>
                    <div class="frame">
                        <div class="heading">What to do when I encounter problems?</div>
                        <div class="content">
                            <div class="p-2">Send me a mail (see imprint)</div>
                        </div>
                    </div>
                    <div class="frame">
                        <div class="heading">Where is this service hosted?</div>
                        <div class="content">
                            <div class="p-2">The service is hosted in germany</div>
                        </div>
                    </div>
                    <div class="frame">
                        <div class="heading">What technology stack are you using?</div>
                        <div class="content">
                            <div class="p-2">The technology stack covers PHP using Laravel, Bulma, VueJS, MetroUI and MySQL</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection