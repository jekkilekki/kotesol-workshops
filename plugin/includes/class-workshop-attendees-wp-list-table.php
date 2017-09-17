<?php
// This class extends the WP_List_Table class, so we need to make sure that it's there
require_once( ABSPATH . 'wp-admin/includes/screen.php' );
require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

/**
 * List all the available Attendees within the system
 */
class Workshop_Attendees_WP_List_Table extends WP_List_Table {

    var $is_search = false;

    function __construct() {
        if ( ! empty( $_REQUEST['s'] ) ) {
            $this->is_search = true;
        }

        parent::__construct( array(
            'plural'    => __( 'Attendees', 'workshop' ),
            'singular'  => __( 'Attendee', 'workshop' ),
        ) );
    }

    /**
     * Perform Attendees Query
     */
//     function prepare_items() {
//         global $workshop_attendees;
//
//         $columns = $this->get_columns();
//         $hidden = array();
//         $sortable = array(
//             'display_name'      => array( 'display_name', 'ASC' ),
//             'first_name'        => array( 'first_name', 'ASC' ),
//             'last_name'         => array( 'last_name', 'ASC' ),
//         );
//         $_sortable = apply_filters( 'workshop_attendees_sortable_columns', $this->get_sortable_columns() );
//
//         foreach ( (array) $_sortable as $id => $data ) {
//             if ( empty( $data ) ) {
//                 continue;
//             }
//
//             $data = (array) $data;
//             if ( ! isset( $data[1] ) ) {
//                 $data[1] = false;
//             }
//
//             $sortable[$id] = $data;
//         }
//
//         $this->_column_headers = array( $columns, $hidden, $sortable );
//
//         $paged = ( isset( $_REQUEST['paged'] ) ) ? intval( $_REQUEST['paged'] ) : 1;
//
//         $args = array(
//                 'paged'         => $paged,
//                 'posts_per_page'=> $per_page,
//                 'post_type'     => 'workshop', // $workshop->workshop_attendees->post_type
//                 'post_status'   => 'any',
//                 'orderby'       => 'title',
//                 'order'         => 'ASC',
//         );
//
//         $args = apply_filters( 'workshop_attendees_query_args', $args );
//
//         if ( isset( $_REQUEST['orderby'] ) ) {
//             switch( $_REQUEST['orderby'] ) {
//                 case 'display_name':
//                     $args['orderby'] = 'title';
//                     break;
//                 case 'first_name':
//                 case 'last_name':
//                     $args['orderby'] = 'meta_value';
//                     $args['meta_key'] = $workshop->workshop_attendees->get_post_meta_key( $_REQUEST['orderby'] );
//             }
//         }
//         if ( isset( $_REQUEST['order'] ) && in_array( strtoupper( $_REQUEST['order'] ), array( 'ASC', 'DESC' ) ) ) {
//             $args['order'] = strtoupper( $_REQUEST['order'] );
//         }
//
//         $this->filters = array(
//                 'show-all'              => __( 'Show all', 'workshop' ),
//                 'with-linked-account'   => __( 'With linked account', 'workshop' ),
//                 'without-linked-account'=> __( 'Without linked account', 'workshop' ),
//         );
//
//         if ( isset( $_REQUEST['filter'] ) && array_key_exists( $_REQUEST['filter'], $this->filters ) ) {
//             $this->active_filter = sanitize_key( $_REQUEST['filter'] );
//         } else {
//             $this->active_filter = 'show-all';
//         }
//
//         switch ( $this->active_filter ) {
//             case 'with-linked-account':
//             case 'without-linked-account':
//                 $args['meta_key'] = $workshop->workshop_attendees->get_post_meta_key( 'linked_account' );
//                 if ( 'with-linked-account' == $this->active_filter ) {
//                     $args['meta_compare'] = '!=';
//                 } else {
//                     $args['meta_compare'] = '=';
//                 }
//                 $args['meta_value'] = '0';
//                 break;
//         }
//
//         if ( $this->is_search ) {
//             add_filter( 'posts_where', array( $this, 'filter_query_for_search' ) );
//         }
//
//         $attendee_workshops = new WP_Query( $args );
//         $items = array();
//         foreach ( $attendee_workshops->get_posts() as $workshop ) {
//             $items[] = $workshop->workshop_attendees->workshop_get_attendees_by( 'ID', $workshop->ID );
//         }
//
//         if ( $this->is_search ) {
//             remove_filter( 'posts_where', array( $this, 'filter_query_for_search' ) );
//         }
//
//         $this->items = $items;
//
//         $this->set_pagination_args( array(
//             'total_items'   => $attendee_workshops->found_posts,
//             'per_page'      => $per_page,
//         ) );
//     }
//
//     function filter_query_for_search( $where ) {
//         global $wpdb;
//         $var = '%' . sanitize_text_field( $_REQUEST['s'] ) . '%';
//         $where .= $wpdb->prepare( ' AND (post_title LIKE %s OR post_name LIKE %s )', $var, $var );
//         return $where;
//     }
//
//     /**
//      * Either there are no Attendees, or the search doesn't match any
//      */
//     function no_items() {
//         esc_html_e( 'No matching Attendees were found.', 'workshop' );
//     }
//
//     /**
//      * Generate the columns of information to be displayed on our List Table
//      */
//     function get_columns() {
//         $columns = array(
//             'display_name'      => __( 'Display Name', 'workshop' ),
//             'first_name'        => __( 'First Name', 'workshop' ),
//             'last_name'         => __( 'Last Name', 'workshop' ),
//             'user_email'        => __( 'E-mail', 'workshop' ),
//             'linked_account'    => __( 'Linked Account', 'workshop' ),
//             'workshops'         => __( 'Workshops Attended', 'workshop' ),
//             'membership'        => __( 'Membership Expiration', 'workshop' ),
//         );
//
//         $columns = apply_filters( 'workshop_attendees_manage_columns', $columns );
//         return $columns;
//     }
//
//     /**
//      * Render a single row
//      */
//     function single_row( $item ) {
//         static $alternate_class = '';
//         $alternate_class = ( '' === $alternate_class ? ' alternate' : '' );
//         $row_class = 'workshop-attendee-static' . $alternate_class . '"';
//
//         echo '<tr id="' . esc_attr( 'workshop-attendee-' . $item->ID ) . '" class="' . esc_attr( $row_class ) . '">';
//         $this->single_row_columns( $item );
//         echo '</tr>';
//     }
//
//     /**
//      * Render columns, some are overridden below
//      */
//     function column_default( $item, $column_name ) {
//         switch ( $column_name ) {
//             case 'first_name':
//             case 'last_name':
//                 return $item->$column_name;
//             case 'user_email':
//                 return '<a href="' . esc_attr( 'mailto:' . $item->user_email ) . '">' . esc_html( $item->user_email ) . '</a>';
//
//             default:
//                 do_action( 'workshop_attendees_custom_columns', $column_name, $item->ID );
//             break;
//         }
//     }
//
//     /**
//      * Render display name, e.g. Attendee name
//      */
//     function column_display_name( $item ) {
//         $item_edit_link = get_edit_post_link( $item->ID );
//         $args = array(
//                 'action'        => 'delete',
//                 'id'            => $item->ID,
//                 '_wpnonce'      => wp_create_nonce( 'workshop-attendee-delete' ),
//             );
//         $item_delete_link = add_query_arg( array_map( 'rawurlencode', $args ), menu_page_url( 'view-attendees', false ) );
//         $item_view_link = get_author_posts_url( $item->ID, $item->user_nicename );
//
//         $output = '';
//
//         $output .= workshop_attendees_get_avatar( $item, 32 );
//
//         if ( current_user_can( 'edit_post', $item->ID ) ) {
//             $output .= '<a href="' . esc_url( $item_edit_link ) . '">' . esc_html( $item->display_name ) . '</a>';
//         } else {
//             $output .= esc_html( $item->display_name );
//         }
//
//         $actions = array();
//         if ( current_user_can( 'edit_post', $item->ID ) ) {
//             $actions['edit'] = '<a href="' . esc_url( $item_edit_link ) . '">' . __( 'Edit', 'workshop' ) . '</a>';
//         }
//         if ( current_user_can( 'delete_post', $item->ID ) ) {
//             $actions['delete'] = '<a href="' . esc_url( $item_delete_link ) . '">' . __( 'Delete', 'workshop' ) . '</a>';
//         }
//         $actions['view'] = '<a href="' . esc_url( $item_view_link ) . '">' . __( 'View Workshops', 'workshop' ) . '</a>';
//         $actions = apply_filters( 'workshop_attendees_row_actions', $actions, $item );
//         $output .= $this->row_actions( $actions, false );
//
//         return $output;
//     }
//
//     /**
//      * Render linked account
//      */
//     function column_linked_account( $item ) {
//         if ( $item->linked_account ) {
//             $account = get_user_by( 'login', $item->linked_account );
//             if ( $account ) {
//                 if ( current_user_can( 'edit_users' ) ) {
//                     return '<a href="' . admin_url( 'user-edit.php?user_id=' . $account->ID ) . '">' . esc_html( $item->linked_account ) . '</a>';
//                 }
//                 return $item->linked_account;
//             }
//         }
//         return '';
//     }
//
//     /**
//      * Render the Workshops Attended count column
//      */
//     function column_workshops( $item ) {
//         global $workshop_attendees;
//         $term = $workshop_attendees->get_author_term( $item );
//         if ( $term ) {
//             $count = $term->count;
//         } else {
//             $count = 0;
//         }
//         return '<a href="' . esc_url( add_query_arg( 'author_name', rawurlencode( $item->user_login ), admin_url( 'edit.php' ) ) ) . '">' . $count '</a>';
//     }
//
//     /**
//      * Allow users to filter the Attendees by various criteria
//      */
//     function extra_tablenav( $which ) {
        ?><div class="alignleft actions"><?php
// if ( 'top' == $which ) {
//     if ( ! empty( $this->filters ) ) {
//         echo '<select name="filter">';
//         foreach ( $this->filters as $key => $value ) {
//             echo '<option value="' . esc_attr( $key ) . '" ' . selected( $this->active_filter, $key, false ) . '>' . esc_attr( $value ) . '</option>';
//         }
//         echo '</select>';
//     }
//     submit_button( __( 'Filter', 'workshop' ), 'secondary', false, false );
// }
        ?></div><?php
//     }
//
//     function display() {
//         global $workshop;
//         $this->search_box( $workshop->workshop_attendees->labels['search_items'], 'workshop' );
//         parent::display();
//     }

} // END class Workshop_Attendees_WP_List_Table
// $workshop_attendees_table = new Workshop_Attendees_WP_List_Table();
