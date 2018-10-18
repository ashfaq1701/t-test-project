<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Photo as PhotoResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\PhotoRepository;

class PhotosController extends Controller {
    public $photoRepository;

    public function __construct(PhotoRepository $photoRepository) {
        $this->middleware('permission:manage_photos',
            ['only' => ['store', 'destroy', 'update']]);
        $this->photoRepository = $photoRepository;
    }

    public function index(Request $request)
    {
        $photos = $this->photoRepository->getPhotos();
        return PhotoResource::collection($photos);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $photo = $this->photoRepository->uploadPhoto($request);
        return new PhotoResource($photo);
    }

    public function show($id)
    {
        $photo = $this->photoRepository->getPhoto($id);
        return new PhotoResource($photo);
    }

    public function update(Request $request, $id)
    {
        $photo = $this->photoRepository->updatePhoto($id, $request);
        return new PhotoResource($photo);
    }

    public function destroy($id)
    {
        return $this->photoRepository->deletePhoto($id);
    }
}