<?php

namespace App\Modules\Matches\Http\Controllers;

use App\Modules\Matches\Match1;
use DB;
use View;
use Widget;

class UpcomingMatchesWidget extends Widget
{

    public function render(array $parameters = []) : string
    {
        $limit = isset($parameters['limit']) ? (int) $parameters['limit'] : self::LIMIT;

        $matches = Match1::orderBy('played_at', 'ASC')->where('played_at', '>=', DB::raw('CURRENT_TIMESTAMP'))
            ->take($limit)->get();

        if ($matches) {
            return View::make('matches::widget', compact('matches'))->render();
        }

        return '';
    }
}
