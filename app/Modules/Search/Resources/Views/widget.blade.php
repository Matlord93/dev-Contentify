<div class="widget widget-global-search">
    {!! Form::open(['url' => 'search/create']) !!}
        <input name="_created_at" type="hidden" value="{!! time() !!}">

        {!! Form::smartText('subject', trans('app.subject')) !!}

        {!! Form::actions(['submit' => trans('app.search')], false) !!}
    {!! Form::close() !!}
</div>
