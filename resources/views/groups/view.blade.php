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
        <div class="column">
            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>{{ __('app.group_view') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                        <form method="POST" action="{{ url('/' . $workspace . '/group/' . $group->id . '/edit') }}">
                            @csrf
                            @method('PATCH')

                            <div class="field">
                                <label class="label">{{ __('app.name') }}</label>
                                <div class="control">
                                    <input class="input" type="text" name="name" value="{{ $group->name }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.description') }}</label>
                                <div class="control">
                                    <textarea class="textarea" name="description">{{ $group->description }}</textarea>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.default') }}</label>
                                <div class="control">
                                    <input type="checkbox" data-role="checkbox" data-caption="{{ __('app.group_set_default') }}" data-style="2" name="def" value="1" <?php if ($group->def) echo 'checked'; ?>>
                                </div>
                            </div>

                            <div class="field">
                                <center><input type="submit" class="button" value="{{ __('app.save') }}"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="button" class="button is-danger" value="{{ __('app.delete') }}" onclick="if (window.confirm('{{ __('app.delete_confirm') }}')) location.href='{{ url('/' . $workspace . '/group/' . $group->id . '/delete') }}';"/></center>
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
                        <center>{{ __('app.agents') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                    <table class="table striped table-border mt-4" data-role="table" data-pagination="true">
                    <thead>
                        <tr>
                            <th class="text-left">{{ __('app.agent_id') }}</th>
                            <th class="text-left">{{ __('app.agent_surname') }} {{ __('app.agent_lastname') }}</th>
                            <th class="text-left">{{ __('app.agent_removegroup') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($groupagents as $agent)
                            <tr>
                                <td>
                                    #{{ $agent->id }}
                                </td>
                                
                                <td class="right">
                                    <a href="{{ url('/' . $workspace . '/agent/' . $agent->id . '/show') }}" title="{{ __('app.view_details') }}">{{ $agent->surname }} {{ $agent->lastname }}</a>
                                </td>
                                
                                <td>
                                    <a href="{{ url('/' . $workspace . '/agent/' . $agent->id . '/group/' . $group->id . '/remove') }}">{{ __('app.agent_removegroup') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <br/>

                    <center><a class="button" href="javascript:void(0)" onclick="location.reload();">{{ __('app.refresh') }}</a>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a class="button" href="javascript:void(0)" onclick="vue.bAddAgentToGroup = true;">{{ __('app.agent_addgroup') }}</a></center><br/>
                </div>
            </div>
            </div>
        </div>

        <div class="modal" :class="{'is-active': bAddAgentToGroup}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
            <p class="modal-card-title">{{ __('app.agent_addgroup') }}</p>
            <button class="delete" aria-label="close" onclick="vue.bAddAgentToGroup = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                <form method="POST" id="edAddAgentGroup">
                    @csrf
                    @method('PATCH')

                    <div class="field">
                        <div class="control">
                            <select name="agent" id="selGroup">
                                @foreach ($allagents as $ag)
                                    <option value="{{ $ag->id }}">{{ $ag->surname }} {{ $ag->lastname }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </section>
            <footer class="modal-card-foot is-stretched">
            <button class="button is-success" onclick="var frm = document.getElementById('edAddAgentGroup'); frm.action = '{{ url('/' . $workspace . '/agent/') }}/' + selGroup.value + '/group/{{ $group->id }}/add'; frm.submit();">{{ __('app.save') }}</button>
            <button class="button" onclick="vue.bAddAgentToGroup = false;">{{ __('app.close') }}</button>
            </footer>
        </div>
        </div>

    </div>
@endsection