<?php

namespace App\Modules\Teams\Http\Controllers;

use App\Modules\Teams\Team;
use BackController;
use Hover;
use HTML;
use ModelHandlerTrait;
use Request;

class AdminTeamsController extends BackController
{

    use ModelHandlerTrait;

    protected $icon = 'flag';

    public function __construct()
    {
        $this->modelClass = Team::class;

        parent::__construct();
    }

    public function index()
    {
        $this->indexPage([
            'buttons'   => ['new', HTML::button(trans('app.object_members'), url('admin/members'), 'users')],
            'tableHead' => [
                trans('app.id')         => 'id',
                trans('app.published')  => 'published',
                trans('app.title')      => 'title',
                trans('app.category')   => 'team_cat_id'
            ],
            'tableRow' => function(Team $team)
            {
                Hover::modelAttributes($team, ['image', 'access_counter', 'creator', 'updated_at']);

                return [
                    $team->id,
                    raw($team->published ? HTML::fontIcon('check') : HTML::fontIcon('times')),
                    raw(Hover::pull().HTML::link('teams/'.$team->id.'/'.$team->slug, $team->title)),
                    $team->teamCat->title,
                ];
            }
        ]);
    }

    public function update(int $id)
    {

        $teams = Team::findOrFail($id);

        if (Request::hasFile('image')) {
            $result = $teams->uploadImage('image');
            if ($result) {
                return $result;
            }
        } elseif (Request::get('image') == '.') {
            $teams->deleteImage('image');
        }

        if (Request::hasFile('banner')) {
            $result = $teams->uploadImage('banner');
            if ($result) {
                return $result;
            }
        } elseif (Request::get('banner') == '.') {
            $teams->deleteImage('banner');
        }

        $teams->save();
    }

    /**
     * Returns the lineup of a team (for example an AJAX call)
     *
     * @param  int $id The ID of the team
     * @return string
     */
    public function lineup(int $id) : string
    {
        /** @var Team $team */
        $team = Team::findOrFail($id);

        $lineup = '';
        foreach ($team->members as $user) {
            if ($lineup) {
                $lineup .= ', ';
            }
            $lineup .= $user->username;
        }

        return $lineup;
    }

		public function callAction($method, $team) 
    { 
        return parent::callAction($method, array_values($team));
     }
	
}
