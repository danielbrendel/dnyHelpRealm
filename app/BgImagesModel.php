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
 * Class BgImagesModel
 * 
 * Represents the interface to background images
 */
class BgImagesModel extends Model
{
    /** 
     * Check if file is a valid image
     * 
     * @param string $imgFile
     * @return boolean
    */
    public static function isValidImage($imgFile)
    {
        $imagetypes = array(
            IMAGETYPE_PNG,
            IMAGETYPE_JPEG,
            IMAGETYPE_BMP,
            IMAGETYPE_GIF
        );

        if (!file_exists($imgFile)) {
            return false;
        }

        foreach ($imagetypes as $type) {
            if (exif_imagetype($imgFile) === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all background images
     * 
     * @return array
     */
    public static function getAllBackgrounds($workspace)
    {
        $bgs = BgImagesModel::where('workspace', '=', $workspace)->get();

        return $bgs;
    }

    /**
     * Query random image
     * 
     * @return string
     */
    public static function queryRandomImage($workspace)
    {
        $images = static::getAllBackgrounds($workspace);

        if (count($images) === 0) {
            return '';
        }

        $randomImage = random_int(1, count($images)) - 1;

        return $images[$randomImage];
    }
}
