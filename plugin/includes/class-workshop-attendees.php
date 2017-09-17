<?php
/**
 * Workshop Attendees
 *
 * Key idea: Create Workshop Attendees to assign to a Workshop
 * without having to give them access to the dashboard through a WP_User account
 */

class Workshop_Attendees {

    var $post_type = 'workshop-attendee';
    var $parent_page = 'edit.php?post_type=workshop';
    var $list_attendees_cap = 'list_users'; // or change this?

    public static $cache_group = 'workshop-attendees';

    /**
     * Initialize our Workshop Attendees class and establish common hooks
     */
    function __construct() {
        global $workshop_attendees;

        // Add the Workshop Attendee management menu
        add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );

        // WP List Table for breaking out our Guest Authors
        // require_once( dirname( __FILE__ ) . '/class-workshop-attendees-wp-list-table.php' );

        // Add Meta Boxes for our Workshop Attendees management interface
        add_action( 'add_meta_boxes', array( $this, 'action_add_meta_boxes' ), 10, 2 );
        add_action( 'wp_insert_post_data', array( $this, 'manage_workshop_attendees_filter_workshop_data' ), 10, 2 );
        add_action( 'save_post', array( $this, 'manage_workshop_attendees_save_meta_fields' ), 10, 2 );

        // Empty associated caches when the Workshop Attendees profile is updated:
        add_filter( 'update_post_metadata', array( $this, 'filter_update_workshop_metadata' ), 10, 5 );

        // Modify the messages that appear when saving or creating:
        add_filter( 'post_updated_messages', array( $this, 'filter_workshop_updated_messages' ) );

        // Allow admins to create or edit Attendee profiles from the Manage Users listing

        // Add support for featured thumbnails that we can use for Attendees

        $this->labels = apply_filters( 'workshop_attendees_labels', array(
            'singular'          => __( 'Attendee', 'workshop' ),
            'plural'            => __( 'Attendees', 'workshop' ),
            'all_items'         => __( 'All Attendees', 'workshop' ),
            'add_new_item'      => __( 'Add New Attendee', 'workshop' ),
            'edit_item'         => __( 'Edit Attendee', 'workshop' ),
            'new_item'          => __( 'New Attendee', 'workshop' ),
            'view_item'         => __( 'View Attendee', 'workshop' ),
            'search_items'      => __( 'Search Attendees', 'workshop' ),
            'not_found'         => __( 'No Attendees found', 'workshop' ),
            'not_found_in_trash'=> __( 'No Attendees found in Trash', 'workshop' ),
            'update_item'       => __( 'Update Attendee', 'workshop' ),
            'metabox_about'     => __( 'About Attendee', 'workshop' ),
        ) );

