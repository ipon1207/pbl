<?php
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
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $attribute = $_POST['attribute'];

    // SQLクエリの準備
    $sql = "INSERT INTO accounts (username, password, email, attribute) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $username, $hashed_password, $email, $attribute);

    if ($stmt->execute()) {
        // 登録成功後、ログインページにリダイレクト
        header("Location: login.php");
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
    <title>ユーザー登録</title>
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
        input[type="text"], input[type="password"], input[type="email"], select {
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
        <h1>ユーザー登録</h1>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="ユーザー名" required>
            <input type="password" name="password" placeholder="パスワード" required>
            <input type="email" name="email" placeholder="メールアドレス" required>
            <select name="attribute" required>
                <option value="" disabled selected>属性を選択してください</option>
                <option value="1">教師</option>
                <option value="2">生徒</option>
                <option value="3">保護者</option>
            </select>
            <input type="submit" value="登録">
        </form>
        <a href="login.php">ログインページに戻る</a>
    </div>
</body>
</html>
