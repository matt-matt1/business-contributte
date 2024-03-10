<?php

namespace App\Presenters;

use App\Model\BusinessFacade;
use Contributte\Translation\Translator;
use Nette;
use Nette\Application\UI\Form;

final class BusinessPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private readonly BusinessFacade $bus,
        private readonly Translator     $translator,
    ) {
    }

    public function render(int $id): void
    {
//        $this->template->addFilter('formPair', function ($control) {
//            $render = $control->form->renderer;
//            $render->attachForm($control->form);
//
//            return $render->renderPair($control);
//        });
        $business = $this->bus->getAll()
            ->get($id);
        if (!$business) {
            $this->error(ucfirst($this->translator->translate('locale.business_not_found')));
        }
        $this->template->business = $business;
    }
}