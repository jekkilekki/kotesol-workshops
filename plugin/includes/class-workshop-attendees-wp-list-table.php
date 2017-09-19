<?php
/**
 * WP_List_Table Class for Attendees Custom Post Type
 *
 * @see https://github.com/Veraxus/wp-list-table-example/blob/master/includes/class-tt-example-list-table.php
 * @see https://www.sitepoint.com/using-wp_list_table-to-create-wordpress-admin-tables/
 */

// This class extends the WP_List_Table class, so we need to make sure that it's there
require_once( ABSPATH . 'wp-admin/includes/screen.php' );
require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

/**
 * List all the available Attendees within the system
 */
class Workshop_Attendees_WP_List_Table extends WP_List_Table {
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        // Process bulk action:
        $this->process_bulk_action();

        $data = $this->query_db();

        usort( $data, array( &$this, 'sort_data' ) );
        $perPage = 20;

        $currentPage = $this->get_pagenum();
        $totalItems = count( $data );

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $data = array_slice( $data, ( ( $currentPage - 1 ) * $perPage ), $perPage );
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->items = $data;
    }

    /**
     * Function to query DB for Attendee data
     */
    protected function query_db( $per_page = 20, $page_number = 1 ) {
        global $wpdb;

        $sql = "SELECT * FROM $wpdb->posts WHERE post_type='attendee'";

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        // New try
        $args = array(
            'post_type'     => 'attendee',
            'posts_per_page'=> $per_page,
            'orderby'       => ! empty( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : 'title',
            'order'         => ! empty( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'ASC',
        );

        $attendeeArray = array();
        $attendees = new WP_Query($args);

        if( $attendees->have_posts() ) :
            while( $attendees->have_posts() ) : $attendees->the_post();

                $id = get_the_ID();
                $attendee_meta = get_post_meta( $id );
                // Add the $item ID to the beginning of the Attendee Meta array:
                array_unshift( $attendee_meta, array( 'id' => $id ) );

                $attendeeArray[$id] = $attendee_meta;
            endwhile;
        endif;

        wp_reset_postdata();

        return $attendeeArray;
    }

    /**
     * Generate the columns of information to be displayed on our List Table
     *
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information.
     */
    public function get_columns() {
        $columns = array(
            'workshop_attendees_display_name'      => __( 'Display Name', 'workshop' ),
            'workshop_attendees_first_name'        => __( 'First Name', 'workshop' ),
            'workshop_attendees_last_name'         => __( 'Last Name', 'workshop' ),
            'workshop_attendees_user_email'        => __( 'E-mail', 'workshop' ),
            'workshop_attendees_linked_account'    => __( 'Linked Account', 'workshop' ),
            // 'workshop_attendees_workshops'         => __( 'Workshops Attended', 'workshop' ),
            'workshop_attendees_membership'        => __( 'Membership', 'workshop' ),
        );
        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return array An associative array containing all the columns that should be hidden.
     */
    protected function get_hidden_columns() {
        return array();
    }

    /**
     * Get a list of sortable columns.
     *
     * Register columns to be sortable (ASC/DESC) here.
     *
     * @return array An associative array containing all the columns that should be sortable.
     */
    protected function get_sortable_columns() {
        $sortable = array(
            'workshop_attendees_display_name'      => array( 'workshop_attendees_display_name', 'ASC' ),
            'workshop_attendees_first_name'        => array( 'workshop_attendees_first_name', 'ASC' ),
            'workshop_attendees_last_name'         => array( 'workshop_attendees_last_name', 'ASC' ),
            'workshop_attendees_membership'        => array( 'workshop_attendees_membership', 'ASC' ),
        );
        return $sortable;
    }

    /**
     * Get default column value.
     *
     * Called when parent class can't find a method specifically for building a given column.
     * For more detailed insight:
     * @see WP_List_Table::single_row_columns()
     *
     * @param object $item A singular item (one full row's worth of data)
     * @param string $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     */
    protected function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'workshop_attendees_first_name':
            case 'workshop_attendees_last_name':
                return $item[$column_name][0];
            case 'workshop_attendees_user_email':
                return '<a href="' . esc_attr( 'mailto:' . $item['workshop_attendees_email'][0] ) . '">' . esc_html( $item['workshop_attendees_email'][0] ) . '</a>';

            default:
                do_action( 'workshop_attendees_custom_columns', $column_name, $item['id'] );
                return print_r( $item, true ); // Show the whole array for troubleshooting purposes.
            break;
        }
    }

    /**
     * Render display name, e.g. Attendee name
     */
    protected function column_workshop_attendees_display_name( $item ) {
        $item_edit_link = get_edit_post_link( $item[0]['id'] );
        $args = array(
                'action'        => 'delete',
                'id'            => $item[0]['id'],
                '_wpnonce'      => wp_create_nonce( 'workshop-attendee-delete' ),
            );
        $item_delete_link = add_query_arg( array_map( 'rawurlencode', $args ), menu_page_url( 'view-attendees', false ) );
        $item_view_link = get_author_posts_url( $item[0]['id'], $item['user_nicename'] );

        $output = '';
        $output .= workshop_attendees_get_avatar( $item, 42 );

        $display_name = $item['workshop_attendees_first_name'][0] . ' ' . $item['workshop_attendees_last_name'][0];

        if ( current_user_can( 'edit_post', $item[0]['id'] ) ) {
            $output .= '<a href="' . esc_url( $item_edit_link ) . '">' . esc_html( $display_name ) . '</a>';
        } else {
            $output .= esc_html( $display_name );
        }

        // Rollover actions:
        $actions = array();
        // Edit Post:
        if ( current_user_can( 'edit_post', $item[0]['id']) ) {
            $actions['edit'] = '<a href="' . esc_url( $item_edit_link ) . '">' . __( 'Edit', 'workshop' ) . '</a>';
        }
        // Delete Post:
        if ( current_user_can( 'delete_post', $item[0]['id'] ) ) {
            $actions['delete'] = '<a href="' . esc_url( $item_delete_link ) . '">' . __( 'Delete', 'workshop' ) . '</a>';
        }
        // View Post:
        $actions['view'] = '<a href="' . esc_url( $item_view_link ) . '">' . __( 'View Workshops', 'workshop' ) . '</a>';
        $actions = apply_filters( 'workshop_attendees_row_actions', $actions, $item );
        $output .= $this->row_actions( $actions, false );

        return $output;
    }

    /**
	 * Render linked account
	 */
	protected function column_workshop_attendees_linked_account( $item ) {
		if ( $item['workshop_attendees_linked_account'] ) {
			$account = get_user_by( 'login', $item['workshop_attendees_linked_account'][0] );
			if ( $account ) {
				if ( current_user_can( 'edit_users' ) ) {
					return '<a href="' . admin_url( 'user-edit.php?user_id=' . $account['id'] ) . '">' . esc_html( $item['workshop_attendees_linked_account'][0] ) . '</a>';
				}
				return $item['workshop_attendees_linked_account'][0];
			}
		}
		return '';
	}

    /**
     * Render Workshops attended column
     */
    protected function column_workshop_attendees_workshops( $item ) {
        if ( $item['workshop_attendees_workshops'] ) {
            $workshops = (array) $item['workshop_attendees_workshops'][0];
            $output = '';
            foreach ( $workshops as $workshop ) {
                $output .= '<a>' . $workshop . '</a>, ';
            }
            return $output;
        }
        return '';
    }

    /**
     * Render Membership column
     */
    protected function column_workshop_attendees_membership( $item ) {
        if ( $item['workshop_attendees_membership'] ) {
            return $item['workshop_attendees_membership'][0];
        }
        return '';
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'title';
        $order = 'asc';
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }
        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }
        $result = strcmp( $a[$orderby], $b[$orderby] );
        if($order === 'asc')
        {
            return $result;
        }
        return -$result;
    }

    /**
     * Text displayed when no Attendee data is available
     */
    public function no_items() {
        _e( 'No Attendees found.', 'workshop' );
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return Array
     */
    public function get_bulk_actions() {
        $actions = [
            'bulk-delete' => __( 'Delete', 'workshop' ),
        ];
        return $actions;
    }

    /**
     * Process bulk actions
     */
    public function process_bulk_action() {
        // Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {
            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, 'workshop-attendee-delete' ) ) {
                die( 'Sorry, you are not permitted to do that.' );
            } else {
                // @TODO self::delete_attendee( absint( $_GET['attendee'] ) );
                wp_redirect( esc_url( add_query_arg() ) );
                exit;
            }
        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete'
            || isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' ) ) {
                $delete_ids = esc_sql( $_POST['bulk-delete'] );

                // loop over the array of record IDs and dlete them
                foreach ( $delete_ids as $id ) {
                    // @TODO self::delete_customer( $id );
                }

                wp_redirect( esc_url( add_query_arg() ) );
                exit;
        }
    }

} // END class Workshop_Attendees_WP_List_Table

/**
 * Helper function to get Attendee Featured Image
 * (or author avatar - if a linked account)
 *
 * @param $item Array The Array of meta data for the Attendee
 * @param $size int The size of the image to return
 *
 * @return String The HTML used to display the image (or nothing)
 */
function workshop_attendees_get_avatar( $item, $size = 32 ) {
    // $item passed in is an array of meta data
    if ( has_post_thumbnail( $item[0]['id'] ) ) {
        return get_the_post_thumbnail( $item[0]['id'], array( $size, $size ) );
    } else {
        return '';
    }
}
