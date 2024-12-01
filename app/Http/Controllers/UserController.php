<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::get();
        return view('file-upload', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $file = $request->file('photo');
        $request->validate([

            'photo' => 'required|mimes:png,jpg,jpeg|max:3000'
            // 'photo' => 'required|mimes:png,jpg,jpeg|max:3000|dimensions:min_width:100,        
            // min_height:100,max_width:1000,max_height:1000'
            //100 means 100px or 3000 means 3 mb
        ]);

        // $file = $request->file('photo');
        // $extension=$file->getClientOriginalExtension();
        // $extension=$file->extension();
        // $extension=$file->hashName();
        // $extension=$file->getClientMimeType();
        // $extension=$file->getSize();
        // return $extension;

        // $fileName = $file->getClientOriginalName();

        //Image will save in public folder
        // $path = $request->file('photo')->store('image', 'public');

        // $fileName = time() . '_' . $file->getClientOriginalName();
        // $path = $request->file('photo')->storeAs('image', $fileName, 'public');

        //Image will save in private folder
        // $path = $request->photo->store('image', 'local');
        // return $path;


        $file = $request->file('photo');
        // $path = $request->photo->store('image', 'public');

        //Move Method for Uploading the images
        $file->move(public_path('uploads'),$file->getClientOriginalName());
        
        User::create([
            // 'file_name' => $path

            //Move Method
            'file_name' => $file->getClientOriginalName()
        ]);

        return redirect()->route('user.index')
            ->with('status', 'User Image Uploaded Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $user = User::find($id);
        return view('file-update', compact(('user')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([

            'photo' => 'required|mimes:png,jpg,jpeg|max:3000'
        ]);


        $user = User::find($id);

        if ($request->hasFile('photo')) {

            $image_path = public_path("storage/") . $user->file_name;
            
            if (file_exists($image_path)) {
                @unlink($image_path);
            }

            $path = $request->photo->store('image', 'public');

            $user->file_name = $path;
            $user->save();


            return redirect()->route('user.index')
                ->with('status', 'User Image Updated Successfully');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();
        $image_path = public_path("storage/") . $user->file_name;
        if (file_exists($image_path)) {
            @unlink($image_path);
        }

        return redirect()->route('user.index')
            ->with('status', 'User Image Deleted Successfully');
    }
}
