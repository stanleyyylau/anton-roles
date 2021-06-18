<?php

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-role-base.php';

class Role_Sales_Manager extends Role_Base {
    protected $caps = array(
        'switch_themes' => false,
        'edit_themes' => false,
        'activate_plugins' => false,
        'edit_plugins' => false,
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
        'edit_dashboard' => false,
        'update_plugins' => false,
        'delete_plugins' => false,
        'install_plugins' => false,
        'update_themes' => false,
        'install_themes' => false,
        'update_core' => false,
        'list_users' => true,
        'remove_users' => true,
        'promote_users' => true,
        'edit_theme_options' => false,
        'delete_themes' => false,
        'export' => false,
        'cfdb7_access' => true,
    );
    protected $role_name;
    protected $role_label;
    protected $allow_roles = array(
      'sales',
      'marketing',
    );
    protected $admin_menu_to_remove = array(
      'edit.php?post_type=elementor_library',
      'elementor',
      'tools.php',
      'edit.php?post_type=acf-field-group',
      'wu-my-account'
    );
    protected $restricted_screens_by_post_type = array(
        'acf-field-group',
    );
    protected $restricted_screens_by_id = array();
    protected $restricted_screens_by_page = array(
        'wu-my-account'
    );

    function __construct() {
        $this->role_name = Enums::$sales_manager_role;
        $this->role_label = Enums::$sales_manager_display_name;
    }
}