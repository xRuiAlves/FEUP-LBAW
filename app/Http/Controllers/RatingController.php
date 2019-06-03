<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

use App\Rating;
use App\Post;

class RatingController extends Controller
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
     * Upvote a post.
     */
    public function upvote(Request $request) {
        $this->authorize('rate', Rating::class);

        $validated_data = $request->validate([
            'post_id' => 'required'
        ]);

        $post_id = $validated_data["post_id"];
        $user_id = auth()->user()->id;

        DB::table('ratings')->updateOrInsert(
            ['post_id' => $post_id, 'user_id' => $user_id],
            ['value' => 1]
        );

        $post = Post::find($post_id);

        return response()->json([
            'rating' => $post->rating
        ], 200);
    }

    /**
     * Downvote a post.
     */
    public function downvote(Request $request) {
        $this->authorize('rate', Rating::class);

        $validated_data = $request->validate([
            'post_id' => 'required'
        ]);

        $post_id = $validated_data["post_id"];
        $user_id = auth()->user()->id;

        DB::table('ratings')->updateOrInsert(
            ['post_id' => $post_id, 'user_id' => $user_id],
            ['value' => -1]
        );

        $post = Post::find($post_id);

        return response()->json([
            'rating' => $post->rating
        ], 200);
    }
}
