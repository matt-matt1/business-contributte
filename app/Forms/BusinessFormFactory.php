<?php

declare(strict_types=1);

namespace App\Forms;

//use App\Model\BusinessFacade;
use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Contributte\Translation\Exceptions\InvalidArgument;
use Contributte\Translation\Translator;
//use Nette\Application\UI\Control;
//use Nette\Application\UI\Form;

final class BusinessFormFactory
{
    public function __construct(
        private readonly Translator  $translator,
//        private readonly BusinessFacade $bus,
//        private readonly FormFactory    $formFactory,
//								private DocumentPresenter       $documentPresenter,
//								private TabsControlFactory      $tabsControlFactory,
//								private PillsControlFactory     $pillsControlFactory,
    )
    {
//        $this->usr = $usr;
//        parent::__construct();
    }

    /**
     * @throws InvalidArgument
     */
    public function create(): BootstrapForm//Form
//    protected function create(): Form
    {
//        $form = $this->formFactory->create();
        $form = new BootstrapForm;
        $form->renderMode = RenderMode::SIDE_BY_SIDE_MODE;
        $form->addText('business_name', ucfirst($this->translator->translate('locale.title:')))
            ->setRequired(ucfirst($this->translator->translate('locale.business_name_required')));
        $form->addText('business_email', ucfirst($this->translator->translate('locale.email')));
        $form->addText('business_website', ucfirst($this->translator->translate('locale.website')));
        $form->addText('business_source', ucfirst($this->translator->translate('locale.source')));
        $row = $form->addRow()->setGridBreakPoint('md');
        $row->addCell(4)
            ->addDate('business_active', ucfirst($this->translator->translate('locale.active')))
            ->setFormat('d M Y');
        $row->addCell(3)
            ->addDate('business_created', ucfirst($this->translator->translate('locale.created')))
            ->setDisabled()
            ->setFormat('d M Y');// add class mx-5
        $row->addCell(3)
            ->addDate('business_updated', ucfirst($this->translator->translate('locale.updated')))
            ->setDisabled()
            ->setFormat('d M Y');
//        $form->addText('business_active', ucfirst($this->translator->translate('locale.active')));
//        $form->addText('business_created', ucfirst($this->translator->translate('locale.created')))
//            ->setDisabled();
//        $form->addText('business_updated', ucfirst($this->translator->translate('locale.updated')))
//            ->setDisabled();

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