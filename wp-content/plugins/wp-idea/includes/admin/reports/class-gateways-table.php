<?php

namespace bpmj\wpidea\admin\reports;

use bpmj\wpidea\admin\reports\Reports_View;
use bpmj\wpidea\View;

class Gateways_Table extends \WP_List_Table
{
    protected $reports_views;

    protected $active_view;

    public function __construct()
    {
        global $status, $page;

        // Set parent defaults
        parent::__construct( array(
            'singular' => edd_get_label_singular(),
            'plural'   => edd_get_label_plural(),
            'ajax'     => false,
        ) );

        $reports_view = new Reports_View();
        $this->reports_views = $reports_view->get_reports_views();
        $this->active_view = $reports_view->get_active_view();
    }

    public function bulk_actions( $which = '' )
    {
        echo View::get_admin('/reports/reports-filter', [
            'reports_views' => $this->reports_views,
            'active_view' => $this->active_view,
        ]);
    }

    /**
     * @var int Number of items per page
     * @since 1.5
     */
    public $per_page = 999;


    /**
     * Gets the name of the primary column.
     *
     * @since 2.5
     * @access protected
     *
     * @return string Name of the primary column.
     */
    protected function get_primary_column_name() {
        return 'label';
    }

    /**
     * This function renders most of the columns in the list table.
     *
     * @access public
     * @since 1.5
     *
     * @param array $item Contains all the data of the downloads
     * @param string $column_name The name of the column
     *
     * @return string Column Name
     */
    public function column_default( $item, $column_name ) {
        return $item[ $column_name ];
    }

    /**
     * Retrieve the table columns
     *
     * @access public
     * @since 1.5
     * @return array $columns Array of all the list table columns
     */
    public function get_columns() {
        $columns = array(
            'label'          => __( 'Gateway', 'easy-digital-downloads' ),
            'complete_sales' => __( 'Complete Sales', 'easy-digital-downloads' ),
            'pending_sales'  => __( 'Pending / Failed Sales', 'easy-digital-downloads' ),
            'total_sales'    => __( 'Total Sales', 'easy-digital-downloads' ),
        );

        return $columns;
    }


    /**
     * Retrieve the current page number
     *
     * @access public
     * @since 1.5
     * @return int Current page number
     */
    public function get_paged() {
        return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
    }


    /**
     * Build all the reports data
     *
     * @access public
     * @since 1.5
     * @return array $reports_data All the data for customer reports
     */
    public function reports_data() {

        $reports_data = array();
        $gateways     = edd_get_payment_gateways();

        foreach ( $gateways as $gateway_id => $gateway ) {

            $complete_count = edd_count_sales_by_gateway( $gateway_id, 'publish' );
            $pending_count  = edd_count_sales_by_gateway( $gateway_id, array( 'pending', 'failed' ) );

            $reports_data[] = array(
                'ID'             => $gateway_id,
                'label'          => $gateway['admin_label'],
                'complete_sales' => edd_format_amount( $complete_count, false ),
                'pending_sales'  => edd_format_amount( $pending_count, false ),
                'total_sales'    => edd_format_amount( $complete_count + $pending_count, false ),
            );
        }

        return $reports_data;
    }


    /**
     * Setup the final data for the table
     *
     * @access public
     * @since 1.5
     * @uses EDD_Gateawy_Reports_Table::get_columns()
     * @uses EDD_Gateawy_Reports_Table::get_sortable_columns()
     * @uses EDD_Gateawy_Reports_Table::reports_data()
     * @return void
     */
    public function prepare_items() {
        $columns               = $this->get_columns();
        $hidden                = array(); // No hidden columns
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->items           = $this->reports_data();

    }
}
