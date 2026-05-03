<?php
session_start();

// ================= 配置区域 =================
$ACCESS_PASSWORD = 'Qwe,.321';  // 管理密码
$dataFile = 'data.json';     // 数据文件
date_default_timezone_set('Asia/Shanghai');
// ===========================================

// --- 登录逻辑 ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_pass'])) {
    if ($_POST['login_pass'] === $ACCESS_PASSWORD) {
        $_SESSION['is_logged_in'] = true;
        header("Location: index.php");
        exit;
    } else {
        $login_error = "密码错误";
    }
}

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - NOC Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body{background:#f4f6f9;height:100vh;display:flex;align-items:center;justify-content:center;}</style>
</head>
<body>
    <div class="card p-4 shadow" style="width:350px;">
        <h4 class="mb-3 text-center">🔒 NOC 管理后台</h4>
        <?php if(isset($login_error)) echo "<div class='alert alert-danger p-2'>$login_error</div>"; ?>
        <form method="POST">
            <input type="password" name="login_pass" class="form-control mb-3" placeholder="输入密码..." required autofocus>
            <button type="submit" class="btn btn-primary w-100">登录</button>
        </form>
    </div>
</body>
</html>
<?php exit; }

// --- 核心配置：类型映射 ---
$typeConfig = [
    'feature' => ['cat' => 'feature', 'label' => '✨ New Feature', 'color' => '#6f42c1', 'bg' => '#f3e5f5', 'border' => '#e1bee7', 'text' => '#4a148c'],
    'promotion' => ['cat' => 'promotion', 'label' => '🎁 Promotion', 'color' => '#e83e8c', 'bg' => '#fce4ec', 'border' => '#f8bbd0', 'text' => '#880e4f'],
    'maintenance' => ['cat' => 'general', 'label' => '🔧 Maintenance', 'color' => '#0dcaf0', 'bg' => '#e0f7fa', 'border' => '#b2ebf2', 'text' => '#006064'],
    'outage' => ['cat' => 'general', 'label' => '🔴 Outage', 'color' => '#dc3545', 'bg' => '#f8d7da', 'border' => '#f5c6cb', 'text' => '#721c24'],
    'recovery' => ['cat' => 'general', 'label' => '✅ Recovery', 'color' => '#198754', 'bg' => '#d1e7dd', 'border' => '#badbcc', 'text' => '#0f5132'],
    'compensation' => ['cat' => 'general', 'label' => '💰 Compensation', 'color' => '#fd7e14', 'bg' => '#fff3cd', 'border' => '#ffecb5', 'text' => '#664d03'],
    'change' => ['cat' => 'general', 'label' => '📝 Services Change', 'color' => '#6c757d', 'bg' => '#f8f9fa', 'border' => '#dee2e6', 'text' => '#343a40'],
    // 新增：一般性通知公告类型 (使用醒目的蓝色主题)
    'notice' => ['cat' => 'general', 'label' => '📢 Notice', 'color' => '#0d6efd', 'bg' => '#cfe2ff', 'border' => '#b6d4fe', 'text' => '#084298']
];

// 读取数据
$announcements = [];
if (file_exists($dataFile)) {
    $content = file_get_contents($dataFile);
    if ($content) {
        $decoded = json_decode($content, true);
        if (is_array($decoded)) {
            // 修复：过滤掉非数组的无效条目（防止 null 导致报错）
            $announcements = array_filter($decoded, 'is_array');
        }
    }
}

