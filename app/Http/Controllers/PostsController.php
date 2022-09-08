<?php

namespace App\Http\Controllers;

use App\Models\Post;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class PostsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get all Posts
     *
     */


    public function index()
    {
        $posts = Post::all();
        return response()->json([
            'success'=>true,
            'message'=>"Ambil Semua Post",
            'data'=> $posts
        ],200);
    }

    public function store(Request $request)
    {
        $validator = $this->validate($request,[
            'title' => 'required',
            'content'=> 'required'
        ]);

        $data = $request->all();
        $post = Post::create($data);

        return response()->json($post);
    }

    public function show($id)
    {
        $data = Post::find($id);
        return response()->json($data);
    }

    public function update(Request $request,$id)
    {
        $post = Post::find($id);
        if(!$post) {
            return response()->json(['message'=>'Data tidak ditemukan'],404);
        }

        $this->validate($request,[
            'title'=>'required',
            'content'=>'required'
        ]);

        $data = $request->all();
        $post->fill($data);
        $post->save();
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        if(!$post) {
            return response()->json(['message'=>'Data tidak ditemukan'],404);
        }

        $post->delete();
        return response()->json(['message'=>'Data berhasil dihapus.'],200);
    }
}
