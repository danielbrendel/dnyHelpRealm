@extends('layouts.layout_mail')

@section('title')
    {{ __('app.mail_ticket_assign_title') }}
@endsection

@section('body')
    {{ __('app.mail_salutation', ['name' => $name]) }}
    <br/><br/>
    {{ __('app.mail_ticket_assign_body') }}
@endsection

@section('action')
    <a class="button" href="{{ url('/' . $workspace . '/ticket/' . $id . '/show') }}" target="_blank">{{ __('app.mail_ticket_assign_open') }}</a>
@endsection