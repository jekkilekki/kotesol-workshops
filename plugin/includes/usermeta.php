<?php
/**
 * Add User meta data for workshop members
 *
 * NOTES:
 * 1. Add User Taxonomy for KOTESOL (Workshop) Chapter
 * 2. Add list of all Workshops attended - either in User Profile page, or separate Users (Members) List in Workshop CPT
 * 3. List of Workshops should be non-editable for the Users themselves - ideally, Members don't login anyway yet
 * 4. Link Users to CMB2 - Add Users to CMB2 - also with REST API
 * 5. -- Check if User already exists, if so, add - if not, create - also update info if necessary
 * 6. Don't allow "Members" to show up in the Users list - create separate "Members" page to list all of them - filter by "Chapter" Taxonomy
 */
/**
 * Workshop member checkbox on editing screens
 *
 * @see https://developer.wordpress.org/plugins/users/working-with-user-metadata/
 * @param $user WP_User user object
 */
function workshop_usermeta_member( $user ) {
    ?>
    <h2><?php esc_html_e( 'KOTESOL Membership', 'workshop' ); ?></h2>
    <table class="form-table">
        <tbody>
            <tr class="workshop-member-wrap">
                <th scope="row"><?php esc_html_e( 'KOTESOL Member', 'workshop' ); ?></th>
                <td>
                    <label for="workshop-member">
                        <input type="checkbox"
                                id="workshop-member"
                                name="workshop-member"
                                value="Workshop Member"
                                <?php echo get_user_meta( $user->ID, 'workshop-member', true ) ? 'checked' : ''; ?>>
                        <?php esc_html_e( 'Workshop Member', 'workshop' ); ?>
                    </label>
                </td>
            </tr>
            <tr class="workshop-membership-date-wrap">
                <th scope="row">
                    <label for="workshop-membership">
                        <?php esc_html_e( 'Membership expiration', 'workshop' ); ?>
                    </label>
                </th>
                <td>
                    <input type="date"
                            id="workshop-membership"
                            name="workshop-membership"
                            value="<?php echo esc_attr( get_user_meta( $user->ID, 'workshop-membership', true ) ); ?>">
                </td>
            </tr>
            <tr class="workshop-attended-wrap">
                <th scope="row">
                    <label for="workshop-attended">
                        <?php esc_html_e( 'Workshops attended', 'workshop' ); ?>
                    </label>
                </th>
                <td>
                    <input type="text"
                            id="workshop-attended"
                            name="workshop-attended"
                            value="<?php echo esc_attr( get_user_meta( $user->ID, 'workshop-attended', true ) ); ?>">
                </td>
            </tr>
        </tbody>
    </table>
    <?php
}

/**
 * Save Workshop member meta.
 *
 * @param $user_id int the ID of the current user.
 *
 * @return bool Meta ID if the key didn't exist, true on
 */
function workshop_usermeta_member_update( $user_id ) {
    // check that the current user has the capability to edit the $user_id
    if ( ! current_user_can( 'edit_user', $user_id  ) ) {
        return false;
    }

    // create/update user meta for the $user_id

    // NOTE: Perhaps store this meta stuff in an array - to do all at once
    return update_user_meta(
        $user_id,
        'workshop-membership',
        $_POST[ 'workshop-membership' ]
    );
}

// Add field to user's own profile editing screen
add_action( 'edit_user_profile', 'workshop_usermeta_member' );

// Add field to user profile editing screen
add_action( 'show_user_profile', 'workshop_usermeta_member' );

// Add save action to user's own profile
add_action( 'personal_options_update', 'workshop_usermeta_member_update' );

// Add save action to user profile editing screen
add_action( 'edit_user_profile_udpate', 'workshop_usermeta_member_update' );


/**
 * Add REST API endpoints for Co-Authors plugin
 *
 * @see http://www.jurecuhalev.com/blog/2016/12/25/co-authors-plus-wordpress-json-rest-api/
 */
if ( function_exists( 'get_coauthors' ) ) {
    add_action( 'rest_api_init', 'workshop_coauthor_attendees' );
    function workshop_coauthor_attendees() {
        register_rest_field( 'workshop',
            'attendees',
            array(
                'get_callback'      => 'workshop_get_coauthor_attendees',
                'update_callback'   => null,
                'schema'            => null,
            )
        );
    }

    function workshop_get_coauthor_attendees( $object, $field_name, $request ) {
        $workshop_attendees = get_coauthors( $object['id'] );

        $attendees = array();
        foreach ( $workshop_attendees as $attendee ) {
            $attendees[] = array(
                'display_name'      => $attendee->display_name,
                'user_nicename'     => $attendee->user_nicename,
                'first_name'        => $attendee->first_name,
                'last_name'         => $attendee->last_name,
                'user_email'        => $attendee->user_email,
                'membership'        => $attendee->workshop_membership,
            );
        }
        
        return $attendees;
    }
}
