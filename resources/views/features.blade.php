{{--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('content')
    <div class="columns is-centered is-vcentered">
        <div class="column is-three-fifths">
            <div class="home-padding">
                <div class="home-headline">
                    <center><h1>{{ __('app.features') }}</h1></center>
                </div>

                <p>{{ env('APP_NAME') }} supports the most important features a support ticket system should provide.
                Visit our <a href="{{ url('/data/documentation.pdf') }}">documentation</a> in order to get detailed information.
				The overall features are as follows:</p>

                <ul>
                    <li>Personal support contact form</li>
                    <li>Unlimited tickets</li>
                    <li>Unlimited agents</li>
                    <li>Unlimited groups</li>
                    <li>Different ticket types</li>
                    <li>Different ticket statuses</li>
                    <li>Different ticket priorities</li>
                    <li>Route tickets through groups</li>
                    <li>Different agent roles (superadmin and agent)</li>
                    <li>Customize ticket contact form</li>
                    <li>Ticket creation by E-Mail</li>
                    <li>E-Mails on different events</li>
                    <li>E-Mail replies</li>
                    <li>Ticket comments, attachments and notes</li>
                    <li>Multilanguage ready</li>
                    <li>Personal FAQ for customers</li>
                    <li>Gravatar support</li>
                    <li>Security (protection against XSS, CSRF, SQL injection, spam)</li>
					<li>Responsive layout</li>
                    <li>Ticket creation API</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
