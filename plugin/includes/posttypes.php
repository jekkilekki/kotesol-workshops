<?php
/**
 * Register a custom post type called "Workshop".
 *
 * @see get_post_type_labels() for label keys.
 * @link https://developer.wordpress.org/reference/functions/register_post_type/
 */
function workshop_cpt_init() {
    $labels = array(
        'name'                  => _x( 'Workshops', 'Post type general name', 'workshop' ),
        'singular_name'         => _x( 'Workshop', 'Post type singular name', 'workshop' ),
        'menu_name'             => _x( 'Workshops', 'Admin Menu text', 'workshop' ),
        'name_admin_bar'        => _x( 'Workshop', 'Add New on Toolbar', 'workshop' ),
        'add_new'               => __( 'Add New', 'workshop' ),
        'add_new_item'          => __( 'Add New Workshop', 'workshop' ),
        'new_item'              => __( 'New Workshop', 'workshop' ),
        'edit_item'             => __( 'Edit Workshop', 'workshop' ),
        'view_item'             => __( 'View Workshop', 'workshop' ),
        'all_items'             => __( 'All Workshops', 'workshop' ),
        'search_items'          => __( 'Search Workshops', 'workshop' ),
        'parent_item_colon'     => __( 'Parent Workshops:', 'workshop' ),
        'not_found'             => __( 'No Workshops found.', 'workshop' ),
        'not_found_in_trash'    => __( 'No Workshops found in Trash.', 'workshop' ),
        'featured_image'        => _x( 'Workshop Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'workshop' ),
        'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'workshop' ),
        'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'workshop' ),
        'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'workshop' ),
        'archives'              => _x( 'Workshop archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'workshop' ),
        'insert_into_item'      => _x( 'Insert into Workshop', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'workshop' ),
        'uploaded_to_this_item' => _x( 'Uploaded to this Workshop', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'workshop' ),
        'filter_items_list'     => _x( 'Filter Workshops list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'workshop' ),
        'items_list_navigation' => _x( 'Workshops list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'workshop' ),
        'items_list'            => _x( 'Workshops list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'workshop' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'workshops' ),
        'capability_type'    => 'workshop',
        'has_archive'        => true,
        'hierarchical'       => false,
        'show_in_rest'       => true,
        'rest_base'          => 'workshops',
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-welcome-learn-more',
        'supports'           => array( 'title', 'editor', 'author' ),
        'map_meta_cap'       => true,
    );

    register_post_type( 'workshop', $args );
}

add_action( 'init', 'workshop_cpt_init' );

/**
 * Flush rewrite rules on activation.
 */
function workshop_rewrite_flush() {
    workshop_cpt_init();
    flush_rewrite_rules();
}
