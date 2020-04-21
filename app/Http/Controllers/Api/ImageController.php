<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Image;
use DB;

class ImageController extends Controller
{
    public function __construct()
    {
        DB::enableQueryLog();
        // return response()->json(DB::getQueryLog());
    }
    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        unlink(public_path('upload').'/'.$image->name);
        $image->delete();
        return response()->noContent();
    }
}
