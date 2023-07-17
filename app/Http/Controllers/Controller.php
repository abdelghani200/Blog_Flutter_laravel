<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function saveImage($image, $path = 'public')
    {
        if(!$image)
        {
            return null;
        }

        $filename = time().'.png';

        // save image
        Storage::disk($path)->put($filename, base64_decode($image));

        return URL::to('/').'/storage/' .$path.'/'.$filename;
    }

}
