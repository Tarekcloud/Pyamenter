<?php

return [
    'services' => '服务',
    'product' => '产品',
    'price' => '价格',
    'status' => '状态',
    'name' => '名称',
    'actions' => '操作',
    'view' => '查看',

    'product_details' => '产品详情',
    'billing_cycle' => '计费周期',
    'cancel' => '取消',
    'cancellation' => ':service 的取消申请',
    'cancel_are_you_sure' => '你确定要取消此服务吗？',
    'cancel_reason' => '取消原因',
    'cancel_type' => '取消方式',
    'cancel_immediate' => '立即取消',
    'cancel_end_of_period' => '在计费周期结束时取消',
    'cancel_immediate_warning' => '点击下方按钮后，该服务将立即被取消，你将无法继续使用。',
    'cancellation_requested' => '已提交取消请求',

    'current_plan' => '当前套餐',
    'new_plan' => '新套餐',
    'change_plan' => '更换套餐',
    'current_price' => '当前价格',
    'new_price' => '新价格',
    'upgrade' => '升级',
    'upgrade_summary' => '升级摘要',
    'total_today' => '今日应付总额',
    'upgrade_service' => '升级服务',
    'upgrade_choose_product' => '选择要升级到的产品',
    'upgrade_choose_config' => '选择升级的配置项',
    'next_step' => '下一步',

    'upgrade_pending' => '当前已有未完成的升级/降级账单，无法继续升级。',

    'outstanding_invoice' => '你有未支付的账单。',
    'view_and_pay' => '点击此处查看并支付',

    'statuses' => [
        'pending' => '待处理',
        'active' => '已激活',
        'cancelled' => '已取消',
        'suspended' => '已暂停',
        'cancellation_pending' => '取消处理中',
    ],
    'billing_cycles' => [
        'day' => '天',
        'week' => '周',
        'month' => '月',
        'year' => '年',
    ],
    'every_period' => '每 :period :unit',
    'price_every_period' => '每 :period :unit :price',
    'price_one_time' => '一次性支付 :price',
    'expires_at' => '到期时间',
    'auto_pay' => '自动支付方式',
    'auto_pay_not_configured' => '未配置',

    'no_services' => '没有找到服务',
    'update_billing_agreement' => '更新支付协议',
    'clear_billing_agreement' => '清除支付协议',
    'select_billing_agreement' => '选择支付协议',

    'remove_payment_method' => '移除支付方式',
    'remove_payment_method_confirm' => '你确定要从此服务中移除支付方式“:name”吗？移除后，此服务将无法自动支付账单。',
];
