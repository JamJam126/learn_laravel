<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function index()
    {
        return Post::with('user')->get(); // RETRIVES ALL POSTS WITH THEIR USER DATA
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // $user = auth()->user();
        // $user = Auth::user();
        // $post = Post::create($validatedData);
        $post = new Post();
        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->user_id = Auth::id();
        $post->save();

        return response()->json($post, 201);
    }

    public function show($id)
    {
        $post = Post::with('user')->find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json($post, 200);
        // return Post::with('user')->findOrFail($id);
    }   
    
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        // Log::info("Auth User ID: " . Auth::id() . " | Post User ID: " . $post->user_id);
        // Log::info("Auth User: " . json_encode(Auth::user()));

        if ($post->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request -> validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);

        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->save();
        // $post -> update($validatedData);
        
        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return response()->json(null,204);
    }

    public function userPosts()
    {
        $user = Auth::user();
        $userId = $user->id;

        $posts = Post::where('user_id',$user->id)->latest()->withCount([
            'likes as isLiked' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            },
        ])
        ->get();
        
        return response()->json($posts);
    }
}