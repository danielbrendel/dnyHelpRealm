@extends('layouts.layout_mail')

@section('title')
    {{ __('app.mail_ticket_reply_agent_title') }}
@endsection

@section('body')
    <strong><i>{{ __('app.mail_reply_info') }}</i></strong>
    <br/><br/>
    {{ __('app.mail_salutation', ['name' => $name]) }}
    <br/><br/>
    {{ __('app.mail_ticket_reply_agent_body') }}
    <br/><br/>
    <hr/>
    <strong>{{ $agent }}:</strong><br/>
    <pre>{{ $message }}</pre>
    <hr/>
    <br/>
@endsection

@section('action')
    <a class="button" href="{{ url('/' . $workspace . '/ticket/show/' . $hash) }}" target="_blank">{{ __('app.mail_ticket_reply_agent_open') }}</a>
@endsection