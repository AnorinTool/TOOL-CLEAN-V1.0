<?php
function logger($msg, $type = 'info') {
    $colors = ['info' => '32', 'error' => '31', 'success' => '32', 'input' => '36', 'warn' => '33'];
    echo "\033[" . $colors[$type] . "m" . $msg . "\033[0m\n";
}

$targetDir = readline("Nhập thư mục chứa file cần test: ");
$successDir = $targetDir . '/success_logs';
$errorDir = $targetDir . '/error_logs';
if(!is_dir($successDir)) mkdir($successDir);
if(!is_dir($errorDir)) mkdir($errorDir);

$files = glob($targetDir . "/*.php");

foreach ($files as $filePath) {
    if (basename($filePath) == basename(__FILE__)) continue;

    $fileName = basename($filePath);
    echo "Testing: $fileName... ";

    // Thêm timeout 5 giây để tránh treo máy
    $cmd = "timeout 5s php " . escapeshellarg($filePath) . " 2>&1";
    $output = shell_exec($cmd);
    
    // Kiểm tra lỗi cú pháp hoặc bị timeout
    if (strpos($output, 'Parse error') !== false || strpos($output, 'Fatal error') !== false || empty($output)) {
        logger("❌ LỖI", 'error');
        rename($filePath, $errorDir . '/' . $fileName);
    } else {
        logger("✅ CHẠY TỐT", 'success');
        rename($filePath, $successDir . '/' . $fileName);
    }
}
logger("🎉 Hoàn tất phân loại!");
