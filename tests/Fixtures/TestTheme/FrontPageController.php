<?php

namespace Twork\Tests\Fixtures\TestTheme;

use Twork\Controller\Controller;

class FrontPageController extends Controller
{
    public function data()
    {
        return [
            'title' => 'Twork',
        ];
    }

    public static function assets()
    {
        return get_template_directory_uri() . '/Fixtures';
    }

    public static function footerScripts()
    {
        return [
            'front-page-js' => self::script(self::assets() . '/js/test.js'),
        ];
    }

    public static function styles()
    {
        return [
            'front-page-css' => self::style(self::assets() . '/css/test.css'),
        ];
    }

    public static function ajaxScripts()
    {
        return [
            'front-page-ajax' => self::script(self::assets() . '/js/ajax.js', ['jquery']),
        ];
    }

    public static function ajaxMethods()
    {
        return [
            'exampleAjaxMethod',
        ];
    }

    public static function exampleAjaxMethod()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'front_page_ajax')) {
            echo 'no';
            wp_die();
        }

        echo 'success';
        wp_die();
    }
}
