<?php

namespace App\Modules\Cups;

use BaseModel;
use Carbon;
use Config;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MsgException;
use User;

/**
 * @property \Carbon $created_at
 * @property \Carbon $deleted_at
 * @property int $round
 * @property int $row
 * @property bool $with_teams
 * @property int $left_participant_id
 * @property int $right_participant_id
 * @property int $winner_id
 * @property int $left_score
 * @property int $right_score
 * @property int $cup_id
 * @property bool $left_confirmed
 * @property bool $right_confirmed
 * @property int $next_match_id
 * @property int $access_counter
 * @property int $creator_id
 * @property int $updater_id
 * @property \App\Modules\Cups\Cup $cup
 * @property \User $creator
 * @property \User|\App\Modules\Cups\Team $left_participant
 * @property \User|\App\Modules\Cups\Team $right_participant
 */
class Match extends BaseModel
{
    /**
     * Name of the event that is fired when a new match has been generated
     */
    const EVENT_NAME_MATCH_GENERATED = 'contentify.cups.matchGenerated';

    public $table = 'cups_matches';

    protected $dates = ['deleted_at'];

    protected $slugable = true;

    protected $fillable = [
        'round',
        'row',
        'with_teams',
        'left_participant_id',
        'right_participant_id',
        'winner_id',
        'left_score',
        'right_score',
        'cup_id',
        'creator_id',
        'created_at',
    ];

    protected $rules = [
        'round'                 => 'integer|min:1',
        'row'                   => 'integer|min:1',
        'with_teams'            => 'boolean',
        'left_participant_id'   => 'integer|min:1',
        'right_participant_id'  => 'integer|min:1',
        'winner_id'             => 'integer|min:0',
        'left_score'            => 'integer|min:0',
        'right_score'           => 'integer|min:0',
        'cup_id'                => 'integer|min:1',
        'creator_id'            => 'integer|min:1',
        'created_at'            => 'required',
    ];

    public static $relationsData = [
        'cup'       => [self::BELONGS_TO, 'App\Modules\Cups\Cup'],
        'creator'   => [self::BELONGS_TO, 'User', 'title' => 'username'],
    ];

    /**
     * Relationship: Returns the left participant of this match
     * 
     * @return BelongsTo
     */
    public function left_participant() //phpcs:ignore PSR1.Methods.CamelCapsMethodName -- @TODO: will need refactoring in the future
    {
        if ($this->with_teams) {
            return $this->belongsTo('App\Modules\Cups\Team', 'left_participant_id');
        } else {
            return $this->belongsTo('User', 'left_participant_id');
        }
    }

    /**
     * Relationship: Returns the right participant of this match.
     * Be careful, can return null (= wildcard)!
     * 
     * @return BelongsTo
     */
    public function right_participant() //phpcs:ignore PSR1.Methods.CamelCapsMethodName -- @TODO: will need refactoring in the future
    {
        if ($this->with_teams) {
            return $this->belongsTo('App\Modules\Cups\Team', 'right_participant_id');
        } else {
            return $this->belongsTo('User', 'right_participant_id');
        }
    }

    /**
     * Returns true if the given user is allowed to confirm 
     * the result (score) of the left participant
     * 
     * @param  User|null $user
     * @return bool
     */
    public function canConfirmLeft(User $user = null) : bool
    {
        if ($user == null or $this->left_confirmed) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($this->left_participant == null) {
            return false;
        }

        if ($this->with_teams) {
            // This means every member of the team is allowed to confirm the result.
            // It does not matter if the member is an organizer.
            // This is just an arbitrary design decision.
            return ($this->left_participant->isMember($user));
        } else {
            return ($user->id == $this->left_participant->id);
        }
    }

    /**
     * Returns true if the given user is allowed to confirm 
     * the result (score) of the right participant
     * 
     * @param  User|null $user
     * @return bool
     */
    public function canConfirmRight($user = null) : bool
    {
        if ($user == null or $this->right_confirmed) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($this->right_participant == null) {
            return false;
        }

        if ($this->with_teams) {
            // This means every member of the team is allowed to confirm the result.
            // It does not matter if the member is an organizer.
            // This is just an arbitrary design decision.
            return ($this->right_participant->isMember($user));
        } else {
            return ($user->id == $this->right_participant->id);
        }
    }

    /**
     * Returns the match that is next / right to this match (in the bracket).
     * Returns null if the corresponding match is not yet generated (or if this match is the final match).
     * 
     * @return self
     */
    public function nextMatch() : self
    {
        return self::findOrFail($this->next_match_id);
    }

    /**
     * Returns the "partner" match of this match. Both matches share the same "next match".
     * Returns null if the corresponding match is not yet generated or not completed.
     * 
     * * @return self|null
     */
    public function partnerMatch()
    {
        if ($this->row % 2 == 1) {
            $partnerRow = $this->row + 1; // The match below (in the bracket)
        } else {
            $partnerRow = $this->row - 1; // The match above (in the bracket)
        }
        return self::whereCupId($this->cup_id)->whereRound($this->round)->whereRow($partnerRow)->first();
    }

