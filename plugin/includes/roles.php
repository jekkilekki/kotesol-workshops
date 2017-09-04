<?php
/**
 * Register Workshop Coordinator role.
 * @link https://developer.wordpress.org/reference/functions/add_role/
 */
function workshop_register_role() {
    add_role( 'workshop_coordinator', 'Workshop Coordinator' );
}

/**
 * Register Workshop Coordinator role.
 * @link https://developer.wordpress.org/reference/functions/remove_role/
 */
function workshop_remove_role() {
    remove_role( 'workshop_coordinator', 'Workshop Coordinator' );
}

/**
 * Grant Workshop capabilities to Administrator, Editor, and Workshop Coordinator
 */
function workshop_add_capabilities() {

    $roles = array( 'administrator', 'editor', 'workshop_coordinator' );

    foreach( $roles as $the_role ) {
        $role = get_role( $the_role );
        $role->add_cap( 'read' );
        $role->add_cap( 'edit_workshops' );
        $role->add_cap( 'publish_workshops' );
        $role->add_cap( 'edit_published_workshops' );
    }

    $manager_roles = array( 'administrator', 'editor' );

    foreach( $manager_roles as $the_role ) {
        $role = get_role( $the_role );
        $role->add_cap( 'read_private_workshops' );
        $role->add_cap( 'edit_others_workshops' );
        $role->add_cap( 'edit_private_workshops' );
        $role->add_cap( 'delete_workshops' );
        $role->add_cap( 'delete_published_workshops' );
        $role->add_cap( 'delete_private_workshops' );
        $role->add_cap( 'delete_others_workshops' );
    }

}

/**
 * Remove Workshop capabilities on plugin deactivation
 */
function workshop_remove_capabilities() {

    $manager_roles = array( 'administrator', 'editor' );

    foreach( $manager_roles as $the_role ) {
        $role = get_role( $the_role );
        $role->remove_cap( 'read' );
        $role->remove_cap( 'edit_workshops' );
        $role->remove_cap( 'publish_workshops' );
        $role->remove_cap( 'edit_published_workshops' );
        $role->remove_cap( 'read_private_workshops' );
        $role->remove_cap( 'edit_others_workshops' );
        $role->remove_cap( 'edit_private_workshops' );
        $role->remove_cap( 'delete_workshops' );
        $role->remove_cap( 'delete_published_workshops' );
        $role->remove_cap( 'delete_private_workshops' );
        $role->remove_cap( 'delete_others_workshops' );
    }

}
