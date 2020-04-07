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
    public function index()
    {
        return response()->json(Image::where('user_id',1)->get());
    }
    public function image_group()
    {
        return response()->json(
            Image::select('type', DB::raw('count(name) as count'), DB::raw('sum(size) as size'))
                ->groupBy('type')
            ->get());
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        $upload_path = public_path('upload');
        $detail = [];
        $files = $request->file('files');
        foreach($files as $key => $file){
            $file_name = $file->getClientOriginalName();
            $file_type = $file->getMimeType();
            $file_size = $file->getSize();
            $ext = $file->getClientOriginalExtension();
            $new_name = time() . $key . '.' . $ext;
            $file->move($upload_path, $new_name);

            $image = Image::create([
                'name' => $new_name,
                'type' => $file_type,
                'size' => $file_size,
                'user_id' => 1//auth()->id
            ]);

            $d = (object) [
                'insert_id' => $image->id,
                'file_name' => $new_name,
                'file_type' => $file_type,
                'file_size' => $file_size
            ];
            array_push($detail,$d);
        }
        return response()->json([
            'success' => 'You have successfully uploaded',
            'files' => $detail
        ]);
    }
    public function show($id)
    {
        return Image::find($id);
    }
    public function edit($id)
    {
        //
    }
    public function update(Request $request, $id)
    {
        $image = Image::findOrFail($id);
        $image->update($request->all());

        return $image;
    }
    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        unlink(public_path('upload').'/'.$image->name);
        $image->delete();
        return 204;
    }
}
