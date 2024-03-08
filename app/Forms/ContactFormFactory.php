<?php

declare(strict_types=1);

namespace App\Forms;


use App\Model\BusinessContactFacade;
use App\Model\BusinessFacade;
use App\Model\ContactMethodFacade;
use Contributte\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

final class ContactFormFactory
{
    public function __construct(
        private readonly Translator  $translator,
        private readonly BusinessFacade $bus,
        private readonly BusinessContactFacade           $con,
        private readonly ContactMethodFacade             $cm,
        private readonly FormFactory    $formFactory,
//								private DocumentPresenter       $documentPresenter,
//								private TabsControlFactory      $tabsControlFactory,
//								private PillsControlFactory     $pillsControlFactory,
    )
    {
//        $this->usr = $usr;
//        parent::__construct();
    }

    /*
     * contact_id  Primary	int(11)			No	None		AUTO_INCREMENT
    2	business_id	int(11)			Yes	NULL
    3	user_id	int(11)			Yes	NULL
    4	contact_first	varchar(100)	utf8_unicode_ci		No	None
    5	contact_last	varchar(100)	utf8_unicode_ci		No	None
    6	contact_type_id  Index	int(11)			No	None
    7	contact_number	varchar(50)
     */

    public function create(): Form
    {
        $form = $this->formFactory->create();
        $form->addText('contact_first', ucfirst($this->translator->translate('locale.firstname')));
//            ->setRequired(ucfirst($this->translator->translate('locale.firstname_required')));
        $form->addText('contact_last', ucfirst($this->translator->translate('locale.lastname')));
        $form->addSelect('contact_type_id',
            ucfirst($this->translator->translate('locale.method')),
            $this->cm->getAll()->fetchPairs('type_id', 'name')
        );
        $form->addText('contact_number', ucfirst($this->translator->translate('locale.value')));
        $form->addDate('contact_active', ucfirst($this->translator->translate('locale.active')));
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