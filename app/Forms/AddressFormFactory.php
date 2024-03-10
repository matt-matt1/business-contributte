<?php

declare(strict_types=1);

namespace App\Forms;

use Contributte\FormsBootstrap\BootstrapForm;
//use Contributte\FormsBootstrap\Enums\DateTimeFormat;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Contributte\Translation\Exceptions\InvalidArgument;
//use App\Model\AddressFacade;
use Contributte\Translation\Translator;

final class AddressFormFactory
{
    public function __construct(
        private readonly Translator  $translator,
//        private readonly AddressFacade                   $addr,
//								private DocumentPresenter       $documentPresenter,
//								private TabsControlFactory      $tabsControlFactory,
//								private PillsControlFactory     $pillsControlFactory,
    )
    {
    }

    public function renderAddressForm($form, $address)
    {
//        $form = $this->getComponent('addressForm');
        $form->setDefaults($address);
    }

    /**
     * @throws InvalidArgument
     */
//    protected function createComponentAddressForm(): Form
    public function create(): BootstrapForm//Form
    {
//        $form = $this->formFactory->create();
//        BootstrapForm::switchBootstrapVersion(Enums\BootstrapVerion::V5)
        $form = new BootstrapForm;
//        $form->setRenderMode(RenderMode::SIDE_BY_SIDE_MODE);
        $form->renderMode = RenderMode::SIDE_BY_SIDE_MODE;
        $form->addText('street_address'/*, ucfirst($this->translator->translate('locale.street_address'))*/)
            ->setRequired(ucfirst($this->translator->translate('locale.street_address_required')))
            ->setPlaceholder(ucfirst($this->translator->translate('locale.street_address')))
            ->setAutocomplete(true);
        $form->addText('line2'/*, ucfirst($this->translator->translate('locale.line2'))*/)
            ->setPlaceholder(ucfirst($this->translator->translate('locale.line2')));
        $row = $form->addRow();
//        $row->setOption('class', 'form-group row d-flex mr-2');
        $row->addCell(4)
            ->addText('city', ucfirst($this->translator->translate('locale.city')));
        $row->addCell(3)// add class mx-5
            ->addText('province', ucfirst($this->translator->translate('locale.province')));
        $row->addCell(2)
            ->addText('post_code', ucfirst($this->translator->translate('locale.post_code')));
//        unset($row);
//        $form->addText('city', ucfirst($this->translator->translate('locale.city')));
//        $form->addText('province', ucfirst($this->translator->translate('locale.province')));
//        $form->addText('post_code', ucfirst($this->translator->translate('locale.post_code')));
//        DateTimeFormat::D_DMY_DASHES;
//        \DateInput::$addionalHtmlClasses = 'datepicker';
        $form->addDate('address_active', ucfirst($this->translator->translate('locale.active')))
            ->setFormat('d M Y');
//		$form->addSubmit('add', ucwords($this->translator->translate('Add')));
//
//		// Handle form submission
//		$form->onSuccess[] = function (Form $form, \stdClass $data): void {
//			try {
//				// Attempt to login user
//				$this->bus->insertObject($data);
//				$this->redirect('Dashboard:');
//			} catch (Nette\Security\AuthenticationException) {
//				$form->addError(ucwords($this->translator->translate('Failed to add this business.')));
//			}
//		};

        return $form;
    }

}