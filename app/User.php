<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2021 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use \App\ClientModel;
use \App\AgentModel;

/**
 * Class User
 * 
 * Represents a user that can either be an agent or a client
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get agent 
     * 
     * @param int $id The ID of the user
     * @return mixed An agent object
     */
    public static function getAgent($id)
    {
        $user = User::where('id', '=', $id)->first();
        if ($user === null) {
            return null;
        }
        
        return AgentModel::queryAgent($user->user_id);
    }

    /**
     * Get user object
     * 
     * @param int $id The ID of the user
     * @return mixed
     */
    public static function get($id)
    {
        $user = User::where('id', '=', $id)->first();

        return $user;
    }

    /**
     * Get user object by email
     * 
     * @param string $email The E-Mail address
     * @return mixed
     */
    public static function getByEmail($email)
    {
        $user = User::where('email', '=', $email)->first();

        return $user;
    }
}
