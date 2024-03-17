<?php

declare(strict_types=1);

namespace App\Presenters;

use Contributte\Translation\Exceptions\InvalidArgument;
use Contributte\Translation\Translator;
use Nette;
//use App\Controls\PillsControl;
//use App\Controls\TabsControl;
//use App\Factories\PillsControlFactory;
//use App\Factories\TabsControlFactory;
//use App\Model\ActionFacade;
use App\Model\AddressFacade;
use App\Model\BusinessContactFacade;
use App\Model\BusinessFacade;
//use App\Model\ContactMethodFacade;
//use App\Model\DocumentFacade;
//use App\Model\JournalFacade;
//use App\Model\UserFacade;
//use Nette\Localization\Translator;
use Nette\Application\BadRequestException;
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

    public function __construct(
        private readonly BusinessFormFactory   $businessForm,
        private readonly AddressFormFactory    $addressForm,
        private readonly ContactFormFactory    $contactForm,
        private readonly Translator            $translator,
        private readonly BusinessFacade        $bus,
        private readonly AddressFacade         $addr,
        private readonly BusinessContactFacade $con,
//        private readonly FormFactory           $formFactory,
	)
	{
        parent::__construct();
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
        $this->template->addFilter('formPair', function ($control) {
            $render = $control->form->renderer;
            $render->attachForm($control->form);

            return $render->renderPair($control);
        });
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

    /**
     * @throws InvalidArgument
     */
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

    /**
     * @throws InvalidArgument
     */
    protected function createComponentAddressForm(): Form
    {
        $form = $this->addressForm->create();
        return $form;
    }

    /**
     * @throws InvalidArgument
     */
    protected function createComponentContactForm(): Form
    {
        $form = $this->contactForm->create();
        return $form;
    }

//    public function handleMore($id)

    /**
     * @throws BadRequestException
     */
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

        $form->setDefaults($businessArray);
        $this->template->business = $businessArray;

        $addresses = $this->addr->getAll()
            ->where('business_id', $id)
            ->fetchAll();
        $this->template->addresses = $addresses;
        $form = $this->getComponent('addressForm');
        $form->setDefaults(reset($addresses)->toArray());

        $contacts = $this->con->getAll()
            ->where('business_id', $id)
            ->fetchAll();
        $this->template->contacts = $contacts;
        $form = $this->getComponent('contactForm');
        $form->setDefaults(reset($contacts)->toArray());
    }
}
