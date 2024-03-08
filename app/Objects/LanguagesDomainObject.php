<?php

namespace App\Objects;

class LanguagesDomainObject
{
    public function __construct(
        public string $domain,
        public LanguagesLocaleObject $locale)
    {
    }
}