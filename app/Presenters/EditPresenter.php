<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\FormFactory;
use App\Model\DuplicateNameException;
use App\Model\UserFacade;
use Nette;
use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Form;


/**
 * Presenter for sign-in and sign-up actions.
 * @property mixed $translator
 */
final class EditPresenter extends Nette\Application\UI\Presenter
{
	use RequireLoggedUser;
	/**
	 * Stores the previous page hash to redirect back after successful login.
	 */
	#[Persistent]
	public string $backlink = '';


	// Dependency injection of form factory and user management facade
	public function __construct(
		private readonly UserFacade  $userFacade,
		private readonly FormFactory $formFactory,
	) {
	}

	protected function createComponentSignInForm(): Form
	{
		$form = $this->formFactory->create();
		$form->addText('username', label: ucfirst(string: $this->translator->translate('locale.username')))
			->setRequired(ucfirst($this->translator->translate('locale.username_required')));

		$form->addPassword('password', ucfirst($this->translator->translate('locale.password')))
			->setRequired(ucfirst($this->translator->translate('locale.password_required')));

		$form->addSubmit('send', ucfirst($this->translator->translate('locale.login')));

		$form->onSuccess[] = function (Form $form, \stdClass $data): void {
			try {
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
		$form->addText('username', ucfirst($this->translator->translate('locale.pick_username')))
			->setRequired(ucfirst($this->translator->translate('locale.username_required')));

		$form->addEmail('email', ucfirst($this->translator->translate('locale.email')))
			->setRequired(ucfirst($this->translator->translate('locale.email_required')));

		$form->addPassword('password', ucfirst($this->translator->translate('locale.create_password')))
//			->setOption('description', sprintf('at least %d characters', $this->userFacade::PasswordMinLength))
			->setOption('description', $this->translator->translate('locale.at_least_d_characters', ['d' => $this->userFacade::PasswordMinLength]))
			->setRequired(ucfirst($this->translator->translate('locale.password_required')))
			->addRule($form::MinLength, null, $this->userFacade::PasswordMinLength);

		$form->addSubmit('send', 'Sign up');

		$form->onSuccess[] = function (Form $form, \stdClass $data): void {
			try {
                $this->userFacade->add($data->username, $data->email, $data->password);
                $this->redirect('Dashboard:');
            } catch (DuplicateNameException) {
                $form['username']->addError('Username is already taken.');
            }
        };

        return $form;
    }


}
