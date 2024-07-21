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


// if POST is sent
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $partner = $_POST["partner"];

    $sql = "";
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
    <title>Setting</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .header h1 {
            margin: 0;
        }
        .header a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>メインページ</h1>
        <?php if ($attribute == 1): ?>
            <a href="add_event.php">イベント追加</a>
        <?php endif; ?>
        <a href="add_appointment.php">面談日程追加</a>
        <a href="talk.php">チャット</a>
        <a href="setting.php">Setting</a>
        <a href="logout.php">ログアウト</a>
    </div>

    <h1>紐づけ</h1>
    <form method="post" action="link_parent_student.php">
        <label for="student_id">Select Student:</label>
        <select name="student_id" required>
            <?php while($row = $result->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['username']; ?></option>
            <?php endwhile; ?>
        </select><br>
        <button type="submit">Link</button>
    </form>


</body>
</html>

<?php
$conn->close();
?>
