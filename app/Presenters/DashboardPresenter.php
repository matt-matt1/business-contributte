<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\FormFactory;
use Contributte\Translation\Exceptions\InvalidArgument;
use Contributte\Translation\Translator;
use Nette;
//use App\Controls\PillsControl;
//use App\Controls\TabsControl;
//use App\Factories\PillsControlFactory;
//use App\Factories\TabsControlFactory;
use App\Model\ActionFacade;
use App\Model\AddressFacade;
use App\Model\BusinessContactFacade;
use App\Model\BusinessFacade;
use App\Model\ContactMethodFacade;
use App\Model\DocumentFacade;
use App\Model\JournalFacade;
use App\Model\UserFacade;
//use Nette\Localization\Translator;
use Nette\Application\UI\Form;
use Ublaboo\DataGrid\DataGrid;
use App\Forms\BusinessFormFactory;
use App\Forms\ContactControl;
use App\Forms\AddressControl;
use Ublaboo\DataGrid\Exception\DataGridException;


/**
 * Presenter for the dashboard view.
 * Ensures the user is logged in before access.
 */
final class DashboardPresenter extends Nette\Application\UI\Presenter
{
//    private UserFacade $usr;

    public function __construct(
        private readonly BusinessFormFactory   $businessForm,
        private readonly AddressControl        $addressForm,
        private readonly ContactControl        $contactForm,
        private readonly Translator            $translator,
        private readonly BusinessFacade        $bus,
//								private UserPresenter           $userPresenter,
        private readonly UserFacade            $userFacade,
        private readonly AddressFacade         $addr,
        private readonly JournalFacade         $jnl,
//								private JournalActionsPresenter $actionsPresenter,
        private readonly BusinessContactFacade $con,
        private readonly ContactMethodFacade   $cm,
//								private ContactMethodsPresenter $methodsPresenter,
        private readonly ActionFacade          $act,
        private readonly FormFactory           $formFactory,
        private readonly DocumentFacade        $doc,
//        private readonly FormFactory $formFactory,
//								private DocumentPresenter       $documentPresenter,
//								private TabsControlFactory      $tabsControlFactory,
//								private PillsControlFactory     $pillsControlFactory,
	)
	{
//        $this->usr = $usr;
        parent::__construct();
//        $this->com('businessForm') = $businessForm;
//        $this->getComponent('addressForm') = $addressForm
	}
/*
    protected function createComponentBusinessForm()
    {
        $form = new BusinessFormFactory;
        return $form;
    }
*/
    public function beforeRender(): void
    {
        $this->template->locale = filter_input(INPUT_GET, 'locale');// $_GET['locale'];
    }

    // Incorporates methods to check user login status
	//use RequireLoggedUser;
    /**
     * @param $name
     * @return void
     * @throws InvalidArgument
     * @throws DataGridException
     */
    public function createComponentBusinessGrid($name): void
    {
		$grid = new DataGrid($this, $name);
		$grid->setPrimaryKey('business_id');
		$grid->setDataSource($this->bus->getAll());
        $grid->setItemsPerPageList([20, 50, 100]);
//		$grid->addColumnNumber('id', 'ID', 'business_id');
		$grid->addColumnText('name',
            ucfirst($this->translator->translate('locale.name')), 'business_name')->setSortable()->setFilterText();
		$grid->addColumnText('email',
            ucfirst($this->translator->translate('locale.email')), 'business_email')->setSortable()->setFilterText();
		$grid->addColumnText('website',
            ucfirst($this->translator->translate('locale.website')), 'business_website')->setSortable()->setFilterText();
		$grid->addColumnText('source',
            ucfirst($this->translator->translate('locale.source')), 'business_source')->setSortable()->setFilterText();
		$grid->addColumnText('active',
            ucfirst($this->translator->translate('locale.active')), 'business_active');
        $grid->addAction('more', 'More', null, ['id' => 'business_id']);
/*        $presenter = $this;

        $grid->setItemsDetail();

        $grid->setItemsDetailForm(function(Nette\Forms\Container $container) use ($grid, $presenter) {
            $container->addHidden('id');
            $container->addText('name');

            $container->addSubmit('save', 'Save')
                ->setValidationScope([$container])
                ->onClick[] = function($button) use ($grid, $presenter) {
                $values = $button->getParent()->getValues();

                $presenter['examplesGrid']->redrawItem($values->id);
            };
        });*/
	}

    protected function createComponentBusinessForm(): Form
//    protected function create(): Form
    {
        $form = $this->formFactory->create();
        $form->addText('business_name', ucfirst($this->translator->translate('locale.title')))
            ->setRequired(ucfirst($this->translator->translate('locale.business_name_required')));
        $form->addText('business_email', ucfirst($this->translator->translate('locale.email')));
        $form->addText('business_website', ucfirst($this->translator->translate('locale.website')));
        $form->addText('business_source', ucfirst($this->translator->translate('locale.source')));
        $form->addDate('business_active', ucfirst($this->translator->translate('locale.active')));
        $form->addDate('business_created', ucfirst($this->translator->translate('locale.created')))
            ->setHtmlAttribute('readonly', true);
        $form->addDate('business_updated', ucfirst($this->translator->translate('locale.updated')))
            ->setHtmlAttribute('readonly', true);
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
        $form->addDate('address_active', ucfirst($this->translator->translate('locale.active')));

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

    protected function createComponentContactForm(): Form
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

//    public function handleMore($id)
    public function renderMore($id): void
    {
        $business = $this->bus->getAll()->get($id);
        /*        $this->flashMessage("Item deleted [$id] (actually, it was not)", 'info');

                if ($this->isAjax()) {
                    $this->redrawControl('flashes');
                    $this['actionsGrid']->reload();
                } else {
                    $this->redirect('this');
                }*/
        if (!$business)
            $this->error('locale.business_not_found');
//        $this->template->setFile('more.latte');
        $form = $this->getComponent('businessForm');
//        $form = new BusinessFormFactory($this->translator, $this->bus, $this->formFactory);
//        $form = $this->businessForm->getComponent('businessForm');
        $this->template->business = $business;
        $form->setDefaults($business->toArray());

        $created = $this->jnl->getAll()
            ->where('business_id', $business->business_id)
            ->where('action_id', 3)
            ->min('date');
        if ($created) {
            $form->setValues(['business_created' => $created]);
//            $form->setDefaults($created);//business_created
//            $this->template->created = $created;
//            ($business->toArray())['created'] = $created;
        }

        $updated = $this->jnl->getAll()
            ->where('business_id', $business->business_id)
            ->where('action_id', 2)
            ->max('date');
        if ($updated) {
            $form->setValues(['business_updated' => $updated]);
//            $form->setDefaults($updated);//business_updated
//            $this->template->updated = $updated;
//            ($business->toArray())['updated'] = $updated;
        }

        $addresses = $this->addr->getAll()
            ->select('*')
            ->where('business_id', $business->business_id)
            ->fetchAll();
//        if (count($addresses) == 1)
//            $this->template->address = reset($addresses);
        $this->template->addresses = $addresses;
//        $form->setDefaults($address);

        $contacts = $this->con->getAll()
            ->select('*')
            ->where('business_id', $business->business_id)
            ->fetchAll();
        $this->template->contacts = $contacts;
    }
}
