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
        <div class="column is-centered">
            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>{{ __('app.system_settings') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                        <strong>{{ __('app.workspace_link') }}</strong><a href="{{ url('/' . $workspace . '?v=c') }}" class="is-wordbreak" target="_blank">{{ url('/' . $workspace) }}</a>
                        <br/><br/>

                        <span><i class="far fa-file-pdf"></i> <a href="{{ url('/data/documentation.pdf') }}" target="_blank"><strong>{{ __('app.documentation_view') }}</strong></a></span>
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
                                    <br/>
                                    <label class="label">{{ __('app.system_info_message') }}</label>
                                    <small>{{ env('APP_ALLOWEDHTMLTAGS') }}</small>
                                    <textarea class="textarea" name="infomessage">{{ $infomessage }}</textarea>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <label class="label">{{ __('app.system_extfilter') }}</label>
                                    <input type="text" class="input" name="extfilter" value="{{ $extfilter }}" placeholder="ext1 ext2 ext3"/>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <input type="checkbox" data-role="checkbox" data-style="2" data-caption="{{ __('app.system_emailconfirm') }}" name="emailconfirm" value="1" <?php if ((bool)$emailconfirm === true) { echo 'checked'; } ?>/>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <input type="checkbox" data-role="checkbox" data-style="2" data-caption="{{ __('app.system_onlycustom') }}" name="onlycustom" value="1" <?php if ((bool)$onlycustom === true) { echo 'checked'; } ?>/>
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

                        <strong>{{ __('app.ticket_types') }}</strong><br/><br/>

                        <table class="table striped table-border mt-4" data-role="table" data-pagination="true"><!--bordered hovered-->
                            <thead>
                                <tr>
                                    <th class="text-left">{{ __('app.ticket_type_id') }}</th>
                                    <th class="text-left">{{ __('app.ticket_type_name') }}</th>
                                    <th class="text-left">{{ __('app.ticket_type_created') }}</th>
                                    <th class="text-left">{{ __('app.ticket_type_edit') }}</th>
                                    <th class="text-left">{{ __('app.ticket_type_remove') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($ticketTypes as $ticketType)
                                    <tr>
                                        <td>
                                            #{{ $ticketType->id }}
                                        </td>

                                        <td class="right">
                                            {{ $ticketType->name }}
                                        </td>

                                        <td>
                                            <div title="{{ $ticketType->created_at }}">{{ $ticketType->created_at->diffForHumans() }}</div>
                                        </td>

                                        <td class="right">
                                            <a href="javascript:void(0);" onclick="vue.editTicketType('{{ $workspace }}', {{ $ticketType->id }});">{{ __('app.ticket_type_edit') }}</a>
                                        </td>

                                        <td class="right">
                                            <a href="{{ url('/' . $workspace . '/tickettype/' . $ticketType->id . '/delete') }}">{{ __('app.ticket_type_remove') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <a href="javascript:void(0);" onclick="vue.addTicketType('{{ $workspace }}');">{{ __('app.ticket_type_create') }}</a>

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
