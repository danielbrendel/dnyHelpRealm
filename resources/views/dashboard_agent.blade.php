<!--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
-->

@extends('layouts.layout_agent', ['user' => $user, 'superadmin' => $superadmin])

@section('content')
    <div class="columns">
        <div class="column is-full">
            <section class="info-tiles">
                <div class="tile is-ancestor has-text-centered ">
                    <div class="tile is-parent">
                        <article class="tile is-child box has-background-lightdark-blue">
                            <p class="title">{{ __('app.welcome') }}</p>
                            <p class="subtitle">{{ __('app.welcome_message', ['name' => $agent->surname . ' ' . $agent->lastname]) }}</p>
                        </article>
                    </div>
                </div>
            </section>
            <section class="info-tiles">
                <div class="tile is-ancestor has-text-centered">
                    <div class="tile is-parent">
                        <article class="tile is-child box">
                            <p class="title">{{ $yours }}/{{ $serving }}</p>
                            <p class="subtitle">{{ __('app.your_tickets') }}</p>
                        </article>
                    </div>
                    <div class="tile is-parent">
                        <article class="tile is-child box">
                            <p class="title">{{ $serving }}</p>
                            <p class="subtitle">{{ __('app.total_tickets') }}</p>
                        </article>
                    </div>
                    <div class="tile is-parent">
                        <article class="tile is-child box">
                            <p class="title">{{ $groups }}</p>
                            <p class="subtitle">{{ __('app.total_groups') }}</p>
                        </article>
                    </div>
                    <div class="tile is-parent">
                        <article class="tile is-child box">
                            <p class="title">{{ $agents }}</p>
                            <p class="subtitle">{{ __('app.total_agents') }}</p>
                        </article>
                    </div>
                </div>
            </section>
            <section class="info-tiles">
                <div class="tile is-ancestor">
                    <div class="tile is-parent">
                        <article class="tile is-child box">
                            <h3>{{ __('app.your_tickets') }}</h3><br>

                            <table class="table striped table-border mt-4" data-role="table" data-pagination="true"
                                data-table-rows-count-title="{{ __('app.table_show_entries') }}" 
                                data-table-search-title="{{ __('app.table_search') }}" 
                                data-table-info-title="{{ __('app.table_row_info') }}"
                                data-pagination-prev-title="{{ __('app.table_pagination_prev') }}"
                                data-pagination-next-title="{{ __('app.table_pagination_next') }}"><!--bordered hovered-->
                                <thead>
                                    <tr>
                                        <th class="text-left">{{ __('app.ticket_id', ['id' => '']) }}</th>
                                        <th class="text-left">{{ __('app.ticket_subject') }}</th>
                                        <th class="text-left">{{ __('app.ticket_date') }}</th>
                                        <th class="text-left">{{ __('app.ticket_group') }}</th>
                                        <th class="text-left">{{ __('app.ticket_status') }}</th>
                                        <th class="text-left">{{ __('app.ticket_prio') }}</th>
                                    </tr>
                                </thead>
            
                                <tbody>
                                    @foreach ($tickets as $ticket)
                                        <tr>
                                            <td>
                                                #{{ $ticket->id}}
                                            </td>
                                            
                                            <td class="right">
                                                <a href="{{ url('/' . $workspace . '/ticket/' . $ticket->id . '/show') }}" title="{{ __('app.view_details') }}">{{ $ticket->subject }}</a>
                                            </td>

                                            <td>
                                                <div title="{{ $ticket->updated_at }}">{{ $ticket->updated_at->diffForHumans() }}</div>
                                            </td>
                                            
                                            <td class="right">
                                                @foreach ($groupnames as $group)
                                                    @if ($group['ticket_id'] == $ticket->id)
                                                        {{ $group['group_name'] }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            
                                            <td class="right">
                                                    @if ($ticket->status == 0)
                                                        <div class="dashboard-badge dashboard-badge-is-red">{{ __('app.ticket_status_confirmation') }}</div>
                                                    @elseif ($ticket->status == 1)
                                                        <div class="dashboard-badge dashboard-badge-is-green">{{ __('app.ticket_status_open') }}</div>
                                                    @elseif ($ticket->status == 2)
                                                        <div class="dashboard-badge dashboard-badge-is-grey">{{ __('app.ticket_status_waiting') }}</div>
                                                    @elseif ($ticket->status == 3)
                                                        <div class="dashboard-badge dashboard-badge-is-brown">{{ __('app.ticket_status_closed') }}</div>
                                                    @endif
                                                </div>
                                            </td>
                                            
                                            <td class="right">
                                                @if ($ticket->prio == 1)
                                                    {{ __('app.prio_low') }}
                                                @elseif ($ticket->prio == 2)
                                                    {{ __('app.prio_med') }}
                                                @elseif ($ticket->prio == 3)
                                                    <b>{{ __('app.prio_high') }}</b>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
            
                            <br/>
            
                                <center><a class="button" href="javascript:void(0)" onclick="location.reload();">{{ __('app.refresh') }}</a></center><br/>
                        </article>
                    </div>
                    <div class="tile is-parent">
                        <article class="tile is-child box">
                            <h3>{{ __('app.ticket_types') }}</h3><br>

                            <div id="chart-no-data">
                                {{ __('app.no_data_available') }}
                            </div>

                            <canvas id="ticketChart"></canvas>

                            
                        </article>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@section('javascript')
    @if (count($typeCounts) > 0)
        var ctx = document.getElementById('ticketChart');

        var pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                    datasets: [{
                    data: [@foreach ($typeCounts as $typeCount) {{ $typeCount['count'] . ',' }} @endforeach],
                    backgroundColor: [
                        @foreach ($typeCounts as $typeCount) {!! "'rgba(" . random_int(0, 255) . ", " . random_int(0, 255) . ", " . random_int(0, 255) . ", 0.2)'," !!} @endforeach
                ],
                borderColor: [
                    @foreach ($typeCounts as $typeCount) {!! "'rgba(200, 200, 200, 1)'," !!} @endforeach
                ],
                borderWidth: 1
                }],
            
                labels: [
                    @foreach ($typeCounts as $typeCount) {!! "'" . $typeCount['name'] . "'," !!} @endforeach
                ]}
        });
    @else
        document.getElementById('chart-no-data').style.display = 'inline-block';
    @endif
@endsection
