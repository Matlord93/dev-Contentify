<?php

namespace App\Modules\Matches\Http\Controllers;

use App\Modules\Matches\Matche;
use FrontController;
use HTML;

class MatchesController extends FrontController
{

    public function __construct()
    {
        $this->modelClass = Matche::class;

        parent::__construct();
    }

    public function index()
    {
        $this->pageView('matches::filter');

        $this->indexPage([
            'buttons'       => null,
            'brightenFirst' => false,
            'filter'        => true,
            'searchFor' => ['rightTeam', 'title'], 
            'tableHead'     => [
                trans('app.date')               => 'played_at',
                trans('app.object_game')        => 'game_id',
                trans('matches::right_team')    => 'right_team_id',
                trans('matches::score')         => 'left_score'
            ],
            'tableRow'      => function(Matche $matche)
            {
                if ($matche->game->icon) {
                    $game = HTML::image(
                        $matche->game->uploadPath().$matche->game->icon, 
                        $matche->game->title, 
                        ['width' => 16, 'height' => 16]
                    );
                } else {
                    $game = null;
                }

                return [
                    $matche->played_at,
                    raw($game),
                    raw(HTML::link(url('matches/'.$matche->id), $matche->right_team->title)),
                    raw($matche->scoreCode())
                ];
            },
            'actions'       => null,
            'pageTitle'     => false,
        ], 'front');
    }

    /**
     * Show a matche
     *
     * @param  int $id The ID of the matche
     * @return void
     * @throws \Exception
     */
    public function show(int $id)
    {
        /** @var matche $matche */
        $matche = matche::findOrFail($id);

        $matche->access_counter++;
        $matche->save();

        $this->title($matche->getTitle());

        $this->pageView('matches::show', compact('matche'));
    }
}
