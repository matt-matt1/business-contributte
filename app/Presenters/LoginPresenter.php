<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\FormFactory;
use App\Model\DuplicateNameException;
use App\Model\UserFacade;
use Contributte\Translation\Translator;
use Nette;
use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Form;


/**
 * Presenter for sign-in and sign-up actions.
 */
final class LoginPresenter extends Nette\Application\UI\Presenter
{
	/**
	 * Stores the previous page hash to redirect back after successful login.
	 */
	#[Persistent]
	public string $backlink = '';


	// Dependency injection of form factory and user management facade
	public function __construct(
		private readonly UserFacade  $userFacade,
		private readonly FormFactory $formFactory,
        private readonly Translator  $translator,
	) {
        parent::__construct();
	}


	public function beforeRender()
	{
		$this->template->noheader = true;
	}

	/**
	 * Create a sign-in form with fields for username and password.
	 * On successful submission, the user is redirected to the dashboard or back to the previous page.
	 */
	protected function createComponentLoginForm(): Form
	{
		$form = $this->formFactory->create();
		$form->addText('username', ucwords($this->translator->translate('Username:')))
			->setRequired(ucwords($this->translator->translate('Please enter your username.')));

		$form->addPassword('password', ucwords($this->translator->translate('Password:')))
			->setRequired(ucwords($this->translator->translate('Please enter your password.')));

		$form->addSubmit('send', ucwords($this->translator->translate('login')));
//		$form->addSubmit('send', 'Sign in');

		// Handle form submission
		$form->onSuccess[] = function (Form $form, \stdClass $data): void {
			try {
				// Attempt to login user
				$this->getUser()->login($data->username, $data->password);
				$this->restoreRequest($this->backlink);
				$this->redirect('Dashboard:');
			} catch (Nette\Security\AuthenticationException) {
				$form->addError(ucwords($this->translator->translate('The username or password you entered is incorrect.')));
			}
		};

		return $form;
	}


	/**
	 * Create a sign-up form with fields for username, email, and password.
	 * On successful submission, the user is redirected to the dashboard.
	 */
	protected function createComponentSignUpForm(): Form
	{
		$form = $this->formFactory->create();
		$form->addText('username', ucwords($this->translator->translate('pick_username')). ':')
			->setRequired(ucwords($this->translator->translate('username_required')));

		$form->addEmail('email', ucwords($this->translator->translate('email')). ':')
			->setRequired(ucwords($this->translator->translate('email_required')));

		$form->addPassword('password', ucwords($this->translator->translate('create_password')). ':')
			->setOption('description',
//                sprintf($this->translator->translate('at least %d characters'), $this->userFacade::PasswordMinLength))
                $this->translator->translate('at_least_d_characters', ['d' => $this->userFacade::PasswordMinLength]))
			->setRequired(ucwords($this->translator->translate('password_required')))
			->addRule($form::MinLength, null, $this->userFacade::PasswordMinLength);

		$form->addSubmit('send', ucwords($this->translator->translate('Sign up')));

		// Handle form submission
		$form->onSuccess[] = function (Form $form, \stdClass $data): void {
			try {
				// Attempt to register a new user
				$this->userFacade->add($data->username, $data->email, $data->password);
				$this->redirect('Dashboard:');
			} catch (DuplicateNameException) {
				// Handle the case where the username is already taken
				$form['username']->addError(ucwords($this->translator->translate('Username is already taken.')));
			}
		};

		return $form;
	}


	/**
	 * Logs out the currently authenticated user.
	 */
	public function actionOut(): void
	{
		$this->getUser()->logout();
	}
}
