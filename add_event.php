<?php
session_start();

// セッションにユーザー名が存在しない場合、ログインページにリダイレクト
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// データベース接続設定
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pbl_db";

// データベース接続を確立
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続チェック
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// フォームが送信された場合
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event = $_POST['event'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $username = $_SESSION['username'];

    // SQLクエリの準備
    $sql = "INSERT INTO schedules (username, event, date, time) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $event, $date, $time);

    if ($stmt->execute()) {
        // イベントが正常に追加されたらメインページにリダイレクト
        header("Location: main.php");
        exit;
    } else {
        echo "エラー: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>イベント追加</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="date"], input[type="time"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"], a {
            display: block;
            width: calc(100% - 20px);
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            margin-top: 10px;
        }
        input[type="submit"]:hover, a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>イベント追加</h1>
        <form method="POST" action="">
            <input type="text" name="event" placeholder="イベント名" required>
            <input type="date" name="date" required>
            <input type="time" name="time" required>
            <input type="submit" value="追加">
        </form>
        <a href="main.php">メインページに戻る</a>
    </div>
</body>
</html>
