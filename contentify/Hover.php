<?php

namespace Contentify;

use BaseModel;
use HTML;

/**
 * The hover class is a helper class used in the backend to display tool tips in tables.
 */
class Hover
{

    /**
     * The content to display
     *
     * @var string|null
     */
    private $content = null;

    /**
     * Code for an HTML element that contains the content
     *
     * @var string
     */
    private $wrapperTag = '<div class="hover-ui">%%</div>';

    /**
     * Constructor.
     * 
     * @param string $text Text to add
     */
    public function __construct(string $text = '')
    {
        if ($text) {
            $this->text($text);
        }
    }

    public function __toString()
    {
        return $this->render();
    }

    /**
     * Adds text to the content.
     * 
     * @param string $text      The text to add
     * @param bool   $stripTags Strip HTML tags from the text?
     * @return self
     */
    public function text(string $text, bool $stripTags = false) : self
    {
        if ($stripTags) {
            $text = strip_tags($text);
        }

        if ($text) {
            $this->content .= $text;
        }

        return $this;
    }

    /**
     * Adds a text line to the content.
     * 
     * @param string $line The text line to add
     * @return self
     */
    public function line(string $line) : self
    {
        if ($line) {
            $this->content .= '<p>'.$line.'</p>';
        }

        return $this;
    }

    /**
     * Adds an image to the content.
     * 
     * @param string      $url        Image URL
     * @param string|null $alt        Image alt attribute
     * @param array       $attributes Image attributes
     * @return self
     */
    public function image(string $url, string $alt = null, array $attributes = []) : self
    {
        if ($url) {
            $this->content .= HTML::image($url, $alt, $attributes);
        }

        return $this;
    }

    /**
     * Adds a heading to the content
     * 
     * @param  string $heading The heading to add
     * @return self
     */
    public function heading(string $heading) : self
    {
        if ($heading) {
            $this->content .= '<h3>'.$heading.'</h3>';
        }

        return $this;
    }

    /**
     * Adds model attribute values to the content
     * 
     * @param BaseModel $model      A model object
     * @param string[]  $attributes Array of model attribute names
     * @return self
     */
    public function modelAttributes(BaseModel $model, array $attributes = []) : self
    {
        foreach ($attributes as $attribute) {
            switch ($attribute) {
                case 'icon':
                    if ($model->icon) {
                        $this->image($model->uploadPath().$model->icon);
                    }
                    break;
                case 'image':
                    if ($model->image) {
                        $this->image($model->uploadPath().$model->image);
                    }
                    break;
                case 'access_counter':
                    $this->line($model->access_counter.'x '.trans('app.accessed'));
                    break;
                case 'creator':
                    if ($model->creator) {
                        $this->line(trans('app.creator').': '.$model->creator->username);
                    }
                    break;
                case 'updated_at':
                    if ($model->updated_at and $model->updated_at != $model->created_at) {
                        $this->line(trans('app.updated_at').': '.$model->updated_at->dateTime());
                    }
                    break;
                default:
                    // Do nothing
            }
        }

        return $this;
    }

    /**
     * Clear the content
     * 
     * @return self
     */
    public function clear() : self
    {
        $this->content = null;

        return $this;
    }

    /**
     * Renders the hover UI element.
     * 
     * @return string
     */
    public function render() : string
    {
        if ($this->content) {
            return str_replace('%%', $this->content, $this->wrapperTag);
        } else {
            return '';
        }
    }

    /**
     * Renders the hover UI element and clears the content.
     * 
     * @return string
     */
    public function pull() : string
    {
        $output = $this->render();

        $this->clear();

        return $output;
    }
}
