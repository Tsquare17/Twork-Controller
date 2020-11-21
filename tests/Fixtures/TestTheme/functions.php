<?php

use Twork\Controller\ControllerDispatcher;
use Twork\Tests\Fixtures\TestTheme\FrontPageController;

$controller = new ControllerDispatcher([
    'templates' => [
        'front-page' => FrontPageController::class,
    ]
]);

// Because WordPress isn't completely loaded during test running, we have to run some things outside of hooks.
$controller->addAjaxActions();
$controller->controllerDispatcher('front-page');
