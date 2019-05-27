@extends ('common.user')
@section ('content')

<h2 class="brand-header">質問投稿</h2>
<div class="main-wrap">
  <div class="container">
    {!! Form::open(['route' => 'question.store']) !!}
      {!! Form::input('hidden', 'user_id', Auth::id(), ['class' => 'form-control']) !!}
      <div class="form-group @if(!empty($errors->first('tag_cayrgory-id'))) has-error @endif">
        <select name="tag_category_id" class = "form-control selectpicker form-size-small " id ="pref_id">
          <option value="">Select category</option>
          @foreach($tag_categories as $tag_category)
            <option value="{{$tag_category->id}}">{{ $tag_category->name }}</option>
          @endforeach
        </select>
        <span class="help-block">{{$errors->first('tag_category_id')}}</span>
      </div>
      <div class="form-group @if(!empty($errors->first('title'))) has-error @endif">
        {!! Form::input('text', 'title', null, ['class' => 'form-control', 'placeholder' => 'Title']) !!}
        <span class="help-block">{{$errors->first('title')}}</span>
      </div>
      <div class="form-group @if(!empty($errors->first('content'))) has-error @endif">
        {!! Form::textarea('content', null, ['class' => 'form-control', 'placeholder' => 'Please write down your question here...']) !!}
        <span class="help-block">{{$errors->first('content')}}</span>
      </div>
      <input name="confirm" class="btn btn-success pull-right" type="submit" value="create">
    {!! Form::close() !!}
  </div>
</div>

@endsection

