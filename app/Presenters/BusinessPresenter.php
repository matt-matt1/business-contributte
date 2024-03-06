<?php

namespace App\Presenters;

use App\Model\BusinessFacade;
use Nette;
use Nette\Application\UI\Form;

final class BusinessPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private BusinessFacade $bus,
    ) {
    }

    public function render(int $id): void
    {
        $business = $this->bus->getAll()
            ->get($id);
        if (!$business) {
            $this->error('Business not found');
        }
        $this->template->business = $business;
    }
}