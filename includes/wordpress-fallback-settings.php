<?php

/**
 * WordpressFallbackSettings
 */
class WordpressFallbackSettings {

    protected $plugin_file_name;

    protected $options_settings_key = 'wordpress-fallback-settings';

    /**
     * Constructor
     */
    public function __construct($plugin_file_name)
    {
        $this->plugin_file_name = $plugin_file_name;

        $this->actions();
        $this->filters();
    }

    /**
     * Create a new instance
     */
    static function create($plugin_file_name)
    {
        return new self($plugin_file_name);
    }

    /**
     * Register actions
     */
    public function actions()
    {
        add_action('admin_menu', [$this, 'admin_menu']);
    }

    /**
     * Register filters
     */
    public function filters()
    {
        add_filter("plugin_action_links_{$this->plugin_file_name}", [$this, 'plugin_action_links']);
    }

    /**
     * Add action links
     */
    public function plugin_action_links($links)
    {
        $settings_url = get_admin_url(null, 'options-general.php?page=wordpress-fallback.php');

        $links[] = '<a href="' . $settings_url . '">' . __('Settings') . '</a>';

        return $links;
    }

    public function admin_menu()
    {
        add_submenu_page(
            'options-general.php',
            'Fallback',
            'Fallback',
            'manage_options', // wordpress-fallback-manage
            'wordpress-fallback.php',
            [$this, 'settings_page']
        );
    }

    public function settings_page()
    {
        $this->store();

        $settings = $this->get();

        require __DIR__ . '/../templates/settings.php';
    }

    public function get()
    {
        return (object)array_merge([
            'production_host' => '',
            'download' => false,
        ], (array)get_option($this->options_settings_key, []));
    }

    protected function store()
    {
        if (!$_POST) {
            return false;
        }

        return update_option($this->options_settings_key, (object)[
            'production_host' => isset($_POST['production_host']) ? trim((string)$_POST['production_host'], ' /') : '',
            'download' => isset($_POST['download']) ? (boolean)$_POST['download'] : false,
        ]);
    }
}
