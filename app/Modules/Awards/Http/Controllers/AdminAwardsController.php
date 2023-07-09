<?php

namespace App\Modules\Awards\Http\Controllers;

use App\Modules\Awards\Award;
use BackController;
use HTML;
use ModelHandlerTrait;

class AdminAwardsController extends BackController
{

    use ModelHandlerTrait;

    protected $icon = 'trophy';

    public function __construct()
    {
        $this->modelClass = Award::class;

        parent::__construct();
    }

    public function index()
    {
        $this->indexPage([
            'tableHead' => [
                trans('app.id')         => 'id',
                trans('app.position')   => 'position',
                trans('app.title')      => 'title',
                trans('app.object_team')        => 'team',
                trans('app.date')       => 'achieved_at'
            ],
            'tableRow' => function(Award $award)
            {
                return [
                    $award->id,
                    raw($award->positionIcon()),
                    $award->title,
                    $award->team ? HTML::link('teams/'.$award->team->id.'/'.$award->team->slug, e($award->team->title)) : '',
                    $award->achieved_at
                ];
            }
        ]);
    }
}
