<div class="widget-matches-featured">
    <a href="{{ url('matches/'.$matche->id) }}">
        @if ($matche->right_team->image)
            <div>
                <img src="{!! $matche->right_team->uploadPath().$matche->right_team->image !!}" width="100" height="100" alt="{{ $matche->game->title }}">
            </div>
        @endif
        <span class="scores">{!! $matche->scoreCode() !!}</span> 
        <span class="right-team">{{ trans('matches::vs').' '.$matche->right_team->title }}</span>
        <div>
            <small class="tournament">{{ $matche->tournament->title }}</small> - 
            <small class="date">{{ $matche->played_at }}</small>
        </div>
    </a>
</div>