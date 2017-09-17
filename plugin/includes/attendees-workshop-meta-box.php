<?php
/**
 * Creates Workshop Attendees as a separate entity from Users.
 *
 * Allows multiple Attendees to be added to Workshops.
 * Is handled in the backend by its own admin page.
 * Similar to Co-Authors Plus - borrows some of the features.
 * @see https://wordpress.org/plugins/co-authors-plus
 */

// Register our models
add_action( 'init', 'workshop_action_init_late', 100 );
add_action( 'admin_init', 'workshop_admin_init' );

// Action to set Attendees when a Workshop is saved.
// add_action( 'save_post', 'workshop_update_post', 10, 2 );
//
// add_filter( 'get_usernumposts', 'workshop_filter_count_workshops', 10, 2 );
//
// // Action to set up Attendees auto-suggest
// add_action( 'wp_ajax_workshop_ajax_suggest', 'workshop_ajax_suggest' );
//
// Handle Attendees meta box
add_action( 'add_meta_boxes', 'workshop_add_attendees_box' );
//
// // Restrict WordPress from "blowing away" term order on bulk edit
// add_filter( 'wp_get_object_terms', 'workshop_filter_object_terms', 10, 4 );
//
// // Delete Attendee Cache on Workshop Save & Workshop Delete
// add_action( 'save_post', 'workshop_clear_cache' );
// add_action( 'delete_post', 'workshop_clear_cache' );
// add_action( 'set_object_terms', 'workshop_clear_cache_on_terms_set', 10, 6 );

function workshop_get_attendees( $post_id = 0 ) {
    global $post, $post_ID, $workshop_attendees, $wpdb;

    $attendees = array();
    $post_id = (int) $post_id;
    if ( ! $post_id && $post_ID ) {
        $post_id = $post_ID;
    }

    if ( ! $post_id && $post ) {
        $post_id = $post->ID;
    }

    if ( $post_id ) {

    }
    return 25;
}

/**
 * Register the taxonomy used to manage relationships,
 * and the Custom Post Type to store Attendee Data.
 */
function workshop_action_init() {
    // Load Attendees functionality if needed:
    $workshop_attendees = new Workshop_Attendees;
}

/**
 * Register the 'Attendee' taxonomy and add Post Type support
 */
function workshop_action_init_late() {
    // Register new taxonomy so that we can store all of the relationships
    $args = array(
        'hierarchical'  => false,
        'label'         => false,
        'query_var'     => false,
        'rewrite'       => false,
        'public'        => false,
        'sort'          => true,
        'args'          => array( 'orderby' => 'term_order' ),
        'show_ui'       => false,
    );

    // Register taxonomy
    register_taxonomy( 'attendee', 'workshop', $args ); // $attendee_taxonomy = 'workshop'
}

function workshop_admin_init() {
    global $pagenow;

    // Add the main JS script and CSS file:
    // add_action( 'admin_enqueue_scripts', 'workshop_enqueue_scripts' );
    // Add necessary JS variables
    // add_action( 'admin_head', 'workshop_js_vars' );

    // Hooks to add Attendees to author column to Edit page

    // Add quick-edit Attendee select field

    // Hooks to modify the Workshop number count on the Users WP List Table
    add_filter( 'manage_workshop_posts_columns', 'workshop_filter_manage_workshop_columns' );
    add_filter( 'manage_workshop_posts_custom_column', 'workshop_attendees_column', 10, 2 );

    // Apply some targeted filters
    // add_action( 'load-edit.php', 'workshop_load_edit' );
}

/**
 * Adds a custom Attendees box
 */
function workshop_add_attendees_box() {
    add_meta_box(
        'workshop_attendees_meta_box',          // Meta Box ID
        __( 'Attendee List', 'workshop' ),      // Meta Box title
        'workshop_attendees_meta_box_callback', // callback to render Meta Box
        'workshop',                             // $attendee_taxonomy
        'side',                                 // Meta Box location
        'low'                                   // Meta Box position
    );
}

/**
 * Attendees Meta Box callback.
 *
 * @param WP_Post $post Current post object.
 */
