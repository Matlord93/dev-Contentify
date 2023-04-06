<?php 

namespace App\Modules\Pages;

use SoftDeletingTrait;
use AbstractStiModel;

/**
 * @property \Carbon                    $created_at
 * @property \Carbon                    $deleted_at
 * @property \Carbon                    $published_at
 * @property string                     $title
 * @property string                     $slug
 * @property string                     $text
 * @property bool                       $published
 * @property bool                       $internal
 * @property bool                       $enable_comments
 * @property int                        $page_cat_id
 * @property int                        $access_counter
 * @property int                        $creator_id
 * @property int                        $updater_id
 * @property \App\Modules\Pages\PageCat $pageCat
 * @property \User                      $creator
 */
class Page extends AbstractStiModel
{

    use SoftDeletingTrait;

    /**
     * All categories are pre-defined, because they have a semantic meaning.
     * This is the ID of the blog posts category.
     */
    const ID_BLOG_POSTS = 1;

    /**
     * All categories are pre-defined, because they have a semantic meaning.
     * This is the ID of the custom pages category.
     */
    const ID_CUSTOM_PAGES = 2;

    /**
     * All categories are pre-defined, because they have a semantic meaning.
     * This is the ID of the custom content category.
     */
    const ID_CUSTOM_CONTENT = 3;

    protected $table = 'pages';

    protected $subclassField = 'page_cat_id';

    protected $dates = ['deleted_at', 'published_at'];

    protected $slugable = true;

    protected $fillable = [
        'title', 
        'text', 
        'published_at', 
        'published',
        'internal',
        'enable_comments',
        'page_cat_id'
    ];

    protected $rules = [
        'title'             => 'required|min:3',
        'published'         => 'boolean',
        'internal'          => 'boolean',
        'enable_comments'   => 'boolean',
        'page_cat_id'       => 'required|integer'
    ];

    public static $relationsData = [
        'pageCat' => [self::BELONGS_TO, 'App\Modules\Pages\PageCat'],
        'creator' => [self::BELONGS_TO, 'User', 'title' => 'username'],
    ];
}
