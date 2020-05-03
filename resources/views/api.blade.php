<!--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
-->

@extends('layouts.layout_home')

@section('content')
    <div class="columns is-centered is-vcentered">
        <div class="column is-three-fifths">
            <div class="home-padding">
                <div class="home-headline">
                    <center><h1>{{ __('app.home_api') }}</h1></center>
                </div>

                <p>
                    If you don't want to use our predefined support contact form, you can also
                    make your own frontend interface to your customers. You can then communicate with
                    our backend to create a ticket to your workspace.
                </p>

                <p>
                    In order to create a ticket call the following API route:
                </p>

                <p>
                    <code>{{ env('APP_URL') }}/api/<strong>{workspace}</strong>/ticket/create</code>
                </p>

                <p>
                    Where {workspace} is your workspace hash name. You can find it in your system settings tab.
                    The following post data fields are supported:
                </p>

                <p>
                    <table>
                        <thead>
                            <th>Field</th>
                            <th>Description</th>
                            <th>Required</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>subject</code></td>
                                <td>The subject of the ticket</td>
                                <td>Required, minimum 5 chars</td>
                            </tr>
                            <tr>
                                <td><code>text</code></td>
                                <td>The ticket text</td>
                                <td>Required, maximum 4096 chars</td>
                            </tr>
                            <tr>
                                <td><code>name</code></td>
                                <td>The name of the customer</td>
                                <td>Required</td>
                            </tr>
                            <tr>
                                <td><code>email</code></td>
                                <td>The e-mail address of the customer</td>
                                <td>Required</td>
                            </tr>
                            <tr>
                                <td><code>type</code></td>
                                <td>Ticket type</td>
                                <td>Required, must match the IDof one of your created ticket types</td>
                            </tr>
                            <tr>
                                <td><code>prio</code></td>
                                <td>Ticket priority</td>
                                <td>Required, 1 = low, 2 = medium, 3 = high</td>
                            </tr>
                            <tr>
                                <td><code>attachment</code></td>
                                <td>A file to be attached</td>
                                <td>Optional</td>
                            </tr>
                        </tbody>
                    </table>
                </p>

                <p>
                    The API endpoint will return a JSON response in order to provide your frontend with the operation result.
                    The JSON response will contain a status code of the operation named 'code' and a field 'data' that holds
                    additional data depending on the result of the operation. Also it will hold the concerned workspace.
                    The following response status codes are possible:
                </p>

                <p>
                    <table>
                        <thead>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Required</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td><code>404 Not Found</code></td>
                            <td>
                                <ul>
                                    <li>The workspace could not be found</li>
                                    <li>The specified ticket type could not be found</li>
                                </ul>
                            </td>
                            <td>
                                <ul>
                                    <li>No workspace found: none</li>
                                    <li>Ticket type not found: field ‚ticket_type‘ with the request value</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><code>500 Internal Server Error</code></td>
                            <td>
                                <ul>
                                    <li>The post data is invalid</li>
                                    <li>The ticket could not be created</li>
                                </ul>
                            </td>
                            <td>
                                <ul>
                                    <li>Invalid post data: field ‚invalid_fields‘ as an array containing an array for each failed post data item with the fields ‚name‘ for the field name and ‚value‘ for the failed value</li>
                                    <li>Ticket not created: A field ‚data‘ with the data that could not be stored</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><code>429 Too Many Requests</code></td>
                            <td>You tried to create too many tickets in a period of time</td>
                            <td>Field ‚ticket_wait_time‘ that holds the time in seconds you have to wait until you may create a ticket again</td>
                        </tr>
                        <tr>
                            <td><code>201 Created</code></td>
                            <td>The ticket has been created</td>
                            <td>Field ‚data‘ containing the stored ticket data</td>
                        </tr>
                        </tbody>
                    </table>
                </p>

                <p>
                    An example response could look like the following:<br/>
                    <code>{</code><br/>
                    <code>&nbsp;&nbsp;"code": "201",</code><br/>
                    <code>&nbsp;&nbsp;"workspace": "(A workspace hash name)",</code><br/>
                    <code>&nbsp;&nbsp;"data": {</code><br/>
                    <code>&nbsp;&nbsp;&nbsp;&nbsp;(Ticket creation data)</code><br/>
                    <code>&nbsp;&nbsp;}</code><br/>
                    <code>}</code><br/>
                </p>
            </div>
        </div>
    </div>
@endsection
