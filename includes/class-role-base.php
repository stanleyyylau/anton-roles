<?php

abstract class Role_Base {
    protected $caps = array();
    protected $role_name;
    protected $role_label;
    protected $allow_roles = array();
    protected $admin_menu_to_remove = array();
    protected $restricted_screens_by_post_type = array();
    protected $restricted_screens_by_id = array();
    protected $restricted_screens_by_page = array(); // value of ?page=XXX

    public function get_allow_roles() {
        return $this->allow_roles;
    }

    public function register_role() {
        $this->add_or_update_role();
    }

    public function remove_admin_menu() {
        foreach ($this->admin_menu_to_remove as $value) {
            remove_menu_page($value);
        }
    }

    public function restrict_admin_page_access() {
        // retrieve the current page's ID
        $current_screen_id = get_current_screen()->id;
        $current_screen_post_type = get_current_screen()->post_type;
        // XXX where XXX ?page=XXX
        $current_page = $_GET['page'] ? $_GET['page'] : '';

        foreach ($this->restricted_screens_by_post_type as $restricted_screen) {
            // compare current screen id against each restricted screen
            if ($current_screen_post_type === $restricted_screen) {
                wp_die(__('You are not allowed to access this page.', 'tcd'));
            }
        }

        foreach ($this->restricted_screens_by_id as $restricted_screen) {
            // compare current screen id against each restricted screen
            if ($current_screen_id === $restricted_screen) {
                wp_die(__('You are not allowed to access this page.', 'tcd'));
            }
        }

        if ($current_page != 'none') {
            foreach ($this->restricted_screens_by_page as $restricted_screen) {
                // compare current screen id against each restricted screen
                if ($current_page === $restricted_screen) {
                    wp_die(__('You are not allowed to access this page.', 'tcd'));
                }
            }
        }
    }

    public function add_or_update_role() {
        $has_role = wp_roles()->is_role( $this->role_name );
        if ($has_role) {
            // update capabilities
            $current_role = get_role( $this->role_name );
            foreach($this->caps as $key => $value) {
                if ($value) {
                    $current_role->add_cap($key);
                } else {
                    $current_role->remove_cap($key);
                }
            }
        } else {
            add_role( $this->role_name, $this->role_label, $this->caps);
        }
    }

    // use as filter, return editable roles
    public function get_editable_roles($roles) {
        foreach ( $roles as $role => $caps ) {
            if ( ! in_array( $role, $this->allow_roles ) )
                unset( $roles[ $role ] );
        }
        return $roles;
    }
}