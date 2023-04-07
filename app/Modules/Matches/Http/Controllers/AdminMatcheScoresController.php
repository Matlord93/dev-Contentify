<?php

namespace App\Modules\Matches\Http\Controllers;

use App\Modules\Matches\MatcheScore;
use BackController;
use Request;
use Response;
use View;

class AdminMatcheScoresController extends BackController
{

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function store()
    {
        if (! $this->checkAccessCreate()) {
            return Response::make(null, 403);
        }

        $matcheScore = new MatcheScore(Request::all());

        $okay = $matcheScore->save();

        if (! $okay) {
            return Response::make(null, 400);
        } else {
            return View::make('matches::admin_map', compact('matcheScore'));
        }
    }

    /**
     * @param int $id The ID of the match score object
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function update(int $id)
    {
        if (! $this->checkAccessUpdate()) {
            return Response::make(null, 403);
        }

        $matcheScore = MatcheScore::findOrFail($id);
        $matcheScore->fill(Request::all());

        $okay = $matcheScore->save();

        if (! $okay) {
            return Response::make(null, 400);
        } else {
            return View::make('matches::admin_map', compact('matcheScore'));
        }
    }

    /**
     * @param int $id The ID of the match score object
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        if (! $this->checkAccessDelete()) {
            return Response::make(null, 403);
        }

        MatcheScore::destroy($id);

        return Response::make(null, 200);
    }
}
