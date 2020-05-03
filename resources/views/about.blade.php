<!--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
-->

@extends('layouts.layout_home')

@section('content')
    <div class="columns is-centered is-vcentered">
        <div class="column is-three-fifths">
            <div class="home-padding">
                <h1>About</h1>

                <p>
                    HelpRealm is a lightweight SaaS service support system for customers of your business. Customers can create support requests
                    via a <strong>personal workspace contact form</strong> or via your <strong>own frontend using our <a href="{{ url('/api') }}">API</a></strong>,
                    specifying data and attachments. For each support request there is a ticket created which is then handled by a registered
                    agent. Tickets can be routed into different groups where initial tickets are routed to a defined index group. Superadmins
                    can manage agents, groups, FAQ and system settings. Customers and agents get notified about ticket events by e-mail.
                    Communication is possible via e-mail or a secret ticket thread form. The support system is especially suitable for
                    freelancers and small teams.
                </p>

                <p>
                    Read our <a href="{{ url('/tac') }}">Terms and Conditions</a> for legal information about this service<br/>
                    Visit the <a href="{{ url('/faq') }}">FAQ</a> in order to get the most frequent questions answered<br/>
                    Read the <a href="{{ url('/data/documentation.pdf') }}">documentation</a> in order to get to know how to use the service<br/>
                </p>
            </div>
        </div>
    </div>
@endsection
