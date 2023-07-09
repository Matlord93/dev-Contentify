<?php

namespace App\Modules\Matches\Http\Controllers;

use App\Modules\Matches\Matche;
use DB;
use View;
use Widget;

class UpcomingMatchesWidget extends Widget
{

    public function render(array $parameters = []) : string
    {
        $limit = isset($parameters['limit']) ? (int) $parameters['limit'] : self::LIMIT;

        $matches = Matche::orderBy('played_at', 'ASC')->where('played_at', '>=', DB::raw('CURRENT_TIMESTAMP'))
            ->take($limit)->get();

        if ($matches) {
            return View::make('matches::widget', compact('matches'))->render();
        }

        return '';
    }
}
