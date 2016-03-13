<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/3/13
 * Time: 15:27
 */

namespace weixin\qy\models;


abstract class Base
{
    abstract public function attributes();

    public function getAttributes()
    {
        $data = [];
        foreach ($this->attributes() as $attr)
            $data[$attr] = $this->$attr;

        return $data;
    }
}