        // Register a post type to store our Attendees that aren't WP users
        $args = array(
                'label'                 => $this->labels['singular'],
                'labels'                => array(
                        'name'              => $this->labels['plural'],
                        'singular_name'     => $this->labels['singular'],
                        'add_new'           => _x( 'Add New', 'attendee', 'workshop' ),
                        'all_items'         => $this->labels['all_items'],
                        'add_new_item'      => $this->labels['add_new_item'],
                        'edit_item'         => $this->labels['edit_item'],
                        'new_item'          => $this->labels['new_item'],
                        'view_item'         => $this->labels['view_item'],
                        'search_items'      => $this->labels['search_items'],
                        'not_found'         => $this->labels['not_found'],
                        'not_found_in_trash'=> $this->labels['not_found_in_trash'],
                ),
                'public'                => true,
                'publicly_queryable'    => false,
                'exclude_from_search'   => true,
                'show_in_menu'          => false,
                'supports'              => array( 'thumbnail' ),
                'taxonomies'            => array( 'workshop' ), // $attendee_taxonomy
                'rewrite'               => false,
                'query_var'             => false,
            );
        register_post_type( $this->post_type, $args );
    }

    /**
     * Filter the messages that appear when saving or updating an Attendee
     */
    function filter_workshop_updated_messages( $messages ) {
        global $post;

        if ( $this->post_type !== $post->post_type ) {
            return $messages;
        }

        // $attendee = $this->get_attendee_by( 'ID', $post->ID );
        // $attendee_link = $this->filter_attendee_link( '', $workshop_attendee->ID, $workshop_attendee->user_nicename );

        $messages[ $this->post_type ] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => sprintf( __( 'Attendee updated. <a href="%s">View profile</a>', 'workshop' ), esc_url( $attendee_link ) ),
            2 => __( 'Custom field updated.', 'workshop' ),
            3 => __( 'Custom field deleted.', 'workshop' ),
            4 => __( 'Attendee updated.', 'workshop' ),
            /* translators: %s: date and time of the revision */
            5 => isset( $_GET['revision'] ) ? sprintf( __( 'Attendee restored to revision from %s', 'workshop' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6 => sprintf( __( 'Attendee updated. <a href="%s">View profile</a>', 'workshop' ), esc_url( $attendee_link ) ),
            7 => __( 'Attendee saved.', 'workshop' ),
            8 => sprintf( __( 'Attendee submitted. <a target="_blank" href="%s">Preview profile</a>', 'workshop' ), esc_url( add_query_arg( 'preview', 'true', $attendee_link ) ) ),
            9 => sprintf( __( 'Attendee scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview profile</a>', 'workshop' ),
                // translators: Publish box date format, see http://php.net/date
            date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $attendee_link ) ),
            10 => sprintf( __( 'Attendee updated. <a target="_blank" href="%s">Preview profile</a>', 'workshop' ), esc_url( add_query_arg( 'preview', 'true', $attendee_link ) ) ),
        );
        return $messages;
    }

    /**
     * Add the admin menus for seeing all Workshop Attendees
     */
    function action_admin_menu() {
        add_submenu_page(
            $this->parent_page,                             // $parent_slug -> edit.php?post_type=workshop
            $this->labels['plural'],                        // $page_title
            $this->labels['plural'],                        // $menu_title
            $this->list_attendees_cap,                      // $capability
            'view-workshop-attendees',                      // $menu_slug
            array( $this, 'view_workshop_attendees_list' )  // $callback
        );
    }

    /**
     * Register the Meta Boxes used for Attendees
     */
    function action_add_meta_boxes() {
        global $workshop;

        // if ( get_post_type() == $this->post_type ) {
            // Remove the submitpost metabox because we have our own
            remove_meta_box( 'submitdiv', $this->post_type, 'side' );
            remove_meta_box( 'slugdiv', $this->post_type, 'normal' );

            add_meta_box( 'workshop-manage-attendee-save',
                __( 'Save', 'workshop' ),
                array( $this, 'metabox_manage_attendee_save' ),
                $this->post_type,
                'side',
                'default'
            );
			add_meta_box( 'workshop-manage-attendee-slug',
                __( 'Unique Slug', 'workshop' ),
                array( $this, 'metabox_manage_attendee_slug' ),
                $this->post_type,
                'side',
                'default'
            );
			// Our metaboxes with co-author details
			add_meta_box( 'workshop-manage-attendee-name',
                __( 'Name', 'workshop' ),
                array( $this, 'metabox_manage_attendee_name' ),
                $this->post_type,
                'normal',
                'default'
            );
			add_meta_box( 'workshop-manage-attendee-contact-info',
                __( 'Contact Info', 'workshop' ),
                array( $this, 'metabox_manage_attendee_contact_info' ),
                $this->post_type,
                'normal',
                'default'
            );
			add_meta_box( 'workshop-manage-attendee-bio',
                $this->labels['metabox_about'],
                array( $this, 'metabox_manage_attendee_bio' ),
                $this->post_type,
                'normal',
                'default'
            );
        // }
    }

    /**
     * View a list table of all Attendees
     */
    function view_workshop_attendees_list() {
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
            echo '<h2>' . esc_html( $this->labels['plural'] );
            // @todo caps check for creating a new user
            $add_new_link = admin_url( "post-new.php?post_type=$this->post_type" );
            echo '<a href="' . esc_url( $add_new_link ) . '" class="add-new-h2">' . esc_html__( 'Add New', 'workshop' ) . '</a>';
            echo '</h2>';
            // $cap_list_table = new Workshop_Attendees_WP_List_Table();
            // $cap_list_table->prepare_items();
            echo '<form id="workshop-attendees-filter" action="" method="GET">';
            echo '<input type="hidden" name="page" value="view-workshop-attendees" />';
            // $cap_list_table->display();
            echo '</form>';
            echo '</div>';
        }
    }

} // END class Workshop_Attendees
$workshop_attendees = new Workshop_Attendees();
