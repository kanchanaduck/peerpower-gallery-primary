<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image;
use DB;
use Auth; 

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
        // return response()->json(Auth::user()->id);
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
    public function store(Request $request)
    {
        $upload_path = public_path('upload');
        $detail = [];
        $files = $request->file('files');
        $countSuccess = 0;
        foreach($files as $key => $file){
            $file_name = $file->getClientOriginalName();
            $file_type = $file->getMimeType();
            $file_size = $file->getSize();
            $ext = $file->getClientOriginalExtension();
            if(  ($ext=='jpg' || $ext=='png' || $ext=='jpeg') && $file_size<= 10485760){
                $new_name = time() . $key . '.' . $ext;
                $file->move($upload_path, $new_name);

                $image = Image::create([
                    'name' => $new_name,
                    'type' => $file_type,
                    'size' => $file_size,
                    'user_id' => Auth::user()->id
                ]);

                $d = (object) [
                    'insert_id' => $image->id,
                    'file_name' => $new_name,
                    'file_type' => $file_type,
                    'file_size' => $file_size
                ];
                array_push($detail,$d);
                $countSuccess++;
            }
        }
        if($countSuccess>0){
            return response()->json([
                'success' => 'You have successfully uploaded',
                'files' => $detail
            ]);
        }
        else{
            return response()->json([
                'not_success' => 'File(s) not supported.',
            ]);
        }
    }
}
