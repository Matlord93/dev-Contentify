<div class="widget widget-matches-latest">
    <ul class="list-unstyled">
        @foreach($matches as $matche)
            <li>
                <a href="{{ url('matches/'.$matche->id) }}" title="{{ $matche->played_at }} | {{ $matche->tournament->title }}">
                    @if ($matche->right_team->image)
                        <img src="{!! $matche->right_team->uploadPath().$matche->right_team->image !!}" width="30" height="30" alt="{{ $matche->right_team->title }}">
                    @endif
                    <span class="right-team"><span class="vs">{{ trans('matches::vs') }}</span> {{ $matche->right_team->title }}</span>
                    <span class="scores">{!! $matche->scoreCode() !!}</span>
                </a>
            </li>
        @endforeach
    </ul>
</div>