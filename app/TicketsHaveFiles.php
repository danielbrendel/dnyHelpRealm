<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 0.1
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TicketsHaveFiles
 * 
 * Represents the relationship between tickets and files
 */
class TicketsHaveFiles extends Model
{
    /**
     * Get size of file
     * 
     * @param int $id The ID of the entry
     * @return int|bool Size in bytes on success or false or -1 on failure
     */
    public static function getFileSize($id)
    {
        $file = TicketsHaveFiles::where('id', '=', $id)->first();
        if (!$file)
            return -1;

        return filesize(base_path() . '/public/uploads/' . $file->file);
    }
}
