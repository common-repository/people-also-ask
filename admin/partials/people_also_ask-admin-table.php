<?php

/**
 * WP List Table Example class
 *
 * @package   WPListTableExample
 * @author    Matt van Andel
 * @copyright 2016 Matthew van Andel
 * @license   GPL-2.0+
 */

/**
 * Example List Table Child Class
 *
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 *
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 *
 *
 * @package WPListTableExample
 * @author  Matt van Andel
 */
class PeopleAlso_Keywords_Admin_Table extends WP_List_Table
{
    /**
     * PeopleAlso_Keywords_Admin_Table constructor.
     *
     * REQUIRED. Set up a constructor that references the parent constructor. We
     * use the parent reference to set some default configs.
     */
    public function __construct()
    {
        // Set parent defaults.
        parent::__construct([
            'singular' => 'palavra',    // Singular name of the listed records.
            'plural'   => 'palavras',   // Plural name of the listed records.
            'ajax'     => false,       // Does this table support ajax?
        ]);
    }

    /**
     * Get a list of columns. The format is:
     * 'internal-name' => 'palavra'
     *
     * REQUIRED! This method dictates the table's columns and codes. This should
     * return an array where the key is the column slug (and class) and the value
     * is the column's code text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     *
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a `column_cb()` method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     *
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information.
     */
    public function get_columns()
    {
        $columns = [
            'cb'              => '<input type="checkbox" />',   // Render a checkbox instead of text.
            'palavra'         => esc_html__('Keyword', 'people-also-ask'),
            'status'          => esc_html__('Status', 'people-also-ask'),
            'post_status'     => esc_html__('Post Status', 'people-also-ask'),            
            'itens'           => esc_html__('Items', 'people-also-ask'),
            'acao'            => esc_html__('Actions', 'people-also-ask')
        ];

        return $columns;
    }

    /**
     * Get a list of sortable columns. The format is:
     * 'internal-name' => 'orderby'
     * or
     * 'internal-name' => ['orderby', true]
     *
     * The second format will make the initial sorting order be descending
     *
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
     * you will need to register it here. This should return an array where the
     * key is the column that needs to be sortable, and the value is db column to
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     *
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within `prepare_items()` and sort
     * your data accordingly (usually by modifying your query).
     *
     * @return array An associative array containing all the columns that should be sortable.
     */
    protected function get_sortable_columns()
    {
        $sortable_columns = [
            'palavra'       => ['palavra', false],
            'status'        => ['status', false],
            'post_status'   => ['post_status', false],
            'itens'         => ['itens', false]
        ];

        return $sortable_columns;
    }

    /**
     * Get default column value.
     *
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'palavra', it would first see if a method named $this->column_palavra()
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as
     * possible.
     *
     * Since we have defined a column_palavra() method later on, this method doesn't
     * need to concern itself with any column with a name of 'palavra'. Instead, it
     * needs to handle everything else.
     *
     * For more detailed insight into how columns are handled, take a look at
     * WP_List_Table::single_row_columns()
     *
     * @param object $item        A singular item (one full row's worth of data).
     * @param string $column_name The name/slug of the column to be processed.
     * @return string Text or HTML to be placed inside the column <td>.
     */
    protected function column_default($item, $column_name)
    {
        

        switch ($column_name) {
            case 'palavra':
            case 'status':
            case 'post_status':
            case 'itens':
                return $item[$column_name];
            case 'acao':
                return  ($item['status'] != __("Finished", 'people-also-ask')) ? '' : '<a href="../?p=' . $item['wp_post_id'] . '&preview=true" target="_blank" class="btn button-primary">' . __('View', 'people-also-ask') . '</a>' . ' ' . '<a href="post.php?post=' . $item['wp_post_id'] . '&action=edit" target="_blank" class="btn button-primary">' . __('Edit Post', 'people-also-ask') .  '</a>';
            default:
                return print_r($item, true); // Show the whole array for troubleshooting purposes.
        }
    }

