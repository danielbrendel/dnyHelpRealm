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
        <div class="column">
            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>{{ __('app.group_create') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                        <form method="POST" action="{{ url('/' . $workspace . '/group/create') }}">
                            @csrf 

                            <div class="field">
                                <label class="label">{{ __('app.name') }}</label>
                                <div class="control">
                                    <input class="input" type="text" name="name" placeholder="{{ __('app.name') }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.description') }}</label>
                                <div class="control">
                                    <textarea class="textarea" name="description" placeholder="{{ __('app.description') }}"></textarea>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.default') }}</label>
                                <div class="control">
                                    <input type="checkbox" name="def" value="1"> {{ __('app.group_set_default') }}
                                </div>
                            </div>

                            <div class="field">
                                <center><input type="submit" class="button" value="{{ __('app.create') }}"/></center>
                            </div>

                            <br/>
                        </form>
                    </div>
                </div>
            </div> 
        </div>
    </div>
@endsection