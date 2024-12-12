<?php
require_once('/var/www/html/moodle/custom/app/Models/BaseModel.php');
require_once('/var/www/html/moodle/custom/app/Models/EventModel.php');

// 接続情報取得
$baseModel = new BaseModel();
$eventModel = new EventModel();
$pdo = $baseModel->getPdo();

$imgNames = ['main_img', 'detail_img'];
$userId = isset($_SESSION['USER']->id) ? (int)$_SESSION['USER']->id : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }
}

// イベント固定データ
$userId = (int)$_SESSION['USER']->id;
$eventId = isset($_POST['eventId']) ? (int)$_POST['eventId'] : null;
$names = $_POST['name'];
$description = $_POST['abstruct'];
$location = $_POST['venue'];
$target = $_POST['target'];
$note = $_POST['note'];

// イベント可変データ
$eachIds = $_POST['each_id'];
$startDates = $_POST['start_date'];
$endDates = $_POST['end_date'];
$capacitys = $_POST['capacity'];
$fileNames = $_POST['files'];
$movieNames = $_POST['movie'];
$teacherNames = $_POST['teacher_name'];

// TO DO バリデーション仕様固まり次第追加すること
$errors = [];
if (empty($names) || mb_strlen($names) > 100) {
    $errors['name'] = '名前は必須で、100文字以内で入力してください。';
}
if (!empty($errors)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'errors' => $errors]);
    exit;
}

try {
    $pdo->beginTransaction();
    $variables = ['mainImgName', 'subImgName'];
    foreach ($imgNames as $key => $imgName) {
        if (isset($_FILES[$imgName])) {
            $file = $_FILES[$imgName];
            if (isset($variables[$key])) {
                $varName = $variables[$key];
                $$varName = $file['name'];
            }
            $upload_dir = '/var/www/html/moodle/custom/upload/img/'; // config等で共通化
            $file_path = $upload_dir . basename($file['name']);
            move_uploaded_file($file['tmp_name'], $file_path);
        }
    }
    // 講義資料アップロード
    if (isset($_FILES['files'])) {
        $upload_dir = '/var/www/html/moodle/custom/upload/file/';
        foreach ($_FILES['files']['name'] as $name) {
            $fileName = $name;
            $file_path = $upload_dir . basename($name);
        }
    }
    // // 講義動画アップロード
    // if (isset($_FILES['movie'])) {
    //     $file = $_FILES['movie'];
    //     $movieName = $file['name'];
    //     $upload_dir = '/var/www/html/moodle/custom/upload/movie/';
    //     $file_path = $upload_dir . basename($file['name']);
    //     move_uploaded_file($file['tmp_name'], $file_path);
    // }

    $event = $eventModel->getEventById($eventId);
    $detailList = $event['details'];

    // ファイル登録内容確認
    if (!$event && ($mainImgName === "" || $subImgName === "" || $fileName === "" || $movieName === "")) {
        // エラーメッセージを返す
        throw new Exception("Error: Event data is missing or one of the required file names is empty.");
    }
    $mainImgName = ($mainImgName !== "") ? $mainImgName : ($event['main_img_name'] ?? "");
    $subImgName = ($subImgName !== "") ? $subImgName : ($event['sub_img_name'] ?? "");
    $fileName = ($fileName !== "") ? $fileName : ($detailList['files_name'] ?? "");
    $movieName = ($movieName !== "") ? $movieName : ($detailList['movie_name'] ?? "");

    if ($event['id']) {
        var_dump('upsert');
        $stmt =  $pdo->prepare("UPDATE moodle.mdl_event
        SET name = ?, 
            description = ?, 
            userid = ?, 
            location = ?, 
            venue = ?, 
            venue_name = ?, 
            target = ?, 
            note = ?, 
            main_img_name = ?, 
            sub_img_name = ?
        WHERE id = ?");

        $stmt->execute([
            $names,
            $description,
            $userId,
            $location,
            $location,
            $location,
            $target,
            $note,
            $mainImgName,
            $subImgName,
            $eventId
        ]);

        foreach ($eachIds as $key => $eachId) {
            $exists = array_filter($detailList, function ($row) use ($eachId) {
                return isset($row["id"]) && $row["id"] == $eachId;
            });
            if (!empty($exists)) {
                $stmt = $pdo->prepare(
                    "UPDATE mdl_event_each 
                    SET start_date = ?, 
                        end_date = ?, 
                        capacity = ?, 
                        files_name = ?, 
                        movie_name = ?,
                        teacher_name = ? 
                    WHERE event_id = ? AND id = ?"
                );
                $stmt->execute([$startDates[$key], $endDates[$key], $capacitys[$key], $fileNames[$key], $movieNames[$key],  $teacherNames[$key], $eventId, $eachIds[$key]]);
            } else {
                $stmt = $pdo->prepare(
                    "
                    INSERT INTO mdl_event_each (
                        event_id, 
                        start_date, 
                        end_date, 
                        capacity, 
                        files_name, 
                        movie_name,
                        teacher_name
                    ) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([$eventId, $startDates[$key], $endDates[$key], $capacitys[$key], $fileNames[$key],  $movieNames[$key], $teacherNames[$key]]);
            }
        }
    } else {
        var_dump('create');
        $stmt = $pdo->prepare("
            INSERT INTO mdl_event (
                name, 
                description, 
                format, 
                categoryid, 
                courseid, 
                groupid, 
                userid, 
                repeatid, 
                modulename, 
                instance, 
                type, 
                eventtype, 
                timestart, 
                timeduration, 
                visible, 
                sequence, 
                timemodified, 
                location, 
                venue, 
                venue_name, 
                target, 
                note, 
                main_img_name, 
                sub_img_name
            ) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $names,
            $description,
            1,
            0,
            1,
            0,
            $userId,
            0,
            0,
            0,
            0,
            'site',
            0000000000,
            0,
            1,
            1,
            0000000000,
            $location,
            $location,
            $location,
            $target,
            $note,
            $mainImgName,
            $subImgName
        ]);
        $eventId = $pdo->lastInsertId();
        foreach ($eachIds as $key => $eachId) {
            $stmt = $pdo->prepare(
                "
                INSERT INTO mdl_event_each (
                    event_id, 
                    start_date, 
                    end_date, 
                    capacity, 
                    files_name, 
                    movie_name,
                    teacher_name
                ) 
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$eventId, $startDates[$key], $endDates[$key], $capacitys[$key], $fileNames[$key],  $movieNames[$key], $teacherNames[$key]]);
        }
    }

    $pdo->commit();

    echo "データが正常に登録されました。";
    echo "<a href='event_customfield.php?id=$eventId'>編集画面へ戻る</a>";
} catch (PDOException $e) {
    $pdo->rollBack();
    die("データベースエラー: " . $e->getMessage());
}
