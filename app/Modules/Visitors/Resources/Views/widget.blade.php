<div class="widget widget-visitors">
    <ul class="list-inline">
    @section('visitors-widget-stats')
        <li>
            <span>{!! trans('app.today') !!}:</span> {!! $today !!}
        </li>
        <li>
            <span>{!! trans('app.yesterday') !!}:</span> {!! $yesterday !!}
        </li>
        <li>
            <span>{!! trans('app.month') !!}:</span> {!! $month !!}
        </li>
        <li>
            <span>{!! trans('app.total') !!}:</span> {!! $total !!}
        </li>
    @show
    </ul>
</div>
