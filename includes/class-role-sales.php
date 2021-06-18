<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-role-base.php';

class Role_Sales extends Role_Base {
    protected $caps = array(
        'switch_themes' => false,
        'edit_themes' => false,
        'activate_plugins' => false,
        'edit_plugins' => false,
        'edit_users' => false,
        'edit_files' => true,
        'manage_options' => false,
        'moderate_comments' => false,
        'manage_categories' => false,
        'manage_links' => false,
        'upload_files' => true,
        'import' => false,
        'unfiltered_html' => true,
        'edit_posts' => true,
        'edit_others_posts' => true,
        'edit_published_posts' => true,
        'publish_posts' => true,
        'edit_pages' => true,
        'read' => true,
        'level_10' => true,
        'level_9' => true,
        'level_8' => true,
        'level_7' => true,
        'level_6' => true,
        'level_5' => true,
        'level_4' => true,
        'level_3' => true,
        'level_2' => true,
        'level_1' => true,
        'level_0' => true,
        'edit_others_pages' => false,
        'edit_published_pages' => true,
        'publish_pages' => true,
        'delete_pages' => true,
        'delete_others_pages' => false,
        'delete_published_pages' => true,
        'delete_posts' => true,
        'delete_others_posts' => false,
        'delete_published_posts' => true,
        'delete_private_posts' => true,
        'edit_private_posts' => true,
        'read_private_posts' => true,
        'delete_private_pages' => true,
        'edit_private_pages' => true,
        'read_private_pages' => true,
        'delete_users' => false,
        'create_users' => false,
        'unfiltered_upload' => true,
        'edit_dashboard' => false,
        'update_plugins' => false,
        'delete_plugins' => false,
        'install_plugins' => false,
        'update_themes' => false,
        'install_themes' => false,
        'update_core' => false,
        'list_users' => false,
        'remove_users' => false,
        'promote_users' => false,
        'edit_theme_options' => false,
        'delete_themes' => false,
        'export' => false,
        'cfdb7_access' => false,
    );
    protected $role_name;
    protected $role_label;
    protected $allow_roles = array();
    protected $admin_menu_to_remove = array(
      'wp-ultimo',
      'wp-ultimo-subscriptions',
      'wp-ultimo-plans',
      'wp-ultimo-coupons',
      'settings.php',
      'wu-my-account'
    );
    protected $restricted_screens_by_post_type = array();
    protected $restricted_screens_by_id = array();
    protected $restricted_screens_by_page = array('wu-my-account');

    function __construct() {
        $this->role_name = Enums::$sales_role;
        $this->role_label = Enums::$sales_role_display_name;
    }
}