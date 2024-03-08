<?php

namespace App\Objects;

class LanguagesLocaleObject
{
//    public $path = '';
//    public $default = null;

    public function __construct(
        public string $code,
        public LanguagesAttributesObject $attr,
    )
    {
//        if (isset($array['path']))
//            $this->path = $array['path'];
//        if (isset($array['default']))
//            $this->default = $array['default'];
    }
}