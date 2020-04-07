<?php

if (!defined('ABSPATH')) {
    exit;
}

return array(
    'enabled' => array(
        'title' => __('Bật/Tắt', 'woo-vnpay'),
        'type' => 'checkbox',
        'label' => __('Bật phương thức thanh toán', 'woo-vnpay'),
        'default' => 'yes'
    ),
    'title' => array(
        'title' => __('Tiêu đề', 'woo-vnpay'),
        'type' => 'text',
        'description' => __('Tên phương thức thanh toán.', 'woo-vnpay'),
        'default' => __('VNPAY', 'woo-vnpay'),
        'desc_tip' => true,
    ),
    'description' => array(
        'title' => __('Mô tả', 'woo-vnpay'),
        'type' => 'textarea',
        'description' => __('Mô tả phương thức thanh toán.', 'woo-vnpay'),
        'default' => __('Thực hiện thanh toán qua VNPAY.', 'woo-vnpay'),
        'desc_tip' => true,
    ),
    'order_desc' => array(
        'title' => __('Nội dung thanh toán', 'woo-vnpay'),
        'type' => 'text',
        'description' => __('Giúp chủ cửa hàng nhận biết thanh toán cho đơn hàng nào.', 'woo-vnpay'),
        'default' => __('DH{{orderid}}', 'woo-vnpay'),
        'desc_tip' => true,
    ),
    'button_label' => array(
        'title' => __('Nút thanh toán', 'woo-vnpay'),
        'type' => 'text',
        'description' => __('Thay đổi tên nút thanh toán.', 'woo-vnpay'),
        'default' => __('Thanh toán qua VNPAY', 'woo-vnpay'),
        'desc_tip' => true,
    ),
    'payment_notice_successful' => array(
        'title' => __('Thông báo khi thanh toán thành công', 'woo-vnpay'),
        'type' => 'textarea',
        'description' => __('{{orderid}} Mã đơn hàng. {{amount}} Số tiền. {{transaction}} Mã giao dịch tại VNPAY.', 'woo-vnpay'),
        'default' => __('Quý khách đã thanh toán {{amount}} thành công cho đơn hàng {{orderid}}. Mã giao dịch tại VNPAY {{transaction}}. Xin chân thành cảm ơn quý khách!', 'woo-vnpay'),
        'desc_tip' => false,
    ),
    'order_created' => array(
        'title' => __('Trạng thái đơn hàng sau khi đặt hàng', 'woo-vnpay'),
        'type' => 'select',
        'options' => array(
            'pending' => __('Chờ thanh toán', 'woocommerce'),
            'processing' => __('Đang xử lý', 'woocommerce'),
            'on-hold' => __('Tạm giữ', 'woocommerce'),
            'completed' => __('Đã hoàn thành', 'woocommerce'),
            'cancelled' => __('Đã hủy', 'woocommerce'),
            'refunded' => __('Đã hoàn lại tiền', 'woocommerce'),
            'failed' => __('Thất bại', 'woocommerce')
        ),
        'default' => 'pending',
        'desc_tip' => true,
    ),
    'payment_success' => array(
        'title' => __('Trạng thái đơn hàng khi thanh toán thành công', 'woo-vnpay'),
        'type' => 'select',
        'options' => array(
            'pending' => __('Chờ thanh toán', 'woocommerce'),
            'processing' => __('Đang xử lý', 'woocommerce'),
            'on-hold' => __('Tạm giữ', 'woocommerce'),
            'completed' => __('Đã hoàn thành', 'woocommerce'),
            'cancelled' => __('Đã hủy', 'woocommerce'),
            'refunded' => __('Đã hoàn lại tiền', 'woocommerce'),
            'failed' => __('Thất bại', 'woocommerce')
        ),
        'default' => 'processing',
        'desc_tip' => true,
    ),
    'payment_failed' => array(
        'title' => __('Trạng thái đơn hàng khi thanh toán thất bại', 'woo-vnpay'),
        'type' => 'select',
        'options' => array(
            'pending' => __('Chờ thanh toán', 'woocommerce'),
            'processing' => __('Đang xử lý', 'woocommerce'),
            'on-hold' => __('Tạm giữ', 'woocommerce'),
            'completed' => __('Đã hoàn thành', 'woocommerce'),
            'cancelled' => __('Đã hủy', 'woocommerce'),
            'refunded' => __('Đã hoàn lại tiền', 'woocommerce'),
            'failed' => __('Thất bại', 'woocommerce')
        ),
        'default' => 'cancelled',
        'desc_tip' => true,
    ),
    'vnp_Url' => array(
        'title' => __('URL khởi tạo giao dịch', 'woo-vnpay'),
        'type' => 'text',
        'description' => __('URL do VNPAY cung cấp.', 'woo-vnpay'),
        'desc_tip' => false,
    ),
    'vnp_TmnCode' => array(
        'title' => __('Partner code', 'woo-vnpay'),
        'type' => 'text',
        'description' => __('Partner code do VNPAY cung cấp.', 'woo-vnpay'),
        'desc_tip' => false,
    ),
    'vnp_HashSecret' => array(
        'title' => __('Secret key', 'woo-vnpay'),
        'type' => 'password',
        'description' => __('Secret key do VNPAY cung cấp.', 'woo-vnpay'),
        'desc_tip' => false,
    )
);
