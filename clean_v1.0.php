<?php
/**
 * PHP CLEAN v1.0 & CÔNG CỤ FIX CODE PHP
 */

function logger($msg, $type = 'info') {
    $colors = ['info' => '32', 'error' => '31', 'success' => '32', 'input' => '36', 'warn' => '33'];
    echo "\033[" . $colors[$type] . "m" . $msg . "\033[0m\n";
}

logger("===PHP CODE CLEAN V1.0===", 'input');

// 1. Chọn chế độ thư mục
$dirChoice = readline("Bạn muốn dùng [1] Thư mục hiện tại hoặc [2] Nhập đường dẫn thủ công? ");
$targetDir = ($dirChoice == '2') ? readline("Nhập đường dẫn: ") : getcwd();
if (!is_dir($targetDir)) {
    logger("Thư mục không tồn tại!", 'error');
    exit;
}

// 2. Chế độ xử lý
$mode = readline("Bạn muốn xử lý [1] Một file hay [2] Tất cả file .php trong thư mục? ");

// 3. Xử lý logic thư mục đầu ra
$outputDir = $targetDir;
if ($mode == '2') {
    $createSubFolder = readline("Tạo thư mục riêng 'fixed_files' cho file đã xử lý? (y/n): ");
    if (strtolower($createSubFolder) == 'y') {
        $outputDir = $targetDir . '/fixed_files';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
            logger("Đã tạo thư mục: fixed_files", 'success');
        }
    }
}

// 4. Hậu tố
$useSuffix = readline("Thêm hậu tố '_fix_xuongdong' vào tên file? (y/n): ");
$suffix = (strtolower($useSuffix) == 'y') ? "_fix_xuongdong" : "";

// Hàm xử lý core
function processFile($filePath, $outputDir, $suffix) {
    $code = file_get_contents($filePath);
    
    // Fix ANSI và format logic
    $code = preg_replace_callback('/"([^"]+)"/', function($matches) {
        return '"' . preg_replace('/\s+/', '', $matches[1]) . '"';
    }, $code);
    
    $output = preg_replace('/([;{}])\s*(?=(?:[^"]*"[^"]*")*[^"]*$)/', "$1\n", $code);
    $output = preg_replace('/^\s*\?>\s*<\?php\s*/', '<?php', $output);
    $output = preg_replace('/<\?php\s+php/', '<?php', $output);
    $output = preg_replace("/\n{2,}/", "\n", $output);
    
    $pathParts = pathinfo($filePath);
    $newFileName = $outputDir . '/' . $pathParts['filename'] . $suffix . '.' . $pathParts['extension'];
    file_put_contents($newFileName, $output);
    return $newFileName;
}

// Thực thi
if ($mode == '1') {
    $fileName = readline("Nhập tên file: ");
    $path = $targetDir . '/' . $fileName;
    if (file_exists($path)) {
        $result = processFile($path, $outputDir, $suffix);
        logger("Đã xử lý: $result", 'success');
    } else {
        logger("Không tìm thấy file!", 'error');
    }
} else {
    $files = glob($targetDir . "/*.php");
    $count = 0;
    foreach ($files as $file) {
        if (basename($file) == 'fix.php') continue;
        processFile($file, $outputDir, $suffix);
        $count++;
    }
    logger("Đã xử lý xong $count file tại thư mục: $outputDir", 'success');
}
?>
