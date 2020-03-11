<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'MainController@index');
Route::get('/home', 'MainController@index');
Route::get('/about', 'MainController@about');
Route::get('/faq', 'MainController@faq');
Route::get('/imprint', 'MainController@imprint');
Route::get('/tac', 'MainController@tac');
Route::get('/news', 'MainController@news');
Route::get('/{workspace}/index', 'MainController@workspaceIndex');
Route::post('/login', 'MainController@login');
Route::any('/logout', 'MainController@logout');
Route::post('/recover', 'MainController@recover');
Route::get('/reset', 'MainController@viewReset');
Route::post('/reset', 'MainController@reset');
Route::get('/register', 'MainController@viewRegister');
Route::post('/register', 'MainController@register');
Route::get('/confirm', 'MainController@confirm');

//Auth::routes();

Route::get('/{workspace}/agent/list', 'AgentController@viewList');
Route::get('/{workspace}/agent/{id}/show', 'AgentController@viewShow');
Route::get('/{workspace}/agent/create', 'AgentController@viewCreate');
Route::post('/{workspace}/agent/create', 'AgentController@createAgent');
Route::patch('/{workspace}/agent/{id}/edit', 'AgentController@editAgent');
Route::any('/{workspace}/agent/{id}/delete', 'AgentController@deleteAgent');
Route::patch('/{workspace}/agent/{id}/active/{status}', 'AgentController@setActiveStatus');
Route::patch('/{workspace}/agent/{agent}/group/{group}/add', 'AgentController@assignToGroup');
Route::any('/{workspace}/agent/{agent}/group/{group}/remove', 'AgentController@removeFromGroup');

Route::get('/{workspace}/ticket/create', 'TicketController@viewCreateTicket');
Route::post('/{workspace}/ticket/create', 'TicketController@createTicket');
Route::post('/{workspace}/ticket/create/agent', 'TicketController@createTicketAgent');
Route::post('/{workspace}/ticket/create/login', 'TicketController@createTicketLogin');
Route::get('/{workspace}/ticket/list', 'TicketController@viewTicketList');
Route::get('/{workspace}/ticket/{id}/show', 'TicketController@viewShowTicketAgent');
Route::get('/{workspace}/ticket/show/{hash}', 'TicketController@viewShowTicketClient');
Route::patch('/{workspace}/ticket/{id}/edit', 'TicketController@editTicket');
Route::delete('/{workspace}/ticket/{id}/delete', 'TicketController@deleteTicket');
Route::patch('/{workspace}/ticket/{ticket}/assign/agent/{agent}', 'TicketController@assignToAgent');
Route::patch('/{workspace}/ticket/{ticket}/assign/client/{client}', 'TicketController@assignToClient');
Route::patch('/{workspace}/ticket/{ticket}/assign/group/{group}', 'TicketController@assignToGroup');
Route::patch('/{workspace}/ticket/{id}/status/{status}', 'TicketController@setStatus');
Route::patch('/{workspace}/ticket/{id}/type/{type}', 'TicketController@setType');
Route::patch('/{workspace}/ticket/{id}/prio/{prio}', 'TicketController@setPriority');
Route::post('/{workspace}/ticket/{id}/comment/add', 'TicketController@addCommentAgent');
Route::post('/{workspace}/ticket/{id}/comment/add/guest', 'TicketController@addCommentCustomer');
Route::patch('/{workspace}/ticket/{id}/comment/{cmt}/edit', 'TicketController@editComment');
Route::patch('/{workspace}/ticket/{id}/comment/{cmt}/edit/customer', 'TicketController@editCommentCustomer');
Route::delete('/{workspace}/ticket/{id}/comment/{cmt}/delete', 'TicketController@deleteComment');
Route::post('/{workspace}/ticket/{hash}/file/add', 'TicketController@addFile');
Route::get('/{workspace}/ticket/search', 'TicketController@viewSearch');
Route::post('/{workspace}/ticket/search', 'TicketController@search');
Route::patch('/{workspace}/ticket/{id}/notes/save', 'TicketController@saveNotes');
Route::get('/{workspace}/ticket/{ticketHash}/file/{id}/get', 'TicketController@getAttachment');
Route::delete('/{workspace}/ticket/{ticketHash}/file/{id}/delete', 'TicketController@deleteAttachment');

Route::get('/{workspace}/group/list', 'GroupsController@listGroups');
Route::get('/{workspace}/group/create', 'GroupsController@viewCreateGroup');
Route::post('/{workspace}/group/create', 'GroupsController@createGroup');
Route::get('/{workspace}/group/{id}/show', 'GroupsController@viewGroup');
Route::patch('/{workspace}/group/{id}/edit', 'GroupsController@editGroup');
Route::any('/{workspace}/group/{id}/delete', 'GroupsController@deleteGroup');

Route::get('/{workspace}/faq/list', 'FaqController@viewList');
Route::get('/{workspace}/faq/create', 'FaqController@viewCreate');
Route::post('/{workspace}/faq/create', 'FaqController@create');
Route::get('/{workspace}/faq/{id}/edit', 'FaqController@viewEdit');
Route::patch('/{workspace}/faq/{id}/edit', 'FaqController@edit');
Route::any('/{workspace}/faq/{id}/delete', 'FaqController@delete');

Route::get('/{workspace}/settings', 'SettingsController@show');
Route::get('/{workspace}/settings/agent', 'SettingsController@showAgent');
Route::get('/{workspace}/settings/client', 'SettingsController@showClient');
Route::patch('/{workspace}/settings/save', 'SettingsController@save');
Route::patch('/{workspace}/settings/locale', 'SettingsController@saveLocale');
Route::patch('/{workspace}/settings/avatar', 'SettingsController@saveAvatar');
Route::get('/{workspace}/settings/system', 'SettingsController@viewSystemSettings');
Route::patch('/{workspace}/settings/system', 'SettingsController@saveSystemSettings');
Route::post('/{workspace}/settings/system/backgrounds/add', 'SettingsController@addBackgroundImage');
Route::any('/{workspace}/settings/system/backgrounds/delete/{name}', 'SettingsController@deleteBackgroundImage');
Route::post('/{workspace}/settings/system/cancel', 'SettingsController@cancelWorkspace');

Route::get('/install', 'InstallerController@viewInstall');
Route::post('/install', 'InstallerController@install');
