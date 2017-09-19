<?php
/**
 * Include and setup custom metaboxes and fields. (make sure you copy this file to outside the CMB2 directory)
 *
 * Be sure to replace all instances of 'workshop_' with your project's prefix.
 * http://nacin.com/2010/05/11/in-wordpress-prefix-everything/
 *
 * @category Workshop
 * @package  Demo_CMB2
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/CMB2/CMB2
 */

/**
 * Get the bootstrap! If using the plugin from wordpress.org, REMOVE THIS!
 */

if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/CMB2/init.php';
}

/**
 * Manually render Attendee List field.
 */
function workshop_status_cb( $field_args, $field ) {
    $classes        = $field->row_classes();
    $id             = $field->args( 'id' );
    $label          = $field->args( 'name' );
    $status         = get_post_meta( get_the_ID(), 'attendee_list', true );
    ?>

    <div class="cmb-row custom-field-row <?php echo esc_attr( $classes ); ?>">
        <div class="cmb-th">
            <label><?php echo esc_attr( $label ); ?></label>
        </div>
        <div class="cmb-td">
            <p>
                <?php
                if ( $status != true ) {
                    esc_html_e( 'In Progress', 'workshop' );
                } else {
                    esc_html_e( 'Completed', 'workshop' );
                }
                ?>
            </p>
        </div>
    </div>

    <?php
}

/**
 * Only show this box in the CMB2 REST API if the user is logged in.
 *
 * @param bool                  $is_allowed     Whether this box and its fields are allowed
 * @param CMB2_REST_Controller  $cmb_controller The controller object.
 *                                              CMB2 object available via `$cmb_controller`
 *
 * @return bool                 Whether this box and its field are allowed to be viewed.
 */
function workshop_limit_rest_view_to_logged_in_users( $is_allowed, $cmb_controller ) {
    if ( ! is_user_logged_in() ) {
        $is_allowed = false;
    }

    return $is_allowed;
}

/**
 * Hook in and add a box to be available in the CMB2 REST API. Can only happen on the `cmb_controller`
 *
 * @link https://github.com/CMB2/CMB2/wiki/REST-API
 */
add_action( 'cmb2_init', 'workshop_register_rest_api_box' );

function workshop_register_rest_api_box() {
    $prefix = 'workshop_';

    $cmb_workshop_rest = new_cmb2_box( array(
        'id'            => $prefix . 'metabox',
        'title'         => esc_html__( 'Workshop Data', 'workshop' ),
        'object_types'  => array( 'workshop' ), // Post type
        'show_in_rest'  => WP_REST_Server::ALLMETHODS, // WP_REST_Server::READABLE|WP_REST_Server::
        // Optional callback to limit box visibility.
        // See: https://github.com/CMB2/CMB2/wiki/REST-API#permissions
        'get_box_permissions_check_cb' => 'workshop_limit_rest_view_to_logged_in_users',
    ) );

	// Location
	$cmb_workshop_rest->add_field( array(
		'name'			=> esc_html__( 'Location:', 'workshop' ),
		'id'			=> $prefix . 'location',
		'type'			=> 'text',
	) );

	// Date & Time
	$cmb_workshop_rest->add_field( array(
		'name'			=> esc_html__( 'Date/Time:', 'workshop' ),
		'id'			=> $prefix . 'datetime',
		'type'			=> 'text_datetime_timestamp',
	) );

    // First Presenter's Information
    $cmb_workshop_rest->add_field( array(
        'name'          => esc_html__( 'Presenter 1 Name:', 'workshop' ),
        'id'            => $prefix . 'presenter_1',
        'type'          => 'text',
    ) );

    $cmb_workshop_rest->add_field( array(
        'name'          => esc_html__( 'Title 1:', 'workshop' ),
        'id'            => $prefix . 'title_1',
        'type'          => 'text',
    ) );

    $cmb_workshop_rest->add_field( array(
        'name'          => esc_html__( 'Abstract 1:', 'workshop' ),
        'id'            => $prefix . 'abstract_1',
        'type'          => 'wysiwyg',
        'options'       => array(
            'textarea_rows' => 5,
        ),
    ) );

    $cmb_workshop_rest->add_field( array(
        'name'          => esc_html__( 'Speaker Bio 1:', 'workshop' ),
        'id'            => $prefix . 'bio_1',
        'type'          => 'wysiwyg',
        'options'       => array(
            'textarea_rows' => 5,
        ),
    ) );

    // Second Presenter's Information
    $cmb_workshop_rest->add_field( array(
        'name'          => esc_html__( 'Presenter 2 Name:', 'workshop' ),
        'id'            => $prefix . 'presenter_2',
        'type'          => 'text',
    ) );

    $cmb_workshop_rest->add_field( array(
        'name'          => esc_html__( 'Title 2:', 'workshop' ),
        'id'            => $prefix . 'title_2',
        'type'          => 'text',
    ) );

    $cmb_workshop_rest->add_field( array(
        'name'          => esc_html__( 'Abstract 2:', 'workshop' ),
        'id'            => $prefix . 'abstract_2',
        'type'          => 'wysiwyg',
        'options'       => array(
            'textarea_rows' => 5,
        ),
    ) );

    $cmb_workshop_rest->add_field( array(
        'name'          => esc_html__( 'Speaker Bio 2:', 'workshop' ),
        'id'            => $prefix . 'bio_2',
        'type'          => 'wysiwyg',
        'options'       => array(
            'textarea_rows' => 5,
        ),
    ) );

    $cmb_workshop_rest->add_field( array(
        'name'          => esc_html__( 'Attendee List', 'workshop' ),
        'id'            => $prefix . 'attendee_list',
        'render_row_cb' => 'workshop_status_cb',
    ) );
}

