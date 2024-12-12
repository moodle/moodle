<?php
require '/var/www/vendor/autoload.php';
require_once('/var/www/html/moodle/custom/app/Models/BaseModel.php');
require_once('/var/www/html/moodle/custom/app/Models/EventModel.php');
require_once('/var/www/html/moodle/custom/app/Models/EventPaymentUserModel.php');
require_once('/var/www/html/moodle/custom/app/Models/EventCustomFieldModel.php');

use Dotenv\Dotenv;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv::createImmutable('/var/www/html/moodle/custom');
$dotenv->load();

$eventId = $_POST['event_id'];
$userId = $_SESSION['USER']->id;
$count = (int)$_POST['ticket'];

$baseModel = new BaseModel();
$eventModel = new EventModel();
$eventPaymentUserModel = new eventPaymentUserModel();
$eventCustomFieldModel = new EventCustomFieldModel();
$pdo = $baseModel->getPdo();
$fieldIds = $eventCustomFieldModel->getEventsCustomFieldByEventId($eventId);

// QR生成
$qrCode = new QrCode('https://example.com');
$writer = new PngWriter();
$qrCodeImage = $writer->write($qrCode)->getString();
$temp_file = tempnam(sys_get_temp_dir(), 'qr_');
$qrCodeBase64 = base64_encode($qrCodeImage);
$dataUri = 'data:image/png;base64,' . $qrCodeBase64;
file_put_contents($temp_file, $qrCodeImage);

$event = $eventModel->getEventById($eventId);
if (isset($event['details']['capacity'])) {
    echo '<div>登録に失敗しました</div>'; // TO DO 後で文言統一
    die();
}
$capacity = reset($event['details'])['capacity']; // 今後単件取得が増えたら単件用の処理も作成する
if ($capacity < $count) {
    // echo '<div>登録に失敗しました</div>';
    // die();
}

$mail = new PHPMailer(true);
try {
    $pdo->beginTransaction();
    // 実際は決済完了後に本登録処理を実行する
    $isPayment = ($pay_method == 3) ? 1 : 0; // TO DO マジックナンバー後で一括でまとめる
    $datetime = date('Y-m-d H:i:s');

    $capacitySum = $eventPaymentUserModel->getCapacitySum($eventId);
    if ($capacitySum + $count > $capacity) {
        echo '<div>登録に失敗しました</div>'; // TO DO 後で文言統一
        die();
    }
    $stmt = $pdo->prepare(
        "
        INSERT INTO mdl_event_payment_user (
            created_at, 
            updated_at, 
            event_id, 
            user_id, 
            count, 
            is_payment
        ) 
        VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$datetime, $datetime, $eventId, $userId, $count, $isPayment]);

    // 一時的にコメントアウト( 仕様確定後にコメント化解除 )
    // foreach($fieldIds as $fieldId){
    //     $eventCustomFieldId = $fieldId['id'];
    //     $fieldName= str_replace(['[', ']'], '', $fieldId['name']);
    //     $fieldValue = $_POST[$fieldName];
    //     try {
    //         $stmt = $pdo->prepare("INSERT INTO mdl_event_application (event_id, user_id, event_custom_field_id, field_value) VALUES (?, ?, ?, ?)");
    //         $stmt->execute([$eventId, $userId, $eventCustomFieldId, $fieldValue]);
    //     } catch (PDOException $e) {
    //         echo '登録に失敗しました: ' . $e->getMessage();
    //         exit();
    //     }
    // }

    $mail->isSMTP();
    $mail->Host = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['MAIL_USERNAME'];
    $mail->Password = $_ENV['MAIL_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->CharSet = PHPMailer::CHARSET_UTF8;
    $mail->Port = $_ENV['MAIL_PORT'];

    $mail->setFrom($_ENV['MAIL_FROM_ADRESS'], 'Sender Name');
    $mail->addAddress($_POST['email'], 'Recipient Name');

    $sendAdresses = !empty($_POST['other_mail_adress']) ? explode(',', $_POST['other_mail_adress']) : [];
    foreach ($sendAdresses as $sendAdress) {
        $mail->addAddress($sendAdress, 'Recipient Name');
    }
    $mail->addReplyTo('no-reply@example.com', 'No Reply');
    $mail->isHTML(true);

    // QRをインライン画像で追加
    $mail->addEmbeddedImage($temp_file, 'qr_code_cid', 'qr_code.png');

    $htmlBody = "
        <div style=\"text-align: center; font-family: Arial, sans-serif;\">
            <p style=\"text-align: left; font-weight:bold;\">" . $_POST['name'] . "さん</p>
            <P style=\"text-align: left; font-size: 13px; margin:0; padding:0;\">ご購入ありがとうございます。チケットのご購入が完了いたしました。</P>
            <P style=\"text-align: left;  font-size: 13px; margin:0; margin-bottom: 30px; \">QRはマイページでも確認できます。</P>
            <div>
                <img src=\"cid:qr_code_cid\" alt=\"QR Code\" style=\"width: 150px; height: 150px; display: block; margin: 0 auto;\" />
            </div>
            <p style=\"margin-top: 20px; font-size: 14px;\">利用期間: 2024年12月31日限り</p>
            <p style=\"margin-top: 30px; font-size: 13px; text-align: left;\">このメールは、配信専用アドレスで配信されています。<br>このメールに返信いただいても、返信内容の確認及びご返信ができません。
            あらかじめご了承ください。</p>
        </div>
    ";

    $mail->Subject = 'チケットの購入が完了しました';
    $mail->Body = $htmlBody;

    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->send();
    echo "<div><P>申し込みが完了いたしました。</P><P>メールにQRチケットを送信いたしました。</P><P>ご確認の程よろしくお願いいたします。</P>
            <a href='/custom/app/Views/event/index.php?id=$eventId'>イベント詳細画面へ</a></div>";

    unlink($temp_file);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "登録に失敗しました: {$e}";
}
