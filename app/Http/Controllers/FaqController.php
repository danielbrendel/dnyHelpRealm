<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2021 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use \App\FaqModel;
Use \App\AgentModel;
use \App\User;
use \App\WorkSpaceModel;

class FaqController extends Controller
{
    /**
     * View FAQ list
     *
     * @param string $workspace
     * @return mixed
     */
    public function viewList($workspace)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        if (!AgentModel::isSuperAdmin(User::getAgent(auth()->id())->id)) {
            return back()->with('error', __('app.superadmin_permission_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->where('deactivated', '=', false)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found_or_deactivated'));
        }

        return view('faq.list', [
            'workspace' => $ws->name,
            'user' => User::get(auth()->id()),
            'faqs' => FaqModel::where('workspace', '=', $ws->id)->get(),
            'superadmin' => User::getAgent(auth()->id())->superadmin,
            'location' => __('app.faq')
        ]);
    }

    /**
     * View FAQ creation form
     *
     * @param string $workspace
     * @return mixed
     */
    public function viewCreate($workspace)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->where('deactivated', '=', false)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found_or_deactivated'));
        }

        if (!AgentModel::isSuperAdmin(User::getAgent(auth()->id())->id)) {
            return back()->with('error', __('app.superadmin_permission_required'));
        }

        return view('faq.create', [
            'workspace' => $ws->name,
            'user' => User::get(auth()->id()),
            'superadmin' => User::getAgent(auth()->id())->superadmin,
            'location' => __('app.faq_create')
        ]);
    }

    /**
     * Create FAQ entry
     *
     * @param string $workspace
     * @return Illuminate\Http\RedirectResponse
     */
    public function create($workspace)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        if (!AgentModel::isSuperAdmin(User::getAgent(auth()->id())->id)) {
            return back()->with('error', __('app.superadmin_permission_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->where('deactivated', '=', false)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found_or_deactivated'));
        }

        $attr = request()->validate([
            'question' => 'required',
            'answer' => 'required|max:4096'
        ]);

        $attr['workspace'] = $ws->id;

        $data = FaqModel::create($attr);
        if ($data === null) {
            return back()->withInput()->with('error', __('app.faq_creation_failed'));
        }

        return back()->with('success', __('app.faq_created'));
    }

    /**
     * View FAQ edit form
     *
     * @param string $workspace
     * @param int $id
     * @return mixed
     */
    public function viewEdit($workspace, $id)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        if (!AgentModel::isSuperAdmin(User::getAgent(auth()->id())->id)) {
            return back()->with('error', __('app.superadmin_permission_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->where('deactivated', '=', false)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found_or_deactivated'));
        }

        $faq = FaqModel::where('id', '=', $id)->where('workspace', '=', $ws->id)->first();
        if ($faq === null) {
            return back()->with('error', __('app.faq_not_found'));
        }

        return view('faq.edit', [
            'workspace' => $ws->name,
            'user' => User::get(auth()->id()),
            'superadmin' => User::getAgent(auth()->id())->superadmin,
            'location' => __('app.faq_edit'),
            'faq' => $faq
        ]);
    }

    /**
     * Edit FAQ data
     *
     * @param string $workspace
     * @param int $id
     * @return mixed
     */
    public function edit($workspace, $id)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        if (!AgentModel::isSuperAdmin(User::getAgent(auth()->id())->id)) {
            return back()->with('error', __('app.superadmin_permission_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->where('deactivated', '=', false)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found_or_deactivated'));
        }

        $attr = request()->validate([
            'question' => 'required',
            'answer' => 'required|max:4096'
        ]);

        $faq = FaqModel::where('id', '=', $id)->where('workspace', '=', $ws->id)->first();
        if ($faq === null) {
            return back()->with('error', __('app.faq_not_found'));
        }

        $faq->question = $attr['question'];
        $faq->answer = $attr['answer'];
        $faq->save();

        return back()->with('success', __('app.faq_edited'));
    }

    /**
     * Delete FAQ entry
     *
     * @param string $workspace
     * @param int $id
     * @return mixed
     */
    public function delete($workspace, $id)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        if (!AgentModel::isSuperAdmin(User::getAgent(auth()->id())->id)) {
            return back()->with('error', __('app.superadmin_permission_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->where('deactivated', '=', false)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found_or_deactivated'));
        }

        $faq = FaqModel::where('id', '=', $id)->where('workspace', '=', $ws->id)->first();
        if ($faq === null) {
            return back()->with('error', __('app.faq_not_found'));
        }

        $faq->delete();

        return redirect('/' . $ws->name . '/faq/list')->with('success', __('app.faq_deleted'));
    }
}
