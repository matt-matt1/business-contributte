<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model\AddressFacade;
use Contributte\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class AddressControl extends Control
{
    public function __construct(
        private readonly Translator  $translator,
        private readonly AddressFacade                   $addr,
        private readonly FormFactory    $formFactory,
//								private DocumentPresenter       $documentPresenter,
//								private TabsControlFactory      $tabsControlFactory,
//								private PillsControlFactory     $pillsControlFactory,
    )
    {
//        $this->usr = $usr;
//        parent::__construct();
    }

    protected function createComponentAddressForm(): Form
    {
        $form = $this->formFactory->create();
        /*
         * 	address_id  Primary	int(11)			No	None		AUTO_INCREMENT
        2	business_id  Index	int(11)			No	0
        3	user_id  Index	int(11)			No	0
        4	street_address	varchar(50)	utf8_unicode_ci		No	None
        5	line2	varchar(50)	utf8_unicode_ci		No
        6	city	varchar(50)	utf8_unicode_ci		No
        7	province	varchar(50)	utf8_unicode_ci		No	ONTARIO	state
        8	post_code	varchar(10)	utf8_unicode_ci		No
        9	address_active	datetime
         */
        $form->addText('street_address', ucfirst($this->translator->translate('locale.street_address')))
            ->setRequired(ucfirst($this->translator->translate('locale.street_address_required')));
        $form->addText('line2', ucfirst($this->translator->translate('locale.line2')));
        $form->addText('city', ucfirst($this->translator->translate('locale.city')));
        $form->addText('province', ucfirst($this->translator->translate('locale.province')));
        $form->addText('post_code', ucfirst($this->translator->translate('locale.post_code')));
        $form->addText('address_active', ucfirst($this->translator->translate('locale.active')));

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