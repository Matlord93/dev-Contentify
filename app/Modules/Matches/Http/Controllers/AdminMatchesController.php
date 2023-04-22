<?php

namespace App\Modules\Matches\Http\Controllers;

use App\Modules\Maps\Map;
use App\Modules\Matches\Matche;
use BackController;
use Hover;
use HTML;
use ModelHandlerTrait;

class AdminMatchesController extends BackController
{

    use ModelHandlerTrait {
        create as traitCreate;
    }

    protected $icon = 'crosshairs';

    public function __construct()
    {
        $this->modelClass = Matche::class;

        parent::__construct();
    }

    public function index()
    {
        $this->indexPage([
            'searchFor' => ['leftTeam', 'title'], 
            'tableHead' => [
                trans('app.id')                 => 'id',
                trans('app.state')              => 'state',
                trans('matches::left_team')     => 'left_team_id',
                trans('matches::right_team')    => 'right_team_id',
                trans('app.object_tournament')  => 'tournament_id',
                trans('matches::played_at')     => 'played_at'
            ],
            'tableRow' => function(Matche $matche)
            {
                Hover::modelAttributes($matche, ['access_counter', 'creator', 'updated_at']);

                return [
                    $matche->id,
                    raw($matche->state == 1 ? HTML::fontIcon('check') : HTML::fontIcon('times')),
                    raw(Hover::pull().HTML::link('matches/'.$matche->id, $matche->left_team->title)),
                    raw(HTML::link('matches/'.$matche->id, $matche->right_team->title)),
                    $matche->tournament->short,
                    $matche->played_at
                ];
            }
        ]);
    }

    public function create()
    {
        $this->traitCreate();

        $maps = Map::all();

        $this->layout->page->with('maps', $maps);
    }
	
	public function callAction($method, $matche) 
    { 
        return parent::callAction($method, array_values($matche));
     }
}
