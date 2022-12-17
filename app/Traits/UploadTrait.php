<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

trait UploadTrait
{
    /**
     * Upload the file with slugging to a given path
     *
     * @param UploadedFile $image
     * @param $path
     * @return string
     */
    public function uploadFile($image)
    {
        if (!$image) {
            return null;
        }

        $extension = $image->getClientOriginalExtension();
        $name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $image_name = md5($name . Str::random(5)) . '.' . $extension;
        $filePath = Carbon::now()->format('Y/m/d/');
        Storage::disk('public')->putFileAs($filePath, $image, $image_name);

        return $filePath . $image_name;
    }
}