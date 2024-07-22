<?php
/*
このプログラムは、学校の先生、生徒、保護者間でのチャットシステムを実装しているPHPコードの一部です。主に以下の機能を持っています：
ユーザー属性（先生、生徒、保護者）に基づいて、会話相手の一覧を取得
先生の場合は、生徒と保護者の一覧を取得し、生徒の下に関連する保護者を表示
生徒と保護者の場合は、先生の一覧を取得
チャットメッセージの保存
フォームから送信されたデータ（送信者、受信者、メッセージ内容など）を取得
データベースにメッセージを保存
エラーハンドリング
必要なデータが不足している場合にエラーメッセージを表示
データベースへの保存が失敗した場合にエラーメッセージを表示

2.1 ユーザー管理
ユーザーは先生、生徒、保護者の3つの属性に分類される
各ユーザーはユニークなユーザー名を持つ
生徒と保護者の関係性は、別のテーブル（parents_students）で管理される
2.2 会話相手の表示
先生の場合：
生徒の一覧を表示
各生徒の下に、関連する保護者を表示
生徒と関連付けられていない保護者も別途表示
生徒と保護者の場合：
先生の一覧を表示
2.3 メッセージ送信
ユーザーは、表示された会話相手に対してメッセージを送信できる
送信されたメッセージは、データベースに保存される
メッセージには、送信者、受信者、メッセージ内容、タイムスタンプ、ユーザー属性が含まれる
*/ 
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

// データベースに接続
$conn = new mysqli($servername, $username, $password, $dbname);
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

// 会話相手の取得
$conversationUsers = [];
if ($attribute == 1) {
    // 先生の場合、まず生徒のみを取得
    $sql = "SELECT id, username, attribute FROM accounts WHERE attribute = 2";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $conversationUsers[] = $row;
        }
    }

    // 保護者を生徒の下にソート
    $sql = "SELECT p.id AS parent_id, p.username AS parent_username, s.username AS student_username 
            FROM parents_students ps
            JOIN accounts p ON ps.parent_id = p.id
            JOIN accounts s ON ps.student_id = s.id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $parentId = $row['parent_id'];
            $parentUsername = $row['parent_username'];
            $studentUsername = $row['student_username'];
            
            // 保護者を生徒の下に移動
            foreach ($conversationUsers as $key => $user) {
                if ($user['username'] == $studentUsername) {
                    $parentUser = ['id' => $parentId, 'username' => $parentUsername, 'attribute' => 3];
                    array_splice($conversationUsers, $key + 1, 0, [$parentUser]);
                    break;
                }
            }
        }
    }

    // 紐づいていない保護者を取得して追加
    $sql = "SELECT id, username FROM accounts 
            WHERE attribute = 3 
            AND id NOT IN (SELECT parent_id FROM parents_students)";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $conversationUsers[] = ['id' => $row['id'], 'username' => $row['username'], 'attribute' => 3];
        }
    }
} elseif ($attribute == 2 || $attribute == 3) {
    // 生徒と保護者の場合、先生を取得
    $sql = "SELECT id, username, attribute FROM accounts WHERE attribute = 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $conversationUsers[] = $row;
        }
    }
}

// 選択された会話相手を取得
$selectedUser = isset($_GET['user']) ? $_GET['user'] : '';

