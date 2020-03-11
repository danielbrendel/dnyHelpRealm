<!--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 0.1
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
-->

@extends('layouts.layout_customer', ['wsobject' => $wsobject, 'bgimage' => $bgimage, 'captchadata' => $captchadata])

@section('content')
            @if ($errors->any())
                <div id="error-message-1">
                    <article class="message is-danger">
                    <div class="message-header">
                        <p>{{ __('error') }}</p>
                        <button class="delete" aria-label="delete" onclick="document.getElementById('error-message-1').style.display = 'none';"></button>
                    </div>
                    <div class="message-body">
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br/>
                        @endforeach
                    </div>
                </article>
                </div>
            @endif

            @if (Session::has('error'))
                <div id="error-message-2">
                    <article class="message is-danger">
                    <div class="message-header">
                        <p>{{ __('error') }}</p>
                        <button class="delete" aria-label="delete" onclick="document.getElementById('error-message-2').style.display = 'none';"></button>
                    </div>
                    <div class="message-body">
                        {{ Session::get('error') }}
                    </div>
                </article>
                </div>
            @endif

            @if (Session::has('success'))
                <div id="success-message">
                    <article class="message is-success">
                    <div class="message-header">
                        <p>{{ __('success') }}</p>
                        <button class="delete" aria-label="delete" onclick="document.getElementById('success-message').style.display = 'none';"></button>
                    </div>
                    <div class="message-body">
                        {{ Session::get('success') }}
                    </div>
                </article>
                </div>
            @endif

    <div class="ticket_guest_show_header">
        <div class="ticket_guest_show_header-title">{{ $ticket->subject }}</div>
        <div class="ticket_guest_show_header-date is-right" title="{{ $ticket->updated_at }}">{{ $ticket->updated_at->diffForHumans() }} <i title="{{ __('app.refresh') }}" class="fas fa-sync-alt" style="cursor: pointer" onclick="location.reload();"></i></div>
    </div>

    <div class="ticket-guest-uniquelink">
        {{ __('app.ticket_unique_link') }} <a class="is-breakall" href="{{ url('/' . $workspace . '/ticket/show/' . $ticket->hash) }}">{{ url('/' . $workspace . '/ticket/show/' . $ticket->hash) }}</a>
    </div>

    <div class="ticket-guest-show-status">
        @if ($ticket->status == 0)
            <div class="dashboard-badge dashboard-badge-is-red">{{ __('app.ticket_status_confirmation') }}</div>
        @elseif ($ticket->status == 1)
            <div class="dashboard-badge dashboard-badge-is-green">{{ __('app.ticket_status_open') }}</div>
        @elseif ($ticket->status == 2)
            <div class="dashboard-badge dashboard-badge-is-grey">{{ __('app.ticket_status_waiting') }}</div>
        @elseif ($ticket->status == 3)
            <div class="dashboard-badge dashboard-badge-is-brown">{{ __('app.ticket_status_closed') }}</div>
        @endif
    </div>

    <div class="ticket-guest-text">
        <pre>{{ $ticket->text }}</pre>
    </div>

    <div class="attachments">
        @foreach ($files as $file)
        <div class="attachments-entry">
            <div class="attachments-icon">
                <i class="fas fa-paperclip"></i>
            </div>

            <div class="attachments-link">
                <a class="is-breakall" href="{{ url('/' . $workspace . '/ticket/' . $ticket->hash . '/file/' . $file['item']->id . '/get') }}" title="{{ $file['item']->file }}"><?php if (strlen($file['item']->file) > 15) { echo substr($file['item']->file, 0, 15) . '...'; } else { echo $file['item']->file;} ?></a>
            </div>

            <div class="attachments-info">
                {{ $file['size'] / 1000}}kb &#9679; {{ $file['ext'] }}
            </div>

            <div class="attachments-delete">
                <i class="fas fa-trash-alt" onclick="vue.currentDeleteFile = '{{ url('/' . $workspace . '/ticket/' . $ticket->hash . '/file/' . $file['item']->id . '/delete') }}'; vue.bShowFileDelete = true;"></i>
            </div>
        </div>
    @endforeach

    <div class="attachments-add">
        <form method="POST" action="{{ url('/' . $workspace . '/ticket/' . $ticket->hash . '/file/add') }}" enctype="multipart/form-data">
            @csrf

            <div class="attachments-add-file">
                <input type="file" name="file" data-role="file" data-button-title="{{ __('app.choose_file') }}">
            </div>

            <input type="hidden" name="captcha" id="attachment-captcha" value="">

            <div class="attachments-add-button">
                <input type="submit" class="button" value="{{ __('app.upload_file') }}" <?php if ($isclosed === true) { echo 'title="' . __('app.ticket_closed') . '" disabled'; } ?>>
            </div>
        </form>
    </div>
    </div>

    <div class="form-wrapper">
            <div class="threadinput">
                <form method="POST" action="{{ url('/' . $workspace . '/ticket/' . $ticket->id . '/comment/add/guest') }}">
                    @csrf

                    <div class="threadinput-text">
                        <textarea class="textarea" name="text" placeholder="{{ __('app.input_your_text') }}" onkeyup="document.getElementById('rc').innerHTML = 4096 - this.value.length;">{{ old('text') }}</textarea>
                    </div>

                    <div class="field" style="top: 5px;">
                        <label class="label">Captcha: {{ $captchadata[0] }} + {{ $captchadata[1] }} = ?</label>
                        <div class="control">
                            <input class="input" onkeyup="vue.invalidTicketCaptcha(); document.getElementById('attachment-captcha').value = this.value;" onchange="vue.invalidTicketCaptcha(); document.getElementById('attachment-captcha').value = this.value;" name="captcha" id="ticketcaptcha" placeholder="{{ $captchadata[0] }} + {{ $captchadata[1] }} = ?" required>
                        </div>
                    </div>

                    <div class="threadinput-button">
                        <input type="submit" class="button is-stretched" value="{{ __('app.post_thread') }}" <?php if ($isclosed === true) { echo 'title="' . __('app.ticket_closed') . '" disabled'; } ?>>
                    </div>

                    <div class="threadinput-remainingchars" id="rc">
                        4096
                    </div>

                    <br/>
                </form>
            </div>

            <br/>

            <div class="thread-content">

            @if (count($thread) > 0)
                @foreach ($thread as $entry)
                    <div class="thread">
                        <div class="thread-header">
                            @foreach ($threaddata as $td)
                                @if ($td['thread_id'] == $entry->id)
                                    <div class="thread-header-avatar" style="background-image: url({{ $td['avatar'] }});"></div>
                                    <div class="thread-header-wrapper">
                                        <div class="thread-header-poster">
                                            {{ $td['name'] }}
                                        </div>
                                        <div class="thread-header-date" title="{{ $entry->created_at }}">
                                            {{ $entry->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="thread-body">
                            <div class="thread-body-text" id="thread-body-text-{{ $entry->id }}">
                                <pre class="is-wordbreak">{{ $entry->text }}</pre>
                            </div>

                            <textarea id="edit-text-{{ $entry->id }}" class="is-hidden">{{ $entry->text }}</textarea>
                        </div>

                        <div class="thread-footer">
                            @if ($entry->user_id === 0)
                                <div class="thread-footer-edit">
                                    <a href="javascript:void(0)" onclick="document.getElementById('edCmtText').value = document.getElementById('edit-text-{{ $entry->id }}').value; document.getElementById('edCmtForm').action = '/{{ $workspace }}/ticket/{{ $ticket->id }}/comment/{{ $entry->id }}/edit/customer'; vue.bShowCmtEdit = true;">{{ __('app.edit_thread_entry') }}</a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="ticket-guest-no-comments">{{ __('app.no_comments_yet') }}</div>
            @endif
            </div>

    </div>

    <div class="modal" :class="{'is-active': bShowCmtEdit}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
            <p class="modal-card-title">{{ __('app.edit_comment') }}</p>
            <button class="delete" aria-label="close" onclick="vue.bShowCmtEdit = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                <form method="POST" id="edCmtForm">
                    @csrf
                    @method('PATCH')
    
                    <textarea name="text" id="edCmtText"></textarea>
                </form>
            </section>
            <footer class="modal-card-foot is-stretched">
            <button class="button is-success" onclick="document.getElementById('edCmtForm').submit();">{{ __('app.save') }}</button>
            <button class="button" onclick="vue.bShowCmtEdit = false;">{{ __('app.cancel') }}</button>
            </footer>
        </div>
    </div>

    <div class="modal" :class="{'is-active': bShowFileDelete}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
            <p class="modal-card-title">{{ __('app.ticket_delete_file') }}</p>
            <button class="delete" aria-label="close" onclick="vue.bShowFileDelete = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                <form method="POST" id="edDeleteFile">
                    @csrf
                    @method('DELETE')
    
                    <div class="field">
                        <div class="control">
                            <label class="label">{{ __('app.ticket_confirm_delete') }}</label>
                        </div>
                    </div>
                </form>
            </section>
            <footer class="modal-card-foot is-stretched">
            <button class="button is-success" onclick="var frm = document.getElementById('edDeleteFile'); frm.action = vue.currentDeleteFile; frm.submit();">{{ __('app.confirm') }}</button>
            <button class="button" onclick="vue.bShowFileDelete = false;">{{ __('app.close') }}</button>
            </footer>
        </div>
        </div>
@endsection
