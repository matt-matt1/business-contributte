<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;

final class PluralsPresenter extends Nette\Application\UI\Presenter
{
    /** @var Nette\Localization\ITranslator @inject */
    public $translator;

    public function renderDefault()
    {
        $t = $this->translator;
        $data = [
            'num_of_apples_0' =>  $t->translate(
                'locale.num_of_apples', ['apples' => 0]
            ),
            'num_of_apples_1' =>  $t->translate(
                'locale.num_of_apples', ['apples' => 1]
            ),
            'num_of_apples_3' =>  $t->translate(
                'locale.num_of_apples', ['apples' => 3]
            ),
            'num_of_apples_9' =>  $t->translate(
                'locale.num_of_apples', ['apples' => 9]
            ),

            'users_0' =>  $t->translate('locale.users', [
                'count' => 0,
                'user_name' => 'Joe',
            ]),
            'users_5' =>  $t->translate('locale.users', [
                'count' => 5,
                'user_name' => 'Joe',
            ]),

            'baby_gender_girl' =>  $t->translate(
                'locale.baby_gender', ['gender' => 'girl']
            ),
            'baby_gender_boy' =>  $t->translate(
                'locale.baby_gender', ['gender' => 'boy']
            ),
            'baby_gender_other' =>  $t->translate(
                'locale.baby_gender', ['gender' => 'other']
            ),

            'organizer_female' =>  $t->translate(
                'locale.organizer_gender', [
                'organizer_gender' => 'female',
                'organizer_name' => 'Miley'
            ]),
            'organizer_male' =>  $t->translate(
                'locale.organizer_gender', [
                'organizer_gender' => 'male',
                'organizer_name' => 'Kyle'
            ]),
            'organizer_other' =>  $t->translate(
                'locale.organizer_gender', [
                'organizer_gender' => 'other',
                'organizer_name' => 'Daniel'
            ]),
        ];
        $this->sendJson($data);
    }
}