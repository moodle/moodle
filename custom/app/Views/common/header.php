<?php
require_once('/var/www/html/moodle/config.php');

// CSRF動的トークン生成
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow"> <!-- releaseまではnoindex設定 !-->
    <link rel="stylesheet" href="/custom/public/css/style.css" type="text/css">
    <link rel="stylesheet" href="/custom/public/css/common.css" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <title>イベント一覧</title>
</head>

<body>
    <header>
        <div class="header_flex">
            <div class="header_title_area">
                <p class="header_title">大阪大学</p>
                <p class="header_sub_title">OSAKA UNIVERSITY</p>
            </div>
            <div>
                <?php if ($_SESSION['USER']->id == 0) {  ?>
                    <button>会員登録</button>
                    <button class="login_button" onclick="window.location.href='/login/index.php'">ログイン</button>
                <?php } else { ?>
                    <button onclick="window.location.href='/custom/app/Views/mypage/index.php'">マイページ</button>
                    <button class="login_button" onclick="window.location.href='/login/logout.php'">ログアウト</button>
                <?php } ?>
            </div>
        </div>
        <div class="sec_header">
            <a class="login-button" onclick="window.location.href='/login/index.php'">講座一覧</a>
            <a>ご利用方法</a>
            <a>よくある質問</a>
            <a>お知らせ</a>
            <a onclick="window.location.href='/custom/app/Views/contact/index.php'">お問い合わせ</a>
            <input type="text" name="keyword" placeholder="講座を検索する">
        </div>
    </header>