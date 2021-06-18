<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://antonstudio.com
 * @since             1.0.0
 * @package           Anton_Roles
 *
 * @wordpress-plugin
 * Plugin Name:       Anton Roles
 * Plugin URI:        https://antonstudio.com/anton-roles
 * Description:       Add custom roles & allow role-based permission (beta)
 * Version:           1.0.3
 * Author:            Anton Studio
 * Author URI:        https://antonstudio.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       anton-roles
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-role-sales-manager.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-role-sales.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-role-marketing.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-role-support-agent.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-role-super-admin.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-role-administrator.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-enums.php';

// this code runs only during plugin activation and never again
//function sal_customize_user_role() {
//    require_once plugin_dir_path( __FILE__ ).'includes/class-sal-customize-user-role.php';
//    Sal_Customize_User_Role::activate();
//}
//register_activation_hook( __FILE__, 'sal_customize_user_role' );

class Anton_Role_Manager {
    private $roles = array();
    private $role_keys = array();
    private $enable_debug = false;

    function __construct() {

        // todo: change this when adding new role
        $this->role_keys = array(
                Enums::$sales_manager_role,
                Enums::$agent_role,
                Enums::$super_admin,
                Enums::$sales_role,
                Enums::$marketing_role,
                Enums::$administrator,
        );

        // todo: change this when adding new role
        $this->roles[Enums::$sales_manager_role] = new Role_Sales_Manager();
        $this->roles[Enums::$agent_role] = new Role_Support_Agent();
        $this->roles[Enums::$super_admin] = new Role_Super_Admin();
        $this->roles[Enums::$sales_role] = new Role_Sales();
        $this->roles[Enums::$marketing_role] = new Role_Marketing();
        $this->roles[Enums::$administrator] = new Role_Administrator();

        $this->init();
    }

    public function init() {
        // remove default WP roles
//        add_action( 'init', array( $this, 'remove_default_roles' ) );

        // add new roles
        add_action( 'init', array( $this, 'add_custom_role_to_network' ) );

        add_action( 'wpmu_new_blog', array( $this, 'add_custom_role_to_network' ) );

        // hide menus by role
        add_action( 'admin_init', array($this, 'remove_admin_menu_by_role'), 99999 ); // remove admin menus by role (only displaying)

        // restrict access
        add_action( 'current_screen', array($this, 'restrict_admin_pages_access') );

        // remove toolbar for non super admin
        add_action( 'admin_bar_menu', array($this, 'remove_extra_toolbar') , 10000009 );

        // Client admin should not be able to see Super Admin and Support Agent role
        add_filter( 'editable_roles', array($this, 'hide_editable_roles') );
        // Only allow user to create new users under roles lower than his own role level only
        add_filter( 'map_meta_cap', array($this, 'dont_allow_user_change_roles_outside_his_allow_range'), 10, 4 );

        if ($this->enable_debug) {
            add_action( 'admin_notices', array($this, 'debug_admin_menus'), 999999 );
            add_action( 'network_admin_notices', array($this, 'debug_network_admin_menus'), 999999 );
        }
    }

    public function debug_admin_menus() {
        if ( !is_admin())
            return;
        global $submenu, $menu, $pagenow;
        if ( current_user_can('manage_options') ) { // ONLY DO THIS FOR ADMIN
            if( $pagenow == 'index.php' ) {  // PRINTS ON DASHBOARD
                echo '<pre>'; print_r( $menu ); echo '</pre>'; // TOP LEVEL MENUS
//            echo '<pre>'; print_r( $submenu ); echo '</pre>'; // SUBMENUS
            }
        }
    }

    public function debug_network_admin_menus() {
        global $submenu, $menu, $pagenow;
        echo '<pre>'; print_r( $menu ); echo '</pre>'; // TOP LEVEL MENUS
//            echo '<pre>'; print_r( $submenu ); echo '</pre>'; // SUBMENUS
    }

