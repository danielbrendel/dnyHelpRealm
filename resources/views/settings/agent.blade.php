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
        <div class="column is-centered">
            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>{{ __('app.settings') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                        <form method="POST" action="{{ url('/' . $workspace . '/settings/save') }}">
                            @csrf
                            @method('PATCH')

                            <div class="field">
                                <label class="label">{{ __('app.settings_surname') }}</label>
                                <div class="control">
                                    <input type="text" name="surname" value="{{ $agent->surname }}"/>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.settings_lastname') }}</label>
                                <div class="control">
                                    <input type="text" name="lastname" value="{{ $agent->lastname }}"/>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.settings_email') }}</label>
                                <div class="control">
                                    <input type="text" name="email" value="{{ $agent->email }}"/>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <input type="checkbox" data-role="checkbox" data-style="2" data-caption="{{ __('app.settings_mailonticketingroup') }}" name="mailonticketingroup" value="1" <?php if ((bool)$agent->mailonticketingroup === true) echo 'checked'; ?>/>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <input type="checkbox" data-role="checkbox" data-style="2" data-caption="{{ __('app.settings_hideclosedtickets') }}" name="hideclosedtickets" value="1" <?php if ((bool)$agent->hideclosedtickets === true) echo 'checked'; ?>/>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.settings_signature') }}</label>
                                <div class="control">
                                    <textarea name="signature" class="textarea">{{ $agent->signature }}</textarea>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.settings_password') }}</label>
                                <div class="control">
                                    <input type="password" name="password"/>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.settings_password_confirmation') }}</label>
                                <div class="control">
                                    <input type="password" name="password_confirm"/>
                                </div>
                            </div>

                            <br/>

                            <div class="field">
                                <center><input type="submit" class="button" value="{{ __('app.save') }}"/></center>
                            </div>

                            <br/>
                        </form>

                        <br/>
                    </div>
                </div>
            </div>
        </div>

        <div class="column is-centered">
            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>{{ __('app.settings_avatar') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                        <form method="POST" action="{{ url('/' . $workspace . '/settings/avatar') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <div class="field">
                                <label class="label">{{ __('app.settings_avatar') }}</label>
                                <div class="control">
                                    <input type="file" name="avatar" data-role="file" data-button-title="{{ __('app.choose_file') }}"/>
                                </div>
                            </div>

                            <br/>

                            <div class="field">
                                <center><input type="submit" class="button" value="{{ __('app.save') }}"/></center>
                            </div>

                            <br/>
                        </form>
                    </div>
                </div>
            </div>

            <br/><br/><br/>

            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>{{ __('app.settings_language') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                        <form method="POST" action="{{ url('/' . $workspace . '/settings/locale') }}">
                            @csrf
                            @method('PATCH')

                            <div class="field">
                                <label class="label">{{ __('app.settings_language') }}</label>
                                <div class="control">
                                    <select name="lang">
                                        @foreach ($langs as $lng)
                                            <option value="{{ $lng }}" <?php if ($lng === $lang) echo 'selected'; ?>>{{ $lng }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <br/>

                            <div class="field">
                                <center><input type="submit" class="button" value="{{ __('app.save') }}"/></center>
                            </div>

                            <br/>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
