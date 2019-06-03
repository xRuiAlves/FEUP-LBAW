<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

use App\Comment;

class CommentController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth', ['except' => ['show']]);
    }

    /**
     * Creates a new comment.
     */
    public function store(Request $request) {
        $this->authorize('create', Comment::class);

        $validated_data = $request->validate([
            'post_id' => 'required',
            'content' => 'required'
        ]);

        $post_id = $validated_data["post_id"];
        $content = $validated_data["content"];

        try {
            $comment = new Comment();
            $comment->user_id = auth()->user()->id;
            $comment->post_id = $post_id;
            $comment->content = $content;
            $comment->save();
            $comment = Comment::findOrFail($comment->id);
        } catch (QueryException $err) {
            if ($err->getCode() == 22001) {
                return response()->json([
                    'message' => 'The comment content must be, at most, 200 characters long.'
                ], 400);
            }
        }

        return response()->json([
            "name" => auth()->user()->name,
            "formatted_timestamp" => $comment->formatted_timestamp,
        ], 200);
    }
}
