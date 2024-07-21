<?php
// データベース接続設定
$servername = "localhost";
$db_username = "root";
$db_password = "";  // MySQLのrootユーザーのパスワードをここに設定します
$dbname = "pbl_db";

// データベース接続を確立
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// 接続チェック
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
