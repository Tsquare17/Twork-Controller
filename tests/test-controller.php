<?php

namespace Twork\Tests;

use Twork\Controller\ControllerDispatcher;
use Twork\Tests\Fixtures\TestTheme\FrontPageController;
use WP_UnitTestCase;

/**
 * Class ThemeFunctionalTest
 *
 * Basic theme functionality test case.
 *
 * @package Twork
 */
class ControllerTest extends WP_UnitTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        do_action('wp_enqueue_scripts');
    }

    /** @test */
    public function styles_load(): void
    {
        $this->assertTrue(wp_style_is('front-page-css'));
    }

    /** @test */
    public function scripts_load(): void
    {
        $this->assertTrue(wp_script_is('front-page-js'));
        $this->assertTrue(wp_script_is('front-page-ajax'));
    }

    /** @test */
    public function post_contains_controller_data(): void
    {
        $this->go_to('/');

        $controller = new ControllerDispatcher([
            'templates' => [
                'front-page' => FrontPageController::class,
            ]
        ]);

        $controller->controllerDispatcher('front-page');

        global $post;

        $this->assertEquals('Twork', $post->twork->title);
    }
}
