<?php
class Custom_User_List_Table extends WP_List_Table {public function prepare_items() {
    // Handle the search query
    $search_query = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';

    // Define your data source here with search parameter
    $args = array(
        'search' => '*' . esc_attr($search_query) . '*',
        // Other query parameters as needed
    );
    $data = get_users($args);

    $columns = $this->get_columns();
    $sortable = $this->get_sortable_columns();

    $this->_column_headers = array($columns, array(), $sortable);
    
    $per_page = 20;
    $current_page = $this->get_pagenum();
    $total_items = count($data);

    $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);

    $this->set_pagination_args(array(
        'total_items' => $total_items,
        'per_page' => $per_page,
    ));

    $this->items = $data;
}
function usort_reorder($a, $b, $orderby, $order) {
    $result = 0;

    // Customize the sorting logic for each column
    switch ($orderby) {
        // case 'name':
        //     $result = strcasecmp($a->display_name, $b->display_name);
        //     break;
        case 'username':
            $result = strcasecmp($a->user_login, $b->user_login);
            break;
        case 'email':
            $result = strcasecmp($a->user_email, $b->user_email);
            break;
        case 'role':
            $result = strcasecmp(implode(', ', $a->roles), implode(', ', $b->roles));
            break;
        case 'points':
            // Customize for your points column
            $result = calculate_total_user_points($a->ID) - calculate_total_user_points($b->ID);
            break;
        default:
            // Default case
            break;
    }

    // Apply the order (asc or desc)
    if ($order === 'desc') {
        $result *= -1;
    }

    return $result;
}


    public function column_default($item, $column_name) {
        switch ($column_name) {
            // case 'name':
            //     return $this->column_name($item);
            case 'username':
                $user_name = '<a href="' . get_edit_user_link($item->ID) . '">' . $item->user_login . '</a>';
                return $user_name;
            case 'email':
                return $item->user_email;
            case 'role':
                $user = get_userdata($item->ID);
                return implode(', ', $user->roles);
            case 'points':
                return calculate_total_user_points($item->ID);
            default:
                return 'N/A';
        }
    }

    // public function column_name($item) {
    //     $avatar = get_avatar($item->ID, 32);
    //     $name = $item->display_name;

    //     $name = sprintf('%s %s', $avatar, $name);

    //     return $name;
    // }

    public function column_add_points($item) {
        $admin_point_adjust = get_option('admin_point_adjust', 0);
        if ($admin_point_adjust == 1) {
            $user_id = $item->ID;
    
            ob_start();
            ?>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display: flex; align-items: center;">
                <input type="number" name="points" placeholder="Enter points" required style="margin-right: 10px; width: 100%">
                <input type="text" name="reason" placeholder="Reason" style="margin-right: 10px; width: 100%;">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <input type="hidden" name="action" value="add_points_manually">
                <?php wp_nonce_field('add_points_manually', 'add_points_manually_nonce'); ?>
                <input type="hidden" name="point_source" value="admin_adjustment">
                <button type="submit" class="button">Add Points</button>
            </form>
            <?php
            return ob_get_clean();
        } else {
            return ''; // Return an empty string for non-admin adjustments
        }
    }public function get_columns() {
        $admin_point_adjust = get_option('admin_point_adjust', 0);
        $columns = array(
            //'name' => 'Name',
            'username' => 'Username',
            'email' => 'Email',
            'role' => 'Role',
            'points' => 'Points',
        );
    
        if ($admin_point_adjust == 1) {
            // Add the "Add Points" column when $admin_point_adjust is 1
            $columns['add_points'] = 'Add Points';
        }
    
        return $columns;
    }
    

    public function get_sortable_columns() {
        return array(
           // 'name' => array('name', false),
            'username' => array('username', false),
            'email' => array('email', false),
            'role' => array('role', false),
            'points' => array('points', false),
        );
    }
}
