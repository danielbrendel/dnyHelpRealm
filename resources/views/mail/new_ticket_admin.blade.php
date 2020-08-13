@extends('layouts.layout_mail')

@section('title')
    {{ __('app.mail_new_ticket_admin_title') }}
@endsection

@section('body')
    {{ __('app.mail_salutation', ['name' => $name]) }}
    <br/><br/>
    {{ __('app.mail_new_ticket_admin_body') }}
    <br/><br/>
    <hr/>
    <strong>{{ $subject }}:</strong><br/>
    <pre>{{ $text }}</pre>
    <hr/>
@endsection

@section('action')
    <a class="button" href="{{ url('/' . $workspace . '/ticket/' . $ticketid . '/show') }}" target="_blank">{{ __('app.mail_new_ticket_admin_open') }}</a>
@endsection
