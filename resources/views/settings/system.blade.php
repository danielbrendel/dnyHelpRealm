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
        <div class="column is-centered">
            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>{{ __('app.system_settings') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                        <strong>{{ __('app.workspace_link') }}</strong><a href="{{ url('/' . $workspace . '/index') }}" target="_blank">{{ url('/' . $workspace . '/index') }}</a>
                        <br/><br/>

                        <form method="POST" action="{{ url('/' . $workspace . '/settings/system') }}">
                            @csrf
                            @method('PATCH')

                            <div class="field">
                                <label class="label">{{ __('app.system_company') }}</label>
                                <div class="control">
                                    <input type="text" name="company" value="{{ $company }}"/>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.system_lang') }}</label>
                                <div class="control">
                                    <select name="lang">
                                        @foreach ($langs as $lng)
                                            <option value="{{ $lng }}" <?php if ($lng === $lang) echo 'selected'; ?>>{{ $lng }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <label class="label">{{ __('app.system_info_message') }}</label>
                                    <textarea class="textarea" name="infomessage">{{ $infomessage }}</textarea>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <input type="checkbox" data-role="checkbox" data-style="2" data-caption="{{ __('app.system_usebgcolor') }}" name="usebgcolor" value="1" <?php if ((bool)$usebgcolor === true) { echo 'checked'; } ?>/>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <label class="label">{{ __('app.system_colorcode') }}</label>
                                    <input type="color" name="bgcolorcode" value="{{ $bgcolorcode }}"/>
                                </div>
                            </div>

                            <br/>

                            <div class="field">
                                <center><input type="submit" class="button" value="{{ __('app.save') }}"/></center>
                            </div>

                            <br/>
                        </form>

                        <hr/>

                        <div class="field">
                            <div class="control">
                                <label class="label">{{ __('app.system_backgrounds') }}</label>
                                @foreach ($bgs as $bg)
                                    <span class="settings-image-item">
                                        <div class="settings-image">
                                            <img src="{{ asset('/gfx/backgrounds/' . $bg->file)}}" width="200" height="150" alt="{{ $bg->file }}" title="{{ $bg->file }}">
                                        </div>
                                        <div class="settings-image-info">
                                            {{ substr($bg->file, 0, 15) . ((strlen($bg->file) > 15) ? '...' : '') }}&nbsp;<i class="fas fa-trash-alt" title="{{ __('app.delete') }}" onclick="if (confirm('{{ __('app.confirm_delete') }}')) { location.href = '{{ url('/' . $workspace . '/settings/system/backgrounds/delete/' . $bg->file) }}' };"></i>
                                        </div>
                                    </span>
                                @endforeach

                                <br/>
    
                                <form method="POST" action="{{ url('/' . $workspace . '/settings/system/backgrounds/add') }}" enctype="multipart/form-data">
                                    @csrf

                                    <div class="attachments-add-file">
                                        <input type="file" name="image" data-role="file" data-button-title="{{ __('app.choose_file') }}">
                                    </div>

                                    <div class="attachments-add-button">
                                        <input type="submit" class="button" value="{{ __('app.upload_file') }}"/>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <hr/>

                        <form id="frmcancel" method="POST" action="{{ url('/' . $workspace . '/settings/system/cancel') }}">
                            @csrf

                            <label class="label">{{ __('app.workspace_cancel') }}</label>

                            <div class="field">
                                <div class="control">
                                <label class="label">Captcha: {{ $captchadata[0] }} + {{ $captchadata[1] }} = ?</label>
                                    <input type="text" name="captcha" placeholder="{{ $captchadata[0] }} + {{ $captchadata[1] }} = ?" required>
                                </div>
                            </div>

                            <div>
                                <button type="button" class="button is-danger" onclick="if (confirm('{{ __('app.workspace_cancel_confirm') }}')) { document.getElementById('frmcancel').submit(); }">{{ __('app.workspace_cancel_btn') }}</button>
                            </div>
                        </form>

                        <br/>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection