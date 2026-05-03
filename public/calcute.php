<?php
// 初始化变量，防止报错
$result = null;
$error = null;

// 定义周期对应的天数
$cycles = [
    'monthly' => ['name' => '月付 (30天)', 'days' => 30],
    'quarterly' => ['name' => '季付 (90天)', 'days' => 90],
    'semiannually' => ['name' => '半年付 (180天)', 'days' => 180],
    'annually' => ['name' => '年付 (365天)', 'days' => 365],
    'biennially' => ['name' => '两年付 (730天)', 'days' => 730],
    'triennially' => ['name' => '三年付 (1095天)', 'days' => 1095],
];

// 默认值
$input_date = date('Y-m-d'); // 默认计算日期为今天
$multiplier = 1.0; // 默认倍率
$exchange_rate = 1.0; // 默认汇率
$price = '';
$expire_date = '';

// 处理表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // 获取输入
        $price = floatval($_POST['price']);
        $cycle_key = $_POST['cycle'];
        $expire_date = $_POST['expire_date'];
        $calc_date = $_POST['calc_date'];
        $multiplier = floatval($_POST['multiplier']);
        $exchange_rate = floatval($_POST['exchange_rate']);

        // 验证
        if (empty($price) || empty($expire_date) || empty($calc_date)) {
            throw new Exception("请填写完整所有必填项。");
        }

        // 日期处理
        $d_expire = new DateTime($expire_date);
        $d_calc = new DateTime($calc_date);
        
        // 如果计算日期在到期日期之后
        if ($d_calc >= $d_expire) {
            throw new Exception("服务已过期或计算日期晚于到期时间，剩余价值为 0。");
        }

        // 计算剩余天数
        $interval = $d_calc->diff($d_expire);
        $remaining_days = $interval->days; // 绝对天数差
        
        // 获取周期总天数
        $total_days = $cycles[$cycle_key]['days'];

        // 核心计算逻辑
        // 1. 每日价值 = 原价 / 周期天数
        $daily_value = $price / $total_days;
        
        // 2. 基础剩余价值 = 每日价值 * 剩余天数
        $base_remaining_value = $daily_value * $remaining_days;

        // 3. 溢价后价值 = 基础剩余价值 * 倍率
        $multiplied_value = $base_remaining_value * $multiplier;

        // 4. 换算货币 = 溢价后价值 * 汇率
        $final_rmb_value = $multiplied_value * $exchange_rate;

        // 格式化输出数据
        $result = [
            'remaining_days' => $remaining_days,
            'base_value' => number_format($base_remaining_value, 2),
            'multiplier' => $multiplier,
            'multiplied_value' => number_format($multiplied_value, 2),
            'final_rmb' => number_format($final_rmb_value, 2)
        ];

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VPS 剩余价值计算器</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding-top: 40px; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; }
        .header-title { color: #0d6efd; font-weight: bold; }
        .result-box { background-color: #e7f1ff; border-left: 5px solid #0d6efd; padding: 20px; margin-top: 20px; border-radius: 4px; }
        .highlight { font-weight: bold; color: #d63384; font-size: 1.2em; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card p-4">
                <h3 class="text-center mb-4 header-title">VPS 剩余价值计算器</h3>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">续费价格 (原币种)</label>
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="例如: 10.00" value="<?php echo htmlspecialchars($price); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">付款周期</label>
                            <select name="cycle" class="form-select">
                                <?php foreach ($cycles as $key => $val): ?>
                                    <option value="<?php echo $key; ?>" <?php if(isset($_POST['cycle']) && $_POST['cycle'] == $key) echo 'selected'; ?>>
                                        <?php echo $val['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">服务到期时间</label>
                        <input type="date" name="expire_date" class="form-control" value="<?php echo htmlspecialchars($expire_date); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">计算基准日期 (默认为今天)</label>
                        <input type="date" name="calc_date" class="form-control" value="<?php echo isset($_POST['calc_date']) ? htmlspecialchars($_POST['calc_date']) : date('Y-m-d'); ?>" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">剩余价值倍率 (系数)</label>
                            <input type="number" step="0.1" name="multiplier" class="form-control" placeholder="1.0" value="<?php echo $multiplier; ?>">
                            <div class="form-text">溢价填 >1 (如1.2)，折价填 <1 (如0.8)</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">货币汇率 (转人民币)</label>
                            <input type="number" step="0.01" name="exchange_rate" class="form-control" placeholder="1.0" value="<?php echo $exchange_rate; ?>">
                            <div class="form-text">如原价是RMB填1，USD填7.25</div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">开始计算</button>
                    </div>
                </form>

                <?php if ($result): ?>
                <div class="result-box">
                    <h5>📊 计算结果：</h5>
                    <p class="mb-1">剩余天数：<strong><?php echo $result['remaining_days']; ?> 天</strong></p>
                    <hr>
                    <p>您的基础剩余价值是 <strong><?php echo $result['base_value']; ?></strong>，</p>
                    <p>乘系数 <strong><?php echo $result['multiplier']; ?></strong> 后，剩余价值为 <strong><?php echo $result['multiplied_value']; ?></strong>。</p>
                    <p class="mt-2">💰 折算为人民币为 <span class="highlight">¥ <?php echo $result['final_rmb']; ?></span> 元。</p>
                    
                    <div class="mt-3">
                        <label class="form-label text-muted small">复制文案：</label>
                        <textarea class="form-control form-control-sm" rows="3" readonly>您的产品，剩余价值 <?php echo $result['base_value']; ?> GBP，倍率 <?php echo $result['multiplier']; ?>，总剩余价值 ¥<?php echo $result['final_rmb']; ?> (剩余 <?php echo $result['remaining_days']; ?> 天)</textarea>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <div class="text-center mt-3 text-muted small">
                &copy; VPS Value Calculator
            </div>
        </div>
    </div>
</div>

</body>
</html>