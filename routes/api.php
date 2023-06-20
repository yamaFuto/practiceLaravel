<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Test;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
// use InterventionImage;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/hello', function () {
    return 'Hello Next.js';
});

Route::post('/test', function (Request $request) {
    $test = new Test;
    $test->title = $request->titleProps;
    $test->color = $request->colorProps;
    $test->save();
});

Route::get("/testGet", function () {
    $test = Test::all();
    return $test;
});

Route::post("/image", function (Request $request) {
    $imageFile = $request->image;
    if(!is_null($imageFile) && $imageFile->isValid()) {
        $fileName = uniqid(rand(). '_');
        $extension = $imageFile->extension();
        $fileNameToStore = $fileName. '.' . $extension;
        $resizedImage = Image::make($imageFile)->resize(1920,1080)->encode();
        Storage::put('public/image/' . $fileNameToStore, $resizedImage);
    }
});

Route::post("/imageS3", function (Request $request) {
    $imageFile = $request->image;
    if(!is_null($imageFile) && $imageFile->isValid()) {

        //名前指定
        $fileName = uniqid(rand(). '_');
        $extension = $imageFile->extension();
        $fileNameToStore = $fileName. '.' . $extension;

        //resize
        $resizedImage = Image::make($imageFile)->resize(1920,1080)->encode();

        //s3へ格納
        $path = Storage::disk('s3')->put("example/${fileNameToStore}", $resizedImage, 'public');
        $image = Storage::disk('s3')->url($path);

        //pathの成型
        $imageA = substr($image, 0, -1);
        $imageUrl = $imageA . "example/${fileNameToStore}";
        return $imageUrl;
        // return $resizedImage;
    }
});

    //php artisan storage:link⇒gdインストール⇒gd extensionをコメントから外す⇒app/configのprovider、aliasに記述

    // $file_name = $request->image->getClientOriginalName();
    // $request->image->storeAs('public/image', $file_name);

    // return $request->image->getErrorMessage();

    // $image_path = $request->file('image')->store('public/shops');
    // return $image_path;
