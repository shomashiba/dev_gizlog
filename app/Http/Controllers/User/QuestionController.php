<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\QuestionsRequest;
use App\Models\Question;
use App\Models\TagCategory;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    protected $question;
    protected $tag_category;
    protected $comment;

    public function __construct(Question $question , TagCategory $tag_category , Comment $comment)
    {
        $this->middleware('auth');
        $this->question = $question;
        $this->tag_category = $tag_category;
        $this->comment = $comment;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        //$inputs = $request->all();

        $questions = $this->question->all();
        $tag_categories = $this->tag_category->all();
        return view('user.question.index', compact('questions','tag_categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tag_categories = $this->tag_category->all();
        return view('user.question.create', compact('tag_categories'));
    }

    public function confirm()
    {
        return view('user.question.confirm');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs = $request->all();
        $this->question->create($inputs);
        return redirect()->to('question');
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function showMypage($userId)
    {
        $userId = Auth::id();
        //$inputs = $request->all();
        $questions = $this->question->fetchQuestion($userId);
        return view('user.question.mypage', compact('questions'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $question = $this->question->find($id);
        return view('user.question.show', compact('question'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $question = $this->question->find($id);
        $tag_categories = $this->tag_category->all();
        return view('user.question.edit',compact('question','tag_categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $inputs = $request->all();
        $this->question->find($id)->fill($inputs)->save();
        return redirect()->to('question');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->question->find($id)->delete();
        return redirect()->to('question');
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function commentStore(Request $request)
    {
        $inputs = $request->all();
        $this->comment->create($inputs);
        return redirect()->to('question');
    }
}
