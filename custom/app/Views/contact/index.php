<?php
require_once('/var/www/html/moodle/custom/app/Controllers/SurveyCustomFieldController.php');
$eventId = 2;
$surveyCustomFieldController = new SurveyCustomFieldController();
$responce = $surveyCustomFieldController->getSurveyCustomField($eventId);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせ</title>
    <link rel="stylesheet" href="/front/style.css" type="text/css">
    <!-- スタイルは完全仮の状態なのでとりえず直書きする 後で個別ファイルに記述する -->
    <style>
        form label {
            margin-bottom: 10px;
        }

        form input,
        form textarea,
        form select {
            display: block;
            margin-bottom: 2rem;
        }

        form input,
        select {
            padding: 8px;
            box-sizing: border-box;
        }

        .label_d_flex {
            display: flex;
        }

        .label_d_flex input {
            margin-bottom: 10px;
        }

        .container {
            margin-top: 80px;
        }

        h2 {
            color: #08153A;
        }

        body {
            padding: 3rem;
        }
    </style>
</head>

<body>
    <header>
        <p>大阪大学 動画プラットフォーム</p>
        <?php if ($_SESSION['USER']->id == 0) {  ?>
            <button class="login-button" onclick="window.location.href='/login/index.php'">ログイン</button>
        <?php } else { ?>
            <P class="user_header_p"><?php echo $_SESSION['USER']->lastname . ' ' . $_SESSION['USER']->firstname ?></P>
            <P class="user_header_p"><?php echo $_SESSION['USER']->email ?></P>
            <button class="login-button" onclick="window.location.href='/login/logout.php'">ログアウト</button>
        <?php } ?>
    </header>
    <div class="container">
        <h2>お問い合わせ</h2>
        <form action="complete.php" method="post">
            <label for="name">名前:</label>
            <input type="hidden" name="event_id" value="<?php echo $eventId ?>">
            <input type="text" id="name" readonly name="name" value="<?php echo $_SESSION['USER']->lastname . ' ' . $_SESSION['USER']->firstname ?>" required>
            <label for="email">メールアドレス:</label>
            <input type="email" id="email" readonly name="email" value="<?php echo $_SESSION['USER']->email ?>" required>
            <label for="email">お問い合わせの項目について:</label>
            <select name="heading">
                <option>募集中・開始前のイベントAについて</option>
                <option>募集中・開始前のイベントBについて</option>
                <option>募集中・開始前のイベントCについて</option>
                <option>会員登録前のご質問</option>
                <option>その他一般的なお問い合わせ</option>
            </select>
            <label for="email">お問い合わせ内容:</label>
            <textarea name="hostlist" cols="40" rows="20"><?php echo $trusted_hosts; ?></textarea>
            <button type="submit">送信</button>
        </form>
    </div>
</body>

</html>