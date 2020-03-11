<!--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 0.1
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
-->

@extends('layouts.layout_agent', ['user' => $user, 'superadmin' => $superadmin])

@section('content')
    <div class="columns">
        <div class="column is-one-third is-centered">
            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>{{ __('app.search') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                        <form method="POST" action="{{ url('/' . $workspace . '/ticket/search') }}">
                            @csrf 

                            <div class="field">
                                <label class="label">{{ __('app.search_query') }}</label>
                                <div class="control">
                                    <input class="input" type="text" name="query" placeholder="{{ __('app.search_query') }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.search_type') }}</label>
                                <div class="is-stretched">
                                    <select name="type">
                                        <option value="1">{{ __('app.search_by_id') }}</option>
                                        <option value="2">{{ __('app.search_by_hash') }}</option>
                                        <option value="3">{{ __('app.search_by_subject') }}</option>
                                        <option value="4">{{ __('app.search_by_text') }}</option>
                                    </select>
                                </div>
                            </div>

                            <center><input type="submit" class="button" value="{{ __('app.search_perform') }}"></center><br/>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection