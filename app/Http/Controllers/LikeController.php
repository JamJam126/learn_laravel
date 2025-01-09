<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    public function like($postId)
    {
        $user = Auth::user();

        DB::transaction(function () use ($user, $postId) {
            $existLike = Like::where('user_id', $user->id)->where('post_id', $postId)->first();
            
            if ($existLike) 
                throw new \Exception('Already liked');
            
            $like = new Like();
            $like->user_id = $user->id;
            $like->post_id = $postId;
            $like->save();

            $post = Post::findOrFail($postId);
            $post->increment('likes');
        });

        return response()->json(['message' => 'Post liked successfully'], 200);
    }

    public function unlike($postId)
    {
        $user = Auth::user();

        DB::transaction(function () use ($user, $postId) {
            $like = Like::where('user_id', $user->id)->where('post_id', $postId)->first();

            if (!$like) 
                throw new \Exception('Like not found');
            
            $like->delete();

            $post = Post::findOrFail($postId);
            $post->decrement('likes');
        });

        return response()->json(['message' => 'Post unliked successfully'], 200);
    }

    public function checkIfLiked($postId)
    {
        $user = Auth::user();

        $existLike = Like::where('user_id', $user->id)->where('post_id', $postId)->exists();

        return response()->json(['isLiked' => $existLike], 200);
    }
}
