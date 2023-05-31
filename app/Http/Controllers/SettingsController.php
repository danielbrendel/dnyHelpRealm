<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use \App\User;
use \App\AgentModel;
use \App\BgImagesModel;
use \App\WorkSpaceModel;
use \App\CaptchaModel;
use \App\GroupsModel;
use \App\TicketModel;
use \App\FaqModel;
use \App\TicketsHaveFiles;
use \App\AgentsHaveGroups;
use \App\TicketThreadModel;
use \App\TicketsHaveTypes;

/**
 * Class SettingsController
 *
 * Perform settings specific computations
 */
class SettingsController extends Controller
{
    /**
     * Redirect depending on entity type
     *
     * @param string $workspace
     * @return Illuminate\Http\RedirectResponse
     */
    public function show($workspace)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        return redirect('/' . $workspace . '/settings/agent');
    }

    /**
     * Show agent settings view
     *
     * @param string $workspace
     * @return mixed
     */
    public function showAgent($workspace)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->where('deactivated', '=', false)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found_or_deactivated'));
        }

        $langs = array();
        $dirs = scandir(base_path() . '/resources/lang');
        foreach ($dirs as $dir) {
            if ($dir[0] != '.') {
                array_push($langs, $dir);
            }
        }

        $agent = User::getAgent(auth()->id());

        return view('settings.agent', [
            'workspace' => $ws->name,
            'location' => __('app.settings'),
            'user' => User::get(auth()->id()),
            'agent' => $agent,
            'lang' => \App::getLocale(),
            'langs' => $langs,
            'superadmin' => $agent->superadmin
        ]);
    }

    /**
     * Save settings
     *
     * @param string $workspace
     * @return Illuminate\Http\RedirectResponse
     */
    public function save($workspace)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        $attr = request()->validate([
            'surname' => 'min:2',
            'lastname' => 'min:2',
            'email' => 'email',
            'password' => 'nullable',
            'password_confirm' => 'nullable'
        ]);

        $user = User::get(auth()->id());
        $agent = User::getAgent(auth()->id());

        if (isset($attr['email'])) { $user->email = $attr['email']; $agent->email = $attr['email']; }
        if (isset($attr['surname'])) $agent->surname = $attr['surname'];
        if (isset($attr['lastname'])) $agent->lastname = $attr['lastname'];
        if (isset($attr['password']) && $attr['password'] != null) {
            if ($attr['password'] != $attr['password_confirm']) {
                return back()->with('error', __('app.settings_password_mismatch'));
            }

            $user->password = password_hash($attr['password'], PASSWORD_BCRYPT);
        }

        $user->save();
        $agent->save();

        return back()->with('success', __('app.settings_saved'));
    }

    /**
     * Get image type
     *
     * @param string $ext The image file extension
     * @param string $file The path to the image file
     * @return int|null Image type identifier or null if not found
     */
    private function GetImageType($ext, $file)
    {
        $imagetypes = array(
            array('png', IMAGETYPE_PNG),
            array('jpg', IMAGETYPE_JPEG),
            array('jpeg', IMAGETYPE_JPEG),
            array('gif', IMAGETYPE_GIF)
        );

        for ($i = 0; $i < count($imagetypes); $i++) {
            if ($ext == $imagetypes[$i][0]) {
                if (exif_imagetype($file) == $imagetypes[$i][1])
                    return $imagetypes[$i][1];
            }
        }

        return null;
    }

    /**
     * Save avatar
     *
     * @param string $workspace
     * @return Illuminate\Http\RedirectResponse
     */
    public function saveAvatar($workspace)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        $attr = request()->validate([
            'avatar' => 'required|file'
        ]);

        $av = request()->file('avatar');
        if ($av != null) {
            $av->move(base_path() . '/public/gfx/avatars', 'tmp.' . $av->getClientOriginalExtension());

            list($width, $height) = getimagesize(base_path() . '/public/gfx/avatars/tmp.' . $av->getClientOriginalExtension());

			$avimg = imagecreatetruecolor(64, 64);
			if (!$avimg)
				return false;

            $srcimage = null;
            $newname =  md5_file(base_path() . '/public/gfx/avatars/tmp.' . $av->getClientOriginalExtension()) . '.' . $av->getClientOriginalExtension();
			switch ($this->GetImageType($av->getClientOriginalExtension(), base_path() . '/public/gfx/avatars/tmp.' . $av->getClientOriginalExtension())) {
				case IMAGETYPE_PNG:
					$srcimage = imagecreatefrompng(base_path() . '/public/gfx/avatars/tmp.' . $av->getClientOriginalExtension());
					imagecopyresampled($avimg, $srcimage, 0, 0, 0, 0, 64, 64, $width, $height);
					imagepng($avimg, base_path() . '/public/gfx/avatars/' . $newname);
					break;
				case IMAGETYPE_JPEG:
					$srcimage = imagecreatefromjpeg(base_path() . '/public/gfx/avatars/tmp.' . $av->getClientOriginalExtension());
					imagecopyresampled($avimg, $srcimage, 0, 0, 0, 0, 64, 64, $width, $height);
					imagejpeg($avimg, base_path() . '/public/gfx/avatars/' . $newname);
					break;
				default:
					return back()->with('error', __('app.settings_avatar_invalid_image_type'));
					break;
			}

            unlink(base_path() . '/public/gfx/avatars/tmp.' . $av->getClientOriginalExtension());

            $user = User::get(auth()->id());
            $user->avatar = $newname;
            $user->save();

            return back()->with('success', __('app.settings_saved'));
        }

        return back()->with('error', __('app.settings_avatar_not_given'));
    }

    /**
     * Save ticket settings
     *
     * @param string $workspace
     * @return Illuminate\Http\RedirectResponse
     */
    public function saveTicketSettings($workspace)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        $attr = request()->validate([
            'mailonticketingroup' => 'nullable|numeric',
            'hideclosedtickets' => 'nullable|numeric',
            'signature' => 'nullable|max:4096'
        ]);

        $user = User::get(auth()->id());
        $agent = User::getAgent(auth()->id());

        if (isset($attr['mailonticketingroup'])) $agent->mailonticketingroup = $attr['mailonticketingroup']; else $agent->mailonticketingroup = false;
        if (isset($attr['hideclosedtickets'])) $agent->hideclosedtickets = $attr['hideclosedtickets']; else $agent->hideclosedtickets = false;
        if (isset($attr['signature'])) $agent->signature = $attr['signature']; else $agent->signature = '';

        $user->save();
        $agent->save();

        return back()->with('success', __('app.settings_saved'));
    }

    /**
     * Store locale
     *
     * @param string $workspace
     * @return Illuminate\Http\RedirectResponse
     */
    public function saveLocale($workspace)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return back()->with('error', __('app.login_required'));
        }

        $attr = request()->validate([
            'lang' => 'required'
        ]);

        \App::setLocale($attr['lang']);

        $user = User::get(auth()->id());
        $user->language = $attr['lang'];
        $user->save();

        return back()->with('success', __('app.settings_saved'));
    }

    /**
     * Show system settings view
     *
     * @param string $workspace
     * @return mixed
     */
    public function viewSystemSettings($workspace)
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

        $langs = array();
        $dirs = scandir(base_path() . '/resources/lang');
        foreach ($dirs as $dir) {
            if ($dir[0] != '.') {
                array_push($langs, $dir);
            }
        }

        $infomessage = $ws->welcomemsg;
        if ($infomessage === '') {
            $infomessage = __('app.ticket_creation_welcomemsg');
        }
		
		$firstTicketCreatedAt = \App\TicketModel::getFirstTicket($ws->id);
		if ($firstTicketCreatedAt === null) {
			$firstTicketCreatedAt = date('Y-m-d');
		} else {
			$firstTicketCreatedAt = $firstTicketCreatedAt->created_at;
		}

        return view('settings.system', [
            'workspace' => $ws->name,
            'location' => __('app.system_settings'),
            'user' => User::get(auth()->id()),
            'agent' => User::getAgent(auth()->id()),
            'superadmin' => User::getAgent(auth()->id())->superadmin,
            'company' => $ws->company,
            'lang' => $ws->lang,
            'apitoken' => $ws->apitoken,
            'usebgcolor' => $ws->usebgcolor,
            'bgcolorcode' => $ws->bgcolorcode,
            'langs' => $langs,
            'bgs' => BgImagesModel::getAllBackgrounds($ws->id),
            'infomessage' => $ws->welcomemsg,
            'formtitle' => $ws->formtitle,
            'ticketcreatedmsg' => $ws->ticketcreatedmsg,
            'allowattachments' => $ws->allowattachments,
            'extfilter' => $ws->extfilter,
            'emailconfirm' => $ws->emailconfirm,
            'formactions' => $ws->formactions,
            'ws' => $ws,
            'ticketTypes' => TicketsHaveTypes::where('workspace', '=', $ws->id)->get(),
            'captchadata' => CaptchaModel::createSum(session()->getId()),
			'export_from_date' => $firstTicketCreatedAt
        ]);
    }

    /**
     * Save system settings
     *
     * @param string $workspace
     * @return Illuminate\Http\RedirectResponse
     */
    public function saveSystemSettings($workspace)
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
            'company' => 'nullable',
            'lang' => 'nullable',
            'usebgcolor' => 'numeric|nullable',
            'bgcolorcode' => 'nullable',
            'infomessage' => 'nullable',
            'formtitle' => 'nullable',
            'ticketcreatedmsg' => 'nullable',
            'emailconfirm' => 'numeric|nullable',
            'inform_admin_new_ticket' => 'numeric|nullable',
            'formactions' => 'numeric|nullable',
            'allowattachments' => 'numeric|nullable',
            'extfilter' => 'nullable'
        ]);

        if (!isset($attr['usebgcolor'])) {
            $attr['usebgcolor'] = false;
        }

        if (!isset($attr['emailconfirm'])) {
            $attr['emailconfirm'] = false;
        }

        if (!isset($attr['allowattachments'])) {
            $attr['allowattachments'] = false;
        }

        if (!isset($attr['formactions'])) {
            $attr['formactions'] = false;
        }

        if (!isset($attr['inform_admin_new_ticket'])) {
            $attr['inform_admin_new_ticket'] = false;
        }

        if (!isset($attr['bgcolorcode'])) {
            $attr['bgcolorcode'] = '#E5E5E6';
        }

        if ((isset($attr['company'])) && (trim(strtolower($attr['company'])) !== trim(strtolower($ws->company)))) {
            $ws->slug = \Str::slug($attr['company'] . '-' . strval($ws->id) . strval(rand(10, 100)));
        }

        if (isset($attr['company'])) $ws->company = $attr['company'];
        if (isset($attr['lang'])) $ws->lang = $attr['lang'];
        if (isset($attr['usebgcolor'])) $ws->usebgcolor = (bool)$attr['usebgcolor'];
        if (isset($attr['bgcolorcode'])) $ws->bgcolorcode = $attr['bgcolorcode'];
        if (isset($attr['emailconfirm'])) $ws->emailconfirm = (bool)$attr['emailconfirm'];
        if (isset($attr['formactions'])) $ws->formactions = (bool)$attr['formactions'];
        if (isset($attr['infomessage'])) $ws->welcomemsg = $attr['infomessage'];
        if (isset($attr['formtitle'])) $ws->formtitle = $attr['formtitle'];
        if (isset($attr['ticketcreatedmsg'])) $ws->ticketcreatedmsg = $attr['ticketcreatedmsg'];
        if (isset($attr['allowattachments'])) $ws->allowattachments = (bool)$attr['allowattachments'];
        if (isset($attr['inform_admin_new_ticket'])) $ws->inform_admin_new_ticket = (bool)$attr['inform_admin_new_ticket'];
        if (isset($attr['extfilter'])) $ws->extfilter = $attr['extfilter'];

        $ws->save();

        return back()->with('success', __('app.settings_saved'));
    }

    /**
     * Add background image
     *
     * @param string $workspace
     * @return Illuminate\Http\RedirectResponse
     */
    public function addBackgroundImage($workspace)
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

        request()->validate(['image' => 'file|required']);

        $att = request()->file('image');
        if ($att != null) {
            $fname = $att->getClientOriginalName() . '_' . uniqid('', true) . '_' . md5($att->getClientOriginalName());
            $fext = $att->getClientOriginalExtension();
            $att->move(public_path() . '/gfx/backgrounds/', $fname . '.' . $fext);
            if (!BgImagesModel::isValidImage(public_path() . '/gfx/backgrounds/' . $fname . '.' . $fext)) {
                unlink(public_path() . '/gfx/backgrounds/', $fname . '.' . $fext);
                return back()->with('error', __('app.invalid_image'));
            }

            $dbentry = new BgImagesModel();
            $dbentry->workspace = $ws->id;
            $dbentry->file = $fname . '.' . $fext;
            $dbentry->save();
        }

        return back()->with('success', __('app.file_uploaded'));
    }

    /**
     * Delete background image
     *
     * @param string $workspace
     * @param string $filename
     * @return Illuminate\Http\RedirectResponse
     */
    public function deleteBackgroundImage($workspace, $filename)
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

        if (file_exists(public_path() . '/gfx/backgrounds/' . $filename) === false) {
            return back()->with('error', __('app.file_not_found'));
        }

        if (($filename === '.') || (strpos($filename, '..') !== false)) {
            return back()->with('error', __('app.invalid_file'));
        }

        if (BgImagesModel::isValidImage(public_path() . '/gfx/backgrounds/' . $filename) === false) {
            return back()->with('error', __('app.invalid_image'));
        }

        $item = BgImagesModel::where('workspace', '=', $ws->id)->where('file', '=', $filename)->first();
        if ($item === null) {
            return back()->with('error', __('app.file_not_found'));
        }

        unlink(public_path() . '/gfx/backgrounds/' . $item->file);

        $item->delete();

        return back()->with('success', __('app.file_deleted'));
    }

    /**
     * Add ticket type
     *
     * @param string $workspace
     * @return mixed
     */
    public function addTicketType($workspace)
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

        $attr = request()->validate([
            'name' => 'required'
        ]);

        $attr['workspace'] = $ws->id;

        $data = TicketsHaveTypes::create($attr);
        if ($data === null) {
            return back()->with('error', __('app.ticket_type_add_failed'));
        }

        return back()->with('success', __('app.ticket_type_added'));
    }

    /**
     * Edit ticket type
     *
     * @param string $workspace
     * @param int $id
     * @return mixed
     */
    public function editTicketType($workspace, $id)
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

        $attr = request()->validate([
            'name' => 'required'
        ]);

        $ticketType = TicketsHaveTypes::where('workspace', '=', $ws->id)->where('id', '=', $id)->first();
        if ($ticketType === null) {
            return back()->with('error', __('app.ticket_type_not_found'));
        }

        $ticketType->name = $attr['name'];
        $ticketType->save();

        return back()->with('success', __('app.ticket_type_edited'));
    }

    /**
     * Remove ticket type
     *
     * @param string $workspace
     * @param int $id
     * @return mixed
     */
    public function deleteTicketType($workspace, $id)
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

        $ttCount = TicketsHaveTypes::where('workspace', '=', $ws->id)->count();
        if ($ttCount === 1) {
            return back()->with('error', __('app.ticket_type_min_one_type'));
        }

        $ticketType = TicketsHaveTypes::where('id', '=', $id)->where('workspace', '=', $ws->id)->first();
        if ($ticketType === null) {
            return back()->with('error', __('app.ticket_type_not_found'));
        }

        $ticketType->delete();

        return back()->with('success', __('app.ticket_type_deleted'));
    }

    /**
     * Cancel workspace
     *
     * @param string $workspace
     * @return mixed
     */
    public function cancelWorkspace($workspace)
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

        $attr = request()->validate(['captcha' => 'required|numeric']);

        if ($attr['captcha'] !== CaptchaModel::querySum(session()->getId())) {
            return back()->with('error', __('app.captcha_invalid'));
        }

        $agents = AgentModel::where('workspace', '=', $ws->id);
        $groups = GroupsModel::where('workspace', '=', $ws->id);
        $faqs = FaqModel::where('workspace', '=', $ws->id);
        $tickets = TicketModel::where('workspace', '=', $ws->id);
        $users = User::where('workspace', '=', $ws->id);

        foreach ($tickets->get() as $ticket) {
            $files = TicketsHaveFiles::where('ticket_hash', '=', $ticket->hash)->get();
            foreach ($files as $file) {
                unlink(public_path() . '/uploads/' . $file->file);
                $file->delete();
            }

            $threads = TicketThreadModel::where('ticket_id', '=', $ticket->id);
            $threads->delete();
        }

        $faqs->delete();

        foreach ($groups->get() as $group) {
            foreach ($agents as $agent) {
                $ingroup = AgentsHaveGroups::where('agent_id', '=', $agent->id)->where('group_id', '=', $group->id);
                $ingroup->delete();
            }
        }

        $groups->delete();
        $agents->delete();
        $users->delete();

        $ws->delete();

        Auth::logout();
        request()->session()->invalidate();

        return redirect('/')->with('success', __('app.workspace_deleted'));
    }

    /**
     * Generate API token
     * @param $workspace
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function generateApiToken($workspace)
    {
        if (!WorkSpaceModel::isLoggedIn($workspace)) {
            return response()->json(array('code' => 500, 'message' => __('app.login_required')));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->where('deactivated', '=', false)->first();
        if ($ws === null) {
            return response()->json(array('code' => 500, 'message' => __('app.workspace_not_found_or_deactivated')));
        }

        if (!AgentModel::isSuperAdmin(User::getAgent(auth()->id())->id)) {
            return response()->json(array('code' => 500, 'message' => __('app.superadmin_permission_required')));
        }

        $ws->apitoken = md5(random_bytes(55));
        $ws->save();

        return response()->json(array('code' => 200, 'message' => __('app.api_token_generated'), 'token' => $ws->apitoken));
    }

    /**
     * Save mailer settings
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function mailer($workspace)
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

        $attr = request()->validate([
            'mailer_useown' => 'nullable|numeric',
            'mailer_host_smtp' => 'nullable',
            'mailer_port_smtp' => 'nullable|numeric',
            'mailer_host_imap' => 'nullable',
            'mailer_port_imap' => 'nullable|numeric',
            'mailer_inbox' => 'nullable',
            'mailer_username' => 'nullable',
            'mailer_password' => 'nullable',
            'mailer_address' => 'nullable|email',
            'mailer_fromname' => 'nullable'
        ]);

        if (!isset($attr['mailer_useown'])) {
            $attr['mailer_useown'] = 0;
        }

        if (!isset($attr['mailer_host_smtp'])) {
            $attr['mailer_host_smtp'] = '';
        }

        if (!isset($attr['mailer_port_smtp'])) {
            $attr['mailer_port_smtp'] = 25;
        }

        if (!isset($attr['mailer_host_imap'])) {
            $attr['mailer_host_imap'] = '';
        }

        if (!isset($attr['mailer_port_imap'])) {
            $attr['mailer_port_imap'] =143 ;
        }

        if (!isset($attr['mailer_inbox'])) {
            $attr['mailer_inbox'] = '';
        }

        if (!isset($attr['mailer_username'])) {
            $attr['mailer_username'] = '';
        }

        if (!isset($attr['mailer_password'])) {
            $attr['mailer_password'] = '';
        }

        if (!isset($attr['mailer_address'])) {
            $attr['mailer_address'] = '';
        }

        if (!isset($attr['mailer_fromname'])) {
            $attr['mailer_fromname'] = $ws->company;
        }

        $ws->mailer_useown = $attr['mailer_useown'];
        $ws->mailer_host_smtp = $attr['mailer_host_smtp'];
        $ws->mailer_port_smtp = $attr['mailer_port_smtp'];
        $ws->mailer_host_imap = $attr['mailer_host_imap'];
        $ws->mailer_port_imap = $attr['mailer_port_imap'];
        $ws->mailer_inbox = $attr['mailer_inbox'];
        $ws->mailer_username = $attr['mailer_username'];
        $ws->mailer_password = $attr['mailer_password'];
        $ws->mailer_address = $attr['mailer_address'];
        $ws->mailer_fromname = $attr['mailer_fromname'];
        $ws->save();

        return back()->with('success', __('app.settings_saved'));
    }

    /**
     * Export tickets to format
     *
     * @param $workspace
     * @return \Illuminate\Http\RedirectResponse
     */
    public function exportTickets($workspace)
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

        $attr = request()->validate([
           'date_from' => 'required',
           'date_to' => 'required',
           'export_type' => 'required'
        ]);

        if ($attr['export_type'] === 'csv') {
            TicketModel::exportTicketsAsCsv($ws->id, $attr['date_from'], $attr['date_to']);
        } else if ($attr['export_type'] === 'json') {
            TicketModel::exportTicketsAsJson($ws->id, $attr['date_from'], $attr['date_to']);
        }

        return back()->with('error', __('app.invalid_export_type', ['type' => $attr['export_type']]));
    }
}
