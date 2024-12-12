<?php
require_once('/var/www/html/moodle/config.php');
require_once('/var/www/html/moodle/custom/app/Controllers/FrontController.php');

$eventId = $_GET['id'];
$frontController = new FrontController();
$responce = $frontController->index($eventId);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/custom/public/css/style.css" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>イベント一覧</title>
</head>

<!-- スタイルは完全仮の状態なのでとりえず直書きする 後で個別ファイルに記述する -->
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0px;
        max-width: 1000px;
    }

    th,
    td {
        border: 1px solid black;
        text-align: left;
        /* 左寄せに変更 */
        padding: 8px;
    }

    th {
        background-color: #f2f2f2;
        /* ヘッダー部分に背景色を追加 */
        width: 30%;
    }

    td {
        width: 70%;
    }

    .table_area {
        margin: 120px auto auto auto;
        width: 60%;
    }

    input {
        width: 90%;
        padding: .5rem;
    }

    .submit_button {
        display: flex;
        margin-top: 2vh;
        justify-content: center;
    }

    .card {
        width: 300px;
        height: 150px;
        background-image: linear-gradient(-225deg, #2CD8D5 0%, #C5C1FF 56%, #FFBAC3 100%);
        border: 1px solid #0aa6cbad;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        font-family: 'Arial', sans-serif;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }

    .card:hover {
        transform: translateY(4px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .name {
        font-size: 24px;
        font-weight: bold;
        color: #ffffff;
    }

    .sub-text {
        font-size: 13px;
        color: #ffffff;
        margin-top: 8px;
    }

    .card_area {
        display: flex;
        justify-content: center;
    }
</style>

<body>
    <header>
        <p>大阪大学 動画プラットフォーム</p>
        <?php if ($_SESSION['USER']->id == 0) {  ?>
            <button class="login-button mypage_button">会員登録</button>
            <button class="login-button" onclick="window.location.href='/login/index.php'">ログイン</button>
        <?php } else { ?>
            <button class="login-button mypage_button" onclick="window.location.href='/custom/app/Views/mypage/index.php'">マイページ</button>
            <button class="login-button" onclick="window.location.href='/login/logout.php'">ログアウト</button>
        <?php } ?>
    </header>

    <div class="table_area">
        <h2>ユーザー情報</h2>
        <table>
            <tr>
                <th>氏名</th>
                <td><input type="text" name="city" value="<?php echo $_SESSION['USER']->lastname . ' ' . $_SESSION['USER']->firstname;  ?>" </td>
            </tr>
            <tr>
                <th>フリガナ</th>
                <td><input type="text" name="kana" value="ナカモリ タモン"></input></td>
            </tr>
            <tr>
                <th>都道府県</th>
                <td><input type="text" name="city" value="<?php echo $_SESSION['USER']->city ?>"></input></td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td><input type="text" name="email" value="<?php echo $_SESSION['USER']->email ?>"></input></td>
            </tr>
            <tr>
                <th>電話番号</th>
                <td><input type="text" name="email" value="000-0000-0000"></input></td>
            </tr>
            <tr>
                <th>ハスワード</th>
                <td><input type="password" name="email" value="password"></input></td>
            </tr>
        </table>
        <div class="submit_button">
            <button type="submit">編集する</button>
        </div>

        <h2 style="text-align: center; margin-top: 5rem">会員証</h2>
        <div class="card_area">
            <div class="card">
                <div class="name">Nakamori Tamon</div>
                <div class="sub-text">会員番号: 121 1235 1234</div>
            </div>
        </div>
    </div>



</body>

</html>