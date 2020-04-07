<?php

if (!class_exists('Woo_VNPAY_Option_Page')) :
    class Woo_VNPAY_Option_Page
    {
        private $Woo_VNPAY_Options;

        public function __construct()
        {
            add_action('admin_menu', array($this, 'register_options_page'));
            register_activation_hook(__FILE__, array($this, 'option_page_data'));
            $this->Woo_VNPAY_Options = get_option('woo_vnpay');
        }

        function register_options_page()
        {
            add_options_page('MoMo for WooCommerce', 'Woo VNPay', 'manage_options', 'vnpay-for-woo-option', array(
                $this,
                'option_page'
            ));
        }

        function option_page()
        {
            require_once('admin/option-page.php');
        }

        function option_page_data()
        {
            $Woo_VNPAY_Options = array();
            $Woo_VNPAY_Options["license_key"] = "";
            add_option('woo_vnpay', $Woo_VNPAY_Options);
        }

    }

endif;
