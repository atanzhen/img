
<?php
session_start();

// 检查是否已经通过密码验证
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // 未通过密码验证，重定向到密码访问页面
    header('Location: index.php');
    exit;
}

// 处理退出登录
if (isset($_POST['logout'])) {
    // 清除验证状态
    unset($_SESSION['authenticated']);
    unset($_SESSION['validated_time']);

    // 重定向到密码访问页面
    header('Location: index.php');
    exit;
}
?>

<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.9, maximum-scale=0.9, user-scalable=no">
<style>
    /* 分页链接样式 */
    .pagination {
        margin-top: 10px;
    }

    .pagination a,
    .pagination span {
        display: inline-block;
        padding: 5px 10px;
        border: 1px solid #ccc;
        margin-right: 5px;
        text-decoration: none;
        color: #333;
        background-color: #f9f9f9;
        border-radius: 3px;
    }

    .pagination a:hover {
        background-color: #e0e0e0;
    }

    .pagination .current {
        font-weight: bold;
        background-color: #ccc;
    }

    /* 表格样式 */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 8px;
        border: 1px solid #ccc;
    }

    th {
        background-color: #f9f9f9;
        font-weight: bold;
    }
       .logout-form {
           /* position: absolute;*/
            bottom: 5px;
        }

        .logout-form input[type="submit"] {
            background: #ff0000;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
</style>

<?php
// 读取调用记录
$callLog = 'log/img-log.txt';
$callData = file($callLog, FILE_IGNORE_NEW_LINES);

// 过滤掉没有 IP 地址的记录
$callData = array_filter($callData, function($line) {
    return strpos($line, ' - ') !== false;
});

// 反转调用记录数组，使最新的记录排在数组的开头
$callData = array_reverse($callData);

// 每页展示的记录数量
$perPage = 20;

// 总记录数量
$totalCount = count($callData);

// 当前页码
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// 计算总页数
$totalPages = ceil($totalCount / $perPage);

// 限制页码范围
$page = max(1, min($page, $totalPages));

// 计算起始索引
$startIndex = ($page - 1) * $perPage;

// 获取当前页的记录
$pageData = array_slice($callData, $startIndex, $perPage);

// 显示调用记录表格
echo '<table>';
echo '<tr><th>IP地址</th><th>调用域名</th><th>记录时间</th></tr>';

foreach ($pageData as $callLine) {
    list($ip, $domain, $timestamp) = explode(' - ', $callLine);
    echo '<tr>';
    echo '<td>' . htmlspecialchars($ip) . '</td>';
    echo '<td>' . htmlspecialchars($domain) . '</td>';
    echo '<td>' . date('Y-m-d H:i:s', intval($timestamp)) . '</td>';
    echo '</tr>';
}

echo '</table>';

// 显示分页链接
echo '<div class="pagination">';

// Display first page and previous page links
if ($page > 1) {
    echo '<a href="?page=1">第一页</a>';
    echo '<a href="?page=' . ($page - 1) . '">上一页</a>';
}

// Display numbered page links
$startPage = max(1, $page - 20);
$endPage = min($startPage + 19, $totalPages);

for ($i = $startPage; $i <= $endPage; $i++) {
    if ($i === $page) {
        echo '<span class="current">' . $i . '</span>';
    } else {
        echo '<a href="?page=' . $i . '">' . $i . '</a>';
    }
}

// Display next page and last page links
if ($page < $totalPages) {
    echo '<a href="?page=' . ($page + 1) . '">下一页</a>';
    echo '<a href="?page=' . $totalPages . '">最后一页</a>';
}
// Display next page and last page links
if ($page < $totalPages) {
    echo '<a href="?page=' . $totalPages . '">合计' . $totalPages . '页</a>';
}

?>

    <!-- 退出登录按钮 -->
    <form class="logout-form" method="post" action=""></p>
        <input type="submit" name="logout" value="退出登录" />
    </form>