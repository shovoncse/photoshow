<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Album;

class AlbumsController extends Controller
{
    public function index(){

        $albums = Album::with('Photos')->get();
        return view('albums.index')->with('albums', $albums);
    }
    
    public function create(){
        return view('albums.create');
    }
    
    public function store(Request $request){
        $this -> validate($request, [
            'name' => 'required',
            'cover_image' => 'image|max:1999'
        ]);

        //Get file name with extension
        $fileNameWithExt = $request -> file('cover_image') ->getClientOriginalName();

        //Get Just The File Name
        $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

        //Get Extension
        $extension = $request-> file('cover_image')->getClientOriginalExtension();

        //Create new Filename
        $fileNameToStore = $filename.'_'.time().'.'.$extension;
        
        //Upload Image
        $path = $request -> file('cover_image')->storeAs('public/album_covers',$fileNameToStore); 

        //Create Album
        $album = new Album;

        $album -> name = $request -> input('name');
        $album -> description = $request -> input('description');
        $album -> cover_image = $fileNameToStore;

        $album -> save();

        return redirect('/albums')->with('success', 'Album Created');
        
        
    }

    public function show($id){
         $albums = Album::with('Photos')->find($id);
        //return $albums -> photos;
        return view('albums.show')->with('album', $albums);
    }
}
