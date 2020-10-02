@extends('layouts.layout_mail')

@section('title')
    {{ __('app.mail_mailservice_timeout_title', ['company' => $company]) }}
@endsection

@section('body')
    {{ __('app.mail_salutation', ['name' => $name]) }}
    <br/><br/>
    {{ __('app.mail_mailservice_timeout_body', ['hostname' => $hostname, 'count' => $count]) }}
    <br/><br/>
@endsection

