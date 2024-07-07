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

// ユーザー情報の取得
$username = $_SESSION['username'];
$sql = "SELECT attribute FROM accounts WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$attribute = $row['attribute'];

$stmt->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メインページ</title>
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
            width: 80%;
            max-width: 800px;
            text-align: center;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #f7f7f7;
        }
        a {
            display: block;
            width: 100%;
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
        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>メインページ</h1>
        <?php if ($attribute == 1): ?>
            <a href="add_event.php">イベント追加</a>
        <?php endif; ?>
        <a href="add_appointment.php">面談日程追加</a>
        <h2>面談日程表</h2>
        <table>
            <tr>
                <th>保護者</th>
                <th>教師</th>
                <th>日付</th>
                <th>時間</th>
                <th>ステータス</th>
            </tr>
            <?php
            $sql = "SELECT * FROM appointments";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['parent_username']}</td>
                        <td>{$row['teacher_username']}</td>
                        <td>{$row['date']}</td>
                        <td>{$row['time']}</td>
                        <td>{$row['status']}</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>予定はありません</td></tr>";
            }
            ?>
        </table>
        <a href="logout.php">ログアウト</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
