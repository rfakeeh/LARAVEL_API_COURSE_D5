<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryCollection;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$results = DB::select('SELECT * FROM categories ORDER BY name ASC');
        //$results = DB::select('SELECT * FROM categories WHERE id = ?', [1]);
        $categories = Category::query(); 

        // Sorting
        $categories = $this->sorted($categories,$request);

        // Paginatable collection
        $categories = new CategoryCollection($categories->get());

        // Pagination
        $categories = $this->paginated($categories,$request);

        return response()->json([
            "status" => "success", 
            "error" => false, 
            "data" => $categories,
        ],200);
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
            "name" => "required|min:3|unique:categories,name",
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "fail", 
                "error" => true, 
                "validation_errors" => $validator->errors()
            ]);
        }

        try {
            //$result = DB::insert('INSERT INTO categories (id, name, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?);', [null, 'Category 1', 'Category 1 description', null, null]);
            //$result = DB::statement('INSERT INTO categories (id, name, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?);', [null, 'Category 1', 'Category 1 description', null, null]);
            $category = Category::create([
                "name" => $request->name,
                "description" => $request->description
            ]);
            return response()->json([
                "status" => "success", 
                "error" => false, 
                "message" => "Success! category created.", 
                "data" => new CategoryResource($category),
            ], 201);
        }
        catch(Exception $exception) {
            return response()->json([
                "status" => "fail", 
                "error" => true, 
                "message" => $exception->getMessage(),
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
        $category = Category::find($id);
        
        if($category) {
            return response()->json([
                "status" => "success", 
                "error" => false, 
                "data" => new CategoryResource($category)
            ], 200);
        }
        return response()->json([
            "status" => "fail", 
            "error" => true, 
            "message" => "Failed! no category found."
        ], 404);
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
        $category = Category::find($id);

        if($category) {

            $validator = Validator::make($request->all(), [
                "name" => "required|min:3",
            ]);
    
            if($validator->fails()) {
                return response()->json([
                    "status" => "fail", 
                    "error" => true, 
                    "validation_errors" => $validator->errors()
                ]);
            }

            $category['name'] = $request->name;

            // if it has description
            if($request->description) {
                $category['description'] = $request->description;
            }

            $category->save();
            return response()->json([
                "status" => "success", 
                "error" => false, 
                "message" => "Success! category updated.", 
                "data" => new CategoryResource($category)
            ], 200);
        }
        return response()->json([
            "status" => "fail", 
            "error" => true, 
            "message" => "Failed no category found."
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if($category) {
            $category->delete();
            return response()->json([
                "status" => "success", 
                "error" => false, 
                "message" => "Success! category deleted."
            ], 200);
        }
        return response()->json([
            "status" => "fail", 
            "error" => true, 
            "message" => "Failed no category found."
        ], 404);     
    }

    /***
     * New function
     */
    public function open_document()
    {
        $category = Category::latest()->first();

        return response()->json([
            "status" => "success", 
            "error" => false, 
            "data" => new CategoryResource($category)
        ], 200);

    }
}
