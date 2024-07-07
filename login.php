<?php
session_start();

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
    $username = $_POST['username'];
    $password = $_POST['password'];

    // SQLクエリの準備
    $sql = "SELECT * FROM accounts WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // ユーザーが見つかった場合
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // パスワードの検証
        if (password_verify($password, $row['password'])) {
            // セッションにユーザー名を保存
            $_SESSION['username'] = $username;
            header("Location: main.php");
            exit;
        } else {
            echo "無効なパスワードです。";
        }
    } else {
        echo "ユーザー名が見つかりません。";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログインページ</title>
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
        input[type="text"], input[type="password"] {
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
        <h1>ログイン</h1>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="ユーザー名" required>
            <input type="password" name="password" placeholder="パスワード" required>
            <input type="submit" value="ログイン">
        </form>
        <a href="register.php">アカウント登録</a>
    </div>
</body>
</html>
