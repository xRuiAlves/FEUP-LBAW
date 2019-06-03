<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

use App\Issue;

class IssueController extends Controller
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
     * Creates a new issue.
     */
    public function create(Request $request) {
        $this->authorize('create', Issue::class);

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required'
        ]);


        $issue = new Issue();
        $issue->creator_id = auth()->user()->id;
        
        // Issue title
        try {
            $issue->title = $request->input('title');
            $issue->save();
        } catch (QueryException $err) {
            if ($err->getCode() == 22001) {
                return response()->json([
                    'message' => 'The issue title must be, at most, 64 characters long.'
                ], 400);
            }
        }

        // Issue content
        try {
            $issue->content = $request->input('content');
            $issue->save();
        } catch (QueryException $err) {
            if ($err->getCode() == 22001) {
                return response()->json([
                    'message' => 'The issue\'s content message must be, at most, 600 characters long.'
                ], 400);
            }
        }

        return response()->json([], 200);
    }
}
