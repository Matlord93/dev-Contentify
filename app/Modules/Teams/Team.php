<?php

namespace App\Modules\Teams;

use BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use SoftDeletingTrait;
use Request;
use Contentify\Uploader;
use File;
use InterImage;


/**
 * @property \Carbon                        $created_at
 * @property \Carbon                        $deleted_at
 * @property string                         $title
 * @property string                         $slug
 * @property string                         $short
 * @property string                         $text
 * @property int                            $position
 * @property bool                           $published
 * @property int                            $team_cat_id
 * @property int                            $country_id
 * @property string                         $image          Logo Image (square)
 * @property string                         $banner         Banner image (rectangle)
 * @property int                            $access_counter
 * @property int                            $creator_id
 * @property int                            $updater_id
 * @property \App\Modules\Matches\Matche[]   $matches
 * @property \User[]                        $members
 * @property \App\Modules\Teams\TeamCat     $teamCat
 * @property \App\Modules\Countries\Country $country
 * @property \App\Modules\Awards\Award[]    $awards
 * @property \User                          $creator
 * @property \User[]                        $users
 */
class Team extends BaseModel
{

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    protected $slugable = true;

    protected $fillable = ['title', 'text', 'position', 'published', 'team_cat_id', 'country_id'];

    protected $rules = [
        'title'         => 'required|min:3',
        'position'      => 'nullable||integer',
        'published'     => 'boolean',
        'team_cat_id'   => 'required|integer',
        'country_id'    => 'nullable|integer',
    ];

    public static $relationsData = [
        'matches'   => [self::HAS_MANY, 'App\Modules\Matches\Matche', 'foreignKey' => 'left_team_id', 'dependency' => true],
        'members'   => [self::BELONGS_TO_MANY, 'User'],
        'teamCat'   => [self::BELONGS_TO, 'App\Modules\Teams\TeamCat'],
        'country'   => [self::BELONGS_TO, 'App\Modules\Countries\Country'],
        'awards'    => [self::HAS_MANY, 'App\Modules\Awards\Award', 'dependency' => true],
        'creator'   => [self::BELONGS_TO, 'User', 'title' => 'username'],
    ];

    /**
     * The BaseModel's handleRelationalArray() method does not support
     * orderBy() for pivot attributes, so we have to use old-school Eloquent instead.
     *
     * @return BelongsToMany
     */
    public function users() : BelongsToMany
    {
        return $this->belongsToMany('User')->withPivot('task', 'description', 'position')
            ->orderBy('pivot_position', 'asc');
    }

    /**
     * Select only those that have been published
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePublished(Builder $query) : Builder
    {
        return $query->wherePublished(true);
    }
	
	    public function uploadImage(string $fieldName)
    {
        $file       = Request::file($fieldName);
        $extension  = $file->getClientOriginalExtension();

        try {
            $imgData = getimagesize($file->getRealPath()); // Try to gather info about the image
        } catch (Exception $e) {
            // Do nothing
        }

        if (! in_array(strtolower($extension), Uploader::ALLOWED_IMG_EXTENSIONS)) {
            return Redirect::route('teams.edit', [$this->id])
            ->withInput()->withErrors([trans('app.invalid_image')]);
        }

        // Check if image has a size. If not, it's not an image. Does not work for SVGs.
        if (strtolower($extension) !== 'svg' and (! isset($imgData[2]) or ! $imgData[2])) {
            return Redirect::route('teams.edit', [$this->id])
                ->withInput()->withErrors([trans('app.invalid_image')]);
        }

        $filePath = public_path().'/uploads/teams/';

        if (File::exists($filePath.$this->getOriginal($fieldName))) {
            File::delete($filePath.$this->getOriginal($fieldName));
        }

        $filename           = $this->id.'_'.$fieldName.'.'.$extension;
        $uploadedFile       = $file->move($filePath, $filename);
        $this->$fieldName   = $filename;
        $this->save();

        if ($fieldName == 'image') {
            if (File::exists($filePath.'80/'.$this->getOriginal($fieldName))) {
                File::delete($filePath.'80/'.$this->getOriginal($fieldName));
            }

            InterImage::make($filePath.'/'.$filename)->resize(80, 80, function ($constraint) {
                /** @var \Intervention\Image\Constraint $constraint */
                $constraint->aspectRatio();
            })->save($filePath.'80/'.$filename);
        }
    }
}
