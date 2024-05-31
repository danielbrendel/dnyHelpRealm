{{--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2024 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ env('APP_NAME') }} - {{ env('APP_DESCRIPTION') }}</title>

        <meta name="author" content="{{ env('APP_AUTHOR') }}">
        <meta name="description" content="{{ env('APP_DESCRIPTION') }}">
        <meta name="keywords" content="{{ env('APP_METATAGS') }}">

        <link rel="icon" type="image/png" href="{{ asset('gfx/logo.png') }}">

        <link rel="stylesheet" type="text/css" href="{{ asset('css/bulma.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/metro-all.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/metro.datatables.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/stripe.css') }}">

        @if (env('APP_ENV') == 'local')
        <script src="{{ asset('js/vue.js') }}"></script>
        @elseif (env('APP_ENV') == 'production')
        <script src="{{ asset('js/vue.min.js') }}"></script>
        @endif
        <script src="https://js.stripe.com/v3/"></script>
        <script src="{{ asset('js/fontawesome.js') }}"></script>
        <script src="{{ asset('js/metro.min.js') }}"></script>
        <script src="{{ asset('js/push.min.js') }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>
    </head>
    <body>
        <div id="app">
            <div class="feedback-error" id="errormsg">
                <div class="feedback-content" id="errormsg-content">
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    @endif

                    @if (Session::has('error'))
                        {{ Session::get('error') }}
                    @endif
                </div>
            </div>

            <div class="feedback-success" id="successmsg">
                <div class="feedback-content" id="successmsg-content">
                    @if (Session::has('success'))
                        {{ Session::get('success') }}
                    @endif
                </div>
            </div>

            <div class="app-userarea">
                <div id="sidebar" class="app-sidebar">
                    <div name="sidebaritem" class="app-sidebar-logo">
                        <div class="app-logo" style="background-image: url({{ asset('/gfx/logo.png') }});" title="{{ env('APP_NAME') . ' | ' . env('APP_DESCRIPTION') }}" onclick="location.href='{{ url('/' . $workspace) }}';"></div>
                    </div>

                    <div name="sidebaritem" class="app-sidebar-item-wrapper" title="{{ __('app.dashboard') }}" onclick="location.href='{{ url('/' . $workspace) }}';">
                        <div class="app-sidebar-item-content"><i class="fas fa-tachometer-alt fa-lg"></i></div>
                    </div>

                    <div name="sidebaritem" class="app-sidebar-item-wrapper" title="{{ __('app.ticket_list') }}" onclick="location.href='{{ url('/' . $workspace . '/ticket/list') }}';">
                        <div class="app-sidebar-item-content"><i class="far fa-list-alt fa-lg"></i></div>
                    </div>

                    <div name="sidebaritem" class="app-sidebar-item-wrapper" title="{{ __('app.ticket_create') }}" onclick="location.href='{{ url('/' . $workspace . '/ticket/create') }}';">
                        <div class="app-sidebar-item-content"><i class="fas fa-plus fa-lg"></i></div>
                    </div>

                    <div name="sidebaritem" class="app-sidebar-item-wrapper" title="{{ __('app.ticket_search') }}" onclick="location.href='{{ url('/' . $workspace . '/ticket/search') }}';">
                        <div class="app-sidebar-item-content"><i class="fas fa-search fa-lg"></i></div>
                    </div>

                    @if ($superadmin)
                        <div name="sidebaritem" class="app-sidebar-item-wrapper" title="{{ __('app.groups') }}" onclick="location.href='{{ url('/' . $workspace . '/group/list') }}';">
                            <div class="app-sidebar-item-content"><i class="fas fa-layer-group fa-lg"></i></div>
                        </div>

                        <div name="sidebaritem" class="app-sidebar-item-wrapper" title="{{ __('app.agent_list') }}" onclick="location.href='{{ url('/' . $workspace . '/agent/list') }}';">
                            <div class="app-sidebar-item-content"><i class="fas fa-user-tie fa-lg"></i></div>
                        </div>

                        <div name="sidebaritem" class="app-sidebar-item-wrapper" title="{{ __('app.faq_list') }}" onclick="location.href='{{ url('/' . $workspace . '/faq/list') }}';">
                            <div class="app-sidebar-item-content"><i class="far fa-question-circle fa-lg"></i></div>
                        </div>

                        <div name="sidebaritem" class="app-sidebar-item-wrapper" title="{{ __('app.system_settings') }}" onclick="location.href='{{ url('/' . $workspace . '/settings/system') }}';">
                            <div class="app-sidebar-item-content"><i class="fas fa-cog fa-lg"></i></div>
                        </div>
                    @endif
                </div>

                <div class="app-content" id="content">
                    <div class="app-navbar">
                        <div class="hamburger-container" @click="toggleMenu()">
                            <div class="hamburger-bar1"></div>
                            <div class="hamburger-bar2"></div>
                            <div class="hamburger-bar3"></div>
                        </div>

                        <div class="app-location" id="location" <?php if (isset($fulllocation)) echo 'title="' . $fulllocation . '"'; ?>><?php if (isset($location)) echo $location; ?></div>

                        <div class="app-logout"><a href="{{ url('/logout') }}"><i class="fas fa-sign-out-alt fa-lg" title="{{ __('app.logout') }}"></i></a></div>
                        <div class="app-account" style="background-image: url({{ asset('gfx/avatars/' . $user->avatar) }});" title="{{ __('app.viewsettings') }}" onclick="location.href='{{ url('/' . $workspace . '/settings') }}';"></div>
                        <div class="app-info"><a href="javascript:void(0)" onclick="vue.bShowAbout = true;"><i class="fas fa-info-circle fa-lg" title="{{ __('app.about') }}"></i></a></div>
                    </div>

                    <div class="container">
                        @yield('content')
                    </div>

                    <div class="modal" :class="{'is-active': bShowAbout}">
                        <div class="modal-background"></div>
                        <div class="modal-card">
                            <header class="modal-card-head is-stretched">
                            <p class="modal-card-title">{{ __('app.about') }}</p>
                            <button class="delete" aria-label="close" onclick="vue.bShowAbout = false;"></button>
                            </header>
                            <section class="modal-card-body is-stretched">
                                <h1>{{ env('APP_NAME') }}</h1>
                                <br/>
                                <h2>Info</h2>
                                <b>Developer:</b> {{ env('APP_AUTHOR') }}<br/>
                                <b>Contact:</b> {{ env('APP_CONTACT') }}<br/>
                                <b>Codename:</b> {{ env('APP_CODENAME') }}<br/>
                                <b>Version:</b> {{ env('APP_VERSION') }}<br/>
                            </section>
                            <footer class="modal-card-foot is-stretched">
                            <button class="button" onclick="vue.bShowAbout = false;">{{ __('app.close') }}</button>
                            </footer>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function showError()
            {
                //Show error div

                document.getElementById('errormsg').style.display = 'inherit';

                setTimeout(function() { document.getElementById('errormsg').style.display = 'none'; }, 5000);
            }

            function showSuccess()
            {
                //Show success div

                document.getElementById('successmsg').style.display = 'inherit';

                setTimeout(function() { document.getElementById('successmsg').style.display = 'none'; }, 5000);
            }

            @if ($errors->any() || Session::has('error'))
                setTimeout('showError()', 500);
            @endif

            @if (Session::has('success'))
                setTimeout('showSuccess()', 500);
            @endif

            var vue = new Vue({
                el: '#app',

                data: {
                    showSidebar: false,
                    bShowCmtEdit: false,
                    bShowAbout: false,
                    bShowAssignAgent: false,
                    bShowAssignGroup: false,
                    bShowChangeClient: false,
                    bShowChangeStatus: false,
                    bShowChangePrio: false,
                    bShowChangeType: false,
                    bAddAgentGroup: false,
                    bShowFileDelete: false,
                    currentDeleteFile: '',
                    bAddAgentToGroup: false,
                    bShowTicketExport: false,
                    bShowOrderAPIAccess: false
                },
                created: function() {
                    if (window.innerWidth <= 775) {
                        this.showSideBar = true;
                    }
                },
                methods: {
                    toggleMenu: function() {
                        //Toggle the sidenav menu

                        this.showSideBar = !this.showSideBar;

                        if (this.showSideBar) {
                            document.getElementById('sidebar').style.width = '0';

                            var elems = document.getElementsByName('sidebaritem');
                            for (i = 0; i < elems.length; i++) {
                                elems[i].style.display = 'none';
                            }
                        } else {
                            document.getElementById('sidebar').style.width = '80px';

                            var elems = document.getElementsByName('sidebaritem');
                            for (i = 0; i < elems.length; i++) {
                                elems[i].style.display = 'block';
                            }
                        }
                    },

                    addTicketType: function(workspace) {
                        //Add ticket type

                        var name = prompt("{{ __('app.ticket_type_input_name') }}");
                        if (name == null) {
                            return;
                        }

                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = "{{ url('/') }}/" + workspace + "/tickettype/add";

                        const hiddenField = document.createElement('input');
                        hiddenField.type = 'hidden';
                        hiddenField.name = '_token';
                        hiddenField.value = "{{ csrf_token() }}";
                        form.appendChild(hiddenField);

                        const nameField = document.createElement('input');
                        nameField.type = 'text';
                        nameField.name = 'name';
                        nameField.value = name;
                        form.appendChild(nameField);

                        document.body.appendChild(form);
                        form.submit();
                    },

                    editTicketType: function(workspace, id) {
                        //Edit ticket type

                        var name = prompt("{{ __('app.ticket_type_input_name') }}");
                        if (name == null) {
                            return;
                        }

                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = "{{ url('/') }}/" + workspace + "/tickettype/" + id + "/edit";

                        const hiddenField = document.createElement('input');
                        hiddenField.type = 'hidden';
                        hiddenField.name = '_token';
                        hiddenField.value = "{{ csrf_token() }}";
                        form.appendChild(hiddenField);

                        const nameField = document.createElement('input');
                        nameField.type = 'text';
                        nameField.name = 'name';
                        nameField.value = name;
                        form.appendChild(nameField);

                        document.body.appendChild(form);
                        form.submit();
                    }
                }
            });

            function showResultMessage(resultType, message)
            {
                if (resultType == 200) {
                    let content = document.getElementById('successmsg-content');
                    content.innerHTML = message;
                    showSuccess();
                } else if (resultType == 500) {
                    let content = document.getElementById('errormsg-content');
                    content.innerHTML = message;
                    showError();
                } else {
                    console.log('resultType ' + resultType + " is unknown");
                }
            }

            function ajaxRequest(method, url, data = {}, successfunc = function(data){}, finalfunc = function(){}, surpressSuccessMessage = false)
            {
                let func = window.axios.get;
                if (method == 'post') {
                    func = window.axios.post;
                } else if (method == 'patch') {
                    func = window.axios.patch;
                } else if (method == 'delete') {
                    func = window.axios.delete;
                }

                func(url, data)
                    .then(function(response){
                        successfunc(response.data);

                        if (!surpressSuccessMessage) {
                            showResultMessage(response.data.code, response.data.message);
                        }
                    })
                    .catch(function (error) {
                        showResultMessage(500, error);
                    })
                    .finally(function(){
                        finalfunc();
                    }
                );
            }

            function fetchNotifications()
            {
                ajaxRequest('get', '{{ url('/clep/notifications') }}', {},
                    function(data){
                        if (data.code === 200) {
                            for (let i = 0; i < data.data.length; i++) {
                                Push.create(data.data[i].title, {
                                    body: data.data[i].message,
                                    icon: '{{ asset('gfx/logo.png') }}',
                                    timeout: 4000,
                                    onClick: function () {
                                        window.focus();
                                        this.close();
                                    }
                                });
                            }
                        }
                    },
                    function(){},
                    true
                );

                setTimeout('fetchNotifications()', 1000 * 60);
            }

            fetchNotifications();

            @yield('javascript')
        </script>
    </body>
</html>
