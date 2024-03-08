<?php

namespace App\Objects;

class LanguagesObject
{
    private $domains;

    public function __construct(
        public LanguagesDomainObject $domain)
    {
            $this->domains[] = $domain;
    }
}