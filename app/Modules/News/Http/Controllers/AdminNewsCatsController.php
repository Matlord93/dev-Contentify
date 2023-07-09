<?php

namespace App\Modules\News\Http\Controllers;

use App\Modules\News\NewsCat;
use BackController;
use Hover;
use ModelHandlerTrait;

class AdminNewsCatsController extends BackController
{

    use ModelHandlerTrait;

    protected $icon = 'newspaper';

    public function __construct()
    {
        $this->modelClass = NewsCat::class;

        parent::__construct();
    }

    public function index()
    {
        $this->indexPage([
            'tableHead' => [
                trans('app.id') => 'id', 
                trans('app.title') => 'title'
            ],
            'tableRow' => function(NewsCat $newsCat)
            {
                Hover::modelAttributes($newsCat, ['image', 'creator', 'updated_at']);

                return [
                    $newsCat->id,
                    raw(Hover::pull(), $newsCat->title)
                ];
            }
        ]);
    }
	
	public function callAction($method, $news_cat) 
    { 
        return parent::callAction($method, array_values($news_cat));
    }
}
