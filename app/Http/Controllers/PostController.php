<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

use App\Post;

class PostController extends Controller
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
     * Creates a new post.
     */
    public function store(Request $request) {
        $this->authorize('create', Post::class);

        $validated_data = $request->validate([
            'event_id' => 'required',
            'content' => 'required'
        ]);

        $event_id = $validated_data["event_id"];
        $content = $validated_data["content"];

        try {
            $post = new Post();
            $post->user_id = auth()->user()->id;
            $post->event_id = $event_id;
            $post->content = $content;
            $post->is_announcement = false;
            $post->save();
            $post = Post::findOrFail($post->id);
        } catch (QueryException $err) {
            if ($err->getCode() == 22001) {
                return response()->json([
                    'message' => 'The post content must be, at most, 300 characters long.'
                ], 400);
            }
        }

        return response()->json([
            "name" => auth()->user()->name,
            "formatted_timestamp" => $post->formatted_timestamp,
        ], 200);
    }
}
