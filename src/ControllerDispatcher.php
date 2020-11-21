<?php

namespace Twork\Controller;

/**
 * Class ControllerDispatcher
 * @package Twork\Controller
 */
class ControllerDispatcher
{
    /**
     * @var array Template controller map.
     */
    protected $templates;

    /**
     * @var string Controller to use.
     */
    protected $controller;

    /**
     * @var array Scripts ready to be enqueued.
     */
    protected $scripts;

    /**
     * @var array Scripts to be enqueued with ajax privileges.
     */
    protected $ajaxScripts;

    /**
     * @var array Styles ready to be enqueued.
     */
    protected $styles;

    /**
     * @var array Scripts from the controller to be enqueued in the footer.
     */
    protected $controllerFooterScripts;

    /**
     * @var array Scripts from the controller to be enqueued in the header.
     */
    protected $controllerHeaderScripts;

    /**
     * @var array Scripts from the controller to be enqueued with ajax privileges.
     */
    protected $controllerAjaxScripts;

    /**
     * @var array Styles from the controller.
     */
    protected $controllerStyles;

    /**
     * Interceptor constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->templates = $config['templates'];

        $this->addAjaxActions();

        add_filter('template_include', [$this, 'controllerDispatcher']);
    }

    /**
     * Register Ajax.
     */
    public function addAjaxActions(): void
    {
        foreach ($this->templates as $template => $controller) {
            foreach ($controller::ajaxMethods() as $method) {
                add_action("wp_ajax_nopriv_{$method}", [$controller, $method]);
                add_action("wp_ajax_{$method}", [$controller, $method]);
            }
            foreach ($controller::loggedInAjaxMethods() as $method) {
                add_action("wp_ajax_{$method}", [$controller, $method]);
            }
        }
    }

    /**
     * template_include filter.
     *
     * @param string $template
     *
     * @return string
     */
    public function controllerDispatcher(string $template): string
    {
        $array         = explode('/', $template);
        $templateFile  = end($array);
        $tworkTemplate = str_replace('.php', '', $templateFile);

        if (isset($this->templates[$tworkTemplate])) {
            $this->controller = $this->templates[$tworkTemplate];

            $this->runController();
        }

        return $template;
    }

    /**
     * Collect and enqueue assets, and render the template.
     */
    public function runController(): void
    {
        /**
         * @var Controller $controller
         */
        $controller = new $this->controller();

        $this->controllerFooterScripts = $controller::footerScripts();
        $this->controllerHeaderScripts = $controller::headerScripts();
        $this->controllerStyles        = $controller::styles();
        $this->controllerAjaxScripts   = $controller::ajaxScripts();

        $this->processScripts();
        $this->processStyles();
        $this->processAjaxScripts();

        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);

        $controller->dispatch();
    }

    /**
     * Process scripts to be enqueued.
     */
    public function processScripts(): void
    {
        $processedFooterScripts = [];
        foreach ($this->controllerFooterScripts as $handle => $script) {
            $script['handle']         = $handle;
            $processedFooterScripts[] = $script;
        }
        $this->controllerFooterScripts = $processedFooterScripts;

        if (!empty($processedFooterScripts)) {
            $this->scripts = $processedFooterScripts;
        }

        $processedHeaderScripts = [];
        foreach ($this->controllerHeaderScripts as $handle => $script) {
            $script['handle']         = $script;
            $processedHeaderScripts[] = $script;
        }
        $this->controllerHeaderScripts = $processedHeaderScripts;

        if (empty($this->scripts)) {
            $this->scripts = $processedHeaderScripts;
        } else {
            $this->scripts = array_merge($this->scripts, $processedHeaderScripts);
        }
    }

    /**
     * Process scripts to be enqueued with ajax privileges.
     */
    public function processAjaxScripts(): void
    {
        $processedAjaxScripts = [];
        foreach ($this->controllerAjaxScripts as $handle => $script) {
            $script['handle']         = $handle;
            $processedAjaxScripts[] = $script;
        }

        $this->ajaxScripts = $processedAjaxScripts;
    }

    /**
     * Process styles to be enqueued.
     */
    public function processStyles(): void
    {
        $registeredStyles = [];
        foreach ($this->controllerStyles as $handle => $style) {
            $style['handle']    = $handle;
            $registeredStyles[] = $style;
        }

        if (empty($this->styles)) {
            $this->styles = $registeredStyles;
        } else {
            $this->styles = array_merge($this->styles, $registeredStyles);
        }
    }

    /**
     * Hooked from wp_enqueue_assets.
     */
    public function enqueueAssets(): void
    {
        $this->registerAjaxScripts();
        $this->enqueueScripts();
        $this->enqueueStyles();
    }

    /**
     * Register ajax scripts.
     */
    public function registerAjaxScripts(): void
    {
        foreach ($this->ajaxScripts as $script) {
            wp_register_script(
                $script['handle'],
                $script['path'],
                $script['dependencies'],
                $script['version'],
                $script['in_footer']
            );

            $nonce = strtolower(str_replace('-', '_', $script['handle']));
            wp_localize_script(
                $script['handle'],
                'tworkRequest',
                [
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'ajaxnonce' => wp_create_nonce($nonce)
                ]
            );
            wp_enqueue_script($script['handle']);
        }
    }

    /**
     * Enqueue scripts.
     */
    protected function enqueueScripts(): void
    {
        foreach ($this->scripts as $script) {
            wp_enqueue_script(
                $script['handle'],
                $script['path'],
                $script['dependencies'],
                $script['version'],
                $script['in_footer']
            );
        }
    }

    /**
     * Enqueue styles.
     */
    protected function enqueueStyles(): void
    {
        foreach ($this->styles as $style) {
            wp_enqueue_style(
                $style['handle'],
                $style['path'],
                $style['dependencies'],
                $style['version'],
                $style['media']
            );
        }
    }
}