function workshop_attendees_meta_box_callback( $post ) {
    global $post, $workshop_attendees, $current_screen;

    $post_id = $post->ID;

    $default_user = apply_filters( 'workshop_default_attendee', wp_get_current_user() );
    $attendees[] = $default_user;

    // $attendees = workshop_get_attendees();

    $count = 0;
    if ( ! empty( $attendees ) ) :
        ?>
        <div id="attendees-readonly" class="hide-if-js">
            <ul>
            <?php
            foreach( $attendees as $attendee ) :
                $count++;
                ?>
                <li>
                    <?php echo get_avatar( $attendee->user_email, '25' ); ?>
                    <span id="<?php echo esc_attr( 'attendee-readonly-' . $count ); ?>" class="attendee-tag">
                        <input type="text" name="attendeesinput[]" readonly="readonly" value="<?php echo esc_attr( $attendee->display_name ); ?>" />
                        <input type="text" name="attendees[]" value="<?php echo esc_attr( $attendee->user_login ); ?>" />
                        <input type="text" name="attendeesemails[]" value="<?php echo esc_attr( $attendee->user_email ); ?>" />
                        <input type="text" name="attendeesnicenames[]" value="<?php echo esc_attr( $attendee->user_nicename ); ?>" />
                    </span>
                </li>
                <?php
            endforeach;
            ?>
            </ul>
            <div class="clear"></div>
            <p><?php echo wp_kses( __( '<strong>Note:</strong> To edit attendees, please enable JavaScript or use a JavaScript-capable browser.', 'workshop' ), array( 'strong' => array() ) ); ?></p>
        </div>
        <?php
    endif;
    ?>

    <div id="attendees-edit" class="hide-if-no-js">
        <p><?php echo wp_kses( __( 'Click on an attendee to change them. Click on <strong>Remove</strong> to remove them.', 'workshop' ), array( 'strong' => array() ) ); ?></p>
    </div>

    <?php wp_nonce_field( 'attendees-edit', 'attendees-nonce' ); ?>

    <?php
}

/**
 * Save Attendees Meta Box content.
 *
 * @param int $post_id Post ID
 */
function workshop_update_post( $post_id, $post ) {
    // bail on autosave
    if ( defined( 'DOING_AUTOSAVE' ) && ! DOING_AUTOSAVE ) {
        return;
    }

    // if ( current_user_can( 'add_attendees' ) ) {
    //     // if workshop_current_user_can_set_attendees and nonce valid
    //     if ( isset( $_POST['attendees-nonce'] ) $$ isset( $_POST['attendees'] ) ) {
    //         check_admin_referer( 'attendees-edit', 'attendees-nonce' );
    //
    //         $attendees = (array) $_POST['attendees'];
    //         $attendees = array_map( 'sanitize_text_field', $attendees );
    //         workshop_add_attendees( $post_id, $attendees );
    //     }
    // } else {
    //     // If the user can't set attendees
    // }
}

/**
 * Add attendees count column on edit pages
 *
 * @param array $post_columns
 */
function workshop_filter_manage_workshop_columns( $columns ) {

    $new_columns = array();
    foreach ( $columns as $key => $value ) {
        $new_columns[ $key ] = $value;
        if ( 'author' === $key || 'coauthors' === $key ) { // @TODO: Remove 'coauthors' later, unless we want to support Co-Authors Plus I guess
            $new_columns['attendees'] = __( 'Attendees', 'workshop' );
        }
    }

    return $new_columns;

}

/**
 * Manages the Attendees column on edit pages
 *
 * @param array $column The array of columns
 * @param int $post_id The ID of the post
 */
function workshop_attendees_column( $column, $post_id ) {
    if ( 'attendees' === $column ) {
        global $post;
        $attendees = workshop_get_attendees( $post->ID );
        echo count( $attendees );
    }
}

/**
 * Add one or more Attendees to a Workshop
 *
 * @param int
 * @param array
 * @param bool
 */
function workshop_add_attendees( $post_id, $attendees, $append = false ) {
    global $current_user, $wpdb;

    $post_id = (int) $post_id;
    $insert = false;

    // Best way to persist order
    if ( $append ) {
        //$existing_attendees = wp_list_pluck( workshop_get_attendees( $post_id ), 'user_login' );
    } else {
        $existing_attendees = array();
    }

    // An attendee is always required - maybe?
    if ( empty( $attendees ) ) {
        $attendees = array( $current_user->user_login );
    }

    // Set the attendees
    $attendees = array_unique( array_merge( $existing_attendees, $attendees ) );
    // $attendee_objects = array();

    wp_set_post_terms( $post_id, $attendees, 'workshop', false ); // 'workshop' = $attendee_taxonomy
    return true;
}
