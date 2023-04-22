<h1 class="page-title">{{ trans_object('match') }}</h1>

<div class="overview clearfix">
@section('matches-matche-overview')
    <div class="left">
        @if ($matche->left_team->image)
            <img src="{!! $matche->left_team->uploadPath().$matche->left_team->image !!}" alt="{{ $matche->left_team->title }}">
        @else
            <img src="{!! asset('img/logo_180.png') !!}" alt="{{ $matche->left_team->title }}">
        @endif
        <div class="team-name">
            <img src="{{ asset('uploads/countries/eu.png') }}"> 
            <a href="{{ url('/teams/'.$matche->left_team->id.'/'.$matche->left_team->slug) }}">{{ $matche->left_team->title }}</a>
        </div>
    </div>
    <div class="mid">
        {!! $matche->scoreCode() !!}
    </div>
    <div class="right">
        @if ($matche->right_team->image)
            <img src="{!! $matche->right_team->uploadPath().$matche->right_team->image !!}" alt="{{ $matche->right_team->title }}">
        @else
            <img src="{!! asset('img/default/no_opponent.png') !!}" alt="{{ $matche->right_team->title }}">
        @endif
        <div class="team-name">
            @if ($matche->right_team->country->icon)
                <img src="{{ $matche->right_team->country->uploadPath().$matche->right_team->country->icon }}">
            @endif
            @if ($matche->right_team->url)
                 <a href="{{ url($matche->right_team->url) }}" target="_blank">{{ $matche->right_team->title }}</a>
            @else
                {{ $matche->right_team->title }}
            @endif
        </div> 
    </div>
@show
</div>
<div class="details">
    <table class="table horizontal">
        <tbody>
        @section('matches-matche-details')
            <tr>
                <th>{!! trans('app.date') !!}</th>
                <td>{{ $matche->played_at->dateTime() }} - {{ $matche::$states[$matche->state] }}</td>
            </tr>
            <tr>
                <th>{!! trans('app.object_game') !!}</th>
                <td>{{ $matche->game->title }}</td>
            </tr>
            <tr>
                <th>{!! trans('app.object_tournament') !!}</th>
                <td>
                    @if ($matche->tournament->url)
                        <a href="{{ $matche->tournament->url }}" target="_blank"  title="{{ $matche->tournament->title }}">{{ $matche->tournament->title }}</a>
                    @else
                        {{ $matche->tournament->title }}
                    @endif
                </td>
            </tr>
            @if ($matche->url)
                <tr>
                    <th>{!! trans('app.url') !!}</th>
                    <td><a href="{{ $matche->url }}" target="_blank" title="{{ trans('app.object_matche')}} {{ trans('app.url') }}">{{ $matche->url }}</a></td>
                </tr>
            @endif
            @if ($matche->broadcast)
                <tr>
                    <th>{!! trans('matches::broadcast') !!}</th>
                    <td><a href="{{ $matche->broadcast }}" target="_blank" title="{{ trans('matches::broadcast') }}">{{ $matche->broadcast }}</a></td>
                </tr>
            @endif
            @if ($matche->left_lineup or $matche->right_lineup)
                <tr>
                    <th>{!! trans('matches::left_lineup') !!}</th>
                    <td>{{ $matche->left_lineup }}</td>
                </tr>
                <tr>
                    <th>{!! trans('matches::right_lineup') !!}</th>
                    <td>{{ $matche->right_lineup }}</td>
                </tr>
            @endif
        @show
        </tbody>
    </table>
</div>

@if ($matche->matche_scores)
    <div class="scores clearfix">
    @section('matches-matche-scores')
        @foreach ($matche->matche_scores as $matcheScore)
            <div class="item">
                @if ($matcheScore->map->image)
                    <img src="{!! $matcheScore->map->uploadPath().$matcheScore->map->image !!}" alt="{{ $matcheScore->map->title }}">
                @endif
                <span>{{ $matcheScore->map->title }}: {{ $matcheScore->left_score }}:{{ $matcheScore->right_score }}</span>
            </div>
        @endforeach
    @show
    </div>
@endif

@if ($matche->text)
    <p>
        {!! $matche->text !!}
    </p>
@endif

{!! Comments::show('matches', $matche->id) !!}
