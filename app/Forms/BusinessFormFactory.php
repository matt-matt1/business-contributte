<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model\BusinessFacade;
use Contributte\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class BusinessFormFactory extends Control
{
    public function __construct(
        private readonly Translator  $translator,
        private readonly BusinessFacade $bus,
        private readonly FormFactory    $formFactory,
//								private DocumentPresenter       $documentPresenter,
//								private TabsControlFactory      $tabsControlFactory,
//								private PillsControlFactory     $pillsControlFactory,
    )
    {
//        $this->usr = $usr;
//        parent::__construct();
    }

    protected function createComponentBusinessForm(): Form
//    protected function create(): Form
    {
        $form = $this->formFactory->create();
        $form->addText('business_name', ucfirst($this->translator->translate('locale.title:')))
            ->setRequired(ucfirst($this->translator->translate('locale.business_name_required')));
        $form->addText('business_email', ucfirst($this->translator->translate('locale.email')));
        $form->addText('business_website', ucfirst($this->translator->translate('locale.website')));
        $form->addText('business_source', ucfirst($this->translator->translate('locale.source')));
        $form->addText('business_active', ucfirst($this->translator->translate('locale.active')));
        $form->addText('business_created', ucfirst($this->translator->translate('locale.created')))
            ->setDisabled();
        $form->addText('business_updated', ucfirst($this->translator->translate('locale.updated')))
            ->setDisabled();
//        $form->addSubmit('add', ucwords($this->translator->translate('Add')));
//
//        // Handle form submission
//        $form->onSuccess[] = function (Form $form, \stdClass $data): void {
//            try {
//                // Attempt to login user
//                $this->bus->insertObject($data);
//                $this->redirect('Dashboard:');
//            } catch (Nette\Security\AuthenticationException) {
//                $form->addError(ucwords($this->translator->translate('Failed to add this business.')));
//            }
//        };

        return $form;
    }

}