// 处理提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    $dateTime = str_replace('T', ' ', $_POST['datetime']);
    if(strlen($dateTime) == 16) $dateTime .= ':00'; 

    $newData = [
        'id' => $_POST['id'] ?: uniqid(),
        'datetime' => $dateTime,
        'type' => $_POST['type'], 
        'title_en' => $_POST['title_en'],
        'title_cn' => $_POST['title_cn'],
        'content_en' => $_POST['content_en'], 
        'content_cn' => $_POST['content_cn'],
    ];

    if ($_POST['action'] === 'update') {
        foreach ($announcements as &$item) {
            if (isset($item['id']) && $item['id'] === $_POST['id']) {
                $item = $newData;
                break;
            }
        }
    } else {
        array_unshift($announcements, $newData);
    }

    usort($announcements, function($a, $b) {
        $timeA = $a['datetime'] ?? ($a['date'] ?? date('Y-m-d'));
        $timeB = $b['datetime'] ?? ($b['date'] ?? date('Y-m-d'));
        return strtotime($timeB) - strtotime($timeA);
    });

    file_put_contents($dataFile, json_encode(array_values($announcements), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: index.php");
    exit;
}

if (isset($_GET['delete'])) {
    $idToDelete = $_GET['delete'];
    $announcements = array_filter($announcements, function($item) use ($idToDelete) {
        return isset($item['id']) && $item['id'] !== $idToDelete;
    });
    file_put_contents($dataFile, json_encode(array_values($announcements), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: index.php");
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    foreach ($announcements as $item) {
        if (isset($item['id']) && $item['id'] === $_GET['edit']) {
            $editData = $item;
            // 兼容旧数据的编辑回显
            if (!isset($editData['datetime']) && isset($editData['date'])) {
                $editData['datetime'] = $editData['date'] . ' 00:00:00';
            }
            if (!isset($editData['type']) && isset($editData['status'])) {
                $map = ['mitigating'=>'maintenance', 'resolved'=>'recovery', 'outage'=>'outage', 'incident'=>'change', 'feature'=>'feature'];
                $editData['type'] = $map[$editData['status']] ?? 'change';
            }
            break;
        }
    }
}

// --- HTML 生成逻辑 ---
function generate_noc_html($data, $typeConfig) {
    if (empty($data)) return '<p>No announcements available.</p>';
    
    $containerId = 'noc-' . uniqid();

    $html = '<div id="' . $containerId . '" style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Arial, sans-serif; color: #333; line-height: 1.6; max-width: 100%; margin: 0 auto;">
    
    <style>
        #' . $containerId . ' .noc-content img { max-width: 100%; height: auto; border-radius: 4px; margin: 10px 0; }
        #' . $containerId . ' .noc-content pre { background: #f4f4f4; padding: 10px; border-radius: 4px; overflow-x: auto; }
        #' . $containerId . ' .noc-content code { background: #f4f4f4; padding: 2px 4px; border-radius: 3px; font-family: monospace; color: #c7254e; }
        #' . $containerId . ' .noc-content a { color: #0056b3; text-decoration: underline; }
        #' . $containerId . ' .noc-content ul, #' . $containerId . ' .noc-content ol { padding-left: 20px; margin: 10px 0; }
        #' . $containerId . ' .noc-btn.active { background-color: #0056b3 !important; color: white !important; }
    </style>

    <div style="border-bottom: 2px solid #0056b3; padding-bottom: 15px; margin-bottom: 25px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 15px;">
            <div>
                <h2 style="margin: 0; font-size: 1.5rem; color: #2c3e50; font-weight: 700;">
                    <span style="color: #0056b3;">TarekCloud</span> Announcements
                </h2>
                <p style="margin: 5px 0 0; color: #7f8c8d; font-size: 0.9rem;">Official News, Updates & Status</p>
            </div>
            
            <div class="noc-filters" style="display: flex; gap: 6px; flex-wrap: wrap;">
                <button onclick="nocFilter(\'all\', this)" class="noc-btn active" style="background: #e9ecef; color: #555; border: none; padding: 8px 15px; border-radius: 4px; font-size: 0.85rem; cursor: pointer; font-weight: 600; transition: all 0.2s;">All</button>
                <button onclick="nocFilter(\'general\', this)" class="noc-btn" style="background: #e9ecef; color: #555; border: none; padding: 8px 15px; border-radius: 4px; font-size: 0.85rem; cursor: pointer; font-weight: 600; transition: all 0.2s;">General Notice</button>
                <button onclick="nocFilter(\'feature\', this)" class="noc-btn" style="background: #e9ecef; color: #555; border: none; padding: 8px 15px; border-radius: 4px; font-size: 0.85rem; cursor: pointer; font-weight: 600; transition: all 0.2s;">New Features</button>
                <button onclick="nocFilter(\'promotion\', this)" class="noc-btn" style="background: #e9ecef; color: #555; border: none; padding: 8px 15px; border-radius: 4px; font-size: 0.85rem; cursor: pointer; font-weight: 600; transition: all 0.2s;">Deals</button>
            </div>
        </div>
    </div>

    <div style="position: relative; padding-left: 20px; min-height: 200px;">
        <div style="position: absolute; left: 0; top: 10px; bottom: 0; width: 2px; background-color: #e9ecef;"></div>
        <div id="noc-list-items">';

    foreach ($data as $item) {
        // --- 修复：确保 item 是数组 ---
        if (!is_array($item)) continue;

        // --- 兼容性修复 ---
        if (isset($item['type'])) {
            $typeKey = $item['type'];
        } elseif (isset($item['status'])) {
             $map = ['mitigating'=>'maintenance', 'resolved'=>'recovery', 'outage'=>'outage', 'incident'=>'change', 'feature'=>'feature'];
             $typeKey = $map[$item['status']] ?? 'change';
        } else {
            $typeKey = 'change';
        }
        $conf = $typeConfig[$typeKey] ?? $typeConfig['change'];

        if (isset($item['datetime'])) {
            $rawTime = $item['datetime'];
        } elseif (isset($item['date'])) {
            $rawTime = $item['date'] . ' 00:00:00';
        } else {
            $rawTime = date('Y-m-d H:i:s');
        }
        $timeStr = date('Y-m-d H:i:s', strtotime($rawTime));
        // ----------------

        $category = $conf['cat'];
        $cleanTitleEn = isset($item['title_en']) ? strip_tags($item['title_en']) : '';
        $titleCn = isset($item['title_cn']) ? $item['title_cn'] : '';
        $contentEn = isset($item['content_en']) ? $item['content_en'] : '';
        $contentCn = isset($item['content_cn']) ? $item['content_cn'] : '';

        // 变更：将标题合并显示逻辑
        $headerTitleHtml = '';
        if ($cleanTitleEn) {
            $headerTitleHtml .= '<span>' . $cleanTitleEn . '</span>';
        }
        // 如果有中文且有英文，加个分隔符
        if ($cleanTitleEn && $titleCn) {
            $headerTitleHtml .= '<span style="margin: 0 8px; opacity: 0.6;">|</span>'; 
        }
        if ($titleCn) {
            $headerTitleHtml .= '<span>' . $titleCn . '</span>';
        }
        if (!$headerTitleHtml) $headerTitleHtml = 'No Title';

        $html .= '
            <div class="noc-item" data-category="' . $category . '" style="margin-bottom: 30px; position: relative; padding-left: 25px;">
                <div style="position: absolute; left: -24px; top: 6px; width: 10px; height: 10px; background: ' . $conf['color'] . '; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 0 0 2px ' . $conf['color'] . ';"></div>
                
                <div style="background: #fff; border: 1px solid #e0e0e0; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); overflow: hidden;">
                    <div style="background: ' . $conf['bg'] . '; padding: 10px 15px; border-bottom: 1px solid ' . $conf['border'] . '; display: flex; align-items: center; flex-wrap: wrap;">
                        <span style="background: ' . $conf['color'] . '; color: #fff; font-size: 0.75rem; padding: 2px 6px; border-radius: 3px; font-weight: bold; margin-right: 10px; text-transform: uppercase; letter-spacing: 0.5px;">' . $conf['label'] . '</span>
                        <div style="font-weight: 700; color: ' . $conf['text'] . '; font-size: 1rem;">' . $headerTitleHtml . '</div>
                        <span style="margin-left: auto; font-size: 0.8rem; color: ' . $conf['text'] . '; opacity: 0.85; font-family: monospace;">' . $timeStr . '</span>
                    </div>
                    <div style="padding: 15px;">
                        <div class="noc-content" style="font-size: 0.95rem; color: #555;">
                            ' . $contentEn . '
                            ' . ($contentCn ? '<div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #f0f0f0; color: #666;">' . $contentCn . '</div>' : '') . '
                        </div>
                    </div>
                </div>
            </div>';
    }

    $html .= '
        </div>
        <div id="noc-no-result" style="display:none; text-align:center; color:#999; padding:20px;">No records found in this category.</div>
    </div>

    <div id="noc-pagination" style="margin-top: 20px; display: flex; justify-content: center; gap: 10px; align-items: center;">
        <button id="noc-prev-btn" onclick="nocChangePage(-1)" style="padding: 5px 15px; background: #fff; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; color: #555;">&laquo; Prev</button>
        <span id="noc-page-info" style="font-size: 0.9rem; color: #666;">Page 1</span>
        <button id="noc-next-btn" onclick="nocChangePage(1)" style="padding: 5px 15px; background: #fff; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; color: #555;">Next &raquo;</button>
    </div>

    <script>
    (function() {
        var currentPage = 1;
        var itemsPerPage = 10;
        var currentFilter = "all";
        var container = document.getElementById("' . $containerId . '");
        var items = container.querySelectorAll(".noc-item");
        
        window.nocFilter = function(filterType, btn) {
            currentFilter = filterType;
            currentPage = 1;
            var btns = container.querySelectorAll(".noc-btn");
            btns.forEach(function(b) { b.classList.remove("active"); b.style.background = "#e9ecef"; b.style.color = "#555"; });
            btn.classList.add("active");
            btn.style.background = "#0056b3"; btn.style.color = "#fff";
            renderList();
        };

        window.nocChangePage = function(delta) {
            currentPage += delta;
            renderList();
        };

        function renderList() {
            var visibleItems = [];
            items.forEach(function(item) {
                var category = item.getAttribute("data-category");
                if (currentFilter === "all" || category === currentFilter) {
                    visibleItems.push(item);
                }
                item.style.display = "none";
            });

            var totalPages = Math.ceil(visibleItems.length / itemsPerPage) || 1;
            if (currentPage < 1) currentPage = 1;
            if (currentPage > totalPages) currentPage = totalPages;

            var startIndex = (currentPage - 1) * itemsPerPage;
            var endIndex = startIndex + itemsPerPage;

            for (var i = 0; i < visibleItems.length; i++) {
                if (i >= startIndex && i < endIndex) visibleItems[i].style.display = "block";
            }

            container.querySelector("#noc-no-result").style.display = visibleItems.length === 0 ? "block" : "none";
            container.querySelector("#noc-page-info").innerText = "Page " + currentPage + " / " + totalPages;
            
            var prevBtn = container.querySelector("#noc-prev-btn");
            var nextBtn = container.querySelector("#noc-next-btn");
            prevBtn.disabled = (currentPage === 1); prevBtn.style.opacity = currentPage === 1 ? 0.5 : 1;
            nextBtn.disabled = (currentPage === totalPages); nextBtn.style.opacity = currentPage === totalPages ? 0.5 : 1;
        }
        renderList();
    })();
    </script>
</div>';
    return $html;
}

$finalHtml = generate_noc_html($announcements, $typeConfig);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOC Generator Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #f4f6f9; padding: 20px; font-family: sans-serif; }
        .note-editor { background: #fff; }
        
        /* 仅在管理员后台的预览区域生效的样式 */
        #admin-preview-wrapper .noc-content { 
            display: none; /* 默认折叠内容 */
        }
        #admin-preview-wrapper .noc-item { 
            cursor: pointer; 
            transition: transform 0.1s;
        }
        #admin-preview-wrapper .noc-item:active {
            transform: scale(0.995);
        }
        /* 添加一个提示，表明可以点击 */
        #admin-preview-wrapper .noc-item:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<div class="container-fluid" style="max-width: 1600px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>🛠️ TarekCloud NOC 控制台 (Pro版)</h4>
        <div>
            <span class="badge bg-info me-3">支持富文本编辑 (图片/代码/加粗)</span>
            <a href="?logout=1" class="btn btn-outline-danger btn-sm">退出登录</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card mb-4 shadow-sm">
                <div class="card-header <?php echo $editData ? 'bg-warning' : 'bg-primary'; ?> text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?php echo $editData ? '✏️ 编辑模式' : '➕ 发布新公告'; ?></h5>
                    <?php if($editData): ?> <a href="index.php" class="btn btn-sm btn-light">取消编辑</a> <?php endif; ?>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php">
                        <input type="hidden" name="id" value="<?php echo $editData['id'] ?? ''; ?>">
                        <input type="hidden" name="action" value="<?php echo $editData ? 'update' : 'add'; ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">发布时间 (精确到秒)</label>
                                <?php
                                    $showTime = $editData['datetime'] ?? ($editData['date'] ?? null ? $editData['date'].' 00:00:00' : date('Y-m-d H:i:s'));
                                    $showTimeInput = str_replace(' ', 'T', $showTime);
                                ?>
                                <input type="datetime-local" step="1" name="datetime" class="form-control" 
                                       value="<?php echo $showTimeInput; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">公告类型</label>
                                <select name="type" class="form-select">
                                    <?php 
                                        $selType = $editData['type'] ?? 'change';
                                        if (!$editData && !isset($editData['type'])) $selType = 'change'; 
                                        if ($editData && !isset($editData['type']) && isset($editData['status'])) {
                                            $map = ['mitigating'=>'maintenance', 'resolved'=>'recovery', 'outage'=>'outage', 'incident'=>'change', 'feature'=>'feature'];
                                            $selType = $map[$editData['status']] ?? 'change';
                                        }
                                    ?>
                                    <optgroup label="功能与活动">
                                        <option value="feature" <?php echo $selType=='feature'?'selected':''; ?>>✨ New Feature</option>
                                        <option value="promotion" <?php echo $selType=='promotion'?'selected':''; ?>>🎁 Promotion</option>
                                    </optgroup>
                                    <optgroup label="General Notice">
                                        <option value="notice" <?php echo $selType=='notice'?'selected':''; ?>>📢 Notice (一般通知)</option>
                                        <option value="maintenance" <?php echo $selType=='maintenance'?'selected':''; ?>>🔧 Maintenance</option>
                                        <option value="outage" <?php echo $selType=='outage'?'selected':''; ?>>🔴 Outage</option>
                                        <option value="recovery" <?php echo $selType=='recovery'?'selected':''; ?>>✅ Recovery</option>
                                        <option value="compensation" <?php echo $selType=='compensation'?'selected':''; ?>>💰 Compensation</option>
                                        <option value="change" <?php echo $selType=='change'?'selected':''; ?>>📝 Services Change</option>

                                    </optgroup>
                                </select>
                            </div>
                        </div>

                        <!-- 变更：合并中英文标题模块并取消必填 -->
                        <div class="mb-3 p-3 bg-light rounded border">
                            <label class="form-label fw-bold mb-3">公告标题 (Title)</label>
                            
                            <div class="mb-3">
                                <label class="form-label small text-muted">英文标题 (English) - <span class="badge bg-secondary">Optional</span></label>
                                <input type="text" name="title_en" class="form-control" placeholder="Ex: New Dashboard Live" value="<?php echo htmlspecialchars($editData['title_en'] ?? ''); ?>">
                            </div>
                            
                            <div>
                                <label class="form-label small text-muted">中文标题 (Chinese) - <span class="badge bg-secondary">Optional</span></label>
                                <input type="text" name="title_cn" class="form-control" placeholder="例: 全新控制面板上线" value="<?php echo htmlspecialchars($editData['title_cn'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-primary">内容详情 (English)</label>
                            <textarea name="content_en" class="summernote" required><?php echo $editData['content_en'] ?? ''; ?></textarea>
                        </div>

                        <!-- 变更：中文内容非必填 -->
                        <div class="mb-3">
                            <label class="form-label text-primary">内容详情 (中文) - <span class="badge bg-secondary">Optional</span></label>
                            <textarea name="content_cn" class="summernote"><?php echo $editData['content_cn'] ?? ''; ?></textarea>
                        </div>

                        <button type="submit" class="btn <?php echo $editData ? 'btn-warning' : 'btn-primary'; ?> w-100 py-2 fw-bold">
                            <?php echo $editData ? '保存修改 (Update)' : '立即发布 (Publish)'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">📋 历史记录</h5>
                </div>
                <div class="list-group list-group-flush" style="max-height: 85vh; overflow-y: auto;">
                    <?php if(empty($announcements)): ?> <div class="p-3 text-muted text-center">暂无数据</div> <?php endif; ?>
                    <?php foreach($announcements as $item): 
                        // --- 修复：确保 item 是数组，防止 null 报错 ---
                        if (!is_array($item)) continue;

                        // --- 修复: 兼容旧数据 (History List) ---
                        $tKey = $item['type'] ?? null;
                        if (!$tKey && isset($item['status'])) {
                             $map = ['mitigating'=>'maintenance', 'resolved'=>'recovery', 'outage'=>'outage', 'incident'=>'change', 'feature'=>'feature'];
                             $tKey = $map[$item['status']] ?? 'change';
                        }
                        if (!$tKey) $tKey = 'change';
                        
                        // 获取对应的 Config
                        $conf = $typeConfig[$tKey] ?? $typeConfig['change'];
                        
                        // 兼容时间
                        $displayTime = $item['datetime'] ?? ($item['date'] ?? 'N/A');
                        
                        // -------------------------------------
                        // 变更：标题显示逻辑，因为变成了选填
                        $title = '无标题 / No Title';
                        if (isset($item['title_cn']) && $item['title_cn'] !== '') {
                            $title = strip_tags($item['title_cn']);
                        } elseif (isset($item['title_en']) && $item['title_en'] !== '') {
                            $title = strip_tags($item['title_en']);
                        }
                        $itemId = isset($item['id']) ? $item['id'] : '';
                    ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between mb-1 align-items-center">
                                <small class="text-muted" style="font-size:11px;"><?php echo $displayTime; ?></small>
                                <span class="badge rounded-pill" style="font-size:10px; background-color: <?php echo $conf['bg']; ?>; color: <?php echo $conf['text']; ?>; border: 1px solid <?php echo $conf['border']; ?>">
                                    <?php echo $conf['label']; ?>
                                </span>
                            </div>
                            <div class="fw-bold text-truncate mb-1"><?php echo $title; ?></div>
                            <div class="btn-group w-100 btn-group-sm">
                                <?php if($itemId): ?>
                                    <a href="?edit=<?php echo $itemId; ?>" class="btn btn-outline-primary">编辑</a>
                                    <a href="?delete=<?php echo $itemId; ?>" class="btn btn-outline-danger" onclick="return confirm('确定删除？');">删除</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">👁️ 实时效果 (富文本预览)</h5>
                    <small style="font-size: 0.7rem; opacity: 0.8; font-weight: normal; margin-left: 10px;">(点击条目展开/折叠详情)</small>
                </div>
                <div class="card-body bg-white p-0">
                    <!-- 增加了 ID: admin-preview-wrapper 用于后台预览样式控制 -->
                    <div id="admin-preview-wrapper" style="transform: scale(0.9); transform-origin: top left; width: 111%;">
                        <?php echo $finalHtml; ?>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">💻 生成代码 (HTML)</h5>
                    <button class="btn btn-sm btn-light" onclick="copyCode()">复制</button>
                </div>
                <div class="card-body">
                    <textarea id="htmlCode" class="form-control" rows="8" style="font-family: monospace; font-size: 11px; color: #d63384;"><?php echo htmlspecialchars($finalHtml); ?></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            placeholder: '在此输入内容...',
            tabsize: 2,
            height: 150,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        // 仅在后台预览区域启用点击展开/折叠功能
        $('#admin-preview-wrapper').on('click', '.noc-item', function(e) {
            // 简单的手风琴效果
            $(this).find('.noc-content').slideToggle(200);
        });
    });

    function copyCode() {
        var copyText = document.getElementById("htmlCode");
        copyText.select();
        navigator.clipboard.writeText(copyText.value).then(() => alert("✅ HTML代码已复制！"));
    }
</script>
</body>
</html>