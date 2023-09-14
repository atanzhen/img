<?php
// 图片文件夹路径
$imageFolder = 'images/';// 这里改成你的图片实际路径

// 检查图片文件夹是否存在图片文件
$imageFiles = glob($imageFolder . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
if (empty($imageFiles)) {
    echo '请上传图片到指定文件夹。';
    exit;
}

// 随机选择一张图片
$randomImage = $imageFiles[array_rand($imageFiles)];

// 获取客户端IP地址
$clientIP = $_SERVER['REMOTE_ADDR'];

// 存储最后一次访问的图片索引
$lastImageIndex = isset($_COOKIE['last_image_index']) ? intval($_COOKIE['last_image_index']) : -1;

// 遍历图片，确保不会在短时间内同一个IP地址显示相同的图片
$filteredImages = array_filter($imageFiles, function ($index) use ($lastImageIndex) {
    return $index !== $lastImageIndex;
}, ARRAY_FILTER_USE_KEY);

// 随机选择一张过滤后的图片
$randomFilteredImage = $filteredImages[array_rand($filteredImages)];

// 更新最后一次访问的图片索引
setcookie('last_image_index', array_search($randomFilteredImage, $imageFiles), time() + 3600);

// 根据图片文件的扩展名设置响应头
$imageExtension = strtolower(pathinfo($randomFilteredImage, PATHINFO_EXTENSION));
if ($imageExtension === 'jpg' || $imageExtension === 'jpeg') {
    header('Content-Type: image/jpeg');
} elseif ($imageExtension === 'png') {
    header('Content-Type: image/png');
} elseif ($imageExtension === 'gif') {
    header('Content-Type: image/gif');
} elseif ($imageExtension === 'webp') {
    header('Content-Type: image/webp');
}

// 存储访问日志和限制日志的文件
$logFile = 'admin/log/img-log.txt';

// 检测调用域名和限制同一IP地址的访问频率
$limit = 10; // 限制的访问次数
$interval = 1; // 限制的时间间隔（秒）

// 检测调用域名
$callingDomain = $_SERVER['HTTP_REFERER'] ?? '';
if (!empty($callingDomain)) {
    $callingDomain = parse_url($callingDomain, PHP_URL_HOST);
    $timestamp = time();
    file_put_contents($logFile, $clientIP . ' - ' . $callingDomain . ' - ' . $timestamp . "\n", FILE_APPEND);
}

// 检查访问频率
$accessCount = 0;
$accessTime = time() - $interval;
if (file_exists($logFile)) {
    $accessData = file($logFile, FILE_IGNORE_NEW_LINES);
    foreach ($accessData as $accessLine) {
        if ($accessLine >= $accessTime) {
            $accessCount++;
        }
    }
}

// 记录当前访问时间
file_put_contents($logFile, time() . "\n", FILE_APPEND);

// 如果访问次数超过限制，返回 404 Not Found
//if ($accessCount > $limit) {
   // http_response_code(404);
  //  exit;
//}

/*把返回404改成返回图片*/
if ($accessCount > $limit) {
    header('Content-Type: image/jpeg');
    readfile('警告.jpg');
    exit;
}
/*把返回404改成返回图片*/
// 获取文件名的 MD5 值
$hashedFileName = md5($randomFilteredImage);
// 提取 MD5 值中的数字部分
$numericFileName = preg_replace('/[^0-9]/', '', $hashedFileName);

// 读取图片文件并输出
if ($imageExtension === 'webp') {
    // 使用 file_get_contents 函数读取 WebP 文件内容
    $imageContent = file_get_contents($randomFilteredImage);
    // 输出图片内容
    echo $imageContent;
} else {
    // 读取其他图片格式文件并输出
    header('Content-Disposition: inline; filename="' . $numericFileName . '.' . $imageExtension . '"');
    readfile($randomFilteredImage);
}
?>
