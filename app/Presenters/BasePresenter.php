<?php

declare(strict_types = 1);

namespace App\Presenters;

use Nette;
use Contributte;

class BasePresenter extends Nette\Application\UI\Presenter
{

    /** @var Nette\Localization\ITranslator @inject */
    public $translator;

    /** @var Contributte\Translation\LocalesResolvers\Session @inject */
    public $translatorSessionResolver;


    public function handleChangeLocale(string $locale): void
    {
        $this->translatorSessionResolver->setLocale($locale);
        $this->redirect('this');
    }


    public function renderDefault(): void
    {
        $this->translator->translate('domain.message');
        $prefixedTranslator = $this->translator->createPrefixedTranslator('domain');
        $prefixedTranslator->translate('message');
    }

}
