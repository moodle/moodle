<?php
require_once('/var/www/html/moodle/custom/app/Controllers/EventController.php');

$eventId = $_GET['id'];
$eventController = new EventController();
$event = $eventController->getEventDetails($eventId);
$detail = reset($event['details']);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['name']); ?></title>
</head>

<!-- スタイルは完全仮の状態なのでとりえず直書きする 後で個別ファイルに記述する -->
<style>
    h2 {
        margin-top: 80px;
        margin-bottom: 3rem;
        text-align: center;
        color: #08153A;
    }

    .event_detail {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-evenly;
        margin: auto;
    }

    .event_detail div {
        width: 45%;
        border: 1px solid #bbbbbb;
        padding: 3rem;
        border-radius: 20px;
        margin-bottom: 3rem;
    }

    .event_detail .title {
        font-size: 20px;
        margin-bottom: 1rem;
    }

    img {
        width: 50px;
    }

    .siryou {
        text-align: center;
        transition: box-shadow 0.3s ease, transform 0.3s ease;
    }

    .siryou:hover {
        box-shadow: 0px 10px 15px rgba(0, 0, 0, 0.2),
            0px 4px 6px rgba(0, 0, 0, 0.15);
        transform: translateY(-5px);
    }

    .siryou a {
        display: flex;
        align-items: center;
        color: black;
        text-decoration: none;
    }

    ul {
        padding-left: 0px;
    }

    .download {
        margin-bottom: 10px;
    }

    .button_area {
        display: flex;
        justify-content: space-evenly;
    }
</style>
<?php include('/var/www/html/moodle/custom/app/Views/common/header.php'); ?>
<h2><?php echo htmlspecialchars($responce['event']['name']); ?></h2>
<div class="event_detail">
    <?php if ($detail['files_name']) { ?>
        <div>
            <P class="title">講義資料</P>
            <P class="download">クリックしてダウンロードされないようにします</P>
            <div class="siryou">
                <a target="_blank" href="/custom/upload/file/<?php echo urlencode($detail['files_name']); ?>">
                    <img src="/custom/public/images/note_hoso.svg">
                    <span style="margin-left: 5px;"><?php echo htmlspecialchars($detail['files_name']) ?></span>
                </a>
            </div>
        </div>
    <?php } else {
        echo "<p>資料はまだアップロードされていません。</p>";
    } ?>
    <div>
        <P class="title">講義動画</P>
        <?php if ($detail['movie_name']) { ?>
            <ul>
                <video width="100%" height="360" controls>
                    <source src="/custom/upload/movie/<?php echo urlencode($detail['movie_name']); ?>" type="video/mp4">
                    このブラウザでは動画を再生できません。
                </video>
            </ul>
        <?php } else {
            echo "このイベントに動画はありません。";
        }
        ?>
    </div>
</div>


<div class="button_area">
    <div><button onclick="window.location.href='/custom/app/Views/survey/survey_application.php?id=<?php echo $eventId; ?>'">アンケートに回答する</button></div>
</div>
<div style="margin-top: 5vh; text-align: center">
    <a href="/custom/app/Views/event/upsert.php?id=<?php echo $eventId ?>">イベント登録</a>
    <a href="/custom/event/file_upload.php?id=<?php echo $eventId ?>">資料アップロード</a>
    <a href="/custom/event/video_upload.php?id=<?php echo $eventId ?>">動画アップロード</a>
    <a href="/custom/app/Views/event/event_customfield.php?id=<?php echo $eventId ?>">イベントカスタムフィールド作成</a>
    <a href="/custom/app/Views/survey/survey_customfield.php?id=<?php echo $eventId ?>">アンケートカスタムフィールド作成</a>
</div>
</body>

</html>