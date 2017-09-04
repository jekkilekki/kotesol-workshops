<?php
/**
 * Auto-update the Attendees field on Save Post.
 */
add_action( 'save_post', 'workshop_attendee_list', 10, 3 );

function workshop_attendee_list( $post_id, $post, $update ) {
    // Exit the function if not a Workshop Post Type
    if ( 'workshop' != get_post_type( $post_id ) ) return;

    if ( isset( $_POST[ 'workshop_attendees' ] ) ) {
        $attendees = $_POST[ 'workshop_attendees' ];
    }

    // Make sure there's content (check Post Title)
    if ( isset( $_POST[ 'post_title' ] ) ) {
        if ( empty( $attendees ) ) {
            update_post_meta( $post_id, 'attendee_list', false );
        } else {
            update_post_meta( $post_id, 'attendee_list', true );
        }
    }
}

/**
 * Register new REST field for attendee_list.
 */
add_action( 'rest_api_init', 'workshop_register_attendee_list' );

function workshop_register_attendee_list() {
    register_rest_field(
        'workshop',
        'attendee_list',
        array(
            'get_callback'      => 'workshop_get_attendee_list_status',
            'update_callback'   => 'workshop_update_attendee_list_status',
            'schema'            => null,
        )
    );
}

function workshop_get_attendee_list_status( $object, $field_name, $request ) {
    return get_post_meta( $object['id'], $field_name, true );
}

function workshop_update_attendee_list_status( $value, $object, $field_name ) {
    // Make sure we're using ONLY a bool
    if ( is_bool( $value ) !== true ) { return; }

    return update_post_meta( $object->ID, $field_name, $value );
}
