<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 0.1
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\TicketModel;
use App\GroupsModel;
use App\AgentModel;
use App\ClientModel;
use App\CaptchaModel;
use App\FaqModel;
use App\BgImagesModel;
use App\WorkSpaceModel;

/**
 * Class MainController
 * 
 * Perform general computations
 */
class MainController extends Controller
{
    /**
     * Return either agent dashboard or ticket creation dashboard
     * 
     * @param string $workspace
     * @return Illuminate\View\View
     */
    public function workspaceIndex($workspace)
    {
        $ws = WorkSpaceModel::where('name', '=', $workspace)->first();
        if ($ws === null) {
            return redirect('/')->with('error', __('app.workspace_not_found'));
        }

        if (Auth::guest()) {
            $img = BgImagesModel::queryRandomImage($ws->id);
            
            $captchadata = CaptchaModel::createSum(session()->getId());

            $infomessage = $ws->welcomemsg;
            if ($infomessage === '') {
                $infomessage = __('app.ticket_creation_welcomemsg');
            }
            $infomessage = strip_tags($infomessage, env('APP_ALLOWEDHTMLTAGS'));

            return view('dashboard_customer', ['workspace' => $ws->name, 'wsobject' => $ws, 'bgimage' => $img, 'captchadata' => $captchadata, 'faqs' => FaqModel::all(), 'infomessage' => $infomessage]);
        } else {
            $tickets = TicketModel::queryAgentTickets(User::getAgent(auth()->id())->id);
            $groups = array();
            foreach ($tickets as $ticket)
            {
                $item = array();
                $item['ticket_id'] = $ticket->id;
                $item['group_name'] = GroupsModel::get($ticket->group)->name;
                array_push($groups, $item);
            }

            $typeServiceRequest = TicketModel::where('type', '=', 1)->count();
            $typeIncident = TicketModel::where('type', '=', 2)->count();
            $typeChange = TicketModel::where('type', '=', 3)->count();

            return view('dashboard_agent', [
                'workspace' => $ws->name,
                'location' => __('app.dashboard'),
                'user' => User::get(auth()->id()),
                'agent' => User::getAgent(auth()->id()),
                'serving' => TicketModel::count(),
                'yours' => TicketModel::where('assignee', '=', User::getAgent(auth()->id())->id)->count(),
                'serviceRequests' => $typeServiceRequest,
                'incidents' => $typeIncident,
                'changes' => $typeChange,
                'groups' => GroupsModel::count(),
                'superadmin' => User::getAgent(auth()->id())->superadmin,
                'agents' => AgentModel::count(),
                'tickets' => $tickets,
                'groupnames' => $groups
            ]);
        }
    }

    /**
     * Default site landing page
     * 
     * @return Illuminate\View\View
     */
    public function index()
    {
        if (!Auth::guest()) {
            $ws = WorkSpaceModel::where('id', '=', User::get(auth()->id())->workspace)->first();
            return redirect('/' . $ws->name . '/index');
        }

        $captchadata = CaptchaModel::createSum(session()->getId());

        return view('home', ['captchadata' => $captchadata]);
    }

    /**
     * View news page
     * 
     * @return mixed
     */
    public function news()
    {
        if (!Auth::guest()) {
            $ws = WorkSpaceModel::where('id', '=', User::get(auth()->id()))->first();
            return redirect('/' . $ws->name . '/index');
        }

        $captchadata = CaptchaModel::createSum(session()->getId());

        return view('news', ['captchadata' => $captchadata]);
    }

    /**
     * View about page
     * 
     * @return mixed
     */
    public function about()
    {
        if (!Auth::guest()) {
            $ws = WorkSpaceModel::where('id', '=', User::get(auth()->id()))->first();
            return redirect('/' . $ws->name . '/index');
        }

        $captchadata = CaptchaModel::createSum(session()->getId());

        return view('about', ['captchadata' => $captchadata]);
    }

