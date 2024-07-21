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
<html>
<head>
    <title>会話ページ</title>
    <style>
        /* CSSスタイルを追加 */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            height: 100vh;
        }
        .conversation-list {
            width: 25%;
            background-color: #1e90ff;
            color: white;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            transition: width 0.3s, padding 0.3s;
            position: relative;
        }
        .conversation-list.collapsed {
            width: 0;
            padding: 0;
            overflow: hidden;
        }
        .conversation-list h2 {
            margin-top: 0;
        }
        .conversation-list ul {
            list-style: none;
            padding: 0;
        }
        .conversation-list li {
            margin: 15px 0;
        }
        .conversation-list a {
            display: block;
            padding: 10px;
            background-color: #4682b4;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .conversation-list a:hover {
            background-color: #5a9bd3;
        }
        .conversation-window {
            flex: 1;
            padding: 20px;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
        }
        .conversation-window .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #1e90ff;
            padding-bottom: 10px;
        }
        .conversation-window .header a {
            color: #1e90ff;
            text-decoration: none;
        }
        .conversation-window .header h2 {
            margin: 0;
        }
        .messages {
            flex: 1;
            margin-top: 20px;
            overflow-y: auto;
        }
        .messages p {
            background-color: #e6f7ff;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .messages p strong {
            color: #1e90ff;
        }
        .chat-input {
            display: flex;
            margin-top: 10px;
        }
        .chat-input input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 2px solid #1e90ff;
            border-radius: 5px;
            margin-right: 10px;
        }
        .chat-input input[type="file"] {
            display: none;
        }
        .chat-input label {
            background-color: #1e90ff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }
        .chat-input button {
            background-color: #1e90ff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .chat-input button:hover {
            background-color: #1c86ee;
        }
        .toggle-button {
            position: fixed;
            top: 20px;
            left: 20%;
            background-color: #1e90ff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
            z-index: 1;
            transition: left 0.3s;
        }
        .toggle-button.collapsed {
            left: 10%
            z-index: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="conversation-list">
            <button class="toggle-button" onclick="toggleConversationList()">←</button>
            <h2>会話相手</h2>
            <ul>
                <li><a href="?user=山田">山田</a></li>
                <li><a href="?user=佐藤">佐藤</a></li>
                <li><a href="?user=鈴木">鈴木</a></li>
            </ul>
        </div>
        <div class="conversation-window">
            <div class="header">
                <a href="main.php" class="back-button">戻る</a>
                <h2>会話</h2>
            </div>
            <div class="messages">
                <?php
                // 会話の表示
                $user = isset($_GET['user']) ? $_GET['user'] : '';
                if ($user) {
                    // データベースから会話を取得して表示
                    echo "<p><strong>$user</strong>: こんにちは！</p>";
                    echo "<p>あなた: こんにちは、元気ですか？</p>";
                    // ...
                } else {
                    echo "<p>会話相手を選択してください。</p>";
                }
                ?>
            </div>
            <div class="chat-input">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="text" name="message" placeholder="メッセージを入力">
                    <input type="file" name="attachment" id="attachment">
                    <label for="attachment">ファイル選択</label>
                    <button type="submit">送信</button>
                </form>
            </div>
        </div>
    </div>
<script>
    function toggleConversationList() {
        const conversationList = document.querySelector('.conversation-list');
        const toggleButton = document.querySelector('.toggle-button');
        conversationList.classList.toggle('collapsed');
        toggleButton.classList.toggle('collapsed');
        if (conversationList.classList.contains('collapsed')) {
            toggleButton.textContent = '→';
            toggleButton.style.left = '10%';
        } else {
            toggleButton.textContent = '←';
            toggleButton.style.left = '20%';
        }
    }
</script>
</body>
</html>
