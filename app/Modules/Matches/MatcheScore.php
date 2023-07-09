<?php

namespace App\Modules\Matches;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Maps\Map;

class MatcheScore extends Model
{

    protected $fillable = ['left_score', 'right_score', 'map_id', 'matche_id'];

    protected $table = 'match_scores';

    protected $rules = [
        'left_score'    => 'required|integer|min:0|max:20',
        'right_score'   => 'required|integer|min:0|max:20',
        'matche_id'      => 'required|integer',
        'map_id'        => 'required|integer',
    ];

    public function match()
    {
        return $this->belongsTo(Matche::class, 'matche_id');
    }

    public function map()
    {
        return $this->belongsTo(Map::class, 'map_id');
    }

    public static function boot()
    {
        parent::boot();

        static::saved(function($matcheScore)
        {
            $matcheScore->match->updateScore();
        });

        static::deleted(function($matcheScore)
        {
            $matcheScore->match->updateScore();
        });
    }
}
