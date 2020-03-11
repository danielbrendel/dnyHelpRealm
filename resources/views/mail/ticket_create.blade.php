@extends('layouts.layout_mail')

@section('title')
    {{ __('app.mail_ticket_create_title') }}
@endsection

@section('body')
    {{ __('app.mail_salutation', ['name' => $name]) }}
    <br/><br/>
    {{ __('app.mail_ticket_create_body') }}
@endsection

@section('action')
    <a class="button" href="{{ url('/' . $workspace . '/ticket/show/' . $hash . '?confirmation=' . $confirmation) }}" target="_blank">{{ __('app.mail_ticket_create_open') }}</a>
@endsection