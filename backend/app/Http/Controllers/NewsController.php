<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryCollection;
use App\Models\News;
use App\Http\Resources\NewsResource;
use App\Http\Resources\NewsCollection;
use App\Models\Image;
use Illuminate\Support\Facades\Validator;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,  $articles)
    {
        // Filter by attribute
        if($request->exists('visible')) {
            $visible = $request->boolean('visible'); // it parses 0/1, true/false, on/off into boolean
            $articles = $articles->where('visible','=',$visible);
        }

        // Filter by attribute 
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        if ($start_date && $end_date) {
            $articles->whereBetween('updated_at', [$start_date, $end_date]);
        }

        // Filter by category
        $category_id = $request->input('category');
        if ($category_id) {
            $articles->whereHas('categories', function($query) use($category_id) {
                //$query->where('categories.id', $category_id);
                $query->whereIn('categories.id', $category_id);
            });
        }
        
        // Filter by query string in title or body
        $q = $request->input('q');
        if ($q) {
            $articles->where(function ($query) use ($q) {
                $query->where('title', 'LIKE', '%' . $q . '%')->orWhere('body', 'LIKE', '%' . $q . '%');
            });
        }

        // Sorting
        $articles = $this->sorted($articles,$request);

        // Paginatable collection
        $articles = new NewsCollection($articles->get());

        // Pagination
        $articles = $this->paginated($articles,$request);

        return $articles;
    }

    public function public_index(Request $request)
    {
        $articles = News::where('visible', true);

        // Filter by author
        $author_id = $request->input('author');
        if ($author_id) {
            $articles->whereHas('user', function($query) use($author_id) {
                $query->where('users.id', $author_id);
            });
        }

        return response()->json([
            "status" => "success", 
            "error" => false, 
            "data" => $this->index($request,$articles),
        ],200);
    }

    public function private_index(Request $request)
    {   
        // Retrieve the authenticated user
        $user = Auth::user();

        // Filter by authenticated user
        $articles = $user->news();

        return response()->json([
            "status" => "success", 
            "error" => false, 
            "data" => $this->index($request,$articles),
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
            "title" => "required|min:3|unique:news,title",
            'thumbnail' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "fail", 
                "error" => true, 
                "validation_errors" => $validator->errors()
            ]);
        }
        
        $news_id = NULL;
        try {
            
            $image_path = $request->file('thumbnail')->store('thumbnails','public');
            $article = News::create([
                "title" => $request->title,
                "body" => $request->body,
                "thumbnail" => $image_path,
                "completed" => $request->completed,
                "visible" => $request->visible,
                "user_id" => Auth::user()->id
            ]);
            $news_id = $article->id;

            if($request->categories) {
                $categories = $request->categories;
                $article->categories()->attach($categories);
            }

            if($request->images) {
                $images = $request->images;
                Image::whereIn('id', $images)->update(['news_id' => $news_id]);
                Image::where('news_id', $news_id)->whereNotIn('id', $images)->delete();
            }
            
            return response()->json([
                "status" => "success", 
                "error" => false, 
                "message" => "Success! news article created.", 
                "data" => new NewsResource($article),
            ], 201);
        }
        catch(Exception $exception) {

            if($news_id) {  
                $article = News::find($news_id);            
                $article->categories()->detach();
                Image::where('news_id', $news_id)->delete();
                $article->delete();
            }
                        
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
        //$article = News::find($id);

        // Retrieve the authenticated user
        $user = Auth::user();

        // Filter by authenticated user
        $article = $user->news()->find($id);

        if($article) {
            return response()->json([
                "status" => "success",
                "error" => false, 
                "data" => new NewsResource($article),
            ], 200);
        }
        
        return response()->json([
            "status" => "failed", 
            "error" => true, 
            "message" => "Failed! no news article found."
        ], 404);
    }

    public function public_show($id)
    {
        $article = News::where('visible', true)->find($id);

        if($article) {
            return response()->json([
                "status" => "success",
                "error" => false, 
                "data" => new NewsResource($article),
            ], 200);
        }
        
        return response()->json([
            "status" => "failed", 
            "error" => true, 
            "message" => "Failed! no news article found."
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

        //$article = News::find($id);

        // Retrieve the authenticated user
        $user = Auth::user();

        // Filter by authenticated user
        $article = $user->news()->find($id);

        if($article) {

            $validator = Validator::make($request->all(), [
                "title" => "required|min:3",
                'thumbnail' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);

            if($validator->fails()) {
                return response()->json([
                    "status" => "fail", 
                    "error" => true, 
                    "validation_errors" => $validator->errors()
                ]);
            }

            $article['title'] = $request->title;

            // if has body
            if($request->body) {
                $article['body'] = $request->body;
            }

            // if has thumbnail image
            if($request->file('thumbnail')) {
                $image_path = $request->file('thumbnail')->store('thumbnails', 'public');
                $article['thumbnail'] = $image_path;
            }

            // if has visible
            if($request->visible) {
                $article['visible'] = $request->visible;
            }

            // if has completed
            if($request->completed) {
                $article['completed'] = $request->completed;
            }

            // if has categories
            if($request->categories) {
                $categories = $request->categories;
                $article->categories()->sync($categories);
            }

            // if has album images
            if($request->images) { 
                $images = $request->images;
                Image::whereIn('id', $images)->update(['news_id' => $id]);
                Image::where('news_id', $id)->whereNotIn('id', $images)->delete();
            }

            $article->save();

            return response()->json([
                "status" => "success", 
                "error" => false, 
                "message" => "Success! news article updated.", 
                "data" => new NewsResource($article)
            ], 201);
        }

        return response()->json([
            "status" => "failed", 
            "error" => true, 
            "message" => "Failed no news article found."
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
        //$article = News::find($id);

        // Retrieve the authenticated user
        $user = Auth::user();

        // Filter by authenticated user
        $article = $user->news()->find($id);

        if($article) {
            $article->categories()->detach();
            Image::where('news_id', $id)->delete();
            $article->delete();
            
            return response()->json([
                "status" => "success", 
                "error" => false, 
                "message" => "Success! news article deleted."
            ], 200);
        }

        return response()->json([
            "status" => "failed", 
            "error" => true, 
            "message" => "Failed no news article found."
        ], 404);
    }
}
