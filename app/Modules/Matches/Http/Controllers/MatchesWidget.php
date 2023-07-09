<?php

namespace App\Modules\Matches\Http\Controllers;

use App\Modules\Matches\Matche;
use View;
use Widget;

class MatchesWidget extends Widget
{

    public function render(array $parameters = []) : string
    {
        $limit = isset($parameters['limit']) ? (int) $parameters['limit'] : self::LIMIT;

        $matches = Matche::whereState(Matche::STATE_CLOSED)->orderBy('played_at', 'DESC')->take($limit)->get();

        if ($matches) {
            return View::make('matches::widget', compact('matches'))->render();
        }

        return '';
    }
}
