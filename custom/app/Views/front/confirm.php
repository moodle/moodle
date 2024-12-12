<?php
require_once('/var/www/html/moodle/custom/app/Controllers/EventCustomFieldController.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $eventId = htmlspecialchars($_POST['event_id'], ENT_QUOTES, 'UTF-8');
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $kana = htmlspecialchars($_POST['kana'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $eventName = htmlspecialchars($_POST['event_name'], ENT_QUOTES, 'UTF-8');
    $ticket = htmlspecialchars($_POST['ticket'], ENT_QUOTES, 'UTF-8');
    $price =  $_POST['price'];
    $triggerOther = htmlspecialchars($_POST['trigger_othier'], ENT_QUOTES, 'UTF-8'); // スペルミス
    $payMethod = htmlspecialchars($_POST['pay_method'], ENT_QUOTES, 'UTF-8');
    $aspiration = htmlspecialchars($_POST['aspiration'], ENT_QUOTES, 'UTF-8');
    $note = htmlspecialchars($_POST['note'], ENT_QUOTES, 'UTF-8');
    $otherMails = !empty($_POST['other_mails']) ? implode(',', $_POST['other_mails']) : [];
    $opportunityOther = !empty($_POST['trigger']) ? implode(',', $_POST['trigger']) : [];
} else {
    header("Location: register.php");
    exit;
}

$eventCustomFieldModel = new eventCustomFieldModel();
$eventCustomFieldList = $eventCustomFieldModel->getEventsCustomFieldByEventId($eventId);

$passages = '';
foreach ($eventCustomFieldList as $eventCustomField) {
    $passages .= '<p><strong>' . $eventCustomField['field_name'] . '</strong><br>' . $_POST[$eventCustomField['name']];
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>確認画面</title>
</head>
<!-- スタイルは完全仮の状態なのでとりえず直書きする 後で個別ファイルに記述する -->
<style>
    h2 {
        padding-left: 3rem;
        margin-top: 80px;
        color: #2D287F;
    }

    p {
        margin-bottom: 1rem;
    }

    .passage {
        padding: 1rem 3rem 0rem 3rem;
        font-size: 15px;
        color: 272727;
    }

    .confirm-details {
        padding: 3rem;
        padding-top: 1rem;
    }

    .confirm-details p {
        margin-top: 3.5vh;
    }

    strong {
        color: #2D287F;
    }

    form {
        padding-left: 3rem;
    }
</style>
<?php include('/var/www/html/moodle/custom/app/Views/common/header.php'); ?>
<div class="container">
    <h2>確認画面</h2>
    <p class="passage">以下の内容で登録しますか？</p>

    <div class="confirm-details">
        <p><strong>名前</strong> <br><?php echo $name; ?></p>
        <p><strong>フリガナ</strong> <br><?php echo $kana; ?></p>
        <p><strong>メールアドレス</strong><br> <?php echo $email; ?></p>
        <p><strong>チケット名称</strong><br> <?php echo $eventName; ?></p>
        <p><strong>チケット枚数</strong><br> <?php echo $ticket . '枚'; ?></p>
        <p><strong>金額</strong><br> <?php echo $price . '円'; ?></p>
        <p><strong>本イベントのことはどうやってお知りになりましたか。（複数選択可）</strong><br>
            <?php
            if (is_array($_POST['trigger'])) {
                foreach ($_POST['trigger'] as $trigger) {
                    echo htmlspecialchars($trigger, ENT_QUOTES, 'UTF-8') . "<br>";
                }
            }
            ?>
        <p><strong>その他</strong> <br><?php echo $triggerOther; ?></p>
        <p><strong>支払方法</strong> <br><?php echo $payMethod; ?></p>
        <p><strong>今後、大阪大学からメールによるイベントのご案内を希望されますか</strong><br><?php echo $aspiration; ?></p>
        <p><strong>複数チケット申し込み者の場合、お連れ様のメールアドレス</strong><br>
            <?php
            if (is_array($_POST['other_mails'])) {
                foreach ($_POST['other_mails'] as $otherMail) {
                    echo htmlspecialchars($otherMail, ENT_QUOTES, 'UTF-8') . "<br>";
                }
            }
            ?>
        <p><strong>備考欄</strong><br><?php echo $note; ?></p>
        <?php echo $passages ?>
    </div>

    <form action="event_application_insert.php" method="post">
        <input type="hidden" name="event_id" value="<?php echo $eventId ?>">
        <input type="hidden" name="name" value="<?php echo $name; ?>">
        <input type="hidden" name="kana" value="<?php echo $kana; ?>">
        <input type="hidden" name="email" value="<?php echo $email; ?>">
        <input type="hidden" name="ticket" value="<?php echo $ticket; ?>">
        <input type="hidden" name="price" value="<?php echo $price; ?>">
        <input type="hidden" name="opportunity" value="<?php echo $opportunityOther; ?>">
        <input type="hidden" name="opportunity_other" value="<?php echo $_POST['opportunity_other']; ?>">
        <input type="hidden" name="pay_method" value="<?php echo $_POST['pay_method']; ?>">
        <input type="hidden" name="is_send" value="<?php echo $_POST['is_send']; ?>">
        <input type="hidden" name="other_mail_adress" value="<?php echo $otherMails; ?>">
        <input type="hidden" name="note" value="<?php echo $_POST['note']; ?>">
        <button type="submit">登録する</button>
        <button type="submit" disabled>修正する</button>
    </form>
</div>
</body>
<?php include('/var/www/html/moodle/custom/app/Views/common/footer.php'); ?>

</html>