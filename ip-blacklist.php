<?php

/*
Plugin Name: Blacklist
Description: A simple plugin to blacklist IP addresses and block unauthorized visitors
Version: 1.0
Author: Dennis Elsinga
*/

class IPBlacklist
{

    /**
     * IP_Whitelist constructor.
     * Initializes the IPBlacklist object.
     * Adds necessary hooks to the WordPress environment.
     */

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_menu_item'));
        add_action('login_init', array($this, 'restriction'));
        add_action('template_redirect', array($this, 'restriction'));
        wp_enqueue_style('ip_blacklist_styles', plugin_dir_url(__FILE__) . 'css/styles.css');
    }

    /**
     * Adds a new menu item in the WordPress admin panel.
     *
     * @return void
     */

    public function add_plugin_menu_item(): void
    {
        add_menu_page(
            'IP Blacklist',
            'IP Blacklist',
            'manage_options',
            'ip-blacklist',
            array($this, 'plugin_menu_callback'),
            'dashicons-lock'
        );
    }

    /**
     * Callback function for the plugin menu page.
     * Handles the addition and deletion of IP addresses.
     * Displays the plugin interface.
     *
     * @return void
     */

    public function plugin_menu_callback(): void
    {
        // Handle addition of IP address
        if (isset($_POST['add'])) {
            $ip_address = sanitize_text_field($_POST['ip_address']);
            if (empty($ip_address)) {
                add_settings_error('ip_blacklist', 'empty_ip_address', __('IP address field cannot be empty.', 'ip-blacklist'));
            } elseif (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
                add_settings_error('ip_blacklist', 'invalid_ip_address', __('Invalid IP address format.', 'ip-blacklist'));
            } elseif (in_array($ip_address, $this->get_ip_addresses())) {
                add_settings_error('ip_blacklist', 'duplicate_ip_address', __('IP address is already in the blacklist.', 'ip-blacklist'));
            } else {
                $this->add($ip_address);
                add_settings_error('ip_blacklist', 'ip_address_added', __('IP address added to blacklist.', 'ip-blacklist'), 'updated');
            }
        }

        // Handle deletion of IP address
        if (isset($_POST['delete'])) {
            $ip_address = sanitize_text_field($_POST['delete']);
            if (!empty($ip_address)) $this->delete($ip_address);
        }

        // Display any errors associated with the "ip_blacklist" setting
        settings_errors('ip_blacklist');

        // Display the plugin interface
        $this->plugin_interface();
    }

    /**
     * Include the interface file, which contains the HTML for the IP ip-blacklist page.
     *
     * @return void
     */

    public function plugin_interface(): void
    {
        include_once('interface.php');
    }

    /**
     * Adds a new IP address to the list of IP addresses.
     *
     * @param string $ip_address The IP address to add.
     * @return void
     */

    public function add(string $ip_address): void
    {
        $ip_addresses = $this->get_ip_addresses();
        $ip_addresses[] = $ip_address;
        update_option('blacklisted-addresses', $ip_addresses);
    }

    /**
     * Deletes an IP address from the list of IP addresses.
     *
     * @param string $ip_address The IP address to delete.
     * @return void
     */

    public function delete(string $ip_address): void
    {
        $ip_addresses = $this->get_ip_addresses();
        $index = array_search($ip_address, $ip_addresses);
        if ($index !== false) {
            unset($ip_addresses[$index]);
            update_option('blacklisted-addresses', $ip_addresses);
        }
    }

    /**
     * Retrieves the IP addresses from the options table or an empty array if none are found.
     *
     * @return false|mixed|null An array of IP addresses or null if there is an error retrieving the option.
     */

    public function get_ip_addresses(): mixed
    {
        return get_option('blacklisted-addresses', []);
    }

    /**
     * Checks if the user's IP address is blacklisted and returns a 401 status code if not.
     *
     * @return void
     */

    public function restriction(): void
    {
        // Check if the user's IP is in the blacklisted IPs array
        $user_ip = $_SERVER['REMOTE_ADDR'];
        if (in_array($user_ip, $this->get_ip_addresses())) {
            status_header(401);
            die();
        }
    }

}

// Create a new instance of the IP_Whitelist class to handle IP blacklisting
$new_blacklist = new IPBlacklist();