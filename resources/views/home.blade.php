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
        <div class="column is-four-fifths">
            <div class="home-padding">
                <div class="home-headline">
                    <center><h1>{{ env('APP_DESCRIPTION') }}</h1></center>
                </div>

                <div class="home-infotext">
                    <h3><center>{{ __('app.home_welcomemsg') }}</center></h3>
                </div>

                @if (env('APP_SHOWSTATISTICS'))
                    <div class="home-statistics fade-in">
                        <center>
                            <div class="home-statistics-item">
                                <div class="home-statistics-item-inner">
                                    <div class="home-statistics-item-info">
                                        <h3>Workspaces</h3>
                                        <span id="count-workspaces">{{ $count_workspaces }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="home-statistics-item">
                                <div class="home-statistics-item-inner">
                                    <div class="home-statistics-item-info">
                                        <h3>Tickets</h3>
                                        <span id="count-tickets">{{ $count_tickets }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="home-statistics-item">
                                <div class="home-statistics-item-inner">
                                    <div class="home-statistics-item-info">
                                        <h3>Agents</h3>
                                        <span id="count-agents">{{ $count_agents }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="home-statistics-item">
                                <div class="home-statistics-item-inner">
                                    <div class="home-statistics-item-info">
                                        <h3>Clients</h3>
                                        <span id="count-clients">{{ $count_clients }}</span>
                                    </div>
                                </div>
                            </div>
                        </center>
                    </div>
                @endif

                <div class="home-signup">
                    <center><button type="button" class="button-signup" onclick="vue.bShowRegister = true;">{{ __('app.register') }}</button></center>
                </div>

                <div class="home-screenshot fade-in">
                    <center><img src="{{ asset('/gfx/preview.png') }}" alt="preview_screenshot"/></center>
                </div>

                <div class="home-paragraph">
                    <h3>Lightweight, effective and supportive</h3>

                    <p>
                        HelpRealm is a lightweight SaaS service support system for customers of your business. Customers can create support requests
                        via a <strong>personal workspace contact form</strong>, <strong>e-mail</strong> or via your <strong>own frontend using our 
                        <a href="{{ url('/api') }}">API</a></strong>, specifying data and attachments. For each support request there is a ticket created 
                        which is then handled by a registered agent. Tickets can be routed into different groups where initial tickets are routed to a 
                        defined index group. Superadmins can manage agents, groups, FAQ and system settings. Customers and agents get notified about ticket 
                        events by e-mail. Communication is possible via e-mail or a secret ticket thread form. The support system is especially suitable for
                        freelancers and small teams.
                    </p>
                </div>

                <div class="home-paragraph">
                    <h3>Extensive and diverse features</h3>

                    <div class="home-features">
                        <div class="home-features-block">
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Dashboard</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Support contact form</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Unlimited tickets</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Unlimited agents</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Unlimited groups</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Custom ticket types</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Different ticket statuses</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Different ticket priorities</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Groups and routing</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Agent roles</div>
                        </div>

                        <div class="home-features-block home-features-block-fix">
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Ticket creation by E-Mail</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;E-Mails on different events</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;E-Mail replies</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Ticket management</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Localization</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Personal FAQ for customers</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Gravatar support</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Security features</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Responsive layout</div>
                            <div class="home-feature-item"><i class="fas fa-star"></i>&nbsp;Ticket API (REST / Widget)</div>
                        </div>
                    </div>
                </div>

                @if ($donationCode !== null)
                    {!! $donationCode !!}
                @endif

                <div class="home-signup home-margin-top-last">
                    <center><button type="button" class="button-signup" onclick="vue.bShowRegister = true;">{{ __('app.register') }}</button></center>
                </div>
            </div>
        </div>
    </div>
@endsection
