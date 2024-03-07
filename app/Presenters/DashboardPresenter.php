<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\FormFactory;
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
use Contributte\Application\UI\BasePresenter;
use Ublaboo\DataGrid\DataGrid;


/**
 * Presenter for the dashboard view.
 * Ensures the user is logged in before access.
 */
final class DashboardPresenter extends Nette\Application\UI\Presenter
{
    private UserFacade $usr;

    public function __construct(
        private readonly Translator  $translator,
	                            private readonly BusinessFacade $bus,
//								private UserPresenter           $userPresenter,
        private readonly UserFacade                      $userFacade,
        private readonly AddressFacade                   $addr,
        private readonly JournalFacade                   $jnl,
//								private JournalActionsPresenter $actionsPresenter,
        private readonly BusinessContactFacade           $con,
        private readonly ContactMethodFacade             $cm,
//								private ContactMethodsPresenter $methodsPresenter,
        private readonly ActionFacade                    $act,
                                private readonly FormFactory    $formFactory,
        private readonly DocumentFacade                  $doc,
//								private DocumentPresenter       $documentPresenter,
//								private TabsControlFactory      $tabsControlFactory,
//								private PillsControlFactory     $pillsControlFactory,
	)
	{
//        $this->usr = $usr;
        parent::__construct();
	}

	// Incorporates methods to check user login status
	//use RequireLoggedUser;
	public function createComponentBusinessGrid($name)
	{
		$grid = new DataGrid($this, $name);
//		$grid->setDataSource($this->db->select('*')->from('ublaboo_example'));
		$grid->setPrimaryKey('business_id');
		$grid->setDataSource($this->bus->getAll());
        $grid->setItemsPerPageList([20, 50, 100], true);
//		$grid->addColumnNumber('id', 'ID', 'business_id');
		$grid->addColumnText('name', ucfirst($this->translator->translate('locale.name')), 'business_name')->setSortable()->setFilterText();
		$grid->addColumnText('email', ucfirst($this->translator->translate('locale.email')), 'business_email')->setSortable()->setFilterText();
		$grid->addColumnText('website', ucfirst($this->translator->translate('locale.website')), 'business_website')->setSortable()->setFilterText();
		$grid->addColumnText('source', ucfirst($this->translator->translate('locale.source')), 'business_source')->setSortable()->setFilterText();
		$grid->addColumnText('active', ucfirst($this->translator->translate('locale.active')), 'business_active');
        $grid->addAction('more', 'More', null, ['id' => 'business_id']);
        $presenter = $this;
/*
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

    protected function createComponentBusinessForm(): Nette\Application\UI\Form
    {
        $form = $this->formFactory->create();
		$form->addText('business_name', ucfirst($this->translator->translate('locale.title:')))
			->setRequired(ucfirst($this->translator->translate('locale.business_name_required')));
        $form->addText('business_email', ucfirst($this->translator->translate('locale.email')));
        $form->addText('business_website', ucfirst($this->translator->translate('locale.website')));
        $form->addText('business_source', ucfirst($this->translator->translate('locale.source')));
        $form->addText('business_active', ucfirst($this->translator->translate('locale.active')));
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

    protected function createComponentAddressForm(): Nette\Application\UI\Form
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

//    public function handleMore($id)
    public function renderMore($id)
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
        $this->template->business = $business;
        $form = $this->getComponent('businessForm');
        $form->setDefaults($business->toArray());
        $addresses = $this->addr->getAll()
            ->select('*')
            ->where('business_id', $business->business_id)
            ->fetchAll();
        $contacts = $this->con->getAll()
            ->select('*')
            ->where('business_id', $business->business_id)
            ->fetchAll();
        $created = $this->jnl->getAll()
//            ->select('*')
            ->where('business_id', $business->business_id)
            ->where('action_id', 3)
            ->min('date');
//            ->fetch();
        $updated = $this->jnl->getAll()
//            ->select('*')
            ->where('business_id', $business->business_id)
            ->where('action_id', 2)
            ->max('date');
//            ->fetch();
        $this->template->addresses = $addresses;
//        $form->setDefaults($address);
    }
}
