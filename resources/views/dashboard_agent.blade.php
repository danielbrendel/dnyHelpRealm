{{--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_agent', ['user' => $user, 'superadmin' => $superadmin])

@section('content')
    <div class="columns">
        <div class="column is-full">
            <section class="info-tiles">
                <div class="tile is-ancestor has-text-centered ">
                    <div class="tile is-parent tile-margin-bottom">
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
                            <p class="title" id="stats-yours">{{ $yours }}/{{ $serving }}</p>
                            <p class="subtitle">{{ __('app.your_tickets') }}</p>
                        </article>
                    </div>
                    <div class="tile is-parent">
                        <article class="tile is-child box">
                            <p class="title" id="stats-serving">{{ $serving }}</p>
                            <p class="subtitle">{{ __('app.total_tickets') }}</p>
                        </article>
                    </div>
                    <div class="tile is-parent">
                        <article class="tile is-child box">
                            <p class="title" id="stats-groups">{{ $groups }}</p>
                            <p class="subtitle">{{ __('app.total_groups') }}</p>
                        </article>
                    </div>
                    <div class="tile is-parent tile-margin-bottom">
                        <article class="tile is-child box">
                            <p class="title" id="stats-agents">{{ $agents }}</p>
                            <p class="subtitle">{{ __('app.total_agents') }}</p>
                        </article>
                    </div>
                </div>
            </section>
            <section class="info-tiles">
                <div class="tile is-ancestor">
                    <div class="tile is-parent">
                        <article class="tile is-child box">
                            <h3>{{ __('app.your_tickets') }}</h3><br/>

                            <table class="table striped table-border mt-4" data-role="table" data-pagination="true"
                                data-table-rows-count-title="{{ __('app.table_show_entries') }}"
                                data-table-search-title="{{ __('app.table_search') }}"
                                data-table-info-title="{{ __('app.table_row_info') }}"
                                data-pagination-prev-title="{{ __('app.table_pagination_prev') }}"
                                data-pagination-next-title="{{ __('app.table_pagination_next') }}">
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
                            <h3>{{ __('app.ticket_types') }}</h3>

                            <br>

                            <div id="chart-no-data">
                                {{ __('app.no_data_available') }}
                            </div>

                            <canvas id="ticketChart"></canvas>

                            <br/>

                            <h3>{{ __('app.ticket_stats') }}</h3>

                            <br>

                            <canvas id="statsChart"></canvas>
                        </article>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@section('javascript')
    @if (count($typeCounts) > 0)
        let ctx = document.getElementById('ticketChart');
        let stx = document.getElementById('statsChart');

        let pieChart = new Chart(ctx, {
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

        let labels = [];
        let data_total = [];
        let remote_data = JSON.parse(`{!! json_encode($stats->toArray()) !!}`);

        let day = 60 * 60 * 24 * 1000;
        let dt = new Date(Date.parse('{{ $stats_start }}'));

        for (let i = 0; i <= {{ $stats_diff }}; i++) {
            let curDate = new Date(dt.getTime() + day * i);
            let curDay = curDate.getDate();
            let curMonth = curDate.getMonth() + 1;

            if (curDay < 10) {
                curDay = '0' + curDay;
            }

            if (curMonth < 10) {
                curMonth = '0' + curMonth;
            }

            labels.push(curDate.getFullYear() + '-' + curMonth + '-' + curDay);
            data_total.push(0);
        }

        remote_data.forEach(function(elem, index) {
            labels.forEach(function(lblElem, lblIndex){
                if (lblElem == elem.created_at) {
                    data_total[lblIndex] = parseInt(elem.count);
                }
            });
        });

        const statscfg = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: '{{ __('app.tickets') }}',
                        backgroundColor: 'rgb(52, 145, 220)',
                        borderColor: 'rgb(52, 145, 220)',
                        data: data_total,
                        barThickness: 15,
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {if (value % 1 === 0) {return value;}}
                        }
                    }
                }
            }
        };
        
        let statsChart = new Chart(
            stx,
            statscfg
        );
    @else
        document.getElementById('chart-no-data').style.display = 'inline-block';
    @endif

    function fetchStatistics()
    {
        ajaxRequest('get', '{{ url('/clep/statistics') }}', {},
            function(data){
                if (data.code === 200) {
                    document.getElementById('stats-yours').innerHTML = data.data.yours + ' / ' + data.data.serving;
                    document.getElementById('stats-serving').innerHTML = data.data.serving;
                    document.getElementById('stats-agents').innerHTML = data.data.agents;
                    document.getElementById('stats-groups').innerHTML = data.data.groups;
                }
            },
            function(){},
            true
        );

        setTimeout('fetchStatistics()', 1000 * 60);
    }

    fetchStatistics();
@endsection
