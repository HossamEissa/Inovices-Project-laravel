<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait UploadImages
{
    public function uploadImage(Request $request, $folder, $file)
    {
        $file_name = $request->file($file)->getClientOriginalName();
        $path = $request->file($file)->storeAs($folder, $file_name, 'attachments');
        $array = [$file_name, $path];
        return $array;
    }
}
