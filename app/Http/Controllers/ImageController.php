<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image;
use DB;
use Auth; 
use App\Http\Requests\StoreImage;

class ImageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        DB::enableQueryLog();
        // return response()->json(DB::getQueryLog());
    }
    public function index()
    {
        return view('pages.gallery');
    }
    public function main()
    {
        return view('pages.main');
    }
    public function lists()
    {
        return response()->json(Image::select('*','name as url')->where('user_id',Auth::user()->id)->get());
    }
    public function group()
    {
        return response()->json(
            Image::select('type', DB::raw('count(name) as count'), DB::raw('sum(size) as size'))
                ->where('user_id',Auth::user()->id)
                ->groupBy('type')
            ->get());
    }
    public function store(StoreImage $request)
    {

        $upload_path = public_path('upload');
        $file = $request->file('files');
        $file_type = $file->getMimeType();
        $file_size = $file->getSize();
        $ext = $file->getClientOriginalExtension();
        $new_name = time().'.' . $ext;
        $file->move($upload_path, $new_name);

        $image = Image::create([
            'name' => $new_name,
            'type' => $file_type,
            'size' => $file_size,
            'user_id' => Auth::user()->id
        ]);

        return response()->json([
            'success' => 'You have successfully uploaded',
            'files' => $image
        ]);
    }
}
