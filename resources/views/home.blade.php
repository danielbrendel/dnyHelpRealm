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
            </div>
        </div>
    </div>
@endsection
