<?php
// Add the Workshop Attendee management menu
add_action( 'admin_menu', 'workshop_action_admin_menu' );

// Add Meta Boxes for our Workshop Attendees management interface
// add_action( 'add_meta_boxes', 'workshop_attendees_add_meta_boxes', 10, 2 );
// add_action( 'wp_insert_post_data', 'workshop_attendees_filter_workshop_data', 10, 2 );
// add_action( 'save_post', 'workshop_attendees_save_meta_fields', 10, 2 );

/**
 * Add the admin menus for seeing all Workshop Attendees
 */
function workshop_action_admin_menu() {
    add_submenu_page(
        'edit.php?post_type=workshop',          // $parent_slug -> edit.php?post_type=workshop
        __( 'Workshop Attendees', 'workshop' ), // $page_title
        __( 'Attendees', 'workshop' ),          // $menu_title
        'list_users',                           // $capability
        'view-workshop-attendees',              // $menu_slug
        'workshop_view_attendees_list'          // $callback
    );
}

/**
 * View a List Table of all Attendees
 */
function workshop_view_attendees_list() {
    // Allow Attendees to be deleted
    if ( isset( $_GET['action'], $_GET[id], $_GET['_wpnonce'] ) && 'delete' == $_GET['action'] ) {
        // Make sure the user is who they say they are
        if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'attendee-delete' ) ) {
            wp_die( esc_html__( "Tsk, tsk. You can't do that.", 'workshop' ) );
        }

        // Make sure the guest author actually exists
    } else {
        echo '<div class="wrap">';
        echo '<div class="icon32" id="icon-users"><br></div>';
        echo '<h2>' . __( 'Attendees', 'workshop' );
        // @todo caps check for creating a new user
        $add_new_link = admin_url( "post-new.php?post_type=attendee" );
        echo '<a href="' . esc_url( $add_new_link ) . '" class="add-new-h2">' . esc_html__( 'Add New', 'workshop' ) . '</a>';
        echo '</h2>';
        $attendee_list_table = new Workshop_Attendees_WP_List_Table();
        $attendee_list_table->prepare_items();
        echo '<form id="workshop-attendees-filter" action="" method="GET">';
        echo '<input type="hidden" name="page" value="view-workshop-attendees" />';
        $attendee_list_table->display();
        echo '</form>';
        echo '</div>';
    }
}
