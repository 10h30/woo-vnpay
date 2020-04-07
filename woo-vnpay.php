<?php
/**
 * Plugin Name: VNPay for WooCommerce
 * Author: Nhựt FS
 * Author URI: https://nhutfs.net
 * Description: Add VNPay payment gateway for WooCommerce
 * Version: 1.0.2
 * Text Domain: woo-vnpay
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    return;
}

if (!class_exists('WooVNPay')) :
    class WooVNPay
    {
        protected $Option_Page;

        /**
         * WooVNPay constructor.
         */
        function __construct()
        {
            add_action('init', array($this, 'initialize'));
        }

        /**
         * WooVNPay initialize.
         */
        function initialize()
        {
            //defines
            $this->define('WOO_VNPAY_PLUGIN_PATH', plugin_dir_path(__FILE__));
            $this->define('WOO_VNPAY_PLUGIN_BASENAME', plugin_basename(__FILE__));
            $this->define('WOO_VNPAY_PLUGIN_URL', plugin_dir_url(__FILE__));

            //includes
            $this->woo_vnpay_include('includes/class-woo-vnpay-payment-gateway.php');
            $this->woo_vnpay_include('includes/class-woo-vnpay-response.php');
            $this->woo_vnpay_include('includes/class-woo-vnpay-license.php');

            //action
            add_filter('woocommerce_payment_gateways', array($this, 'add_gateway_class'));
            add_filter('plugin_action_links_' . WOO_VNPAY_PLUGIN_BASENAME, array($this, 'add_settings_link'));
            add_action('wp_enqueue_scripts', array($this, 'plugin_assets'), 1);

            new VNPAYResponse();
        }

        /**
         * define
         *
         * @param $name
         * @param bool $value
         */
        function define($name, $value = true)
        {
            if (!defined($name)) {
                define($name, $value);
            }
        }

        /**
         * get path
         *
         * @param string $path
         *
         * @return string
         */
        function get_path($path = '')
        {
            return WOO_VNPAY_PLUGIN_PATH . $path;
        }

        /**
         * include
         *
         * @param $file
         */
        function woo_vnpay_include($file)
        {
            $path = $this->get_path($file);
            if (file_exists($path)) {
                include_once($path);
            }
        }

        /**
         * @param $methods
         * @return array
         */
        public function add_gateway_class($methods)
        {
            $methods[] = 'Woo_VNPay_Payment_Gateway';
            return $methods;
        }


        public function add_settings_link($links)
        {
            $plugin_links = array(
                '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=woo_vnpay') . '">' . __('Thiết lập', 'woo-momo') . '</a>'
            );

            return array_merge($plugin_links, $links);
        }


        public function plugin_assets()
        {
            wp_enqueue_style('woo-vnpay-style', WOO_VNPAY_PLUGIN_URL . 'assets/css/woo-vnpay.css', array(), '1.0.0', 'all');
        }

    }

    new WooVNPay();
endif;