    public function dont_allow_user_change_roles_outside_his_allow_range( $caps, $cap, $user_ID, $args ) {
        $role_key = $this->get_current_user_role();
        if ($role_key === Enums::$super_admin) {
            return $caps;
        }

        if ( ( $cap === 'edit_user' || $cap === 'delete_user' ) && $args ) {
            $the_user = get_userdata( $user_ID ); // The user performing the task
            $user     = get_userdata( $args[0] ); // The user being edited/deleted

            if ($the_user->ID != 1 && $user->ID == 1) {
                $caps[] = 'not_allowed';
                return $caps;
            }

            if ( $the_user && $user && $the_user->ID != $user->ID /* User can always edit self */ ) {
//                $allowed = wpse_188863_get_allowed_roles( $the_user );
                $allowed = $this->roles[$role_key]->get_allow_roles();

                if ( array_diff( $user->roles, $allowed ) ) {
                    // Target user has roles outside of our limits
                    $caps[] = 'not_allowed';
                }
            }
        }

        return $caps;
    }

    public function hide_editable_roles($roles) {
        $role_key = $this->get_current_user_role();

        if($role_key == Enums::$super_admin) {
            return $roles;
        }

        $allowed = $this->roles[$role_key]->get_allow_roles();
        foreach ( $roles as $role => $caps ) {
            if ( ! in_array( $role, $allowed ) )
                unset( $roles[ $role ] );
        }
        return $roles;
    }

    public function remove_extra_toolbar($wp_admin_bar) {
        // hide these nodes for non super admin
        if (!current_user_can('super_admin')) {
            $wp_admin_bar->remove_node('wp-ultimo');
        }
        $user = wp_get_current_user();
        if ( $user->ID !== 1 ) { // not super admin
            $wp_admin_bar->remove_node('wp-ultimo');
        }
    }

    public function restrict_admin_pages_access() {
        $role_key = $this->get_current_user_role();
        $this->roles[$role_key]->restrict_admin_page_access();
    }
    private function get_current_user_role() {
        $user = wp_get_current_user();
        if ( $user->ID == 1 ) {
            return Enums::$super_admin;
        }
        foreach ($this->role_keys as $key) {
            if ( in_array( $key, (array) $user->roles ) ) {
                return $key;
            }
        }
        return Enums::$agent_role;
    }
    public function remove_admin_menu_by_role() {
        $role_Key = $this->get_current_user_role();
        $this->roles[$role_Key]->remove_admin_menu();
    }
    public function add_custom_role_to_network($network_wide ) {
        if ( is_multisite() && $network_wide ) {
            // run the code for all sites in a Multisite network
            foreach ( get_sites(['fields'=>'ids']) as $blog_id ) {
                switch_to_blog( $blog_id );
                $this->add_custom_wp_role();
            }
            restore_current_blog();
        }
        else {
            $this->add_custom_wp_role();
        }
    }
    private function add_custom_wp_role() {
        foreach ($this->roles as $role_name => $role) {
            $role->register_role();
        }
    }
    public function remove_default_roles() {
        remove_role( 'editor' );
        remove_role( 'author' );
        remove_role( 'contributor' );
        remove_role( 'subscriber' );
    }
}

new Anton_Role_Manager();


/*
 * github update
 */
if ( ! defined( 'ABSPATH' ) || class_exists( 'WPGitHubUpdater' ) || class_exists( 'WP_GitHub_Updater' ) ) {
} else {
    require_once plugin_dir_path( __FILE__ ) . 'updater.php';
}
if (is_admin()) { // note the use of is_admin() to double check that this is happening in the admin
    $config = array(
        'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
        'proper_folder_name' => 'anton-roles', // this is the name of the folder your plugin lives in
        'api_url' => 'https://api.github.com/repos/stanleyyylau/anton-roles', // the GitHub API url of your GitHub repo
        'raw_url' => 'https://raw.github.com/stanleyyylau/anton-roles/master', // the GitHub raw url of your GitHub repo
        'github_url' => 'https://github.com/stanleyyylau/anton-roles', // the GitHub url of your GitHub repo
        'zip_url' => 'https://github.com/stanleyyylau/anton-roles/zipball/master', // the zip url of the GitHub repo
        'sslverify' => true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
        'requires' => '5.7', // which version of WordPress does your plugin require?
        'tested' => '5.7', // which version of WordPress is your plugin tested up to?
        'readme' => 'README.md', // which file to use as the readme for the version number
        'access_token' => '', // Access private repositories by authorizing under Plugins > GitHub Updates when this example plugin is installed
    );
    new WP_GitHub_Updater($config);
}
