<!--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2021 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
-->

@extends('layouts.layout_agent', ['user' => $user, 'superadmin' => $superadmin])

@section('content')
    <div class="columns">
        <div class="column">
            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>{{ __('app.ticket_found') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
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
                                    @foreach ($groups as $group)
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

                <center><a href="{{ url('/ticket/search') }}" class="button">{{ __('app.go_back') }}</a></center>
                <br/>
                </div>
            </div>
        </div>
        </div>
    </div>
@endsection