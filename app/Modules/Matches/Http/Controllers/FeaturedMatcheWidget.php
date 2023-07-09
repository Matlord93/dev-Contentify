<?php

namespace App\Modules\Matches\Http\Controllers;

use App\Modules\Matches\Matche;
use View;
use Widget;

class FeaturedMatcheWidget extends Widget
{

    public function render(array $parameters = []) : string
    {
        $matche = Matche::orderBy('played_at', 'DESC')->whereFeatured(true)->where('state', '!=', Matche::STATE_HIDDEN)->first();

        if ($matche) {
            return View::make('matches::featured_widget', compact('matche'))->render();
        }

        return '';
    }
}
