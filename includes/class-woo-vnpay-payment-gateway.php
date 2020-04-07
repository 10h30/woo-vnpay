<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Woo_VNPay_Payment_Gateway')) :
    class Woo_VNPay_Payment_Gateway extends WC_Payment_Gateway
    {
        /**
         * Woo_VNPay_Payment_Gateway constructor.
         */
        public function __construct()
        {
            $this->id = 'woo_vnpay';
            $this->has_fields = false;
            $this->method_title = __('VNPAY', 'woo-vnpay');
            $this->method_description = __('Thanh toán qua VNPAY.', 'woo-vnpay');

            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->instructions = "";

            // Actions
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_filter('woocommerce_order_button_text', array($this, 'order_button_name'));
            add_action('woocommerce_thankyou_' . $this->id, array($this, 'vnpay_payment_results'));
        }

        public function init_form_fields()
        {
            $this->form_fields = include('vnpay/vnpay-settings.php');
        }

        /**
         * @param int $order_id
         * @return array
         */
        public function process_payment($order_id)
        {
            $order = wc_get_order($order_id);
            $order->update_status($this->get_option('order_created'));

            return array(
                'result' => 'success',
                'redirect' => $this->get_pay_url($order)
            );
        }

        /**
         * @param $order
         *
         * @return string
         */
        public function get_pay_url($order)
        {
            $vnp_TmnCode = $this->get_option('vnp_TmnCode');
            $vnp_HashSecret = $this->get_option('vnp_HashSecret');
            $vnp_Url = $this->get_option('vnp_Url');
            $vnp_Returnurl = admin_url('admin-ajax.php?action=vnpay_response&type=international');
            $array = array(
                '{{orderid}}' => $order->get_id()
            );
            $order_desc = strtr($this->get_option('order_desc'), $array);
            $vnp_TxnRef = $order->get_id();
            $vnp_OrderInfo = $order_desc;
            $vnp_OrderType = 'other';
            $vnp_Amount = number_format($order->get_total(), 2, '.', '') * 100;
            $vnp_Locale = 'vn';
            $vnp_BankCode = '';
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
            $inputData = array(
                "vnp_Version" => "2.0.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
            );
            if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                $inputData['vnp_BankCode'] = $vnp_BankCode;
            }
            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . $key . "=" . $value;
                } else {
                    $hashdata .= $key . "=" . $value;
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash = hash('sha256', $vnp_HashSecret . $hashdata);
                $vnp_Url .= 'vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;
            }

            return $vnp_Url;
        }

        public function vnpay_payment_results($order_id)
        {
            $hashSecret = $this->get_option('vnp_HashSecret');
            $inputData = array();
            $data = $_REQUEST;
            foreach ($data as $key => $value) {
                if (substr($key, 0, 4) == "vnp_") {
                    $inputData[$key] = $value;
                }
            }
            $vnp_SecureHash = $inputData['vnp_SecureHash'];
            unset($inputData['vnp_SecureHashType']);
            unset($inputData['vnp_SecureHash']);
            ksort($inputData);
            $i = 0;
            $hashData = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashData = $hashData . '&' . $key . "=" . $value;
                } else {
                    $hashData = $hashData . $key . "=" . $value;
                    $i = 1;
                }
            }

            $secureHash = hash('sha256', $hashSecret . $hashData);
            if ($secureHash == $vnp_SecureHash) {
                if ($_GET['vnp_ResponseCode'] == '00') {
                    $amount = $inputData['vnp_Amount'] / 100;
                    $array = array(
                        '{{orderid}}' => $order_id,
                        '{{amount}}' => number_format($amount, '0', '.', '.') . 'đ',
                        '{{transaction}}' => $inputData['vnp_TransactionNo']
                    );
                    $payment_notice_successful = strtr($this->get_option('payment_notice_successful'), $array);
                    ?>
                    <section class="vnpay-payment-gateway-wrapper">
                        <div class="vnpay-payment-gateway-notification">
                            <img src="<?= WOO_VNPAY_PLUGIN_URL . 'assets/images/correct.svg'; ?>"
                                 alt="Giao dịch thành công">
                            <h2 class="notification-title">Thanh toán thành công</h2>
                            <p class="notification-content"><?php echo wpautop($payment_notice_successful); ?></p>
                        </div>
                    </section>
                    <?php
                } else {
                    ?>
                    <section class="vnpay-payment-gateway-wrapper">
                        <div class="vnpay-payment-gateway-notification">
                            <img src="<?= WOO_VNPAY_PLUGIN_URL . 'assets/images/caution.svg'; ?>"
                                 alt="Thanh toán không thành công">
                            <h2 class="notification-title">Thanh toán không thành công</h2>
                            <p class="notification-content">Đã có lỗi trong quá trình thanh toán, vui lòng thử
                                lại!</p>
                        </div>
                    </section>
                    <?php
                }
            } else {
                ?>
                <section class="vnpay-payment-gateway-wrapper">
                    <div class="vnpay-payment-gateway-notification">
                        <img src="<?= WOO_VNPAY_PLUGIN_URL . 'assets/images/caution.svg'; ?>"
                             alt="Cảnh báo">
                        <h2 class="notification-title">Cảnh báo</h2>
                        <p class="notification-content">Chữ ký không hợp lệ</p>
                    </div>
                </section>
                <?php
            }

        }

        /**
         * @param $order_button_name
         * @return string
         */
        public function order_button_name($order_button_name)
        {
            $chosen_payment_method = WC()->session->get('chosen_payment_method');
            if ($chosen_payment_method == 'woo_vnpay') {
                $order_button_name = $this->get_option('button_label');;
            } ?>
            <script type="text/javascript">
                (function ($) {
                    $('form.checkout').on('change', 'input[name^="payment_method"]', function () {
                        var t = {
                            updateTimer: !1, dirtyInput: !1,
                            reset_update_checkout_timer: function () {
                                clearTimeout(t.updateTimer)
                            }, trigger_update_checkout: function () {
                                t.reset_update_checkout_timer(), t.dirtyInput = !1,
                                    $(document.body).trigger("update_checkout")
                            }
                        };
                        t.trigger_update_checkout();
                    });
                })(jQuery);
            </script><?php
            return $order_button_name;
        }

        /**
         * @return bool
         */
        public function isValidCurrency()
        {
            return in_array(get_woocommerce_currency(), array('VND'));
        }

        public function admin_options()
        {
            if ($this->isValidCurrency()) {
                parent::admin_options();
            } else {
                ?>
                <div class="inline error">
                    <p>
                        <strong><?php _e('Phương thức thanh toán không khả dụng', 'woo-vnpay'); ?></strong>:
                        <?php _e('VNPAY không hỗ trợ đơn vị tiền tệ của bạn. Hiện tại, VNPAY chỉ hỗ trợ đơn vị tiền tệ Việt Nam Đồng (VND).', 'woo-vnpay'); ?>
                    </p>
                </div>
                <?php
            }
        }
    }
endif;