    /**
     * Tries to generate the next match.
     * If this is possible, it returns the match. 
     * If not, it returns null.
     * 
     * @return self|null
     */
    public function generateNext()
    {
        if ($this->next_match_id) {
            return null;
        }

        // Only do the following checks if this is not a wildcard-match
        if ($this->right_participant_id) {
            if (! $this->left_confirmed or ! $this->right_confirmed) {
                return null;
            }

            // A draw is not possible. There has to be a winner.
            if ($this->left_score == $this->right_score) {
                $this->left_confirmed = false;
                $this->right_confirmed = false;
                $this->save();
                return null;
            }

            if ($this->left_score > $this->right_score) {
                $this->winner_id = $this->left_participant_id;
            } else {
                $this->winner_id = $this->right_participant_id;
            }
            $this->save();
        }

        // Cup completed?
        if ($this->round + 1 == $this->cup->rounds()) {
            $points = Config::get('cups::cup_points');
            if ($this->with_teams) {
                Team::find($this->winner_id)->increment('cup_points', $points);
            } else {
                User::find($this->winner_id)->increment('cup_points', $points);
            }

            $this->cup->closed = true;
            $this->cup->save();
            return null;
        }

        $partnerMatch = $this->partnerMatch();

        // Return if the corresponding match is not yet generated or not completed
        if (! $partnerMatch or $partnerMatch->winner_id == 0) {
            return null;
        }

        $matchData = [
            'round'                 => $this->round + 1,
            'row'                   => ceil($this->row / 2),
            'with_teams'            => $this->with_teams,
            'left_participant_id'   => $this->winner_id,
            'right_participant_id'  => $partnerMatch->winner_id,
            'cup_id'                => $this->cup_id,
            'creator_id'            => 1, // System
            'created_at'            => new Carbon,
        ];

        $newMatch = new Match($matchData);
        $newMatch->save();

        $this->next_match_id = $newMatch->id;
        $this->save();

        $partnerMatch->next_match_id = $newMatch->id;
        $this->save();
        
        event(self::EVENT_NAME_MATCH_GENERATED, [$newMatch]);

        return $newMatch;
    }
    
     /**
     * Tries to update the winner of a match (not of a wildcard-match!)
     * Does not check if the current user has the right to do this 
     * - this has to be checked before calling this method.
     * 
     * @return void
     * @throws MsgException
     */
    public function updateWinner()
    {
        $nextMatch = $match->nextMatch();
        
        if (! $match->right_participant_id or ! $match->winner_id or ! $nextMatch or $nextMatch->winner_id) {
            throw new MsgException(trans('app.not_possible'));
        }
        
        if ($match->left_participant_id == $match->winner_id) {
            $match->winner_id = $match->right_participant_id;
            $match->left_score = 0;
            $match->right_score = 1;
        } else {
            $match->winner_id = $match->left_participant_id;
            $match->left_score = 1;
            $match->right_score = 0;
        }
        
        if ($match->row == 2 * $nextMatch->row) {
            $nextMatch->right_participant_id = $match->winner_id;
        } else {
            $nextMatch->left_participant_id = $match->winner_id;
        }
        
        $match->forceSave();
        $nextMatch->forceSave();
    }
    
    /**
     * Confirms the results of a match
     *
     * @param int $leftScore The score of the participant on the left side
     * @param int $rightScore The score of the participant on the right side
     * @param bool $left If true, confirm the left result. If false, confirm the right.
     * @return Match|null Returns the next match or null if there is no next match
     * @throws MsgException
     */
    public function confirm(int $leftScore, int $rightScore, bool $left)
    {
        if ($leftScore == $rightScore) {
            throw new MsgException(trans('app.not_possible'));
        }

        if ($left) {
            if (! $this->canConfirmLeft(user())) {
                throw new MsgException(trans('app.not_possible'));
            }

            // If the result has been changed by the left participant, the right has to confirm it again
            if ($this->left_score != $leftScore or $this->right_score != $rightScore) {
                $this->right_confirmed = false;
            }

            $this->left_confirmed = true;
        } else {
            if (! $this->canConfirmRight(user())) {
                throw new MsgException(trans('app.not_possible'));
            }
 
            // If the result has been changed by the right participant, the left has to confirm it again
            if ($this->left_score != $leftScore or $this->right_score != $rightScore) {
                $this->left_confirmed = false;
            }

            $this->right_confirmed = true;
        }

        $this->left_score = $leftScore;
        $this->right_score = $rightScore;
        $this->save();

        $newMatch = $this->generateNext();

        // Create next matches for wildcard-matches
        if ($this->round == 1) {
            // Remember: Wildcard-matches can only appear in the first row (so we do not need to check this)
            $wildcards = Match::whereCupId($this->cup_id)->whereRightParticipantId(0)->whereNextMatchId(0)
                ->orderBy('row')->get();

            /** @var Match $wildcard */
            foreach ($wildcards as $wildcard) {
                // It's enough to create  the next match of one of the pair matches
                if ($wildcard->row % 2 == 1) { 
                    $wildcard->generateNext();
                }
            }
        }
        
        return $newMatch;
    }
}