    /**
     * Get value for checkbox column.
     *
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @param object $item A singular item (one full row's worth of data).
     * @return string Text to be placed inside the column <td>.
     */
    protected function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],  // Let's simply repurpose the table's singular
            $item['ID']                // The value of the checkbox should be the record's ID.
        );
    }

    /**
     * Get code column value.
     *
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'palavra'. Every time the class
     * needs to render a column, it first looks for a method named
     * column_{$column_palavra} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     *
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links are
     * secured with wp_nonce_url(), as an expected security measure.
     *
     * @param object $item A singular item (one full row's worth of data).
     * @return string Text to be placed inside the column <td>.
     */
    protected function column_palavra($item)
    {
        

        $page = isset($_REQUEST['page']) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : '';

        $actions['view_post'] = ($item['status'] != __("Finished", 'people-also-ask')) ? '' : '<a href="../?p=' . $item['wp_post_id'] . '&preview=true" target="_blank">' . __('View', 'people-also-ask') . '</a>';
        $actions['edit_post'] = ($item['status'] != __("Finished", 'people-also-ask')) ? '' : '<a href="post.php?post=' . $item['wp_post_id'] . '&action=edit" target="_blank">' . __('Edit Post', 'people-also-ask') .  '</a>';
        
        // Build delete row action.
        $delete_query_args = [
            'page'      => $page,
            'action'    => 'delete',
            $this->_args['singular'] => $item['ID']
        ];

        $actions['delete'] = sprintf(
            '<a href="%1$s">%2$s</a>',
            esc_url(wp_nonce_url(add_query_arg($delete_query_args, 'admin.php'), 'deletekeyword_' . $item['ID'])),
            esc_html__('Delete', 'people-also-ask')
        );


        
        
        // Return the code contents.
        return sprintf(
            '<a href="post.php?post=' . $item['wp_post_id'] . '&action=edit" target="_blank" target="_blank">%2$s</a>%3$s',
            $item['ID'],
            $item['palavra'],
            $this->row_actions($actions)
        );
    }

    protected function column_status($item) {
        
        if ($item['status'] == __("Finished", 'people-also-ask') || $item['status'] == 'Finalizado')
            return '<span class="col-finalizado" data-status="'. __("Finished", 'people-also-ask') .'">' . __('Finished', 'people-also-ask') . '</span>';            
        else if ($item['status'] == __('Pending', 'people-also-ask') || $item['status'] == 'Pendente')
            return '<span class="col-pendente" data-status="Pendente">' . __('Pending', 'people-also-ask') . '</span>';
        else if ($item['status'] == __('No content', 'people-also-ask') || $item['status'] == 'Sem conteúdo')
            return '<span class="col-pendente" data-status="' . __('No content', 'people-also-ask') .'">' . __('No content', 'people-also-ask') . '</span>';
        else
            return '<span class="col-processando" data-status="' . $item['status'] . '">' . $item['status'] . '</span>';

    }

    protected function column_post_status($item) {
        
        if ($item['post_status'] == 'draft')
            return '<span class="col-rascunho">' . __('Draft', 'people-also-ask') . '</span>';
        else if ($item['post_status'] == 'publish')
            return '<span class="col-finalizado">' . __('Published', 'people-also-ask') . '</span>';
        else
            return '-';
    }

    /**
     * Get an associative [option_name => option_code] with the list
     * of bulk actions available on this table.
     *
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible code'
     *
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     *
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     *
     * @return array An associative array containing all the bulk actions.
     */
    protected function get_bulk_actions()
    {
        $actions = [
            // 'view_post' => esc_html__('View', 'people-also-ask'),
            // 'edit_post' => esc_html__('Edit Post', 'people-also-ask'),
            'delete' => esc_html__('Delete', 'people-also-ask')            
        ];

        return $actions;
    }

    /**
     * Handle bulk actions.
     *
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     */
    protected function process_bulk_action()
    {
        global $wpdb;

        if (isset($_REQUEST[$this->_args['singular']])) {

            $idItem = isset($_REQUEST['palavra']) ? sanitize_text_field( wp_unslash( $_REQUEST['palavra'] ) ) : '';

            if (!isset($_REQUEST['_wpnonce']) || $_REQUEST['_wpnonce'] == '') {
                die('Falha na verificação de segurança! (1)');
            } else {
                if (isset($_REQUEST['_wpnonce']) && $_REQUEST['_wpnonce'] != '') {
                    if (
                        !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])), 'bulk-' . $this->_args['plural'])
                        &&
                        !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])), 'deletekeyword_' . $idItem)
                    ) {
                        die('Falha na verificação de segurança! (2)');
                    }
                }
            }




            $keywords_params = array_map('sanitize_text_field', (array) (isset($_REQUEST[$this->_args['singular']]) ? wp_unslash($_REQUEST[$this->_args['singular']]) : ''));
            
            
            $ids = is_array($keywords_params) ? $keywords_params : array($keywords_params);
            $placeholders = implode(',', array_fill(0, count($ids), '%d'));

            switch ($this->current_action()) {       
                case 'delete':

                    // Deletar da tabela people_also_askeds_other_people_searched
                    $wpdb->query($wpdb->prepare("
                        DELETE FROM {$wpdb->prefix}people_also_askeds_other_people_searched 
                        WHERE asked_id IN (SELECT ID FROM {$wpdb->prefix}people_also_askeds WHERE ID IN ($placeholders))
                    ", ...$ids));
            
                    // Deletar da tabela people_also_askeds_related
                    $wpdb->query($wpdb->prepare("
                        DELETE FROM {$wpdb->prefix}people_also_askeds_related 
                        WHERE asked_id IN (SELECT ID FROM {$wpdb->prefix}people_also_askeds WHERE ID IN ($placeholders))
                    ", ...$ids));
            
                    // Deletar da tabela people_also_askeds
                    $wpdb->query($wpdb->prepare("
                        DELETE FROM {$wpdb->prefix}people_also_askeds 
                        WHERE ID IN ($placeholders)
                    ", ...$ids));
            
                    break;
                default:
                    break;
            }


            // $ids = is_array($keywords_params)
            //     ? implode(',', array_map('intval', $keywords_params))
            //     : array(intval($keywords_params));

            // switch ($this->current_action()) {       
            //     case 'delete':
                

            //         break;
            //     default:
            //         break;
            // }
        }
    }

    /**
     * Prepares the list of items for displaying.
     *
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here.
     *
     * @global wpdb $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     */
    public function prepare_items()
    {
        global $wpdb;

        /**
         * REQUIRED for pagination.
         */
        $per_page = 200;
        $current_page = $this->get_pagenum();
        $offset_page = ($current_page - 1) * $per_page;

        /**
         * Total active keywords.
         */
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}people_also_askeds ");

        /*
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & codes), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = $this->get_sortable_columns();

        /*
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * three other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = [$columns, $hidden, $sortable];

        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();







        // If no sort, default to ID.
        // $orderby = isset($_REQUEST['orderby']) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : '';

        // if ($orderby == '')
        //     $orderby = 'ID';

        // if (!in_array($orderby, array_keys($sortable), true)) {
        //     $orderby = 'ID';
        // }
        
        // $order = isset($_REQUEST['order']) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : '';
        // $orderby_sql =  sanitize_sql_orderby("{$orderby} {$order}");

        // if ($order != 'ASC') {
        //     $order = 'DESC';
        // }
        
    
            
        // /*
        //  * GET THE DATA!
        //  */
        // $data = $wpdb->get_results($wpdb->prepare(
        //     "SELECT
        //         c.ID, c.palavra, c.status, c.wp_post_id, c.itens, p.post_status
        //     FROM {$wpdb->prefix}people_also_askeds AS c
        //     LEFT JOIN {$wpdb->prefix}posts AS p ON p.ID = c.wp_post_id AND c.wp_post_id > 0
        //     ORDER BY {$orderby_sql}
        //     LIMIT %1\$d OFFSET %2\$d",
        //     $per_page,
        //     $offset_page,
        // ), ARRAY_A);









        // If no sort, default to ID.
        $orderby = isset($_REQUEST['orderby']) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : '';

        if ($orderby == '') {
            $orderby = 'ID';
        }

        // Verifica se o valor de $orderby está entre os campos ordenáveis permitidos
        if (!in_array($orderby, array_keys($sortable), true)) {
            $orderby = 'ID';
        }

        $order = isset($_REQUEST['order']) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : '';

        // Garante que apenas 'ASC' ou 'DESC' sejam usados
        $order = strtoupper($order);
        if (!in_array($order, ['ASC', 'DESC'], true)) {
            $order = 'DESC';
        }

        // Agora, $orderby e $order são considerados seguros após a validação
        $orderby_sql = "{$orderby} {$order}";

        /*
        * GET THE DATA!
        */
        $query = "
            SELECT
                c.ID, c.palavra, c.status, c.wp_post_id, c.itens, p.post_status
            FROM {$wpdb->prefix}people_also_askeds AS c
            LEFT JOIN {$wpdb->prefix}posts AS p ON p.ID = c.wp_post_id AND c.wp_post_id > 0
            ORDER BY {$orderby_sql}
            LIMIT %d OFFSET %d
        ";

        // Executa a consulta preparada com placeholders
        $data = $wpdb->get_results($wpdb->prepare($query, $per_page, $offset_page), ARRAY_A);






        //echo '$per_page' . $per_page;
        /*
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;

        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args([
            'total_items' => $total_items,                     // WE have to calculate the total number of items.
            'per_page'    => $per_page,                        // WE have to determine how many items to show on a page.
            'total_pages' => ceil($total_items / $per_page),   // WE have to calculate the total number of pages.
        ]);

        
    }
}
