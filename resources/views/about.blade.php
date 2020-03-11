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
            <h1>About</h1>

            <p>
                HelpRealm is a lightweight support system for your customers. Customers can create support requests 
                specifying text content and attachments. For each support request there is a ticket created which is then handled 
                by a registered agent. Tickets can be routed into different groups where initial tickets are routed to a defined 
                index group. Superadmins can manage agents and groups. Customers and agents get notified about ticket updates by e-mail.
                The support system is especially suitable for freelancers and small teams. This product is a full working SaaS solution.
            </p>

            <p>
                Read our <a href="{{ url('/tac') }}">Terms and Conditions</a> for legal information about this service<br/>
                Visit the <a href="{{ url('/faq') }}">FAQ</a> in order to get the most frequent questions answered<br/>
            </p>
        </div>
    </div>
@endsection