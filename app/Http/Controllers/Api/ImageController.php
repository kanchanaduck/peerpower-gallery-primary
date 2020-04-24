<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Image as ImageResource;
use Auth;
class ImageController extends Controller
{
    public function index(){
        // return response()->json(Auth::user()->id);
        return ImageResource::collection(Image::all());
    }
    public function show($id){
        return new ImageResource(Image::find($id));
    }
    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        Storage::disk('upload')->delete($image->name);
        $image->delete();
        return response()->noContent();
    }
}
