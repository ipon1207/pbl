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

// 現在の年月を取得
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$month = isset($_GET['month']) ? $_GET['month'] : date('m');

// 前の月と次の月の年月を計算
$prevYear = $year;
$prevMonth = $month - 1;
if ($prevMonth < 1) {
    $prevYear--;
    $prevMonth = 12;
}

$nextYear = $year;
$nextMonth = $month + 1;
if ($nextMonth > 12) {
    $nextYear++;
    $nextMonth = 1;
}

// 月の日数を取得
$daysInMonth = date('t', strtotime($year . '-' . $month . '-01'));

// カレンダーのデータを取得
$calendarData = array();
for ($day = 1; $day <= $daysInMonth; $day++) {
    $date = $year . '-' . $month . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
    
    // 面談予定を取得
    $sql = "SELECT * FROM appointments WHERE date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $appointmentsResult = $stmt->get_result();
    
    // イベントを取得
    $sql = "SELECT * FROM schedules WHERE date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $eventsResult = $stmt->get_result();
    
    $calendarData[$date] = array(
        'appointments' => $appointmentsResult->fetch_all(MYSQLI_ASSOC),
        'events' => $eventsResult->fetch_all(MYSQLI_ASSOC)
    );
}

// 予定の更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['event_id'];
    $eventTitle = $_POST['event_title'];
    $eventDate = $_POST['event_date'];
    $eventTime = $_POST['event_time'];

    $sql = "UPDATE schedules SET event = ?, date = ?, time = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $eventTitle, $eventDate, $eventTime, $eventId);
    $stmt->execute();
    $stmt->close();
}
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
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
        .calendar-container {
            margin-top: 20px;
        }
        .calendar {
            width: 100%;
            border-collapse: collapse;
        }
        .calendar th {
            background-color: #f7f7f7;
            padding: 10px;
            text-align: center;
        }
        .calendar td {
            padding: 10px;
            border: 1px solid #ccc;
            vertical-align: top;
            height: 100px;
        }
        .calendar .day {
            font-weight: bold;
        }
        .calendar .event,
        .calendar .appointment {
            margin-top: 5px;
            padding: 2px;
            cursor: pointer;
        }
        .calendar .event {
            background-color: #f0f0f0;
        }
        .calendar .appointment {
            background-color: #d4edda;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .month-navigation {
            margin-bottom: 10px;
        }
        .month-navigation a {
            display: inline-block;
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 0 5px;
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
        <?php if (($attribute == 1) || ($attribute==3)): ?>
            <a href="setting.php">設定</a>
        <?php endif; ?>
        <a href="logout.php">ログアウト</a>
    </div>
    
    <div class="container">
        <div class="calendar-container">
            <div class="month-navigation">
                <a href="?year=<?php echo $prevYear; ?>&month=<?php echo $prevMonth; ?>">&lt; 先月</a>
                <span><?php echo $year; ?>年<?php echo $month; ?>月</span>
                <a href="?year=<?php echo $nextYear; ?>&month=<?php echo $nextMonth; ?>">来月 &gt;</a>
            </div>
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
                $firstDayOfWeek = date('w', strtotime($year . '-' . $month . '-01'));
                $daysInMonth = date('t', strtotime($year . '-' . $month . '-01'));
                $currentDay = 1;
                
                for ($week = 1; $week <= 6; $week++) {
                    echo "<tr>";
                    
                    for ($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++) {
                        if (($week === 1 && $dayOfWeek < $firstDayOfWeek) || $currentDay > $daysInMonth) {
                            echo "<td></td>";
                        } else {
                            $date = $year . '-' . $month . '-' . str_pad($currentDay, 2, '0', STR_PAD_LEFT);
                            echo "<td>";
                            echo "<div class='day'>" . $currentDay . "</div>";
                            
                            foreach ($calendarData[$date]['events'] as $event) {
                                echo "<div class='event' onclick='openModal(\"" . $event['id'] . "\", \"" . $event['event'] . "\", \"" . $event['date'] . "\", \"" . $event['time'] . "\")'>" . $event['event'] . "</div>";
                            }
                            
                            foreach ($calendarData[$date]['appointments'] as $appointment) {
                                echo "<div class='appointment' onclick='openModal(\"" . $appointment['id'] . "\", \"" . $appointment['parent_username'] . " - " . $appointment['teacher_username'] . "\", \"" . $appointment['date'] . "\", \"" . $appointment['time'] . "\")'>";
                                echo $appointment['parent_username'] . " - " . $appointment['teacher_username'];
                                echo "</div>";
                            }
                            
                            echo "</td>";
                            $currentDay++;
                        }
                    }
                    
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
    
    <!-- モーダルウィンドウ -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 id="modal-title"></h3>
            <form id="event-form" method="post">
                <input type="hidden" id="event-id" name="event_id">
                <label for="event-title">タイトル:</label>
                <input type="text" id="event-title" name="event_title" required><br>
                <label for="event-date">日付:</label>
                <input type="date" id="event-date" name="event_date" required><br>
                <label for="event-time">時間:</label>
                <input type="time" id="event-time" name="event_time" required><br>
                <button type="submit">更新</button>
            </form>
        </div>
    </div>
    
    <script>
        var modal = document.getElementById("modal");
        var modalTitle = document.getElementById("modal-title");
        var eventForm = document.getElementById("event-form");
        var eventId = document.getElementById("event-id");
        var eventTitle = document.getElementById("event-title");
        var eventDate = document.getElementById("event-date");
        var eventTime = document.getElementById("event-time");
        
        function openModal(id, title, date, time) {
            modalTitle.textContent = title;
            eventId.value = id;
            eventTitle.value = title;
            eventDate.value = date;
            eventTime.value = time;
            modal.style.display = "block";
        }
        
        function closeModal() {
            modal.style.display = "none";
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