    /**
     * View faq page
     * 
     * @return mixed
     */
    public function faq()
    {
        if (!Auth::guest()) {
            $ws = WorkSpaceModel::where('id', '=', User::get(auth()->id()))->first();
            return redirect('/' . $ws->name . '/index');
        }

        $captchadata = CaptchaModel::createSum(session()->getId());

        return view('faq', ['captchadata' => $captchadata]);
    }

    /**
     * View imprint page
     * 
     * @return mixed
     */
    public function imprint()
    {
        if (!Auth::guest()) {
            $ws = WorkSpaceModel::where('id', '=', User::get(auth()->id()))->first();
            return redirect('/' . $ws->name . '/index');
        }

        $captchadata = CaptchaModel::createSum(session()->getId());

        return view('imprint', ['captchadata' => $captchadata]);
    }

    /**
     * View tac page
     * 
     * @return mixed
     */
    public function tac()
    {
        if (!Auth::guest()) {
            $ws = WorkSpaceModel::where('id', '=', User::get(auth()->id()))->first();
            return redirect('/' . $ws->name . '/index');
        }

        $captchadata = CaptchaModel::createSum(session()->getId());

        return view('tac', ['captchadata' => $captchadata]);
    }

    /**
     * Perform login
     * 
     * @return Illuminate\Http\RedirectResponse
     */
    public function login()
    {
        if (Auth::guest()) {
            $attr = request()->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('email', '=', $attr['email'])->first();
            if ($user !== null) {
                if ($user->account_confirm !== '_confirmed') {
                    return back()->with('error', __('app.account_not_yet_confirmed'));
                }
            }
            
            if (Auth::attempt([
                'email' => $attr['email'],
                'password' => $attr['password']
            ])) {
                $entity = User::getAgent(auth()->id());
                if ($entity) {
                    if (!$entity->active) {
                        Auth::logout();
                        request()->session()->invalidate();

                        return redirect('/')->with('error', __('app.agent_is_inactive'));
                    }
                }

                $ws = WorkSpaceModel::where('id', '=', $entity->workspace)->first();
                if ($ws === null) {
                    return back()->with('error', __('app.workspace_not_found'));
                }

                return redirect('/' . $ws->name . '/index');
            } else {
                return redirect('/')->with('error', __('app.invalid_credentials'));
            }
        } else {
            return redirect('/')->with('error', __('app.login_already_done'));
        }
    }

    /**
     * Perform logout
     * 
     * @return Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        if(Auth::check()) {
            $ws = WorkSpaceModel::where('id', '=', User::get(auth()->id())->workspace)->first();

            Auth::logout();
            request()->session()->invalidate();

            return  redirect('/')->with('success', __('app.logout_success'));
        } else {
            return  redirect('/')->with('error', __('app.not_logged_in'));
        }
    }

    /**
     * Send email with password recovery link to user
     * 
     * @return Illuminate\Http\RedirectResponse
     */
    public function recover()
    {
        $attr = request()->validate([
            'email' => 'required|email'
        ]);

        $user = User::getByEmail($attr['email']);
        if (!$user) {
            return back()->with('error', 'email_not_found');
        }

        $entity = User::getAgent($user->id);

        $user->password_reset = md5($user->email . date('c') . uniqid('', true));
        $user->save();

        $htmlCode = view('mail.pwreset', ['name' => $entity->firstname . ' ' . $entity->lastname, 'hash' => $user->password_reset])->render();
        @mail($user->email, '[' . env('APP_NAME') . '] ' . __('app.mail_password_reset_subject'), wordwrap($htmlCode, 70), 'Content-type: text/html; charset=utf-8' . "\r\n");

        return back()->with('success', __('app.pw_recovery_ok'));
    }

    /**
     * Password reset view
     * 
     * @return Illuminate\View\View
     */
    public function viewReset()
    {
        $img = 'bg' . random_int(1, 4) . '.jpg';

        return view('resetpw', [
            'bgimage' => $img,
            'hash' => request('hash'),
            'workspace' => ''
        ]);
    }

