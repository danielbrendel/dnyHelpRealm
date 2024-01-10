<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2024 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AgentsHaveGroups
 * 
 * Represents the relationship between agents and groups
 */
class AgentsHaveGroups extends Model
{
    /**
     * Put agent in group
     * 
     * @param int $agent The agent ID
     * @param int $group The group ID
     * @return bool
     */
    public static function putAgentInGroup($agent, $group)
    {
        $record = AgentsHaveGroups::where('group_id', '=', $group)->where('agent_id', '=', $agent)->get();
        if (!$record->isEmpty())
            return true;

        $record = new AgentsHaveGroups();
        $record->agent_id = $agent;
        $record->group_id = $group;
        $record->save();

        return true;
    }

    /**
     * Remove agent from group
     * 
     * @param int $agent The agent ID
     * @param int $group The group ID
     * @return bool
     */
    public static function removeAgentFromGroup($agent, $group)
    {
        $record = AgentsHaveGroups::where('group_id', '=', $group)->where('agent_id', '=', $agent);
        if ($record) {
            $record->delete();

            return true;
        }

        return false;
    }
}
