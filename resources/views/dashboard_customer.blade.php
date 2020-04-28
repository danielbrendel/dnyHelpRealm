<!--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
-->

@extends('layouts.layout_customer', ['wsobject' => $wsobject, 'bgimage' => $bgimage, 'captchadata' => $captchadata])

@section('content')
    <center><h1 class="ticket-headline">{{ __('app.ticket_create') }}</h1></center>

    <div class="ticket-welcome-msg">{!! $infomessage !!}</div>

    @if ($errors->any())
        <div id="error-message-1">
            <article class="message is-danger">
            <div class="message-header">
                <p>{{ __('app.error') }}</p>
                <button class="delete" aria-label="delete" onclick="document.getElementById('error-message-1').style.display = 'none';"></button>
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
        <div id="error-message-2">
            <article class="message is-danger">
            <div class="message-header">
                <p>{{ __('app.error') }}</p>
                <button class="delete" aria-label="delete" onclick="document.getElementById('error-message-2').style.display = 'none';"></button>
            </div>
            <div class="message-body">
                {{ Session::get('error') }}
            </div>
        </article>
        </div>
        <br/>
    @endif

    @if (Session::has('success'))
        <div id="success-message">
            <article class="message is-success">
            <div class="message-header">
                <p>{{ __('app.success') }}</p>
                <button class="delete" aria-label="delete" onclick="document.getElementById('success-message').style.display = 'none';"></button>
            </div>
            <div class="message-body">
                {{ Session::get('success') }}
            </div>
        </article>
        </div>
        <br/>
    @endif

    <div class="form-wrapper">
        <form method="POST" action="{{ url('/' . $workspace . '/ticket/create') }}" id="formCreateTicket">
            @csrf

            <div class="ticketform-element-half">
                <p id="help-ticket-name" class="help is-danger is-hidden">{{ __('app.ticket_hint_name') }}</p>
                <div class="control has-icons-left">
                    <input type="text" onkeyup="javascript:vue.invalidTicketName()" onchange="javascript:vue.invalidTicketName()" class="input" name="name" id="ticketname" placeholder="{{ __('app.name') }}" value="{{ old('name') }}" required>
                    <span class="icon is-small is-left ticket-form-icons">
                    <i class="fas fa-user"></i>
                    </span>
                </div>
            </div>

            <div class="ticketform-element-half">
                <p id="help-ticket-email" class="help is-danger is-hidden">{{ __('app.ticket_hint_email') }}</p>
                <div class="control has-icons-left">
                    <input type="email" onkeyup="javascript:vue.invalidTicketEmail()" onchange="javascript:vue.invalidTicketEmail()" class="input" name="email" id="ticketemail" placeholder="{{ __('app.email') }}" value="{{ old('email') }}" required>
                    <span class="icon is-small is-left ticket-form-icons">
                    <i class="fas fa-envelope"></i>
                    </span>
                </div>
            </div>

            <div class="ticketform-element-half">
                <div class="is-stretched">
                    <select class="select is-stretched" name="type">
                        @foreach ($ticketTypes as $ticketType)
                            <option value="{{ $ticketType->id }}">{{ __('app.type') }} - {{ $ticketType->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="ticketform-element-half">
                <div class="is-stretched">
                    <select class="select is-stretched" name="prio">
                        <option value="1">{{ __('app.priority') }} - {{ __('app.prio_low') }}</option>
                        <option value="2">{{ __('app.priority') }} - {{ __('app.prio_med') }}</option>
                        <option value="3">{{ __('app.priority') }} - {{ __('app.prio_high') }}</option>
                    </select>
                </div>
            </div>

            <div class="ticketform-element-full">
                <div class="control">
                    <input class="input" onkeyup="javascript:vue.invalidTicketSubject()" onchange="javascript:vue.invalidTicketSubject()" type="text" name="subject" id="ticketsubject" placeholder="{{ __('app.subject') }}" value="{{ old('subject') }}" required>
                </div>
                <p id="help-ticket-subject" class="help is-danger is-hidden">{{ __('app.ticket_hint_subject') }}</p>
            </div>

            <div class="ticketform-element-full">
                <div class="control">
                    <textarea class="textarea" name="text" id="tickettext" placeholder="{{ __('app.text') }}" required>{{ old('text') }}</textarea>
                </div>
            </div>

            <div class="ticketform-element-full">
                <label class="label">Captcha: {{ $captchadata[0] }} + {{ $captchadata[1] }} = ?</label>
                <div class="control">
                    <input class="input" onkeyup="vue.invalidTicketCaptcha()" onchange="javascript:vue.invalidTicketCaptcha()" name="captcha" id="ticketcaptcha" placeholder="{{ $captchadata[0] }} + {{ $captchadata[1] }} = ?" required>
                </div>
            </div>

            <div class="ticketform-element-full" style="color: rgb(150, 150, 150)">
                {{ __('app.files_upload_afterwards') }}
            </div>

            <span>
                <input type="submit" id="createticketsubmit" class="button" value="{{ __('app.create') }}">&nbsp;&nbsp;

                @if ((isset($faqs)) && (count($faqs) > 0))
                    <i class="far fa-question-circle faq-icon" title="{{ __('app.faq_customer') }}" onclick="vue.toggleFaq();"></i>
                @endif
            </span>

            <span class="is-right">
                <a class="is-green" href="javascript:void(0)" onclick="vue.bShowOpenTicket = true;">{{ __('app.open_ticket') }}</a> | <a href="javascript:void(0)" onclick="vue.bShowLogin = true;">{{ __('app.login_as_agent') }}</a>
            </span>
        </form>
    </div>
@endsection
