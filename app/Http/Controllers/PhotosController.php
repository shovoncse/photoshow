<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Photo;

class PhotosController extends Controller
{
    public function create($album_id){
        return view('photos.create')->with('album_id', $album_id);
    }



    public function store(Request $request){
        $this -> validate($request, [
            'title' => 'required',
            'photo' => 'image|max:1999'
        ]);

        //Get file name with extension
        $fileNameWithExt = $request -> file('photo') ->getClientOriginalName();

        //Get Just The File Name
        $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

        //Get Extension
        $extension = $request-> file('photo')->getClientOriginalExtension();

        //Create new Filename
        $fileNameToStore = $filename.'_'.time().'.'.$extension;
        
        //Upload Image
        $path = $request -> file('photo')->storeAs('public/photos/'.$request->input('album_id'),$fileNameToStore); 

        //Create Album
        $photo = new Photo;

        $photo -> album_id = $request -> input('album_id');
        $photo -> title = $request -> input('title');
        $photo -> size = $request -> file('photo')->getClientSize();
        $photo -> description = $request -> input('description');
        $photo -> photo = $fileNameToStore;

        $photo -> save();

        return redirect('/albums/'.$request->input('album_id'))->with('success', 'Photo Uploaded');
        
        
    }

    public function show($id){
        $photo = Photo::find($id);
        return view('photos.show')->with('photo',$photo );
    }


    public function destroy($id){
        
        $photo = Photo::find($id);
        if(Storage::delete('public/photos/'.$photo->album_id.'/'.$photo->photo)){
            $photo->delete();

            return redirect('/')->with('success', 'Photo Deleted');
        }


    }
}
 