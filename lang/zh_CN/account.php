<?php

return [
    'account' => '账户',
    'personal_details' => '个人资料',
    'security' => '安全',
    'credits' => '余额',

    'change_password' => '修改密码',

    'two_factor_authentication' => '双重验证',
    'two_factor_authentication_description' => '启用双重验证可为你的账户增加一层额外的安全保护。',
    'two_factor_authentication_enabled' => '你的账户已启用双重验证。',
    'two_factor_authentication_enable' => '启用双重验证',
    'two_factor_authentication_disable' => '关闭双重验证',
    'two_factor_authentication_disable_description' => '你确定要关闭双重验证吗？这将移除账户的额外安全保护。',
    'two_factor_authentication_enable_description' => '要启用双重验证，你需要使用 Google Authenticator 或 Authy 等验证器应用扫描下方的二维码。',
    'two_factor_authentication_qr_code' => '使用你的验证器应用扫描以下二维码：',
    'two_factor_authentication_secret' => '或手动输入以下代码：',

    'sessions' => '会话',
    'sessions_description' => '管理并登出你在其他浏览器或设备上的活跃会话。',
    'logout_sessions' => '退出此会话',

    'input' => [
        'current_password' => '当前密码',
        'current_password_placeholder' => '你的当前密码',
        'new_password' => '新密码',
        'new_password_placeholder' => '你的新密码',
        'confirm_password' => '确认密码',
        'confirm_password_placeholder' => '确认你的新密码',

        'two_factor_code' => '输入验证器应用中的验证码',
        'two_factor_code_placeholder' => '你的双重验证验证码',

        'currency' => '货币',
        'amount' => '金额',
        'payment_gateway' => '支付网关',
    ],

    'notifications' => [
        'password_changed' => '密码已更改。',
        'password_incorrect' => '当前密码不正确。',
        'two_factor_enabled' => '双重验证已启用。',
        'two_factor_disabled' => '双重验证已关闭。',
        'two_factor_code_incorrect' => '验证码错误。',
        'session_logged_out' => '会话已退出。',
    ],

    'no_credit' => '你没有可用余额。',
    'add_credit' => '充值',
    'credit_deposit' => '余额充值（:currency）',

    'payment_methods' => '支付方式',
    'recent_transactions' => '最近交易',
    'saved_payment_methods' => '已保存的支付方式',
    'setup_payment_method' => '设置新的支付方式',
    'no_saved_payment_methods' => '你没有保存任何支付方式。',
    'saved_payment_methods_description' => '管理你已保存的支付方式，以便更快速结账和自动付款。',
    'no_saved_payment_methods_description' => '你可以添加支付方式，让未来付款更快更方便，并为你的服务启用自动扣款。',
    'add_payment_method' => '添加支付方式',
    'payment_method_statuses' => [
        'active' => '启用',
        'inactive' => '停用',
        'expired' => '已过期',
        'pending' => '待处理',
    ],
    'payment_method_added' => '支付方式已添加。',
    'payment_method_add_failed' => '添加支付方式失败，请重试。',
    'services_linked' => '已关联 :count 个服务',
    'remove' => '移除',
    'remove_payment_method' => '移除支付方式',
    'remove_payment_method_confirm' => '你确定要移除 :name 吗？此操作无法撤销。',
    'expires' => '于 :date 到期',
    'cancel' => '取消',
    'confirm' => '是的，移除',
    'email_notifications' => '邮件通知',
    'in_app_notifications' => '应用内通知',
    'notifications_description' => '管理你的通知偏好。你可以选择通过邮件、应用内推送，或两者同时接收通知。',
    'notification' => '通知',

    'push_notifications' => '推送通知',
    'push_notifications_description' => '启用推送通知，即使你不在网站上，也能在浏览器中接收实时更新。',
    'enable_push_notifications' => '启用推送通知',
    'push_status' => [
        'not_supported' => '你的浏览器不支持推送通知。',
        'denied' => '推送通知已被阻止，请在浏览器设置中启用。',
        'subscribed' => '推送通知已启用。',
    ],
];
