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
    <div class="column">
        <div class="column is-three-fifths is-centered" style="top: -48px;">
            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>{{ __('app.ticket_create') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                        <form method="POST" action="{{ url('/' . $workspace . '/ticket/create/agent') }}" enctype="multipart/form-data">
                            @csrf 

                            <br/>

                            @if ($errors->any())
                            <div class="field" id="error-message-1">
                                <article class="message is-danger">
                                    <div class="message-header">
                                        <p>{{ __('error') }}</p>
                                        <button type="button" class="delete" aria-label="delete" onclick="document.getElementById('error-message-1').style.display = 'none';"></button>
                                    </div>
                                    <div class="message-body">
                                        @foreach ($errors->all() as $error)
                                            {{ $error }}<br/>
                                        @endforeach
                                    </div>
                                </article>
                            </div>
                            <br/>
                            @endif

                            @if (Session::has('error'))
                            <div class="field" id="error-message-2">
                                <article class="message is-danger">
                                    <div class="message-header">
                                        <p>{{ __('error') }}</p>
                                        <button type="button" class="delete" aria-label="delete" onclick="document.getElementById('error-message-2').style.display = 'none';"></button>
                                    </div>
                                    <div class="message-body">
                                        {{ Session::get('error') }}
                                    </div>
                                </article>
                            </div>
                            <br/>
                            @endif

                            <div class="field">
                                <label class="label">{{ __('app.name') }}</label>
                                <div class="control">
                                    <input class="input" type="text" name="name" placeholder="{{ __('app.name') }}" value="{{ old('name') }}" required>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.email') }}</label>
                                <div class="control">
                                    <input class="input" type="email" name="email" placeholder="{{ __('app.email') }}" value="{{ old('email') }}" required>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.subject') }}</label>
                                <div class="control">
                                    <input class="input" type="text" name="subject" placeholder="{{ __('app.subject') }}" value="{{ old('subject') }}" required>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.text') }}</label>
                                <div class="control">
                                    <textarea class="textarea" name="text" placeholder="{{ __('app.text') }}" required>{{ old('text') }}</textarea>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.type') }}</label>
                                <div class="is-stretched">
                                    <select class="is-stretched" name="type">
                                        <option value="1">{{ __('app.ticket_type_service_request') }}</option>
                                        <option value="2">{{ __('app.ticket_type_incident') }}</option>
                                        <option value="3">{{ __('app.ticket_type_change') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.priority') }}</label>
                                <div class="is-stretched">
                                    <select class="is-stretched" name="prio">
                                        <option value="1">{{ __('app.prio_low') }}</option>
                                        <option value="2">{{ __('app.prio_med') }}</option>
                                        <option value="3">{{ __('app.prio_high') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.group') }}</label>
                                <div class="is-stretched">
                                    <select class="is-stretched" name="group">
                                        @foreach ($groups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.assignee') }}</label>
                                <div class="is-stretched">
                                    <select class="is-stretched" name="assignee">
                                        <option value="0" selected>-</option>
                                        @foreach ($agents as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->surname . ' ' . $agent->lastname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <br/>

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