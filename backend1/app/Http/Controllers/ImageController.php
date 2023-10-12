<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use App\Http\Resources\ImageResource;
use App\Http\Resources\ImageCollection;
use Illuminate\Support\Facades\Validator;
use Exception;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "fail", 
                "error" => true, 
                "validation_errors" => $validator->errors()
            ]);
        }

        try {
            $image_path = $request->file('image')->store('images','public');
            $image = Image::create([
                "url" => $image_path
            ]);
            return response()->json([
                "status" => "success", 
                "error" => false, 
                "message" => "Success! image created.", 
                "data" => $image,
            ], 201);
        } catch(Exception $exception) {
            return response()->json([
                "status" => "failed", 
                "error" => $exception->getMessage()
            ], 404);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $image = Image::find($id);

        if($image) {
            $image->delete();
            return response()->json([
                "status" => "success", 
                "error" => false,
                 "message" => "Success! image deleted."
            ], 200);
        }
        return response()->json([
            "status" => "failed", 
            "error" => true, 
            "message" => "Failed no image found."
        ], 404);    
    }
}
