<?php

namespace App\Objects;

class LanguagesAttributesObject
{
    public function __construct(
        public string $path,
        public $default=null,
    )
    {
    }
}