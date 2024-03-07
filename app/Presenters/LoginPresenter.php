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
		$this->template->locale = filter_input(INPUT_GET, 'locale');// $_GET['locale'];
	}

	/**
	 * Create a sign-in form with fields for username and password.
	 * On successful submission, the user is redirected to the dashboard or back to the previous page.
	 */
	protected function createComponentLoginForm(): Form
	{
		$form = $this->formFactory->create();
		$form->addText('username', ucfirst($this->translator->translate('locale.username')))
			->setRequired(ucfirst($this->translator->translate('locale.username_required')));

		$form->addPassword('password', ucfirst($this->translator->translate('locale.password')))
			->setRequired(ucfirst($this->translator->translate('locale.password_required')));

		$form->addSubmit('send', ucfirst($this->translator->translate('locale.login')));
//		$form->addSubmit('send', 'Sign in');

		// Handle form submission
		$form->onSuccess[] = function (Form $form, \stdClass $data): void {
			try {
				// Attempt to login user
				$this->getUser()->login($data->username, $data->password);
				$this->restoreRequest($this->backlink);
				$this->redirect('Dashboard:');
			} catch (Nette\Security\AuthenticationException) {
				$form->addError(ucfirst($this->translator->translate('locale.username_password_incorrect')));
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
		$form->addText('username', ucfirst($this->translator->translate('locale.pick_username')). ':')
			->setRequired(ucfirst($this->translator->translate('locale.username_required')));

		$form->addEmail('email', ucfirst($this->translator->translate('locale.email')). ':')
			->setRequired(ucfirst($this->translator->translate('locale.email_required')));

		$form->addPassword('password', ucfirst($this->translator->translate('locale.create_password')). ':')
			->setOption('description',
//                sprintf($this->translator->translate('at least %d characters'), $this->userFacade::PasswordMinLength))
                $this->translator->translate('locale.at_least_d_characters', ['d' => $this->userFacade::PasswordMinLength]))
			->setRequired(ucfirst($this->translator->translate('locale.password_required')))
			->addRule($form::MinLength, null, $this->userFacade::PasswordMinLength);

		$form->addSubmit('send', ucfirst($this->translator->translate('locale.register')));

		// Handle form submission
		$form->onSuccess[] = function (Form $form, \stdClass $data): void {
			try {
				// Attempt to register a new user
				$this->userFacade->add($data->username, $data->email, $data->password);
				$this->redirect('Dashboard:');
			} catch (DuplicateNameException) {
				// Handle the case where the username is already taken
				$form['username']->addError(ucfirst($this->translator->translate('locale.username_taken')));
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
