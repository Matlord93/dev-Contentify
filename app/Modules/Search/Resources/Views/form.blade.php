<h1 class="page-title">{!! trans('app.search') !!}</h1>

{!! Form::errors($errors) !!}

@if (isset($resultBags) and count($resultBags) == 0)
    <p>{{ trans('search::no_results') }}</p>
@endif

{!! Form::open(['url' => 'search/create']) !!}
    <input name="_created_at" type="hidden" value="{!! time() !!}">

    {!! Form::smartText('subject', trans('app.term')) !!}

    {!! Form::actions(['submit' => trans('app.search')], false) !!}
{!! Form::close() !!}

@section('search-results')
    @if (isset($resultBags))
        @foreach ($resultBags as $resultBag)
            <h3>{{ trans('search::results_type') }} "{{ trans('app.object_'.$resultBag['title']) }}":</h3>

            <ul>
                @foreach ($resultBag['results'] as $title => $url)
                    <li>{!! HTML::link($url, $title) !!}</li>
                @endforeach
            </ul>
        @endforeach
    @endif
@show
