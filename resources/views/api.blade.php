{{--
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2024 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
--}}

@extends('layouts.layout_home')

@section('title', __('app.home_api'))

@section('content')
    <div class="columns is-centered is-vcentered">
        <div class="column is-three-fifths">
            <div class="home-padding has-tables">
                <div class="home-headline">
                    <center><h1>{{ __('app.home_api') }}</h1></center>
                </div>

                <p>
                    If you don't want to use our predefined support contact form, you can also
                    make your own frontend interface to your customers. You can then communicate with
                    our backend to create a ticket to your workspace using our REST API.
                </p>

                @if (env('APP_PAYFORAPI'))
                <p>
                    <strong>
                        Note: API access is granted by paying a small fee. This fee is only paid once and
                        grants you unlimited API access. The current fee is {{ env('STRIPE_COSTS_LABEL') }}.
                        You can buy access via the system settings panel.
                    </strong>
                </p>
                @endif

                <p>
                    In order to create a ticket call the following API route as POST request:
                </p>

                <p>
                    <code>{{ env('APP_URL') }}/api/<strong>{workspace}</strong>/ticket/create</code>
                </p>

                <p>
                    Where {workspace} is your workspace hash name. You can find it in your system API settings.
                    Also you need an API token for every request to our API for security reasons. An API token is
                    automatically generated on registration, but you can generate new tokens in the system settings
                    menu. The following post data fields are supported:
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
                                <td><code>apitoken</code></td>
                                <td>The workspace API token</td>
                                <td>Required</td>
                            </tr>
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
                                <td>Required, must match the ID of one of your created ticket types</td>
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
                            <th>Additional data</th>
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
                            <td><code>403 Forbidden</code></td>
                            <td>
                                <ul>
                                    <li>The API access has not yet been purchased</li>
                                    <li>The request API token is invalid</li>
                                </ul>
                            </td>
                            <td>
                                <ul>
                                    <li>A field 'paidforapi' with value <i>false</i></li>
                                    <li>A field 'apitoken' with the invalid token</li>
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
                    An example response can look like the following:<br/>
                    <code>{</code><br/>
                    <code>&nbsp;&nbsp;"code": "201",</code><br/>
                    <code>&nbsp;&nbsp;"workspace": "(A workspace hash name)",</code><br/>
                    <code>&nbsp;&nbsp;"data": {</code><br/>
                    <code>&nbsp;&nbsp;&nbsp;&nbsp;(Ticket creation data)</code><br/>
                    <code>&nbsp;&nbsp;}</code><br/>
                    <code>}</code><br/>
                </p>

                <br/>

                <h2>Further API requests</h2>

                <br/>

                <h3>Get ticket information</h3>

                <p>
                    In order to query specific ticket information you can call:<br/>
                    <code>{{ env('APP_URL') }}/api/<strong>{workspace}</strong>/ticket/info</code>
                </p>

                <p>
                    Arguments:<br/>
                    <table>
                        <thead>
                            <th>Field</th>
                            <th>Description</th>
                            <th>Required</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>apitoken</code></td>
                                <td>The workspace API token</td>
                                <td>Required</td>
                            </tr>
                            <tr>
                                <td><code>hash</code></td>
                                <td>The hash of a ticket of your workspace</td>
                                <td>Required</td>
                            </tr>
                        </tbody>
                    </table>
                </p>

                <p>
                    Response:<br/>
                    <code>{</code><br/>
                    <code>&nbsp;&nbsp;"code": "200",</code><br/>
                    <code>&nbsp;&nbsp;"workspace": "(A workspace hash name)",</code><br/>
                    <code>&nbsp;&nbsp;"data": {</code><br/>
                    <code>&nbsp;&nbsp;&nbsp;&nbsp;(Ticket information data)</code><br/>
                    <code>&nbsp;&nbsp;}</code><br/>
                    <code>}</code><br/>
                </p>

                <hr/>

                <h3>Get ticket thread</h3>

                <p>
                    To retrieve ticket thread posts you can call:<br/>
                    <code>{{ env('APP_URL') }}/api/<strong>{workspace}</strong>/ticket/thread</code>
                </p>

                <p>
                    Arguments:<br/>
                <table>
                    <thead>
                    <th>Field</th>
                    <th>Description</th>
                    <th>Required</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td><code>apitoken</code></td>
                        <td>The workspace API token</td>
                        <td>Required</td>
                    </tr>
                    <tr>
                        <td><code>hash</code></td>
                        <td>The hash of a ticket of your workspace</td>
                        <td>Required</td>
                    </tr>
                    <tr>
                        <td><code>paginate</code></td>
                        <td>Thread posts below this ID will be returned</td>
                        <td>Optional</td>
                    </tr>
                    <tr>
                        <td><code>limit</code></td>
                        <td>Maximum amount of returned thread posts</td>
                        <td>Optional, default 10</td>
                    </tr>
                    </tbody>
                </table>
                </p>

                <p>
                    Response:<br/>
                    <code>{</code><br/>
                    <code>&nbsp;&nbsp;"code": "200",</code><br/>
                    <code>&nbsp;&nbsp;"workspace": "(A workspace hash name)",</code><br/>
                    <code>&nbsp;&nbsp;"ticket": "(The associated ticket hash)",</code><br/>
                    <code>&nbsp;&nbsp;"data": {</code><br/>
                    <code>&nbsp;&nbsp;&nbsp;&nbsp;(Ticket thread data)</code><br/>
                    <code>&nbsp;&nbsp;}</code><br/>
                    <code>}</code><br/>
                </p>

                <hr/>

                <h3>Get ticket attachments</h3>

                <p>
                    In order to get a list of attachments you can call:<br/>
                    <code>{{ env('APP_URL') }}/api/<strong>{workspace}</strong>/ticket/attachments</code>
                </p>

                <p>
                    Arguments:<br/>
                <table>
                    <thead>
                    <th>Field</th>
                    <th>Description</th>
                    <th>Required</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td><code>apitoken</code></td>
                        <td>The workspace API token</td>
                        <td>Required</td>
                    </tr>
                    <tr>
                        <td><code>hash</code></td>
                        <td>The hash of a ticket of your workspace</td>
                        <td>Required</td>
                    </tr>
                    </tbody>
                </table>
                </p>

                <p>
                    Response:<br/>
                    <code>{</code><br/>
                    <code>&nbsp;&nbsp;"code": "200",</code><br/>
                    <code>&nbsp;&nbsp;"workspace": "(A workspace hash name)",</code><br/>
                    <code>&nbsp;&nbsp;"ticket": "(The associated ticket hash)",</code><br/>
                    <code>&nbsp;&nbsp;"data": {</code><br/>
                    <code>&nbsp;&nbsp;&nbsp;&nbsp;(Ticket attachment data)</code><br/>
                    <code>&nbsp;&nbsp;}</code><br/>
                    <code>}</code><br/>
                </p>

                <hr/>

                <h3>Add customer comment</h3>

                <p>
                    In order to add a customer comment you can call:<br/>
                    <code>{{ env('APP_URL') }}/api/<strong>{workspace}</strong>/ticket/comment/add/customer</code>
                </p>

                <p>
                    Arguments:<br/>
                <table>
                    <thead>
                    <th>Field</th>
                    <th>Description</th>
                    <th>Required</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td><code>apitoken</code></td>
                        <td>The workspace API token</td>
                        <td>Required</td>
                    </tr>
                    <tr>
                        <td><code>hash</code></td>
                        <td>The hash of a ticket of your workspace</td>
                        <td>Required</td>
                    </tr>
                    <tr>
                        <td><code>text</code></td>
                        <td>The text content</td>
                        <td>Required</td>
                    </tr>
                    </tbody>
                </table>
                </p>

                <p>
                    Response:<br/>
                    <code>{</code><br/>
                    <code>&nbsp;&nbsp;"code": "200",</code><br/>
                    <code>&nbsp;&nbsp;"workspace": "(A workspace hash name)",</code><br/>
                    <code>&nbsp;&nbsp;"ticket": "(The associated ticket hash)",</code><br/>
                    <code>&nbsp;&nbsp;"cmt_id": "(ID of the added comment)"</code><br/>
                    <code>}</code><br/>
                </p>

                <hr/>

                <h3>Edit customer comment</h3>

                <p>
                    In order to edit a customer comment you can call:<br/>
                    <code>{{ env('APP_URL') }}/api/<strong>{workspace}</strong>/ticket/comment/edit/customer</code>
                </p>

                <p>
                    Arguments:<br/>
                <table>
                    <thead>
                    <th>Field</th>
                    <th>Description</th>
                    <th>Required</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td><code>apitoken</code></td>
                        <td>The workspace API token</td>
                        <td>Required</td>
                    </tr>
                    <tr>
                        <td><code>hash</code></td>
                        <td>The hash of a ticket of your workspace</td>
                        <td>Required</td>
                    </tr>
                    <tr>
                        <td><code>cmt_id</code></td>
                        <td>The ID of the comment</td>
                        <td>Required</td>
                    </tr>
                    <tr>
                        <td><code>text</code></td>
                        <td>The new text content to be stored</td>
                        <td>Required</td>
                    </tr>
                    </tbody>
                </table>
                </p>

                <p>
                    Response:<br/>
                    <code>{</code><br/>
                    <code>&nbsp;&nbsp;"code": "200",</code><br/>
                    <code>&nbsp;&nbsp;"workspace": "(A workspace hash name)",</code><br/>
                    <code>&nbsp;&nbsp;"ticket": "(The associated ticket hash)",</code><br/>
                    <code>&nbsp;&nbsp;"cmt_id": "(ID of the edited comment)"</code><br/>
                    <code>}</code><br/>
                </p>

                <hr/>

                <h3>Add ticket attachment</h3>

                <p>
                    In order to add a ticket attachment you can call:<br/>
                    <code>{{ env('APP_URL') }}/api/<strong>{workspace}</strong>/ticket/attachment/add</code>
                </p>

                <p>
                    Arguments:<br/>
                <table>
                    <thead>
                    <th>Field</th>
                    <th>Description</th>
                    <th>Required</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td><code>apitoken</code></td>
                        <td>The workspace API token</td>
                        <td>Required</td>
                    </tr>
                    <tr>
                        <td><code>hash</code></td>
                        <td>The hash of a ticket of your workspace</td>
                        <td>Required</td>
                    </tr>
                    <tr>
                        <td><code>attachment</code></td>
                        <td>File to be added</td>
                        <td>Required</td>
                    </tr>
                    </tbody>
                </table>
                </p>

                <p>
                    Response:<br/>
                    <code>{</code><br/>
                    <code>&nbsp;&nbsp;"code": "200",</code><br/>
                    <code>&nbsp;&nbsp;"workspace": "(A workspace hash name)",</code><br/>
                    <code>&nbsp;&nbsp;"ticket": "(The associated ticket hash)",</code><br/>
                    <code>&nbsp;&nbsp;"file": {</code><br/>
                    <code>&nbsp;&nbsp;&nbsp;&nbsp;(Attachment info)</code><br/>
                    <code>&nbsp;&nbsp;}</code><br/>
                    <code>}</code><br/>
                </p>

                <hr/>

                <h3>Delete ticket attachment</h3>

                <p>
                    In order to delete a ticket attachment you can call:<br/>
                    <code>{{ env('APP_URL') }}/api/<strong>{workspace}</strong>/ticket/attachment/delete</code>
                </p>

                <p>
                    Arguments:<br/>
                <table>
                    <thead>
                    <th>Field</th>
                    <th>Description</th>
                    <th>Required</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td><code>apitoken</code></td>
                        <td>The workspace API token</td>
                        <td>Required</td>
                    </tr>
                    <tr>
                        <td><code>hash</code></td>
                        <td>The hash of a ticket of your workspace</td>
                        <td>Required</td>
                    </tr>
                    <tr>
                        <td><code>file_id</code></td>
                        <td>The ID of the attachment</td>
                        <td>Required</td>
                    </tr>
                    </tbody>
                </table>
                </p>

                <p>
                    Response:<br/>
                    <code>{</code><br/>
                    <code>&nbsp;&nbsp;"code": "200",</code><br/>
                    <code>&nbsp;&nbsp;"workspace": "(A workspace hash name)",</code><br/>
                    <code>&nbsp;&nbsp;"ticket": "(The associated ticket hash)",</code><br/>
                    <code>&nbsp;&nbsp;"success": "(true on success)"</code><br/>
                    <code>}</code><br/>
                </p>

                <hr/>

                <h3>Widget</h3>

                <p>
                    You can also use the embeddable widget in order to let users create support requests comfortably from your website.
                    When enabled and activated then a support icon will be shown in the bottom right corner of your page which users
                    can use to open a form to enter their support request data. After successfully submitting the form, a new ticket
                    will be created for the requesting user.
                </p>

                <p>
                    In order to embed the widget, you need to activate the feature in your system settings. There is a separate key for the 
                    widget feature in order to keep things distinguishable. Similar to the REST API key, you can also always generate a new 
                    widget API key whenever you want.
                </p>

                <p>
                    The following code describes a basic widget initialization.
                </p>

                <p>
                    <code>&lt;script src="{{ asset('js/widget.js') }}"&gt;&lt;/script&gt;</code><br/><br/>

                    <code>&lt;div id="support-widget"&gt;&lt;/div&gt;</code><br/><br/>

                    <code>let widget = new HelpRealmWidget({</code><br/>
                        <code>&nbsp;&nbsp;elem: '#support-widget',</code><br/>
                        <code>&nbsp;&nbsp;workspace: 'your-workspace-hash',</code><br/>
                        <code>&nbsp;&nbsp;apiKey: 'your-widget-api-key',</code><br/>
                        <code>&nbsp;&nbsp;header: 'url/to/your/header/image.png',</code><br/>
                        <code>&nbsp;&nbsp;logo: 'url/to/your/logo/image.png',</code><br/>
                        <code>&nbsp;&nbsp;button: 'url/to/your/button/image.png',</code><br/>
                        <code>&nbsp;&nbsp;fileUpload: true,</code><br/>
                        <code>&nbsp;&nbsp;lang: {</code><br/>
                            <code>&nbsp;&nbsp;&nbsp;&nbsp;title: 'Contact Us!',</code><br/>
                            <code>&nbsp;&nbsp;&nbsp;&nbsp;lblInputName: 'Enter your name',</code><br/>
                            <code>&nbsp;&nbsp;&nbsp;&nbsp;lblInputEmail: 'Enter your E-Mail',</code><br/>
                            <code>&nbsp;&nbsp;&nbsp;&nbsp;lblInputSubject: 'What is your topic?',</code><br/>
                            <code>&nbsp;&nbsp;&nbsp;&nbsp;lblInputMessage: 'What is on your mind?',</code><br/>
                            <code>&nbsp;&nbsp;&nbsp;&nbsp;lblInputFile: 'Attachment (optional)',</code><br/>
                            <code>&nbsp;&nbsp;&nbsp;&nbsp;btnSubmit: 'Submit',</code><br/>
                            <code>&nbsp;&nbsp;&nbsp;&nbsp;error: 'Elem {elem} is invalid or missing',</code><br/>
                            <code>&nbsp;&nbsp;&nbsp;&nbsp;access: 'Access denied!',</code><br/>
                        <code>},</code><br/>
                        <code>&nbsp;&nbsp;ticket: {</code><br/>
                            <code>&nbsp;&nbsp;&nbsp;&nbsp;type: 1,</code><br/>
                            <code>&nbsp;&nbsp;&nbsp;&nbsp;prio: 1</code><br/>
                        <code>&nbsp;&nbsp;},</code><br/>
                    <code>});</code><br/>
                </p>

                <br/>

                <p>
                    The following methods are also available:<br/>

                    <table>
                        <thead>
                            <th>Method</th>
                            <th>Description</th>
                        </thead>

                        <body>
                            <tr>
                                <td><code>showWidget(flag)</code>&nbsp;&nbsp;</td>
                                <td>Show or hide widget depending on the boolean flag value</td>
                            </tr>
                            <tr>
                                <td><code>toggleWidget()</code></td>
                                <td>Toggle the widget depending on the current visibility state</td>
                            </tr>
                            <tr>
                                <td><code>isOpened()</code></td>
                                <td>Returns true or false depending on whether the widget support form is currently openend</td>
                            </tr>
                            <tr>
                                <td><code>release()</code></td>
                                <td>Should be called whenever your widget instance shall be released</td>
                            </tr>
                        </tbody>
                    </table>
                </p>

                <br/><br/><br/>
            </div>
        </div>
    </div>
@endsection
