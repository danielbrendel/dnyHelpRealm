@extends('layouts.layout_mail')

@section('title')
    {{ __('app.mail_account_created_title') }}
@endsection

@section('body')
    {{ __('app.mail_salutation', ['name' => $name]) }}
    <br/><br/>
    {{ __('app.mail_account_created_body') }}
    <br/><br/>
    <hr>
    <i>{{ $password }}</i>
    <hr>
@endsection

@section('action')
    <a class="button" href="{{ url('/' . $workspace) }}">{{ __('app.mail_open_app') }}</a>
@endsection