    /**
     * Reset new password
     * 
     * @return Illuminate\Http\RedirectResponse
     */
    public function reset()
    {
        $attr = request()->validate([
            'password' => 'required',
            'password_confirm' => 'required'
        ]);

        if ($attr['password'] != $attr['password_confirm']) {
            return back()->with('error', __('app.password_mismatch'));
        }

        $user = User::where('password_reset', '=', request('hash'))->first();
        if (!$user) {
            return redirect('/')->with('error', __('app.hash_not_found'));
        }

        $user->password = password_hash($attr['password'], PASSWORD_BCRYPT);
        $user->save();

        return redirect('/')->with('success', __('app.password_reset_ok'));
    }

    /**
     * Process registration
     * 
     * @return mixed
     */
    public function register()
    {
        $attr = request()->validate([
            'company' => 'required',
            'fullname' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'password_confirmation' => 'required',
            'captcha' => 'required|numeric'
        ]);

        $attr['lang'] = 'en';
        $attr['usebgcolor'] = false;
        $attr['bgcolorcode'] = 'F5F5F6';
        $attr['welcomemsg'] = __('app.system_welcomemsg');

        $attr['name'] = md5($attr['fullname'] . $attr['email'] . random_bytes(55));

        $workspace = WorkSpaceModel::get($attr['name']);
        if ($workspace !== null) {
            return back()->with('error', __('app.workspace_already_exists'));
        }

        $emailuser = User::getByEmail($attr['email']);
        if ($emailuser !== null) {
            return back()->with('error', __('app.email_already_in_use'));
        }

        if ($attr['captcha'] !== CaptchaModel::querySum(session()->getId())) {
            return back()->with('error', __('app.captcha_invalid'));
        }

        if ($attr['password'] !== $attr['password_confirmation']) {
            return back()->with('error', __('app.password_mismatch'));
        }

        $workspace = WorkSpaceModel::create($attr);
        if ($workspace === null) {
            return back()->with('error', __('app.workspace_creation_failed'));
        }

        $user = new \App\User;
        $user->workspace = $workspace->id;
        $user->name = 'admin';
        $user->email = $attr['email'];
        $user->password = password_hash($attr['password'], PASSWORD_BCRYPT);
        $user->account_confirm = md5($user->email . date('Y-m-d H:i:s') . random_bytes(55));
        $user->avatar = 'default.png';
        $user->user_id = 0;
        $user->language = 'en';
        $user->save();

        if (strpos($attr['name'], ' ') !== false) {
            $surname = substr($attr['fullname'], 0, strpos($attr['fullname'], ' '));
            $lastname = substr($attr['fullname'], strpos($attr['fullname'], ' ') + 1);
        } else {
            $surname = '';
            $lastname = $attr['fullname'];
        }

        $agent = new \App\AgentModel;
        $agent->workspace = $workspace->id;
        $agent->surname = $surname;
        $agent->lastname = $lastname;
        $agent->email = $attr['email'];
        $agent->superadmin = true;
        $agent->position = 'Administrator';
        $agent->user_id = $user->id;
        $agent->save();

        $user->user_id = $agent->id;
        $user->save();

        $group = new \App\GroupsModel;
        $group->workspace = $workspace->id;
        $group->name = '1st level group';
        $group->description = 'The primary group';
        $group->def = true;
        $group->save();

        $groupMember = new \App\AgentsHaveGroups;
        $groupMember->agent_id = $agent->id;
        $groupMember->group_id = $group->id;
        $groupMember->save();

        $htmlCode = view('mail.workspace_created', ['name' => $attr['fullname'], 'hash' => $user->account_confirm])->render();
        @mail($attr['email'], '[' . env('APP_NAME') . '] Your Workspace', wordwrap($htmlCode, 70), 'Content-type: text/html; charset=utf-8' . "\r\n");

        return redirect('/')->with('success', __('app.signup_welcomemsg'));
    }

    /**
     * Confirm account
     */
    public function confirm()
    {
        $hash = request('hash');

        $user = User::where('account_confirm', '=', $hash)->first();
        if ($user === null) {
            return back()->with('error', __('app.account_confirm_token_not_found'));
        }

        $user->account_confirm = '_confirmed';
        $user->save();

        return redirect('/')->with('success', __('app.account_confirmed_ok'));
    }
}
