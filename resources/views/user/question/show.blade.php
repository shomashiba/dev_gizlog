@extends ('common.user')
@section ('content')

<h1 class="brand-header">質問詳細</h1>
<div class="main-wrap">
  <div class="panel panel-success">
    <div class="panel-heading">
      <img src="{{ $question->User->avatar }}" class="avatar-img">
      <p>&nbsp;{{ $question->User->name }}さんの質問&nbsp;&nbsp;(&nbsp;{{ $question->TagCategory->name }}&nbsp;)&nbsp;{{ $question->created_at->format('Y-m-d H:i:s') }}</p>
      <p class="question-date"></p>
    </div>
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <tbody>
          <tr>
            <th class="table-column">Title</th>
            <td class="td-text">{{ $question->title }}</td>
          </tr>
          <tr>
            <th class="table-column">Question</th>
            <td class='td-text'>{{ $question->content }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
    <div class="comment-list">
      @foreach ($question->comment as $comment)
        <div class="comment-wrap">
          <div class="comment-title">
            <img src="{{ $comment->User->avatar }}" class="avatar-img">
            <p>{{$comment->user->name}}</p>
            <p class="comment-date">{{ $comment->created_at->format('Y-m-d H:i:s') }}</p>
          </div>
          <div class="comment-body">{{ $comment->comment}}</div>
        </div>
      @endforeach
    </div>
  <div class="comment-box">
    <form action="{{ route('question.commentStore',$question->id) }}" method="post">
      @csrf
      <input name="user_id" type="hidden" value="{{Auth::user()->id }}">
      <input name="question_id" type="hidden" value="{{$question->id}}">
      <div class="comment-title">
        <img src="{{ Auth::user()->avatar }}" class="avatar-img"><p>コメントを投稿する</p>
      </div>
      <div class="comment-body @if(!empty($errors->first('comment'))) has-error @endif">
        <textarea class="form-control" placeholder="Add your comment..." name="comment" cols="50" rows="10"></textarea>
        <span class="help-block">{{$errors->first('comment')}}</span>
      </div>
      <div class="comment-bottom">
        <button type="submit" class="btn btn-success">
          <i class="fa fa-pencil" aria-hidden="true"></i>
        </button>
      </div>
    </form>
  </div>
</div>
@endsection