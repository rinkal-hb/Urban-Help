<?php

if (!function_exists('uploadImageToStorage')) {
    /**
     * Upload image to storage and return the path
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @param string $imagePath
     * @return string
     */
    function uploadImageToStorage($image, $imagePath)
    {
        return $image->store($imagePath, 'public');
    }
}