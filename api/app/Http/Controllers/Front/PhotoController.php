<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;

class PhotoController extends Controller {
    public function get($path) {
        return response()->file(public_path('uploads/images/' . $path));
    }
}