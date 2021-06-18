<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-role-base.php';

class Role_Marketing extends Role_Base {
    protected $caps = array(
        'switch_themes' => true,
        'edit_themes' => true,
        'activate_plugins' => true,
        'edit_plugins' => true,
        'edit_users' => true,
        'edit_files' => true,
        'manage_options' => true,
        'moderate_comments' => true,
        'manage_categories' => true,
        'manage_links' => true,
        'upload_files' => true,
        'import' => true,
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
        'edit_others_pages' => true,
        'edit_published_pages' => true,
        'publish_pages' => true,
        'delete_pages' => true,
        'delete_others_pages' => true,
        'delete_published_pages' => true,
        'delete_posts' => true,
        'delete_others_posts' => true,
        'delete_published_posts' => true,
        'delete_private_posts' => true,
        'edit_private_posts' => true,
        'read_private_posts' => true,
        'delete_private_pages' => true,
        'edit_private_pages' => true,
        'read_private_pages' => true,
        'delete_users' => true,
        'create_users' => true,
        'unfiltered_upload' => true,
        'edit_dashboard' => true,
        'update_plugins' => true,
        'delete_plugins' => true,
        'install_plugins' => true,
        'update_themes' => true,
        'install_themes' => true,
        'update_core' => true,
        'list_users' => true,
        'remove_users' => true,
        'promote_users' => true,
        'edit_theme_options' => true,
        'delete_themes' => true,
        'export' => true,
        'cfdb7_access' => true,
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
        'wu-my-account',
    );
    protected $restricted_screens_by_post_type = array();
    protected $restricted_screens_by_id = array();
    protected $restricted_screens_by_page = array('wu-my-account');

    function __construct() {
        $this->role_name = Enums::$marketing_role;
        $this->role_label = Enums::$marketing_role_display_name;
    }
}