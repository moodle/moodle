<?php
require_once('/var/www/html/moodle/custom/app/Controllers/FrontController.php');
$eventId = $_GET['id'];
$frontController = new FrontController();
$event = $frontController->detail($eventId);
$detailList = $event['details'];
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/custom/public/css/style.css" type="text/css">
    <title>イベント詳細</title>
</head>
<!-- スタイルは完全仮の状態なのでとりえず直書きする 後で個別ファイルに記述する -->
<style>
    h2 {
        color: #08153a;
    }

    p {
        color: #272727;
    }
</style>

<?php include('/var/www/html/moodle/custom/app/Views/common/header.php'); ?>
<div class="event_detail_area">
    <p class="event_detail_title">講座詳細</p>
    <div class="event_detail_container">
        <img src="/custom/upload/img/<?php echo htmlspecialchars($event['sub_img_name']) ?>" />
    </div>
    <p style="margin-top:3vh; font-size: 25px; font-weight:bold;"><?php echo htmlspecialchars($event['name']) ?></p>
    <h2><?php echo $responce['event']['name'] ?></h2>
    <P style="width: 50%; margin: initial; font-size: 18px; font-weight: bold; margin-top: 3vh; border-bottom: 1px solid #c9c9c9;">講座概要</P>
    <P style="width: 50%; margin: initial; margin-top: 1vh;">
        <?php echo nl2br(htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8')) ?>
    </P>
    <P style="width: 50%; margin: initial; font-size: 18px; font-weight: bold; margin-top: 3vh; border-bottom: 1px solid #c9c9c9;">お申し込みについて</P>
    <P style="width: 50%; margin: initial; margin-top: 1vh;">
        <spam>【定員】</spam><?php echo htmlspecialchars(reset($detailList)['capacity'] . '名') ?>
    </P>
    <P style="width: 50%; margin: initial; margin-top: 1vh;">
        <spam>【会場】</spam>
        <?php $venue = $event['venue'] == 1 ? 'オンライン' : ($event['venue'] == 2 ? 'オンデマンド' : $event['venue_name']); ?>
        <?php echo htmlspecialchars($venue) ?>
    </P>
    <P style="width: 50%; margin: initial; margin-top: 1vh;">
        <spam>【受講対象】</spam><?php echo htmlspecialchars($event['target']) ?>
    </P>
    <P style="width: 50%; margin: initial; margin-top: 1vh;">
        <spam>【受付状態】</spam><?php echo htmlspecialchars($event['target']) ?>
    </P>
    <P style="width: 50%; margin: initial; margin-top: 1vh;">
        <spam>【備考】</spam><?php echo htmlspecialchars($event['note']) ?>
    </P>
    <table>
        <tr>
            <th>料金区分</th>
            <th>受講料</th>
            <th>講師名</th>
            <th></th>
        </tr>
        <?php foreach ($detailList as $key => $detail) { ?>
            <tr>
                <td>第<?php echo $key + 1 ?>回講座</td>
                <td>5,000円</td>
                <td><?php echo htmlspecialchars($detail['teacher_name']) ?></td>
                <?php if ($_SESSION['USER']->id): ?>
                    <td>
                        <button class="login-button" onclick="window.location.href='/custom/app/Views/front/event_application.php?id=<?php echo $eventId; ?>';">
                            お申込み画面へ
                        </button>
                    </td>
                <?php else: ?>
                    <td>
                        <button class="login-button" onclick="window.location.href='/login/index.php';">
                            お申込み画面へ
                        </button>
                    </td>
                <?php endif; ?>
            </tr>
        <?php } ?>
        <tr>
            <td>まとめてお申込み</td>
            <td>45,000円</td>
            <td>田中 太郎, 大野 次郎, 佐藤 連,鈴木 凛</td>
            <?php if ($_SESSION['USER']->id): ?>
                <td>
                    <button class="login-button" onclick="window.location.href='/custom/app/Views/front/event_application.php?id=<?php echo $eventId; ?>';">
                        お申込み画面へ
                    </button>
                </td>
            <?php else: ?>
                <td>
                    <button class="login-button" onclick="window.location.href='/login/index.php';">
                        お申込み画面へ
                    </button>
                </td>
            <?php endif; ?>
        </tr>
    </table>
</div>
</body>
<?php include('/var/www/html/moodle/custom/app/Views/common/footer.php'); ?>