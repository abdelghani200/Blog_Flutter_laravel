<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // get all posts

    public function index()
    {
        return response([
            'posts' => Post::orderBy('created_at', 'desc')->with('user:id, name, image')->withCount('comments', 'likes')
            ->with('likes', function($like){
                return $like->where('user_id', auth()->user()->id)
                    ->select('id', 'user_id', 'post_id')->get();
            })
            ->get()
        ], 200);
    }

    // show post

    public function show()
    {
        return response([
            'post' => Post::where('id', $id)->withCount('comments', 'likes')->get()
        ], 200);
    }

    // create post

    public function store(Request $request)
    {
        $data = $request->validate([
            'body' => 'required|string'
        ]);

        $image = $this->saveImage($request->image, 'post');

        $post = Post::create([
            'body' => $data['body'],
            'user_id' => auth()->user()->id,
            'image' => $image
        ]);

        return response([
            'messages' => 'Post Created',
            'post' => $post
        ], 200);
    }

    // update post

    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post)
        {
            return response([
                'messages' => 'Post not found',
            ], 403);
        }

        if ($post->user_id != auth()->id)
        {
            return response([
                'messages' => 'Permission denied',
            ], 403);
        }

        $data = $request->validate([
            'body' => 'required|string'
        ]);

        $post->update([
            'body' => $data['body'],
        ]);

        return response([
            'messages' => 'Post Updated',
            'post' => $post
        ], 200);
    }

    // delete post

    public function destroy()
    {
        $post = Post::find($id);

        if (!$post)
        {
            return response([
                'messages' => 'Post not found',
            ], 403);
        }

        if ($post->user_id != auth()->id)
        {
            return response([
                'messages' => 'Permission denied',
            ], 403);
        }

        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response([
            'messages' => 'Post deleted',
        ], 200);
    }

}
