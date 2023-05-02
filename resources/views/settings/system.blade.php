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
                                    <label class="label">{{ __('app.system_form_title') }}</label>
                                    <input class="input" name="formtitle" value="{{ $formtitle }}"/>
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
                                    <label class="label">{{ __('app.system_ticket_created_msg') }}</label>
                                    <textarea class="textarea" name="ticketcreatedmsg">{{ $ticketcreatedmsg }}</textarea>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <input type="checkbox" data-role="checkbox" data-style="2" data-caption="{{ __('app.ticket_allow_attachments') }}" name="allowattachments" value="1" <?php if ((bool)$allowattachments === true) { echo 'checked'; } ?>/>
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
                                    <input type="checkbox" data-role="checkbox" data-style="2" data-caption="{{ __('app.system_inform_admin_new_ticket') }}" name="inform_admin_new_ticket" value="1" <?php if ((bool)$ws->inform_admin_new_ticket === true) { echo 'checked'; } ?>/>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <input type="checkbox" data-role="checkbox" data-style="2" data-caption="{{ __('app.system_formactions') }}" name="formactions" value="1" <?php if ((bool)$formactions === true) { echo 'checked'; } ?>/>
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

                        <strong>{{ __('app.mailer_service') }}</strong><br/>

                        <form method="POST" action="{{ url('/' . $workspace . '/settings/system/mailer') }}">
                            @csrf

                            <div class="field">
                                <div class="control">
                                    <input type="checkbox" name="mailer_useown" data-role="checkbox" data-style="2" data-caption="{{ __('app.use_own_mailer') }}" value="1" @if ($ws->mailer_useown) {{ 'checked' }} @endif>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.mailer_host_smtp') }}</label>
                                <div class="control">
                                    <input type="text" name="mailer_host_smtp" value="{{ $ws->mailer_host_smtp }}"/>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.mailer_port_smtp') }}</label>
                                <div class="control">
                                    <input type="text" name="mailer_port_smtp" value="{{ $ws->mailer_port_smtp }}"/>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.mailer_host_imap') }}</label>
                                <div class="control">
                                    <input type="text" name="mailer_host_imap" value="{{ $ws->mailer_host_imap }}"/>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.mailer_port_imap') }}</label>
                                <div class="control">
                                    <input type="text" name="mailer_port_imap" value="{{ $ws->mailer_port_imap }}"/>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.mailer_inbox') }}</label>
                                <div class="control">
                                    <input type="text" name="mailer_inbox" value="{{ $ws->mailer_inbox }}"/>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.mailer_username') }}</label>
                                <div class="control">
                                    <input type="text" name="mailer_username" value="{{ $ws->mailer_username }}"/>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.mailer_password') }}</label>
                                <div class="control">
                                    <input type="password" name="mailer_password" value="{{ $ws->mailer_password }}"/>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.mailer_address') }}</label>
                                <div class="control">
                                    <input type="text" name="mailer_address" value="{{ $ws->mailer_address }}"/>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.mailer_fromname') }}</label>
                                <div class="control">
                                    <input type="text" name="mailer_fromname" value="{{ $ws->mailer_fromname }}"/>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <input type="submit" value="{{ __('app.save') }}">
                                </div>
                            </div>
                        </form>

                        <hr/>

                        @if ($ws->paidforapi)
                            <strong>{{ __('app.system_api_token') }}</strong><br/>
                            <div class="field">
                                <div class="control">
                                    <input type="text" id="apitoken" value="{{ $apitoken }}"/>
                                </div>
                            </div>

                            <input type="button" class="button" onclick="generateApiToken()" value="{{ __('app.system_api_token_generate') }}">
                        @else
                            <strong>{{ __('app.system_api_token') }}</strong><br/>

                            <a href="javascript:void(0);" onclick="vue.bShowOrderAPIAccess = true;">{{ __('app.buy_api_access_link') }}</a>
                        @endif

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

                        <div class="field">
                            <a href="javascript:void(0);" onclick="vue.bShowTicketExport = true;">{{ __('app.ticket_export') }}</a>
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

        <div class="modal" :class="{'is-active': bShowOrderAPIAccess}">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head is-stretched">
                    <p class="modal-card-title">{{ __('app.buy_api_access_title') }}</p>
                    <button class="delete" aria-label="close" onclick="vue.bShowOrderAPIAccess = false;"></button>
                </header>
                <section class="modal-card-body is-stretched">
                    <div class="field">
                        <label class="label">{!! __('app.buy_api_access_info', ['costs' => env('STRIPE_COSTS_LABEL')]) !!}</label>
                    </div>

                    <form action="{{ url('/' . $workspace . '/payment/charge') }}" method="post" id="payment-form" class="stripe">
                        @csrf

                        <div class="form-row">
                            <label for="card-element">
                                {{ __('app.credit_or_debit_card') }}
                            </label>
                            <div id="card-element"></div>

                            <div id="card-errors" role="alert"></div>
                        </div>

                        <button>{{ __('app.submit_payment') }}</button>
                    </form>
                </section>
                <footer class="modal-card-foot is-stretched">
                    <button class="button" onclick="vue.bShowOrderAPIAccess = false;">{{ __('app.close') }}</button>
                </footer>
            </div>
        </div>

        <div class="modal" :class="{'is-active': bShowTicketExport}">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head is-stretched">
                    <p class="modal-card-title">{{ __('app.ticket_export') }}</p>
                    <button class="delete" aria-label="close" onclick="vue.bShowTicketExport = false;"></button>
                </header>
                <section class="modal-card-body is-stretched">
                    <form method="POST" action="{{ url('/' . $workspace . '/system/tickets/export') }}">
                        @csrf

                        <div class="field">
                            <label class="label">{{ __('app.date_from') }}</label>
                            <div class="control">
                                <input type="date" class="input" name="date_from" value="{{ date('Y-m-d', strtotime($export_from_date)) }}">
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.date_to') }}</label>
                            <div class="control">
                                <input type="date" class="input" name="date_to" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">{{ __('app.format') }}</label>
                            <div class="control">
                                <select name="export_type">
                                    <option value="csv">CSV</option>
                                    <option value="json">JSON</option>
                                </select>
                            </div>
                        </div>

                        <br/>

                        <div class="field">
                            <div class="control">
                                <input type="submit" class="button is-success" value="{{ __('app.export') }}">
                            </div>
                        </div>
                    </form>
                </section>
                <footer class="modal-card-foot is-stretched">
                    <button class="button" onclick="vue.bShowTicketExport = false;">{{ __('app.close') }}</button>
                </footer>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    @if ($ws->paidforapi)
        function generateApiToken()
        {
            ajaxRequest('patch', '{{ url('/' . $workspace . '/settings/system/apitoken') }}', {},
                function(data){
                    document.getElementById('apitoken').value = data.token;
                },
                function(){}
            );
        }
    @endif

    document.addEventListener('DOMContentLoaded', function() {
        var stripe = Stripe('{{ env('STRIPE_TOKEN_PUBLIC') }}');
        var elements = stripe.elements();

        const style = {
            base: {
                fontSize: '16px',
                color: '#32325d',
            },
        };

        const card = elements.create('card', {style});
        card.mount('#card-element');

        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const {token, error} = await stripe.createToken(card);

            if (error) {
                const errorElement = document.getElementById('card-errors');
                errorElement.textContent = error.message;
            } else {
                stripeTokenHandler(token);
            }
        });
    });

    const stripeTokenHandler = (token) => {
        const form = document.getElementById('payment-form');
        const hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', token.id);
        form.appendChild(hiddenInput);
        form.submit();
    }
@endsection
