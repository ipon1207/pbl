<?php
session_start();

// セッションにユーザー名が存在しない場合、ログインページにリダイレクト
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// データベース接続設定ファイルをインクルード
include 'config.php';

// ログインユーザー名を取得
$parent_username = $_SESSION['username'];

// 教師のユーザー名を取得
$sql = "SELECT username FROM accounts WHERE attribute = 1";
$result = $conn->query($sql);

// 教師のユーザー名を配列に保存
$teacher_usernames = [];
while ($row = $result->fetch_assoc()) {
    $teacher_usernames[] = $row['username'];
}

// 面談予定の追加
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_username = $_POST['teacher_username'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $availability = $_POST['availability'];

    $sql = "INSERT INTO appointments (parent_username, teacher_username, date, time, availability) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $parent_username, $teacher_username, $date, $time, $availability);
    $stmt->execute();
    $stmt->close();

    header("Location: main.php");
    exit;
}

// 現在の年月を取得
$year = date('Y');
$month = date('m');
$day = date('d');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>面談日程追加</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 800px;
            text-align: center;
            position: relative;
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
            text-align: center;
            word-wrap: break-word;
        }
        th {
            background-color: #f7f7f7;
        }
        .calendar a {
            display: block;
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
            word-wrap: break-word;
        }
        .calendar a:hover {
            background-color: #0056b3;
        }
        .user-info {
            position: absolute;
            top: 10px;
            right: 10px;
            text-align: right;
        }
        .appointment-form {
            display: none;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }
        .appointment-form.active {
            display: flex;
        }
        select, input[type="time"], input[type="date"] {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            cursor: pointer;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .current-date {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 18px;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            .container {
                width: 95%;
            }
            select, input[type="time"], input[type="date"], input[type="submit"] {
                width: calc(100% - 22px);
            }
        }
    </style>
    <script>
        function openForm(date) {
            document.getElementById('appointmentForm').classList.add('active');
            document.getElementById('dateInput').value = date;
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="current-date">
            <?php echo "現在の年月日: " . $year . "年" . $month . "月" . $day . "日"; ?>
        </div>
        <div class="user-info">
            <a href="main.php">メインページに戻る</a>
        </div>
        <h1>面談日程追加</h1>
        <h2>カレンダー</h2>
        <table class="calendar">
            <tr>
                <th>日</th>
                <th>月</th>
                <th>火</th>
                <th>水</th>
                <th>木</th>
                <th>金</th>
                <th>土</th>
            </tr>
            <?php
            $firstDayOfWeek = date('w', strtotime("$year-$month-01"));
            $daysInMonth = date('t', strtotime("$year-$month-01"));

            echo "<tr>";
            for ($i = 0; $i < $firstDayOfWeek; $i++) {
                echo "<td></td>";
            }

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                if (($i % 7) == 0) {
                    echo "</tr><tr>";
                }
                echo "<td>";
                echo "<a href='javascript:void(0)' onclick='openForm(\"$date\")'>" . $day . "</a>";
                echo "</td>";
                $i++;
            }

            while (($i % 7) != 0) {
                echo "<td></td>";
                $i++;
            }

            echo "</tr>";
            ?>
        </table>

        <div id="appointmentForm" class="appointment-form">
            <form method="POST" action="add_appointment.php">
                <input type="hidden" id="dateInput" name="date" required>
                <select name="teacher_username" required>
                    <option value="">教師を選択</option>
                    <?php foreach ($teacher_usernames as $username): ?>
                        <option value="<?php echo $username; ?>"><?php echo $username; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="time" name="time" required>
                <select name="availability" required>
                    <option value="〇">〇</option>
                    <option value="×">×</option>
                </select>
                <input type="submit" value="面談日程追加">
            </form>
        </div>
    </div>
</body>
</html>
