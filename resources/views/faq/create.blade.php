<!--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
-->

@extends('layouts.layout_agent', ['user' => $user, 'superadmin' => $superadmin])

@section('content')
    <div class="columns">
        <div class="column">
            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>{{ __('app.faq_create') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                        <form method="POST" action="{{ url('/' . $workspace . '/faq/create') }}">
                            @csrf 

                            <div class="field">
                                <label class="label">{{ __('app.faq_question') }}</label>
                                <div class="control">
                                    <input class="input" type="text" name="question" placeholder="{{ __('app.faq_question') }}" value="{{ old('question') }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">{{ __('app.faq_answer') }}</label>
                                <div class="control">
                                    <textarea class="textarea" name="answer" placeholder="{{ __('app.faq_answer') }}">{{ old('answer') }}</textarea>
                                </div>
                            </div>

                            <div class="field">
                                <center><input type="submit" class="button" value="{{ __('app.create') }}"/></center>
                            </div>

                            <br/>
                        </form>
                    </div>
                </div>
            </div> 
        </div>
    </div>
@endsection