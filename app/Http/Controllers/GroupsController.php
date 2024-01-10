<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2024 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\GroupsModel;
use \App\User;
use Auth;
use \App\AgentModel;
use \App\TicketModel;
use \App\AgentsHaveGroups;
use \App\WorkSpaceModel;

/**
 * Class GroupsController
 *
 * Handle group related computations
 */
class GroupsController extends Controller
{
    /**
     * List all groups
     *
     * @param string $workspace
     * @return Illuminate\View\View
     */
    public function listGroups($workspace)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->where('deactivated', '=', false)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found_or_deactivated'));
        }

        return view('groups.list', [
            'workspace' => $ws->name,
            'user' => User::get(auth()->id()),
            'groups' => GroupsModel::where('workspace', '=', $ws->id)->get(),
            'superadmin' => User::getAgent(auth()->id())->superadmin,
            'location' => __('app.groups')
        ]);
    }

    /**
     * Show group creation dialog
     *
     * @param string $workspace
     * @return Illuminate\View\View
     */
    public function viewCreateGroup($workspace)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->where('deactivated', '=', false)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found_or_deactivated'));
        }

        return view('groups.create', [
            'workspace' => $ws->name,
            'location' => __('app.group_create'),
            'user' => User::get(auth()->id()),
            'superadmin' => User::getAgent(auth()->id())->superadmin
        ]);
    }

    /**
     * Create new group
     *
     * @param string $workspace
     * @return Illuminate\Http\RedirectResponse
     */
    public function createGroup($workspace)
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
            'name' => 'required|min:5',
            'description' => 'required|min:5',
            'def' => 'nullable|numeric'
        ]);

        $attr['workspace'] = $ws->id;

        if (!isset($attr['def'])) $attr['def'] = 0;

        $check = GroupsModel::where('name', '=', $attr['name'])->where('workspace', '=', $ws->id)->first();
        if ($check) {
            return back()->with('error', __('app.group_already_exists'));
        }

        $group = GroupsModel::create($attr);
        if ($group) {
            return redirect('/' . $ws->name . '/group/' . $group->id . '/show')->with('success', __('app.group_created'));
        } else {
            return back()->with('error', __('app.group_creation_failure'));
        }
    }

    /**
     * View specific group
     *
     * @param string $workspace
     * @param $id
     * @return mixed
     */
    public function viewGroup($workspace, $id)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->where('deactivated', '=', false)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found_or_deactivated'));
        }

        $group = GroupsModel::where('id', '=', $id)->where('workspace', '=', $ws->id)->first();
        if (!$group) {
            return back()->with('error', __('app.group_not_found'));
        }

        $allagents = AgentModel::where('workspace', '=', $ws->id)->get();

        $groupagents = array();
        $agingroups = AgentsHaveGroups::where('group_id', '=', $id)->get();
        foreach ($agingroups as $ag) {
            $item = AgentModel::where('id', '=', $ag->agent_id)->first();
            array_push($groupagents, $item);
        }

        return view('groups.view', [
            'workspace' => $ws->name,
            'location' => __('app.group_view'),
            'user' => User::get(auth()->id()),
            'group' => $group,
            'superadmin' => User::getAgent(auth()->id())->superadmin,
            'groupagents' => $groupagents,
            'allagents' => $allagents
        ]);
    }

    /**
     * Edit group data
     *
     * @param string $workspace
     * @param $id
     * @return Illuminate\Http\RedirectResponse
     */
    public function editGroup($workspace, $id)
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
            'name' => 'nullable|min:5',
            'description' => 'nullable',
            'def' => 'numeric'
        ]);

        if (!isset($attr['def'])) {
            $attr['def'] = false;
        }

        $group = GroupsModel::where('id', '=', $id)->where('workspace', '=', $ws->id)->first();
        if ($group) {
            if (isset($attr['name'])) $group->name = $attr['name'];
            if (isset($attr['description'])) $group->description = $attr['description'];
            $group->def = (bool)$attr['def'];
            $group->save();

            return back()->with('success', __('app.group_data_stored'));
        } else {
            return back()->with('error', __('app.group_not_found'));
        }
    }

    /**
     * Delete a group
     *
     * @param string $workspace
     * @param $id
     * @return Illuminate\Http\RedirectResponse
     */
    public function deleteGroup($workspace, $id)
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

        $hastickets = TicketModel::where('workspace', '=', $ws->id)->where('group', '=', $id)->where('status', '<>', '3')->first();
        if ($hastickets) {
            return back()->with('error', __('app.group_not_empty'));
        }

        $group = GroupsModel::where('id', '=', $id)->where('workspace', '=', $ws->id)->first();
        if ($group) {
            $group->delete();

            return redirect('/' . $ws->name . '/group/list')->with('success', __('app.group_deleted'));
        } else {
            return back()->with('error', __('app.group_not_found'));
        }
    }
}
