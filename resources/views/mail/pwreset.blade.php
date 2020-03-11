@extends('layouts.layout_mail')

@section('title')
    {{ __('app.mail_password_reset_title') }}
@endsection

@section('body')
    {{ __('app.mail_salutation', ['name' => $name]) }}
    <br/><br/>
    {{ __('app.mail_password_reset_body') }}
@endsection

@section('action')
    <a class="button" href="{{ url('/reset?hash=' . $hash) }}">{{ __('app.mail_password_reset') }}</a>
@endsection