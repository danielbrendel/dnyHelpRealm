@extends('layouts.layout_mail')

@section('title')
    {{ __('app.mail_ticket_in_group_title') }}
@endsection

@section('body')
    {{ __('app.mail_salutation', ['name' => $name]) }}
    <br/><br/>
    {{ __('app.mail_ticket_in_group_body') }}
    <br/><br/>
    <hr/>
    {{ $custname }} / {{ $email }}<br/>
    <strong>{{ $subject }}:</strong><br/>
    <pre>{{ $text }}</pre>
    <hr/>
@endsection

@section('action')
    <a class="button" href="{{ url('/' . $workspace . '/ticket/' . $ticketid . '/show') }}" target="_blank">{{ __('app.mail_ticket_in_group_open') }}</a>
@endsection
