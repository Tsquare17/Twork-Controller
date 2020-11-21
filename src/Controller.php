<?php

namespace Twork\Controller;

use stdClass;

/**
 * Class Controller
 * @package Twork\Controller
 */
abstract class Controller
{
    /**
     * Return an array of variables to pass to the template.
     *
     * @return array
     */
    public function data()
    {
        return [];
    }

    /**
     * Scripts to be enqueued in the footer.
     *
     * @return array
     */
    public static function footerScripts()
    {
        return [];
    }

    /**
     * Scripts to be enqueued in the header.
     *
     * @return array
     */
    public static function headerScripts()
    {
        return [];
    }

    /**
     * Styles to be enqueued.
     *
     * @return array
     */
    public static function styles()
    {
        return [];
    }

    /**
     * Scripts to be enqueued with ajax privileges.
     *
     * @return array
     */
    public static function ajaxScripts()
    {
        return [];
    }

    /**
     * An array of methods to be allowed access via ajax without being logged in.
     *
     * @return array
     */
    public static function ajaxMethods()
    {
        return [];
    }

    /**
     * An array of methods to be allowed access via ajax while logged in.
     *
     * @return array
     */
    public static function loggedInAjaxMethods()
    {
        return [];
    }

    /**
     * Shorthand script specification.
     *
     * @param       $path
     * @param array|null $dependencies
     * @param null $version
     * @param bool $inFooter
     *
     * @return array
     */
    protected static function script($path, array $dependencies = null, $version = null, $inFooter = true): array
    {
        return [
            'path' => $path,
            'dependencies' => $dependencies,
            'version' => $version,
            'in_footer' => $inFooter,
        ];
    }

    /**
     * Shorthand style specification.
     *
     * @param            $path
     * @param null $version
     * @param array|null $dependencies
     * @param null $media
     *
     * @return array
     */
    protected static function style($path, $version = null, array $dependencies = null, $media = null): array
    {
        return [
            'path' => $path,
            'dependencies' => $dependencies,
            'version' => $version,
            'media' => $media,
        ];
    }

    /**
     * Attach controller data to the post object.
     */
    public function dispatch(): void
    {
        global $post;
        $post->twork = new stdClass();

        foreach ($this->data() as $key => $value) {
            $post->twork->$key = $value;
        }
    }
}
