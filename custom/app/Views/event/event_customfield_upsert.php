<?php
require_once("/var/www/html/moodle/config.php");

$host = $CFG->dbhost;
$dbname = $CFG->dbname;
$username = $CFG->dbuser;
$password = $CFG->dbpass;

// PDOでデータベース接続
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo '接続失敗: ' . $e->getMessage();
    exit();
}

// フォームデータの取得
$fieldNames = $_POST['fieldName'];
$names = $_POST['name'];
$sorts = $_POST['sort'];
$fieldTypes = $_POST['fieldType'];
$fieldOptions = $_POST['fieldOptions'];
$eventId = $_POST['eventId'];
$ids = $_POST['id'];
// データベースにデータを挿入
try {
    for ($i = 0; $i < count($fieldNames); $i++) {
        $id = $ids[$i];
        $fieldName = $fieldNames[$i];
        $name = $names[$i];
        $sort = $sorts[$i];
        $fieldType = $fieldTypes[$i];
        $fieldOption = isset($fieldOptions[$i]) ? $fieldOptions[$i] : null;

        $stmt = $pdo->prepare("SELECT id FROM mdl_event_customfield WHERE event_id = ?");
        $stmt->execute([$eventId]);
        $fieldIds = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($id || $id != 0) {
            $stmt = $pdo->prepare("UPDATE mdl_event_customfield 
            SET field_name = ?, 
                name = ?, 
                sort = ?, 
                field_type = ?, 
                field_options = ? 
            WHERE id = ?");
            $stmt->execute([$fieldName, $name, $sort, $fieldType, $fieldOption, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO mdl_event_customfield (event_id, field_name, name, sort, field_type, field_options) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$eventId, $fieldName, $name, $sort, $fieldType, $fieldOption]);
        }

        foreach ($fieldIds as $fieldId) {
            if (!empty($ids) && !in_array($fieldId['id'], $ids)) {
                $stmt = $pdo->prepare("UPDATE mdl_event_customfield 
                SET is_delete = ?
                WHERE id = ?");
                $stmt->execute([true, $fieldId['id']]);
            }
        }
    }

    echo "データが正常に登録されました。";
    echo "<a href='event_customfield.php?id=$eventId'>編集画面へ戻る</a>";
} catch (PDOException $e) {
    die("データベースエラー: " . $e->getMessage());
}
