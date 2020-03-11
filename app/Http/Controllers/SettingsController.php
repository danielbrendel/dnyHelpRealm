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
use \App\User;
use \App\AgentModel;
use \App\BgImagesModel;
use \App\WorkSpaceModel;

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

        $langs = array();
        $dirs = scandir(base_path() . '/resources/lang');
        foreach ($dirs as $dir) {
            if ($dir[0] != '.') {
                array_push($langs, $dir);
            }
        }
        
        return view('settings.agent', [
            'workspace' => $ws->name,
            'location' => __('app.settings'),
            'user' => User::get(auth()->id()),
            'agent' => User::getAgent(auth()->id()),
            'lang' => \App::getLocale(),
            'langs' => $langs,
            'superadmin' => User::getAgent(auth()->id())->superadmin
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

        $ws = WorkSpaceModel::where('name', '=', $workspace)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found'));
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

        return view('settings.system', [
            'workspace' => $ws->name,
            'location' => __('app.system_settings'),
            'user' => User::get(auth()->id()),
            'agent' => User::getAgent(auth()->id()),
            'superadmin' => User::getAgent(auth()->id())->superadmin,
            'company' => $ws->company,
            'lang' => $ws->lang,
            'usebgcolor' => $ws->usebgcolor,
            'bgcolorcode' => $ws->bgcolorcode,
            'langs' => $langs,
            'bgs' => BgImagesModel::getAllBackgrounds($ws->id),
            'infomessage' => $ws->welcomemsg
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

        $ws = WorkSpaceModel::where('name', '=', $workspace)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found'));
        }

        $attr = request()->validate([
            'company' => 'nullable',
            'lang' => 'nullable',
            'usebgcolor' => 'numeric|nullable',
            'bgcolorcode' => 'nullable',
            'infomessage' => 'nullable'
        ]);

        if (!isset($attr['usebgcolor'])) {
            $attr['usebgcolor'] = false;
        }

        if (!isset($attr['bgcolorcode'])) {
            $attr['bgcolorcode'] = '#F5F5F6';
        }

        $ws->company = $attr['company'];
        $ws->lang = $attr['lang'];
        $ws->usebgcolor = (bool)$attr['usebgcolor'];
        $ws->bgcolorcode = $attr['bgcolorcode'];
        $ws->welcomemsg = $attr['infomessage'];
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

        $ws = WorkSpaceModel::where('name', '=', $workspace)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found'));
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

        $ws = WorkSpaceModel::where('name', '=', $workspace)->first();
        if ($ws === null) {
            return back()->with('error', __('app.workspace_not_found'));
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
}
