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
use App\Forms\ContactFormFactory;
use App\Forms\AddressFormFactory;
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
        private readonly AddressFormFactory    $addressForm,
        private readonly ContactFormFactory    $contactForm,
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
    {
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

        $form = $this->businessForm->create();
        return $form;
    }

    protected function createComponentAddressForm(): Form
    {
        $form = $this->addressForm->create();
        return $form;
    }

    protected function createComponentContactForm(): Form
    {
        $form = $this->contactForm->create();
        return $form;
    }

//    public function handleMore($id)
    public function renderMore($id): void
    {
        $business = $this->bus->getAll()->get($id);
        /*

                if ($this->isAjax()) {
                    $this->redrawControl('flashes');
                    $this['actionsGrid']->reload();
                } else {
                    $this->redirect('this');
                }*/
        if (!$business) {
//            $this->flashMessage("Item deleted [$id] (actually, it was not)", 'info');
            $this->error('locale.business_not_found');
        }
//        $form = $this->businessForm->create();
        $form = $this->getComponent('businessForm');
//        $form->setDefaults($business->toArray());
        $businessArray = $business->toArray();

        $created = $this->jnl->getAll()
            ->where('business_id', $business->business_id)
            ->where('action_id', 3)
            ->min('date');
        if ($created) {
            $businessArray['business_created'] = $created;
//            $form->setDefaults(['business_created' => $created]);
        }

        $updated = $this->jnl->getAll()
            ->where('business_id', $business->business_id)
            ->where('action_id', 2)
            ->max('date');
        if ($updated) {
            $businessArray['business_updated'] = $updated;
//            $form->setDefaults(['business_updated' => $updated]);
        }

        $form->setDefaults($businessArray);
        $this->template->business = $businessArray;

        $addresses = $this->addr->getAll()
            ->select('*')
            ->where('business_id', $business->business_id)
            ->fetchAll();
//        if (count($addresses) == 1)
//            $this->template->address = reset($addresses);
        $this->template->addresses = $addresses;
//        $form->setDefaults($address);
        $i = 0;
        foreach ($addresses as $address) {
//            $form = $this->getComponent('addressForm');
            $form = $this->addressForm->create();
            $form->setDefaults($address);//->toArray()
//            $this->template->addresses[$i++] = $form;
        }

        $contacts = $this->con->getAll()
            ->select('*')
            ->where('business_id', $business->business_id)
            ->fetchAll();
        $this->template->contacts = $contacts;
    }
}
