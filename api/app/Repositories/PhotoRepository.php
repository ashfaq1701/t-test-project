<?php

namespace App\Repositories;

use App\Models\Photo;
use Illuminate\Support\Facades\Auth;

class PhotoRepository {
    public function __construct() {
    }

    public function getPhotos() {
        $photos = Photo::all();
        return $photos;
    }

    public function deletePhoto($photoId) {
        $user = Auth::user();
        $photo = Photo::find($photoId);
        if (!$user->hasPermissionTo('manage_all_photos')) {
            if ($photo->user_id != $user->id) {
                return response()->json([
                    'message' => 'You have permission to delete your own photos only'
                ], 403);
            }
        }
        unlink(public_path('/uploads/images/' . $photo->file_name));
        Photo::destroy($photoId);
    }

    public function getPhoto($photoId) {
        $photo = Photo::find($photoId);
        return $photo;
    }

    public function uploadPhoto ($request) {
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $name = 'img-' . time(). '-' . str_random(2) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/images');
            $image->move($destinationPath, $name);

            $photo = new Photo();
            $photo->file_name = $name;
            $photo->user_id = Auth::user()->id;
            $photo->save();

            return $photo;
        }
    }

    public function updatePhoto($id, $request) {
        $user = Auth::user();
        $photo = Photo::find($id);
        if (!$user->hasPermissionTo('manage_all_photos')) {
            if ($photo->user_id != $user->id) {
                return response()->json([
                    'message' => 'You have permission to update your own photos only'
                ], 403);
            }
        }
        // Update specific stuffs
        return $photo;
    }
}