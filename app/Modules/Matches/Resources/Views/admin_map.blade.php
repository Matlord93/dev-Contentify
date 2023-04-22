<span class="item" data-id="{!! $matcheScore->id !!}" data-map-id="{!! $matcheScore->map->id !!}" data-left-score="{!! $matcheScore->left_score !!}" data-right-score="{!! $matcheScore->right_score !!}">
    @if ($matcheScore->map->image)
        <img src="{!! $matcheScore->map->uploadPath().'16/'.$matcheScore->map->image !!}" alt="Icon">
    @endif
    
    {!! $matcheScore->map->title !!}: <span class="score">{!! $matcheScore->left_score !!}:{!! $matcheScore->right_score !!}</span>
</span>