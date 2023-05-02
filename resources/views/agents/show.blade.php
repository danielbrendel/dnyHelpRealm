<!--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

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
                        <center>{{ __('app.agent_view') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                        <form method="POST" action="{{ url('/' . $workspace . '/agent/' . $agent->id . '/edit') }}">
                            @csrf 
                            @method('PATCH')

                            <div class="field">
                                <label class="label">{{ __('app.agent_surname') }}</label>
                                <div class="control">
                                    <input class="input" type="text" name="surname" value="{{ $agent->surname }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.agent_lastname') }}</label>
                                <div class="control">
                                    <input class="input" type="text" name="lastname" value="{{ $agent->lastname }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.agent_email') }}</label>
                                <div class="control">
                                    <input class="input" type="text" name="email" value="{{ $agent->email }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.agent_position') }}</label>
                                <div class="control">
                                    <input class="input" type="text" name="position" value="{{ $agent->position }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.agent_active') }}</label>
                                <div class="control">
                                    <input type="checkbox" data-role="checkbox" data-style="2" data-caption="{{ __('app.agent_set_active') }}" name="active" value="1" <?php if ($agent->active) echo 'checked'; ?>>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.agent_superadmin') }}</label>
                                <div class="control">
                                    <input type="checkbox" data-role="checkbox" data-style="2" data-caption="{{ __('app.agent_set_superadmin') }}" name="superadmin" value="1" <?php if ($agent->superadmin) echo 'checked'; ?>>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.password') }}</label>
                                <div class="control">
                                    <input class="input" type="password" name="password">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.password_confirmation') }}</label>
                                <div class="control">
                                    <input class="input" type="password" name="password_confirm">
                                </div>
                            </div>

                            <div class="field">
                                <center><input type="submit" class="button" value="{{ __('app.save') }}"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="button" class="button is-danger" value="{{ __('app.delete') }}" onclick="if (window.confirm('{{ __('app.delete_confirm') }}')) location.href='{{ url('/' . $workspace . '/agent/' . $agent->id . '/delete') }}';"/></center>
                            </div>

                            <br/>
                        </form>
                    </div>
                </div>
            </div> 
        </div>

        <div class="column">
            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>{{ __('app.agent_groups') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                    <table class="table striped table-border mt-4" data-role="table" data-pagination="true"><!--bordered hovered-->
                        <thead>
                            <tr>
                                <th class="text-left">{{ __('app.group_id') }}</th>
                                <th class="text-left">{{ __('app.group_name') }}</th>
                                <th class="text-left">{{ __('app.agent_removegroup') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($groups as $group)
                                <tr>
                                    <td>
                                        #{{ $group['data']->id }}
                                    </td>
                                    
                                    <td class="right">
                                        {{ $group['data']->name }}
                                    </td>

                                    <td class="right">
                                        <a href="{{ url('/' . $workspace . '/agent/' . $agent->id . '/group/' . $group['data']->id . '/remove') }}">{{ __('app.agent_removegroup') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div>
                        <center><a href="javascript:void(0)" class="button" onclick="vue.bAddAgentGroup = true">{{ __('app.agent_addgroup') }}</a></center>
                    </div>

                    <br/>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" :class="{'is-active': bAddAgentGroup}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
            <p class="modal-card-title">{{ __('app.agent_addgroup') }}</p>
            <button class="delete" aria-label="close" onclick="vue.bAddAgentGroup = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                <form method="POST" id="edAddAgentGroup">
                    @csrf
                    @method('PATCH')

                    <div class="field">
                        <div class="control">
                            <select name="agent" id="selGroup">
                                @foreach ($allgroups as $ag)
                                    <option value="{{ $ag->id }}">{{ $ag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </section>
            <footer class="modal-card-foot is-stretched">
            <button class="button is-success" onclick="var frm = document.getElementById('edAddAgentGroup'); frm.action = '{{ url('/' . $workspace . '/agent/' . $agent->id) }}/group/' + selGroup.value + '/add'; frm.submit();">{{ __('app.save') }}</button>
            <button class="button" onclick="vue.bAddAgentGroup = false;">{{ __('app.close') }}</button>
            </footer>
        </div>
        </div>
    </div>

    </div>
@endsection