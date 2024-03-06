<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\FormFactory;
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
use Ublaboo\DataGrid\DataGrid;


/**
 * Presenter for the dashboard view.
 * Ensures the user is logged in before access.
 */
final class DashboardPresenter extends Nette\Application\UI\Presenter
{
    private UserFacade $usr;

    public function __construct(//private Nette\Database\Explorer $database,
	                            private BusinessFacade          $bus,
//								private UserPresenter           $userPresenter,
								UserFacade              $usr,
	                            AddressFacade           $addr,
	                            JournalFacade           $jnl,
//								private JournalActionsPresenter $actionsPresenter,
	                            BusinessContactFacade   $con,
	                            ContactMethodFacade     $cm,
//								private ContactMethodsPresenter $methodsPresenter,
	                            ActionFacade            $act,
                                private FormFactory $formFactory,
	                            DocumentFacade          $doc,
//								private DocumentPresenter       $documentPresenter,
//								private TabsControlFactory      $tabsControlFactory,
//								private PillsControlFactory     $pillsControlFactory,
	)
	{
        $this->usr = $usr;
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
//		$grid->addColumnNumber('id', 'ID', 'business_id');
		$grid->addColumnText('name', 'Name', 'business_name');
		$grid->addColumnText('email', 'Email', 'business_email');
		$grid->addColumnText('website', 'website', 'business_website');
		$grid->addColumnText('source', 'source', 'business_source');
		$grid->addColumnText('active', 'active', 'business_active');
        $grid->addAction('more', 'More', null, ['' => 'business_id']);
	}

    public function handleMore($id)
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
            $this->error('Business not found');
        $this->template->business = $business;
    }

    protected function createComponentBusinessForm(): Nette\Application\UI\Form
    {
		$form = $this->formFactory->create();
		$form->addText('name', 'Title:')
			->setRequired('Please enter the business name.');
        $form->addText('email', 'Email:');
        $form->addText('website', 'website:');
        $form->addText('source', 'source:');
        $form->addText('active', 'active:');

		$form->addSubmit('add', 'Add');

		// Handle form submission
		$form->onSuccess[] = function (Form $form, \stdClass $data): void {
			try {
				// Attempt to login user
				$this->bus->insertObject($data);
				$this->redirect('Dashboard:');
			} catch (Nette\Security\AuthenticationException) {
				$form->addError('Failed to add this business.');
			}
		};

		return $form;
	}

}
