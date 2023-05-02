{{--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_agent', ['user' => $user, 'superadmin' => $superadmin])

@section('content')
    <div class="columns">
        <div class="column">
            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>#{{ $ticket->id }}: {{ $ticket->subject }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                        <div class="ticket-agent-menu">
                            <div class="ticket-agent-menu-item">
                                <a href="javascript:void(0)" onclick="vue.bShowAssignAgent = true;"><i class="fas fa-bolt" title="{{ __('app.ticket_assign_agent') }}"></i></a>
                            </div>

                            <div class="ticket-agent-menu-item">
                                <a href="javascript:void(0)" onclick="vue.bShowAssignGroup = true;"><i class="fas fa-user-friends" title="{{ __('app.ticket_assign_group') }}"></i></a>
                            </div>

                            <div class="ticket-agent-menu-item">
                                <a href="javascript:void(0)" onclick="vue.bShowChangeStatus = true;"><i class="fab fa-affiliatetheme" title="{{ __('app.ticket_change_status') }}"></i></a>
                            </div>

                            <div class="ticket-agent-menu-item">
                                <a href="javascript:void(0)" onclick="vue.bShowChangePrio = true;"><i class="fas fa-database" title="{{ __('app.ticket_change_prio') }}"></i></a>
                            </div>

                            <div class="ticket-agent-menu-item">
                                <a href="javascript:void(0)" onclick="vue.bShowChangeType = true;"><i class="fas fa-i-cursor" title="{{ __('app.ticket_change_type') }}"></i></a>
                            </div>

                            <div class="ticket-agent-menu-item @if ($ticket->status !== 3) is-hidden @endif" id="delete-ticket-link">
                                <a href="javascript:void(0);" onclick="if (confirm('{{ __('app.delete_confirm') }}')) location.href = '{{ url('/' . $workspace . '/ticket/' . $ticket->id . '/delete') }}';"><i class="fas fa-trash-alt" title="{{ __('app.delete') }}"></i></a>
                            </div>

                            <div class="ticket-agent-menu-item is-right2" style="color: rgb(150, 150, 150);" title="{{ $ticket->created_at }}">
                                <i title="{{ __('app.created_at', ['date' => $ticket->created_at]) }}" class="far fa-calendar-alt"></i>&nbsp;{{ $ticket->created_at->diffForHumans() }}
                            </div>
                        </div>

                        <div class="ticket-agent-text">
                            <pre class="is-wordbreak">{{ $ticket->text }}</pre>
                        </div>

                        <div class="dashboard-card">
                            <div class="left">
                                <div id="view-status">
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
                                <p>
                                    <a href="javascript:void(0);" onclick="alert('{{ $ticket->hash }}');">{{ __('app.ticket_hash') }}</a>
                                </p>
                                <p id="view-type">
                                    {{ $ticketType->name }}
                                </p>
                                <p>
                                    <a href="mailto:{{ $ticket->email }}">{{ __('app.ticket_created_for', ['clientname' => $ticket->name]) }}</a><br/>{{ $ticket->email }}
                                </p>
                            </div>

                            <div class="right2">
                                <p>&nbsp;</p>
                                <p id="view-prio">@if ($ticket->prio == 1)
                                        {{ __('app.prio_low') }}
                                    @elseif ($ticket->prio == 2)
                                        {{ __('app.prio_med') }}
                                    @elseif ($ticket->prio == 3)
                                        <b>{{ __('app.prio_high') }}</b>
                                    @endif
                                </p>
                                <p id="view-group">{{ $group }}</p>
                                <p id="view-agent">{{ __('app.ticket_assignee') }}: @if ($agent != null) {{ $agent->surname . ' ' . $agent->lastname }} @else {{ ' - ' }} @endif</p>
                            </div>
                        </div>

                        <br/>

                        @if ($allowattachments)
                        <div class="window-field">
                            <div class="window-field-inner">
                                <div class="window-field-headline">{{ __('app.attachments') }}</div>

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

                                        <div class="attachments-add-button">
                                            <input type="submit" class="button" value="{{ __('app.upload_file') }}">
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <br/>
                        </div>
                        @endif

                        <div class="window-field">
                            <div class="window-field-inner">
                                <div class="window-field-headline">{{ __('app.notes') }}</div>

                                <form method="POST" action="{{ url('/' . $workspace . '/ticket/' . $ticket->id . '/notes/save') }}">
                                    @csrf
                                    @method('PATCH')

                                    <div class="field">
                                        <center><textarea name="notes" id="notes" class="textarea" style="width: 90%;">{{ $ticket->notes }}</textarea></center>
                                    </div>

                                    <div class="field">
                                        <center><input type="button" class="button" onclick="ajaxSaveNotes()" value="{{ __('app.save') }}"/></center>
                                    </div>

                                    <br/>
                                </form>
                            </div>
                        </div>

                        <br/>
                    </div>
                </div>
            </div>
        </div>

        <div class="column">
            <div class="window-item">
                <div class="window-item-header">
                    <div class="window-item-header-body">
                        <center>{{ __('app.ticket_thread') }}</center>
                    </div>
                </div>

                <div class="window-item-content">
                    <div class="window-item-content-body">
                        <div class="threadinput">
                            <form method="POST" action="{{ url('/' . $workspace . '/ticket/' . $ticket->id . '/comment/add') }}">
                                @csrf

                                <div class="threadinput-text">
                                    <textarea class="textarea" name="text" placeholder="{{ __('app.input_your_text') }}" onkeyup="document.getElementById('rc').innerHTML = 4096 - this.value.length;"></textarea>
                                </div>

                                <div class="threadinput-button">
                                    <input type="submit" class="button is-stretched" value="{{ __('app.post_thread') }}">
                                </div>

                                <div class="threadinput-remainingchars" id="rc">
                                    4096
                                </div>

                                <br/>
                            </form>
                        </div>

                        @if (count($thread) > 0)
                            @foreach ($thread as $entry)
                                <div class="thread">
                                    <div class="thread-header">
                                        @foreach ($threaddata as $td)
                                            @if ($td['thread_id'] == $entry->id)
                                                <div class="thread-header-avatar" style="background-image: url({{$td['avatar']}});"></div>

                                                <div class="thread-header-wrapper">
                                                    <div class="thread-header-poster">
                                                        <a name="thread-post-{{ $entry->id }}">{{ $td['name'] }}</a>
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
                                        @if ($entry->user_id == $user->id)
                                            <div class="thread-footer-edit">
                                                <a href="javascript:void(0)" onclick="document.getElementById('edCmtText').value = document.getElementById('edit-text-{{ $entry->id }}').value; document.getElementById('edCmtForm').action = '/{{ $workspace }}/ticket/{{ $ticket->id }}/comment/{{ $entry->id }}/edit'; vue.bShowCmtEdit = true;">{{ __('app.edit_thread_entry') }}</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="no-comments-yet">{{ __('app.no_comments_yet') }}</div>
                        @endif

                        <br/>
                    </div>
                </div>
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

        <div class="modal" :class="{'is-active': bShowAssignAgent}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
            <p class="modal-card-title">{{ __('app.ticket_assign_agent') }}</p>
            <button class="delete" aria-label="close" onclick="vue.bShowAssignAgent = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                <form method="POST" id="edAssignAgentForm">
                    @csrf
                    @method('PATCH')

                    <div class="field">
                        <div class="control">
                            <select name="agent" id="selAgent">
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}" <?php if ($agent->user_id == $user->id) echo 'selected'; ?>>{{ $agent->surname . ' ' . $agent->lastname }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </section>
            <footer class="modal-card-foot is-stretched">
            <button class="button is-success" onclick="ajaxAssignAgent();">{{ __('app.save') }}</button>
            <button class="button" onclick="vue.bShowAssignAgent = false;">{{ __('app.close') }}</button>
            </footer>
        </div>
        </div>

        <div class="modal" :class="{'is-active': bShowAssignGroup}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
            <p class="modal-card-title">{{ __('app.ticket_assign_group') }}</p>
            <button class="delete" aria-label="close" onclick="vue.bShowAssignGroup = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                <form method="POST" id="edAssignGroupForm">
                    @csrf
                    @method('PATCH')

                    <div class="field">
                        <div class="control">
                            <select name="agent" id="selGroup">
                                @foreach ($groups as $grp)
                                    <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </section>
            <footer class="modal-card-foot is-stretched">
            <button class="button is-success" onclick="ajaxAssignGroup()">{{ __('app.save') }}</button>
            <button class="button" onclick="vue.bShowAssignGroup = false;">{{ __('app.close') }}</button>
            </footer>
        </div>
        </div>

        <div class="modal" :class="{'is-active': bShowChangeStatus}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
            <p class="modal-card-title">{{ __('app.ticket_change_status') }}</p>
            <button class="delete" aria-label="close" onclick="vue.bShowChangeStatus = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                <form method="POST" id="edchangeStatusForm">
                    @csrf
                    @method('PATCH')

                    <div class="field">
                        <div class="control">
                            <select name="status" id="selStatus" onchange="if (this.value === '3') { document.getElementById('fieldClosingNotification').classList.remove('is-hidden'); } else { document.getElementById('fieldClosingNotification').classList.add('is-hidden'); }">
                                <option value="1">{{ __('app.ticket_status_open') }}</option>
                                <option value="2">{{ __('app.ticket_status_waiting') }}</option>
                                <option value="3">{{ __('app.ticket_status_closed') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="field is-hidden" id="fieldClosingNotification">
                        <div class="control">
                            <input id="cbClosingNotification" type="checkbox" value="1" data-role="checkbox" data-style="2" data-caption="{{ __('app.ticket_closing_do_not_send_email') }}">
                        </div>
                    </div>
                </form>
            </section>
            <footer class="modal-card-foot is-stretched">
            <button class="button is-success" onclick="ajaxChangeStatus();">{{ __('app.save') }}</button>
            <button class="button" onclick="vue.bShowChangeStatus = false;">{{ __('app.close') }}</button>
            </footer>
        </div>
        </div>

        <div class="modal" :class="{'is-active': bShowChangePrio}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
            <p class="modal-card-title">{{ __('app.ticket_change_prio') }}</p>
            <button class="delete" aria-label="close" onclick="vue.bShowChangePrio = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                <form method="POST" id="edchangePrioForm">
                    @csrf
                    @method('PATCH')

                    <div class="field">
                        <div class="control">
                            <select name="agent" id="selPrio">
                                <option value="1">{{ __('app.prio_low') }}</option>
                                <option value="2">{{ __('app.prio_med') }}</option>
                                <option value="3">{{ __('app.prio_high') }}</option>
                            </select>
                        </div>
                    </div>
                </form>
            </section>
            <footer class="modal-card-foot is-stretched">
            <button class="button is-success" onclick="ajaxChangePrio();">{{ __('app.save') }}</button>
            <button class="button" onclick="vue.bShowChangePrio = false;">{{ __('app.close') }}</button>
            </footer>
        </div>
        </div>

        <div class="modal" :class="{'is-active': bShowChangeType}">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head is-stretched">
            <p class="modal-card-title">{{ __('app.ticket_change_type') }}</p>
            <button class="delete" aria-label="close" onclick="vue.bShowChangeType = false;"></button>
            </header>
            <section class="modal-card-body is-stretched">
                <form method="POST" id="edchangeTypeForm">
                    @csrf
                    @method('PATCH')

                    <div class="field">
                        <div class="control">
                            <select name="agent" id="selType">
                                @foreach ($ticketTypes as $ticketType)
                                    <option value="{{ $ticketType->id }}">{{ $ticketType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </section>
            <footer class="modal-card-foot is-stretched">
            <button class="button is-success" onclick="ajaxChangeType();">{{ __('app.save') }}</button>
            <button class="button" onclick="vue.bShowChangeType = false;">{{ __('app.close') }}</button>
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
    </div>
@endsection

@section('javascript')
    function ajaxAssignAgent()
    {
        ajaxRequest('patch',
            '{{ url('/' . $workspace . '/ticket/' . $ticket->id . '/assign/agent/') }}/' + document.getElementById('selAgent').value,
            {},
            function(data){
                document.getElementById('view-agent').innerHTML = '{{ __('app.ticket_assignee') }}: ' + document.getElementById('selAgent').options[document.getElementById('selAgent').selectedIndex].text;
            },
            function(){
                vue.bShowAssignAgent = false;
            }
        );
    }

    function ajaxAssignGroup()
    {
        ajaxRequest('patch',
            '{{ url('/' . $workspace . '/ticket/' . $ticket->id . '/assign/group/') }}/' + document.getElementById('selGroup').value,
            {},
            function(data){
                document.getElementById('view-group').innerHTML = document.getElementById('selGroup').options[document.getElementById('selGroup').selectedIndex].text;
            },
            function(){
                vue.bShowAssignGroup = false;
            }
        );
    }

    function ajaxChangeStatus()
    {
        let append = '';
        if (document.getElementById('selStatus').value == '3' && document.getElementById('cbClosingNotification').checked) {
            append = '?skipClosingNotification=1';
        }

        ajaxRequest('patch',
        '{{ url('/' . $workspace . '/ticket/' . $ticket->id . '/status/') }}/' + document.getElementById('selStatus').value + append,
        {},
        function(data){
            let prev = document.getElementsByClassName('dashboard-badge');
            for (i = 0; i < prev.length; i++) { prev[i].parentNode.removeChild(prev[i]) };

            if (document.getElementById('selStatus').value == 0) {
                document.getElementById('view-status').innerHTML = '<div class="dashboard-badge dashboard-badge-is-red">{{ __('app.ticket_status_confirmation') }}</div>';
            } else if (document.getElementById('selStatus').value == 1) {
                document.getElementById('view-status').innerHTML = '<div class="dashboard-badge dashboard-badge-is-green">{{ __('app.ticket_status_open') }}</div>';
            } else if (document.getElementById('selStatus').value == 2) {
                document.getElementById('view-status').innerHTML = '<div class="dashboard-badge dashboard-badge-is-grey">{{ __('app.ticket_status_waiting') }}</div>';
            } else if (document.getElementById('selStatus').value == 3) {
                document.getElementById('view-status').innerHTML = '<div class="dashboard-badge dashboard-badge-is-brown">{{ __('app.ticket_status_closed') }}</div>';
                document.getElementById('delete-ticket-link').classList.remove('is-hidden');
            }
        },
        function(){
            vue.bShowChangeStatus = false;
        }
        );
    }

    function ajaxChangePrio()
    {
        ajaxRequest('patch',
            '{{ url('/' . $workspace . '/ticket/' . $ticket->id . '/prio/') }}/' + document.getElementById('selPrio').value,
            {},
            function(data){
                if (document.getElementById('selPrio').value == 1)
                    document.getElementById('view-prio').innerHTML = '{{ __('app.prio_low') }}';
                else if (document.getElementById('selPrio').value == 2)
                    document.getElementById('view-prio').innerHTML = '{{ __('app.prio_med') }}';
                else if (document.getElementById('selPrio').value == 3)
                    document.getElementById('view-prio').innerHTML = '<b>{{ __('app.prio_high') }}</b>';
            },
            function(){
                vue.bShowChangePrio = false;
            }
        );
    }

    function ajaxChangeType()
    {
        ajaxRequest('patch',
            '{{ url('/' . $workspace . '/ticket/' . $ticket->id . '/type/') }}/' + document.getElementById('selType').value,
            {},
            function(data){
                document.getElementById('view-type').innerHTML = document.getElementById('selType').options[document.getElementById('selType').selectedIndex].text;
            },
            function(){
                vue.bShowChangeType = false;
            }
        );
    }

    function ajaxSaveNotes()
    {
        ajaxRequest('patch',
            '{{ url('/' . $workspace . '/ticket/' . $ticket->id . '/notes/save') }}',
            {
                notes: document.getElementById('notes').value
            },
            function(data){
            },
            function(){
            }
        );
    }
@endsection
