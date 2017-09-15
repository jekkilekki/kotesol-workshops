<?php
/**
 * Plugin Name: Workshop
 * Plugin URI:  https://github.com/jekkilekki/workshop-plugin
 * Description: A Workshop manager.
 * Version:     0.0.1
 * Author:      Aaron Snowberger
 * Author URI:  https://aaron.kr/
 * Text Domain: workshop
 * Domain Path: /languages
 * License:     GPL2
 *
 * Workshop is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Workshop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Workshop. If not, see https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html.
 */

/**
 * Register Workshop Post Type.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/posttypes.php';
register_activation_hook( __FILE__, 'workshop_rewrite_flush' );

/**
 * Register Workshop Coordinator Role.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/roles.php';
register_activation_hook( __FILE__, 'workshop_register_role' );
register_deactivation_hook( __FILE__, 'workshop_remove_role' );

/*
 * Add/remove capabilities to Workshop Coordinator.
 */
register_activation_hook( __FILE__, 'workshop_add_capabilities' );
register_deactivation_hook( __FILE__, 'workshop_remove_capabilities' );

/**
 * Mark the Workshop as having an Attendees List or not.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/status.php';

/**
 * Add additional User meta information for Workshop attendees.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/usermeta.php';

/**
 * Add in CMB2 for fun new fields.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/cmb2-functions.php';

/**
 * Grant Workshop access for index pages for certain users.
 */
add_action( 'pre_get_posts', 'workshop_grant_access' );

function workshop_grant_access( $query ) {
    // BE CAREFUL about which $query you look for
    if ( isset( $query->query_vars[ 'post_type' ] ) ) {
        if ( $query->query_vars[ 'post_type' ] == 'workshop' ) {
            if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) { // https://github.com/WP-API/WP-API/issues/926
                if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
                    $query->set( 'post_status', 'private' );
                } elseif ( current_user_can( 'workshop_coordinator' ) ) {
                    $query->set( 'post_status', 'private' );
                    $query->set( 'author', get_current_user_id() );
                }
            }
        }
    }
}

/**
 * Remove "Private: " from the titles of private workshops.
 */
add_filter( 'the_title', 'workshop_remove_private_prefix' );

function workshop_remove_private_prefix( $title ) {
    $title = str_replace( 'Private: ', '', $title );
    return $title;
}
