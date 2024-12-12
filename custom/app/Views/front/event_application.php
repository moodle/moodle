<?php
require_once('/var/www/html/moodle/custom/app/Controllers/EventCustomFieldController.php');
$eventId = $_GET['id'];
$eventCustomFieldController = new EventCustomFieldController();
$responce = $eventCustomFieldController->getEventCustomField($eventId);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お申込みフォーム</title>
    <!-- スタイルは完全仮の状態なのでとりえず直書きする 後で個別ファイルに記述する -->
    <style>
        form label {
            margin-bottom: 10px;
            display: inline-block;
        }

        form input,
        form textarea,
        form select {
            display: block;
            margin-bottom: 2rem;
            width: 50%;
        }

        form input,
        form select {
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
            color: #2D287F;
        }

        .label_name {
            color: #2D287F;
            font-weight: bold;
        }

        form {
            padding: 3rem
        }

        h2 {
            padding-left: 3rem;
            margin-top: 80px;
            color: #2D287F;
        }

        form textarea {
            height: 10vh;
        }

        .radio-group {
            display: flex;
        }

        .radio-group label {
            display: inline-block;
            margin-right: 20px;
        }

        .checkbox-group label {
            display: inline-block;
            margin-bottom: 0px;
        }

        .checkbox-group input,
        .radio-group input {
            display: initial;
            width: initial;
            margin-right: .5rem;
        }

        #submit {
            background-color: #5b5b5b;
        }
    </style>
</head>
<?php include('/var/www/html/moodle/custom/app/Views/common/header.php'); ?>
<div class="container">
    <h2>お申込みフォーム</h2>
    <form action="/custom/app/Views/front/confirm.php" method="post">
        <input type="hidden" name="event_id" value="<?php echo $eventId ?>">
        <label class="label_name" for="name">名前</label>
        <input type="text" id="name" readonly name="name" value="<?php echo $_SESSION['USER']->lastname . ' ' . $_SESSION['USER']->firstname ?>" required>
        <label class="label_name" for="kana">フリガナ</label>
        <input type="text" id="name" readonly name="kana" value="ナカモリ タモン" required>
        <label class="label_name" for="email">メールアドレス</label>
        <input type="email" id="email" readonly name="email" value="<?php echo $_SESSION['USER']->email ?>" required>
        <label class="label_name" for="email">チケット名称</label>
        <input type="event_name" name="event_name" value="<?php echo $responce['event']['name'] ?>">
        <label class="label_name" for="email">チケット枚数</label>
        <input type="number" name="ticket" value="">
        <label class="label_name" for="price">金額</label>
        <input type="number" name="price" readonly value="">
        <label class="label_name" for="trigger">本イベントのことはどうやってお知りになりましたか</label>
        <div class="checkbox-group">
            <label>
                <input type="checkbox" checked name="trigger[]" value="チラシ(その他の欄にどこでご覧になったかをご記入ください)"><span>チラシ(その他の欄にどこでご覧になったかをご記入ください)</span>
            </label><br>
            <label>
                <input type="checkbox" name="trigger[]" value="ウェブサイト(その他の欄にウェブサイト名をご記入ください)"><span>ウェブサイト(その他の欄にウェブサイト名をご記入ください)</span>
            </label><br>
            <label>
                <input type="checkbox" name="trigger[]" value="本プラットフォームからのメール"><span>本プラットフォームからのメール</span>
            </label><br>
            <label>
                <input type="checkbox" name="trigger[]" value="SNS(X,Instagram,Facebookなど)"><span>SNS(X,Instagram,Facebookなど)</span>
            </label><br>
            <label>
                <input type="checkbox" name="trigger[]" value="21世紀懐徳堂からのメールマガジン"><span>21世紀懐徳堂からのメールマガジン</span>
            </label><br>
            <label>
                <input type="checkbox" name="trigger[]" value="大阪大学卒業生メールマガジン"><span>大阪大学卒業生メールマガジン</span>
            </label><br>
            <label>
                <input type="checkbox" name="trigger[]" value="Peatixからのメール"><span>Peatixからのメール</span>
            </label><br>
            <label>
                <input type="checkbox" name="trigger[]" value="知人からの紹介"><span>知人からの紹介</span>
            </label><br>
            <label>
                <input type="checkbox" name="trigger[]" value="講師・スタッフからの紹介"><span>講師・スタッフからの紹介</span>
            </label><br>
            <label>
                <input type="checkbox" name="trigger[]" value="自治体の広報・掲示"><span>自治体の広報・掲示</span>
            </label><br>
        </div>
        <label class="label_name" for="trigger_othier">その他</label>
        <textarea row="20px" name="trigger_othier"></textarea>
        <label class="label_name" style="width: 100%" for="pay_method">支払方法</label>
        <div class="radio-group">
            <label>
                <input type="radio" checked name="pay_method" value="クレジットカード">クレジットカード
            </label><br>
            <label>
                <input type="radio" name="pay_method" value="コンビニ払い">コンビニ払い
            </label><br>
            <label>
                <input type="radio" name="pay_method" value="口座振替">口座振替
            </label><br>
        </div>
        <label class="label_name" style="width: 100%" for="aspiration">今後大阪大学からメールによるイベントのご案内を希望されますか</label>
        <div class="radio-group">
            <label>
                <input type="radio" checked name="aspiration" value="はい">はい
            </label><br>
            <label>
                <input type="radio" name="aspiration" value="いいえ">いいえ
            </label><br>
        </div>
        <label class="label_name" for="other_mails">複数チケット申し込み者の場合、お連れ様のメールアドレス</label>
        <div style="display:flex">
            <input type="mail" style="margin-right: 2rem" name="other_mails[]" value=""><button id="add_email">追加</button>
        </div>
        <label id="note" class="label_name" for="note">備考欄</label>
        <textarea row="20px" name="note"></textarea>
        <?php echo $responce['passage'] ?><br>
        <div class="radio-group">
            <label>
                <input id="consent" type="checkbox" name="consent" value="1"><span style="font-weight: bold; color: #2D287F;">この申し込みは保護者の許可を得ています</span>
            </label><br>
        </div>
        <button id="submit" disabled type="submit">確認画面へ</button>
    </form>
</div>
</body>
<?php include('/var/www/html/moodle/custom/app/Views/common/footer.php'); ?>

</html>
<script>
    // ブラウザバック対応
    $(window).on('pageshow', function() {
        if ($('#consent').length > 0) {
            $('#consent').prop('checked', false);
            $('#submit').prop('disabled', true);
        }
        const price = 5000 * $('input[name="ticket"]').val();
        $('input[name="price"]').val(price);
    });
    $('input[name="ticket"]').on('change', function() {
        const price = 5000 * $(this).val();
        $('input[name="price"]').val(price);
    });
    $('#add_email').on('click', function() {
        event.preventDefault();
        const elem = '<input type="mail" name="other_mails[]" value="">';
        $("#note").before(elem);
    });
    $('#consent').change(function() {
        if ($(this).prop('checked')) {
            $('#submit').css('background-color', '#2D287F');
            $('#submit').prop('disabled', false);
        } else {
            $('#submit').css('background-color', '#5b5b5b');
            $('#submit').prop('disabled', true);
        }
    });
</script>