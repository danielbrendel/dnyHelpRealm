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

class BgImageModel extends Model
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
    public static function getAllBackgrounds()
    {
        $files = scandir(public_path() . '/gfx/backgrounds');

        $images = array();
        foreach ($files as $file) {
            if ($file[0] === '.') {
                continue;
            }

            if (static::isValidImage(public_path() . '/gfx/backgrounds/' . $file)) {
                $images[] = $file;
            }
        }

        return $images;
    }

    /**
     * Query random image
     * 
     * @return string
     */
    public static function queryRandomImage()
    {
        $images = static::getAllBackgrounds();

        $randomImage = random_int(1, count($images)) - 1;

        return $images[$randomImage];
    }
}
