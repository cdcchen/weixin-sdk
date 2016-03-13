<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/3/13
 * Time: 15:26
 */

namespace weixin\qy\models;


class Article extends Base
{
    public $title;
    public $thumb_media_id;
    public $author;
    public $content_source_url;
    public $content;
    public $digest;
    public $show_cover_pic;

    public function attributes()
    {
        return ['title', 'thumb_media_id', 'author', 'content_source_url', 'content', 'digest', 'show_cover_pic'];
    }
}