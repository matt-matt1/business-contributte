<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * Creates the main application router with defined routes.
	 */
	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		// Default route that maps to the Dashboard
		$router->addRoute('<presenter>/<action>[/<id>]', 'Dashboard:default');
/*		$router->addRoute('<locale>/<presenter>/<action>[/<id>]', [
			'presenter' => 'Dashboardx',
			'action' => 'default',
			'id' => null,
			'locale' => [
				Route::FILTER_TABLE => [
					'cs' => 'cs',
					'sk' => 'sk',
					'pl' => 'pl',
					'en' => 'en'
				]
			]
		]);*/
		return $router;
	}
}
