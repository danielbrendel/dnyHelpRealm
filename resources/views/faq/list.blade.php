{{--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2024 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_agent', ['user' => $user, 'superadmin' => $superadmin])

@section('content')
    <div class="columns">
        <div class="column">
            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>{{ __('app.faq') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                    <table class="table striped table-border mt-4" data-role="table" data-pagination="true"
                        data-table-rows-count-title="{{ __('app.table_show_entries') }}" 
                        data-table-search-title="{{ __('app.table_search') }}" 
                        data-table-info-title="{{ __('app.table_row_info') }}"
                        data-pagination-prev-title="{{ __('app.table_pagination_prev') }}"
                        data-pagination-next-title="{{ __('app.table_pagination_next') }}">
                    <thead>
                        <tr>
                            <th class="text-left">{{ __('app.faq_id') }}</th>
                            <th class="text-left">{{ __('app.faq_question') }}</th>
                            <th class="text-left">{{ __('app.faq_answer') }}</th>
                            <th class="text-left">{{ __('app.faq_last_updated') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($faqs as $faq)
                            <tr>
                                <td>
                                    #{{ $faq->id }}
                                </td>
                                
                                <td class="right">
                                    <a href="{{ url('/' . $workspace . '/faq/' . $faq->id . '/edit') }}" title="{{ __('app.view_details') }}">{{ $faq->question }}</a>
                                </td>
                                
                                <td>
                                    <?php
                                        if (strlen($faq->answer) > 20) {
                                            echo substr($faq->answer, 0, 20) . '...';
                                        } else {
                                            echo $faq->answer;
                                        }
                                    ?>
                                </td>

                                <td><div title="{{ $faq->updated_at }}">{{ $faq->updated_at->diffForHumans() }}</div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <br/>

                    <center><a class="button" href="javascript:void(0)" onclick="location.reload();">{{ __('app.refresh') }}</a>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a class="button is-success" href="{{ url('/'. $workspace . '/faq/create') }}">{{ __('app.create') }}</a></center><br/>
                </div>
            </div>
            </div>
        </div>
    </div>
@endsection