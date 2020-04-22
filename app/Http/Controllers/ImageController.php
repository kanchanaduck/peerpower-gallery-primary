<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image;
use DB;
use Auth; 
use Illuminate\Http\File;
use App\Http\Requests\StoreImage;
use Illuminate\Support\Facades\Storage;

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

        $file = $request->file('files');
        $file_type = $file->getMimeType();
        $file_size = $file->getSize();
        $file_name = $file->getClientOriginalName();
        $ext = $file->getClientOriginalExtension();
        $new_name = $file_name.'_'.time().'.' . $ext;
        
        $file->storeAs('upload', $new_name);

        $image = Image::create([
            'name' => $new_name,
            'type' => $file_type,
            'size' => $file_size,
            'user_id' => Auth::user()->id
        ]);

        return response()->json([
            'success' => 'You have successfully uploaded',
            'files' => $image
        ], 201);
    }
}
