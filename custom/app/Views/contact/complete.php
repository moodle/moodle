<?php
require_once('/var/www/html/moodle/config.php');
$eventId = $_POST['event_id'];
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['name']); ?></title>
    <link rel="stylesheet" href="/front/style.css" type="text/css">
</head>
<!-- スタイルは完全仮の状態なのでとりえず直書きする 後で個別ファイルに記述する -->
<style>
    body {
        margin: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: Arial, sans-serif;
    }

    div {
        text-align: center;
    }
</style>

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
    <div>
        <p>お問い合わせいただきありがとうございました</p>
        <a style="margin-top: 2vh; display: inline-block" href="/custom/app/Views/front/index.php">トップページへ戻る</a>
    </div>
</body>