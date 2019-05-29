@extends ('common.user')
@section ('content')

<h1 class="brand-header">質問編集</h1>

<div class="main-wrap">
  <div class="container">
    {!! Form::open(['route' => ['question.confirm', $question->id], 'method' => 'POST']) !!}
      {!! Form::input('hidden', 'id', $question->id, ['class' => 'form-control']) !!}
      {!! Form::input('hidden', 'user_id', Auth::id(), ['class' => 'form-control']) !!}
      <div class="form-group @if(!empty($errors->first('tag_category_id'))) has-error @endif">
        <select name='tag_category_id' class = "form-control selectpicker form-size-small" id ="pref_id">
          <option value="{{$question->TagCategory->id}}">{{$question->TagCategory->name}}</option>
            @foreach($tag_categories as $tag_category)
            <option value="{{$tag_category->id}}">{{ $tag_category->name }}</option>
          @endforeach
        </select>
        <span class="help-block">{{$errors->first('tag_category_id')}}</span>
      </div>
      <div class="form-group @if(!empty($errors->first('title'))) has-error @endif">
        <input class="form-control" placeholder="title" name="title" type="text" value="{{ $question->title }}">
        <span class="help-block">{{$errors->first('title')}}</span>
      </div>
      <div class="form-group @if(!empty($errors->first('content'))) has-error @endif">
        <textarea class="form-control" placeholder="content" name="content" type="text" cols="50" rows="10">{{ $question->content }}</textarea>
        <span class="help-block">{{$errors->first('content')}}</span>
      </div>
      <input name="confirm" class="btn btn-success pull-right" type="submit" value="update">
    {!! Form::close() !!}
  </div>
</div>

@endsection

