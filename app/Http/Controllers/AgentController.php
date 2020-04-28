<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use \App\AgentModel;
use Auth;
use \App\AgentsHaveGroups;
use \App\GroupsModel;
use \App\WorkSpaceModel;

/**
 * Class AgentController
 * 
 * Handle agent specific computations
 */
class AgentController extends Controller
{
    /**
     * View list of agents
     * 
     * @param string $workspace
     * @return \Illuminate\View\View
     */
    public function viewList($workspace)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found'));
        }

        return view('agents.list', [
            'workspace' => $ws->name,
            'user' => User::get(auth()->id()),
            'agents' => AgentModel::where('workspace', '=', $ws->id)->get(),
            'superadmin' => User::getAgent(auth()->id())->superadmin,
            'location' => __('app.agent_list')
        ]);
    }

    /**
     * View specific agent data
     * 
     * @param string $workspace
     * @return \Illuminate\View\View
     */
    public function viewShow($workspace, $id)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found'));
        }

        $agent = AgentModel::where('id', '=', $id)->where('workspace', '=', $ws->id)->first();
        if (!$agent) {
            return redirect('/' . $ws->name . '/index')->with('error', __('app.agent_not_found'));
        }

        $groups = array();
        foreach (AgentsHaveGroups::where('agent_id', '=', $id)->get() as $item) {
            $entry = array();
            $entry['group'] = $item;
            $entry['data'] = GroupsModel::where('id', '=', $item->group_id)->first();
            array_push($groups, $entry);
        }

        return view('agents.show', [
            'workspace' => $ws->name,
            'location' => __('app.agent_show'),
            'user' => User::get(auth()->id()),
            'agent' => $agent,
            'groups' => $groups,
            'allgroups' => GroupsModel::where('workspace', '=', $ws->id)->get(),
            'superadmin' => User::getAgent(auth()->id())->superadmin
        ]);
    }

    /**
     * Show creation view
     * 
     * @param string $workspace
     * @return \Illuminate\View\View
     */
    public function viewCreate($workspace)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found'));
        }

        return view('agents.create', [
            'workspace' => $ws->name,
            'location' => __('app.agent_create'),
            'user' => User::get(auth()->id()),
            'superadmin' => User::getAgent(auth()->id())->superadmin
        ]);
    }

    /**
     * Create new agent
     * 
     * @param string $workspace
     * @return Illuminate\Http\RedirectResponse
     */
    public function createAgent($workspace)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        if (!AgentModel::isSuperAdmin(User::getAgent(auth()->id())->id)) {
            return back()->with('error', __('app.superadmin_permission_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found'));
        }

        $attr = request()->validate([
            'surname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email',
            'position' => 'required',
            'superadmin' => 'nullable|numeric',
            'password' => 'required',
            'password_confirm' => 'required'
        ]);

        if ($attr['password'] != $attr['password_confirm']) {
            return back()->with('error', __('app.agent_password_mismatch'));
        }

        $attr['user_id'] = 0;
        $attr['workspace'] = $ws->id;

        $pw = $attr['password'];
        unset($attr['password']);
        unset($attr['password_confirm']);
        $data = AgentModel::create($attr);
        
        $userdata = new \App\User;
        $userdata->workspace = $ws->id;
        $userdata->name = $data->surname . ' ' . $data->lastname;
        $userdata->email = $data->email;
        $userdata->user_id = $data->id;
        $userdata->password = password_hash($pw, PASSWORD_BCRYPT);
        $userdata->account_confirm = '';
        $userdata->avatar = 'default.png';
        $userdata->save();

        $data->user_id = $userdata->id;
        $data->active = true;
        $data->save();

        $htmlCode = view('mail.account_created', ['workspace' => $ws->name, 'name' => $userdata->name, 'password' => $pw])->render();
        @mail($data->email, '[' . $ws->company . '] ' . __('app.account_created'), wordwrap($htmlCode, 70), 'Content-type: text/html; charset=utf-8' . "\r\nFrom: " . env('APP_NAME') . " " . env('MAILSERV_EMAILADDR') . "\r\nReply-To: " . env('MAILSERV_EMAILADDR') . "\r\n");

        return back()->with('success', __('app.agent_created'));
    }

    /**
     * Edit agent data
     * 
     * @param string $workspace
     * @param $id
     * @return Illuminate\Http\RedirectResponse
     */
    public function editAgent($workspace, $id)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found'));
        }

        if (!AgentModel::isSuperAdmin(User::getAgent(auth()->id())->id)) {
            return back()->with('error', __('app.superadmin_permission_required'));
        }
        
        $attr = request()->validate([
            'surname' => 'nullable',
            'lastname' => 'nullable',
            'email' => 'email|nullable',
            'position' => 'nullable',
            'superadmin' => 'nullable|numeric',
            'active' => 'nullable|numeric',
            'password' => 'nullable',
            'password_confirm' => 'nullable'
        ]);
        
        if (isset($attr['password']) && $attr['password'] != null) {
            if (!isset($attr['password_confirm']) || $attr['password'] !== $attr['password_confirm']) {
                return back()->with('error', __('app.password_mismatch'));
            }
        }
        
        $agent = AgentModel::where('id', '=', $id)->where('workspace', '=', $ws->id)->first();
        if ($agent) {
            if ($attr['surname'] != null) $agent->surname = $attr['surname'];
            if ($attr['lastname'] != null) $agent->lastname = $attr['lastname'];
            if ($attr['email'] != null) $agent->email = $attr['email'];
            if ($attr['position'] != null) $agent->position = $attr['position'];
            $agent->superadmin = (isset($attr['superadmin']) && $attr['superadmin'] !== 0) ? true : false;
            $agent->active = (isset($attr['active']) && $attr['active'] != null) ? true : false;
            $agent->save();

            $user = User::where('user_id', '=', $id)->first();
            if (isset($attr['email']) && $attr['email'] != null) $user->email = $attr['email'];
            if (isset($attr['password']) && $attr['password'] != null) $user->password = password_hash($attr['password'], PASSWORD_BCRYPT);
            $user->save();

            return back()->with('success', __('app.agent_data_saved'));
        } else {
            return back()->with('error', __('app.agent_not_found'));
        }
    }

    /**
     * Delete agent
     * 
     * @param string $workspace
     * @return Illuminate\Http\RedirectResponse
     */
    public function deleteAgent($workspace, $id)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        if (!AgentModel::isSuperAdmin(User::getAgent(auth()->id())->id)) {
            return back()->with('error', __('app.superadmin_permission_required'));
        }

        if (User::getAgent(auth()->id())->id == $id) {
            return back()->with('error', __('app.may_not_delete_self'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found'));
        }
        
        $agent = AgentModel::where('id', '=', $id)->where('workspace', '=', $ws->id);
        $user = User::where('user_id', '=', $id)->where('workspace', '=', $ws->id);

        if (($agent) && ($user)) {
            $groups = AgentsHaveGroups::where('agent_id', '=', $agent->first()->id);
            $groups->delete();

            $agent->delete();
            $user->delete();

            return redirect('/' . $ws->name . '/agent/list')->with('success', __('app.agent_deleted'));
        } else {
            return back()->with('error', __('app.agent_not_found'));
        }
    }

    /**
     * Set activation status
     * 
     * @param string $workspace
     * @return Illuminate\Http\RedirectResponse
     */
    public function setActiveStatus($workspace, $id, $status)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        if (!AgentModel::isSuperAdmin(User::getAgent(auth()->id())->id)) {
            return back()->with('error', __('app.superadmin_permission_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found'));
        }

        $agent = AgentModel::where('id', '=', $id)->where('workspace', '=', $ws->id)->first();
        if ($agent) {
            $agent->active = $status;
            $agent->save();
        }

        return back()->with('success', __('app.agent_status_set'));
    }

    /**
     * Assign agent to group
     * 
     * @param string $workspace
     * @param $agent
     * @param $group
     * @return Illuminate\Http\RedirectResponse
     */
    public function assignToGroup($workspace, $agent, $group)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        if (!AgentModel::isSuperAdmin(User::getAgent(auth()->id())->id)) {
            return back()->with('error', __('app.superadmin_permission_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found'));
        }

        $groupData = GroupsModel::where('workspace', '=', $ws->id)->where('id', '=', $group)->first();
        if ($groupData === null) {
            return back()->with('error', __('app.group_not_found'));
        }

        if (AgentsHaveGroups::putAgentInGroup($agent, $group)) {
            return back()->with('success', __('app.agentgroup_added_successfully'));
        } else {
            return back()->with('error', __('app.agentgroup_failed_to_add'));
        }
    }

    /**
     * Remove agent from group
     * 
     * @param string $workspace
     * @param $agent
     * @param $group
     * @return Illuminate\Http\RedirectResponse
     */
    public function removeFromGroup($workspace, $agent, $group)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        if (!AgentModel::isSuperAdmin(User::getAgent(auth()->id())->id)) {
            return back()->with('error', __('app.superadmin_permission_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found'));
        }

        $groupData = GroupsModel::where('workspace', '=', $ws->id)->where('id', '=', $group)->first();
        if ($groupData === null) {
            return back()->with('error', __('app.group_not_found'));
        }

        if (AgentsHaveGroups::removeAgentFromGroup($agent, $group)) {
            return back()->with('success', __('app.agentgroup_removed_successfully'));
        } else {
            return back()->with('error', __('app.agentgroup_failed_to_remove'));
        }
    }
}
