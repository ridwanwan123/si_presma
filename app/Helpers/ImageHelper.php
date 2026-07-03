<?php

namespace App\Helpers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageHelper
{
    public static function compress($file, $path, $quality = 75)
    {
        $manager = new ImageManager(new Driver());

        $image = $manager->read($file);

        // resize optional (biar lebih ringan lagi)
        $image->scale(width: 1200);

        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        $fullPath = storage_path('app/public/' . $path . '/' . $filename);

        // pastikan folder ada
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }

        $image->toJpeg($quality)->save($fullPath);

        return $path . '/' . $filename;
    }
}