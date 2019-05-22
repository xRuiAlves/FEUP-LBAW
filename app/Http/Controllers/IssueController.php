<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $this->authorize('create', Issue::class);     // TODO: Why not working?

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required'
        ]);
        
        if ($validator->fails()) {
            // TODO: What to do here?
            return back();
        }

        $issue = new Issue();
        $issue->title = $request->input('title');
        $issue->content = $request->input('content');
        

        $issue->creator_id = auth()->user()->id;
        $issue->save();
        
        return back();
    }
}
