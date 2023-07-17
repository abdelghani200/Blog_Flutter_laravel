<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // get all comments
    public function index($id)
    {
        $post = Post::find($id);

        if (!$post)
        {
            return response([
                'messages' => 'Post not found',
            ], 403);
        }

        return response([
            'comment' => $post->comments()->with('user:id, name, image')->get()
        ], 200);
    }

    // create comment

    public function store(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post)
        {
            return response([
                'messages' => 'Comment not found',
            ], 403);
        }

        $data = $request->validate([
            'comment' => 'required|string'
        ]);

        Comment::create([
            'comment' => $data['comment'],
            'post_id' => $id,
            'user_id' => auth()->user()->id
        ]);

        return response([
            'Message' => 'Comment Created'
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment)
        {
            return response([
                'messages' => 'comment not found',
            ], 403);
        }

        if ($comment->user_id != auth()->user()->id)
        {
            return response([
                'messages' => 'Permission denied',
            ], 403);
        }

        $data = $request->validate([
            'comment' => 'required|string'
        ]);

        $comment->update([
            'comment' => $data['comment']
        ]);

        return response([
            'message' => 'Comment updated'
        ], 200);

    }

    // delete comment

    public function destroy()
    {
        $comment = Comment::find($id);

        if (!$comment)
        {
            return response([
                'messages' => 'comment not found',
            ], 403);
        }

        if ($comment->user_id != auth()->id)
        {
            return response([
                'messages' => 'Permission denied',
            ], 403);
        }

        $comment->delete();

        return response([
            'messages' => 'comment deleted',
        ], 200);
    }

}
