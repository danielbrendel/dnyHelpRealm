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
        <div class="column is-three-fifths is-centered">
            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>{{ __('app.agent_create') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                        <form method="POST" action="{{ url('/' . $workspace . '/agent/create') }}">
                            @csrf 

                            <div class="field">
                                <label class="label">{{ __('app.agent_surname') }}</label>
                                <div class="control">
                                    <input class="input" type="text" name="surname" placeholder="{{ __('app.agent_surname') }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.agent_lastname') }}</label>
                                <div class="control">
                                    <input class="input" type="text" name="lastname" placeholder="{{ __('app.agent_lastname') }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.agent_email') }}</label>
                                <div class="control">
                                    <input class="input" type="text" name="email" placeholder="{{ __('app.agent_email') }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.agent_position') }}</label>
                                <div class="control">
                                    <input class="input" type="text" name="position" placeholder="{{ __('app.agent_position') }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.agent_password') }}</label>
                                <div class="control">
                                    <input class="input" type="password" name="password" placeholder="{{ __('app.agent_password') }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.agent_password_confirm') }}</label>
                                <div class="control">
                                    <input class="input" type="password" name="password_confirm" placeholder="{{ __('app.agent_password_confirm') }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.agent_superadmin') }}</label>
                                <div class="control">
                                    <input type="checkbox" data-role="checkbox" data-style="2" data-caption="{{ __('app.agent_set_superadmin') }}" name="superadmin" value="1">
                                </div>
                            </div>

                            <div class="field">
                                <center><input type="submit" class="button" value="{{ __('app.create') }}"/></center><br/>
                            </div>

                            <br/>
                        </form>
                    </div>
                </div>
            </div> 
        </div>
    </div>
@endsection