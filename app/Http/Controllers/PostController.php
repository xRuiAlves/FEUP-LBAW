<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

use App\Post;
use App\Event;

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
        $validated_data = $request->validate([
            'event_id' => 'required',
            'content' => 'required',
            'is_announcement' => 'required'
        ]);

        $event_id = $validated_data["event_id"];
        $content = $validated_data["content"];
        $is_announcement = $validated_data["is_announcement"];

        if ($is_announcement) {
            $this->authorize('eventSettings', Event::find($event_id));
        } else {
            $this->authorize('create', Post::class);
        }

        try {
            $post = new Post();
            $post->user_id = auth()->user()->id;
            $post->event_id = $event_id;
            $post->content = $content;
            $post->is_announcement = $is_announcement;
            $post->save();
            $post = Post::findOrFail($post->id);
        } catch (QueryException $err) {
            if ($err->getCode() == 22001) {
                return response()->json([
                    'message' => 'The content must be, at most, 300 characters long.'
                ], 400);
            }
        }

        return response()->json([
            "name" => auth()->user()->name,
            "formatted_timestamp" => $post->formatted_timestamp,
        ], 200);
    }

    /**
     * Deletes a post.
     */
    public function delete(Request $request) {
        $validated_data = $request->validate([
            'id' => 'required',
        ]);

        $id = $validated_data["id"];

        try {
            $post = Post::findOrFail($id);

            if ($post->is_announcement) {
                $this->authorize('eventSettings', Event::findOrFail($post->event_id));
            } else {
                $this->authorize('delete', Post::class);
            }

            Post::destroy($id);
            return response()->json([], 200);
        } catch (ModelNotFoundException $err) {
            return response()->json([], 404);
        } catch (QueryException $err) {
            return response()->json([], 400);
        }
    }
}