/**
 * Hook in and add a box to be available in the CMB2 REST API. Can only happen on the `cmb_controller`
 *
 * @link https://github.com/CMB2/CMB2/wiki/REST-API
 */
add_action( 'cmb2_init', 'workshop_attendee_box_register_rest_api_box' );

function workshop_attendee_box_register_rest_api_box() {
    $prefix = 'workshop_attendees_';

    $cmb_attendee_rest = new_cmb2_box( array(
        'id'            => $prefix . 'metabox',
        'title'         => esc_html__( 'Attendee Info', 'workshop' ),
        'object_types'  => array( 'attendee' ), // Post type
        'show_in_rest'  => WP_REST_Server::ALLMETHODS, // WP_REST_Server::READABLE|WP_REST_Server::
        // Optional callback to limit box visibility.
        // See: https://github.com/CMB2/CMB2/wiki/REST-API#permissions
        'get_box_permissions_check_cb' => 'workshop_limit_rest_view_to_logged_in_users',
    ) );

    // Last Name
    $cmb_attendee_rest->add_field( array(
        'name'          => esc_html__( 'Last Name:', 'workshop' ),
        'id'            => $prefix . 'last_name',
        'type'          => 'text',
    ) );

	// First Name
	$cmb_attendee_rest->add_field( array(
		'name'			=> esc_html__( 'First Name:', 'workshop' ),
		'id'			=> $prefix . 'first_name',
		'type'			=> 'text',
	) );

	// Email
	$cmb_attendee_rest->add_field( array(
		'name'			=> esc_html__( 'Email:', 'workshop' ),
		'id'			=> $prefix . 'email',
		'type'			=> 'text_email',
	) );

	// Membership
	$cmb_attendee_rest->add_field( array(
		'name'			=> esc_html__( 'Membership:', 'workshop' ),
		'id'			=> $prefix . 'membership',
		'type'			=> 'text_date'
	) );

	// Chapter
	$cmb_attendee_rest->add_field( array(
		'name'           => esc_html__( 'Chapter:', 'workshop' ),
		'desc'           => esc_html__( 'Which KOTESOL chapter does this attendee belong to? (if applicable)', 'workshop' ),
		'id'             => $prefix . 'chapter_select',
		'taxonomy'       => 'chapter', //Enter Taxonomy Slug
		'type'           => 'taxonomy_select',
		'remove_default' => 'true' // Removes the default metabox provided by WP core. Pending release as of Aug-10-16
	) );

	/**
	 * Additional Attendee Data
	 */
	$cmb_attendee_additional_rest = new_cmb2_box( array(
        'id'            => $prefix . 'additional_metabox',
        'title'         => esc_html__( 'Additional Data', 'workshop' ),
        'object_types'  => array( 'attendee' ), // Post type
        'show_in_rest'  => WP_REST_Server::ALLMETHODS, // WP_REST_Server::READABLE|WP_REST_Server::
        // Optional callback to limit box visibility.
        // See: https://github.com/CMB2/CMB2/wiki/REST-API#permissions
        'get_box_permissions_check_cb' => 'workshop_limit_rest_view_to_logged_in_users',
    ) );

	// Website
	$cmb_attendee_additional_rest->add_field( array(
		'name'			=> esc_html__( 'Website:', 'workshop' ),
		'id'			=> $prefix . 'website',
		'type'			=> 'text_url',
	) );

	// Facebook
	$cmb_attendee_additional_rest->add_field( array(
		'name'			=> esc_html__( 'Facebook:', 'workshop' ),
		'id'			=> $prefix . 'facebook',
		'type'			=> 'text_url',
	) );

	// Twitter
	$cmb_attendee_additional_rest->add_field( array(
		'name'			=> esc_html__( 'Twitter:', 'workshop' ),
		'id'			=> $prefix . 'twitter',
		'type'			=> 'text_url',
	) );

	// LinkedIn
	$cmb_attendee_additional_rest->add_field( array(
		'name'			=> esc_html__( 'LinkedIn:', 'workshop' ),
		'id'			=> $prefix . 'linkedin',
		'type'			=> 'text_url',
	) );

	// Bio
	$cmb_attendee_additional_rest->add_field( array(
        'name'          => esc_html__( 'About Attendee:', 'workshop' ),
        'id'            => $prefix . 'bio',
        'type'          => 'wysiwyg',
        'options'       => array(
            'textarea_rows' => 5,
        ),
    ) );
}