// フォームから送信されたデータを取得
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // POSTデータの存在チェックとデフォルト値の設定
    $teacher = isset($_POST['teacher']) ? $_POST['teacher'] : '';
    $student = isset($_POST['student']) ? $_POST['student'] : '';
    $chat = isset($_POST['chat']) ? $_POST['chat'] : '';
    $attribute = isset($_POST['attribute']) ? intval($_POST['attribute']) : 0;
    $sent_user = $_POST['sent_user'];

    // データが空でないことを確認
    if (!empty($teacher) && !empty($student) && !empty($chat) && $attribute > 0) {
        // プリペアドステートメントを使用してINSERT文を実行
        $stmt = $conn->prepare("INSERT INTO chat (time, teacher, student, chat, attribute, sent_user) VALUES (NOW(), ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $teacher, $student, $chat, $attribute, $sent_user);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            //echo "データが正常に保存されました。";
        } else {
            echo "データの保存に失敗しました。";
        }
        $stmt->close();
    } else {
        echo "必要なデータが不足しています。すべてのフィールドを入力してください。";
    }
}
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
            left: 10%;
            z-index: 0;
        }
        .message {
            position: relative; /* 親要素に相対位置を設定 */
        }
        .message small {
            font-size: 0.8em;
            color: #888;
            display: block;
            margin-top: 5px;
            position: absolute; /* 絶対位置を設定 */
            right: 5%; /* 右端から5%の位置に配置 */
            bottom: 0; /* メッセージの下端に合わせる */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="conversation-list">
            <button class="toggle-button" onclick="toggleConversationList()">←</button>
            <h2>会話相手</h2>
            <ul>
                <?php foreach ($conversationUsers as $user): ?>
                    <li><a href="?user=<?php echo $user['username']; ?>"><?php echo $user['username']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="conversation-window">
            <div class="header">
                <a href="index.php" class="back-button">戻る</a>
                <h2>会話</h2>
            </div>
            <div class="messages">
                <?php
                // 会話の表示
                if ($selectedUser) {
                    // データベースから会話を取得して表示
                    $sql = "SELECT c.time, c.teacher, c.student, c.chat, c.attribute, c.sent_user
                            FROM chat c
                            WHERE (c.teacher = ? AND c.student = ?) OR (c.teacher = ? AND c.student = ?)
                            ORDER BY c.time ASC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssss", $username, $selectedUser, $selectedUser, $username);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $time = $row['time'];
                            $teacher = $row['teacher'];
                            $student = $row['student'];
                            $chat = $row['chat'];
                            $chatAttribute = $row['attribute'];
                            $sent_user = $row['sent_user'];

                            if ($chatAttribute == 1) {
                                // 保護者の場合、生徒を取得
                                $sql = "SELECT s.username AS student_username
                                        FROM parents_students ps
                                        JOIN accounts s ON ps.student_id = s.id
                                        WHERE ps.parent_id = (SELECT id FROM accounts WHERE username = ?)";
                                $stmt2 = $conn->prepare($sql);
                                $stmt2->bind_param("s", $student);
                                $stmt2->execute();
                                $result2 = $stmt2->get_result();

                                if ($result2->num_rows > 0) {
                                    $row2 = $result2->fetch_assoc();
                                    $student = $row2['student_username'];
                                }

                                $stmt2->close();
                            }

                            echo "<div class='message'>";
                            echo "<p><strong>" . htmlspecialchars($sent_user) . "</strong>: " . htmlspecialchars($chat) . "</p>";
                            echo "<small>" . $row['time'] . "</small>";
                            echo "</div>";
                            
                        }
                    } else {
                        echo "<p>会話がありません。</p>";
                    }

                    $stmt->close();
                } else {
                    echo "<p>会話相手を選択してください。</p>";
                }
                ?>
            </div>
            <div class="chat-input">
                <form action="<?php echo $_SERVER['PHP_SELF'] . '?user=' . $selectedUser; ?>" method="post" enctype="multipart/form-data">
                    <input type="text" name="chat" placeholder="メッセージを入力">
                    <input type="file" name="attachment" id="attachment">
                    <label for="attachment">ファイル選択</label>
                    <input type="hidden" name="teacher" value="<?php echo $teacher; ?>">
                    <input type="hidden" name="student" value="<?php echo $student; ?>">
                    <input type="hidden" name="attribute" value="<?php echo $attribute; ?>">
                    <input type="hidden" name="sent_user" value="<?php echo $sent_user; ?>">
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

    // 会話履歴を一番下にスクロール
    const messagesDiv = document.querySelector('.messages');
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
</script>
</body>
</html>

<?php
// データベース接続を閉じる
$conn->close();
?>
