<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\Posts\UpdatePostRequest;
use App\Post;
use App\Http\Requests\Posts\CreatePostsRequest;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{

    public function __construct(){

        $this->middleware('verifyCategoriesCount')->only(['create','store']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('posts.index')->with('posts',Post::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create')->with('categories',Category::all())->with('tags',Tag::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePostsRequest $request)
    {

        $image = ($request->image->store('posts'));

        $post = Post::create([
            'title' =>$request->title,
            'description' => $request->description,
            'content' => $request->content,
            'image'=> $image,
            'published_at' => $request->published_at,
            'category_id' =>$request->category,
            'user_id' => auth()->user()->id

        ]);
        if ($request->tags){
            $post->tags()->attach($request->tags);
        }
        session()->flash('success','Post Created Successfully');
        return redirect(route('posts.index'));
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view('posts.create')->with('post',$post)->with('categories',Category::all())->with('tags',Tag::all());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostRequest  $request, Post $post)
    {

        $data = $request->only(['title','description','published_at','content','category_id']);

        // check if new image
        if($request->hasFile('image')){

          $image = $request->image->store('posts');
          $post->deleteImage();
           // Storage::delete($post->image);

            $data['image'] = $image;
        }

        if ($request->tags){
            $post->tags()->sync($request->tags);
        }

        $post->update($data);
        //update attributes

        //flash message
        session()->flash('success','Post Updated Successfully');

        return redirect(route('posts.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $post = Post::withTrashed()->where('id',$id)->firstOrFail(); // Automatically shows 404 page on not found
        if ($post->trashed()){
            $post->deleteImage();
            //Storage::delete($post->image);
            $post->forceDelete();
        }
        else {
            $post->delete();
        }
        session()->flash('success','Post Deleted Successfully');
        return redirect(route('posts.index'));
    }


    /**
     * Display a list of all trash posts
     */
    public function trashed(){

        $trashed = Post::onlyTrashed()->get(); // Fetch all the posts that have been trashed
        return view('posts.index')->with('posts',$trashed);
        // return view('posts.index')->withPosts($trashed); // Equivalent to the former command,
        // I find the former command more easier to read
    }

    public function restore($id){

        $post = POST::withTrashed()->where('id',$id)->firstOrFail();
        $post->restore();
        session()->flash('success','Post Restored Successfully');
        return redirect()->back();
    }
}
