@extends('layouts.layout_mail')

@section('title')
    {{ __('app.mail_workspace_created_title') }}
@endsection

@section('body')
    {{ __('app.mail_salutation', ['name' => $name]) }}
    <br/><br/>
    {{ __('app.mail_workspace_created_body') }}
@endsection

@section('action')
    <a class="button" href="{{ url('/confirm?hash=' . $hash) }}">{{ __('app.mail_workspace_created_confirm') }}</a>
@endsection