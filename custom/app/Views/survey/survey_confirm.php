<?php
require_once('/var/www/html/moodle/custom/app/Controllers/EventCustomFieldController.php');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $eventId = htmlspecialchars($_POST['event_id'], ENT_QUOTES, 'UTF-8');
    $impression = htmlspecialchars($_POST['impression'], ENT_QUOTES, 'UTF-8');
    $participationExperience = htmlspecialchars($_POST['participation_experience'], ENT_QUOTES, 'UTF-8');
    $reasonOther =  htmlspecialchars($_POST['reason_other'], ENT_QUOTES, 'UTF-8');
    $understand = htmlspecialchars($_POST['understand'], ENT_QUOTES, 'UTF-8');
    $goodPoint = htmlspecialchars($_POST['good_point'], ENT_QUOTES, 'UTF-8');
    $goodPointOther = htmlspecialchars($_POST['good_point_other'], ENT_QUOTES, 'UTF-8');
    $timePoint = htmlspecialchars($_POST['time_point'], ENT_QUOTES, 'UTF-8');
    $envlonment = htmlspecialchars($_POST['envlonment'], ENT_QUOTES, 'UTF-8');
    $envlonmentReason = htmlspecialchars($_POST['envlonment_reason'], ENT_QUOTES, 'UTF-8');
    $occupation = htmlspecialchars($_POST['occupation'], ENT_QUOTES, 'UTF-8');
    $area = htmlspecialchars($_POST['area'], ENT_QUOTES, 'UTF-8');
} else {
    header("Location: register.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>確認画面</title>
    <link rel="stylesheet" href="/front/style.css" type="text/css">
</head>
<!-- スタイルは完全仮の状態なのでとりえず直書きする 後で個別ファイルに記述する -->
<style>
    h2 {
        margin-top: 80px;
        color: #08153A;
    }

    body {
        padding: 3rem;
    }

    p {
        margin-bottom: 1rem;
    }

    .passage {
        font-size: 18px, ;
        margin-bottom: 2rem;
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
    <div class="container">
        <h2>確認画面</h2>
        <p class="passage">以下の内容で登録しますか？</p>

        <div class="confirm-details">
            <p><strong>名前</strong> <br><?php echo $name; ?></p>
            <p><strong>メールアドレス</strong><br> <?php echo $email; ?></p>
            <p><strong>本日の講義内容について、ご意見・ご感想をお書きください</strong><br><?php echo $impression; ?></p>
            <p><strong>今までに大阪大学公開講座のプログラムに参加されたことはありますか</strong> <br><?php echo $participationExperience; ?></p>
            <p>
                <strong>
                    今回が初回受講の方は、以下の質問にすべてご回答ください。これまでアンケートにご協力いただいた方の回答は任意です。<br>
                    本日のプログラムをどのようにしてお知りになりましたか。（複数回答可）
                </strong><br>
                <?php
                $oppprtunityPassage = '';
                if (is_array($_POST['opportunity'])) {
                    foreach ($_POST['opportunity'] as $opportunity_item) {
                        $oppprtunityPassage .= $opportunity_item . ',';
                        echo htmlspecialchars($opportunity_item, ENT_QUOTES, 'UTF-8') . "<br>";
                    }
                }
                ?>
            </p>
            <p><strong>本日のテーマを受講した理由は何ですか？（複数回答可）</strong><br>
                <?php
                $oppprtunityPassage = '';
                if (is_array($_POST['reason'])) {
                    foreach ($_POST['reason'] as $opportunity_item) {
                        $oppprtunityPassage .= $opportunity_item . ',';
                        echo htmlspecialchars($opportunity_item, ENT_QUOTES, 'UTF-8') . "<br>";
                    }
                }
                ?>
            </p>
            <p><strong>その他</strong><br><?php echo $reasonOther; ?></p>
            <p><strong>本日のプログラムの理解度について、あてはまるもの１つをお選びください</strong><br><?php echo $understand; ?></p>
            <p>
                <strong>
                    本日のプログラムで特に良かった点について教えてください。以下にあてはまるものがあれば、一つお選びください。<br>
                    あてはまるものがなければ、「その他」の欄に記述してください<br>
                </strong> <?php echo $goodPoint; ?>
            </p>
            <p><strong>その他</strong><br><?php echo $goodPointOther; ?></p>
            <p><strong>本日のプログラムの開催時間(○○分)について、あてはまるもの１つをお選びください</strong><br><?php echo $timePoint; ?></p>
            <p>
                <strong>
                    本日のプログラムの開催環境について、あてはまるものを１つお選びください。 <br>
                    ※「あまり快適ではなかった」「全く快適ではなかった」と回答された方は次の質問にその理由を教えてください
                </strong><br><?php echo $envlonment; ?>
            </p>
            <p><strong>問9で「あまり快適ではなかった」「全く快適ではなかった」と回答された方はその理由を教えてください</strong><br><?php echo $envlonmentReason; ?></p>
            <p><strong>ご職業等を教えてください</strong><br><?php echo $occupation; ?></p>
            <p><strong>お住いの地域を教えてください（〇〇県△△市のようにご回答ください）</strong><br><?php echo $area; ?></p>


        </div>

        <!-- <form action="survey_application_insert.php" method="post"> -->
        <form action="complete.php" method="post">
            <input type="hidden" name="event_id" value="<?php echo $eventId ?>">
            <input type="hidden" name="name" value="<?php echo $name; ?>">
            <input type="hidden" name="email" value="<?php echo $email; ?>">

            <input type="hidden" name="impression" value="<?php echo $_POST['impression']; ?>">
            <input type="hidden" name="participation_experience" value="<?php echo $_POST['participation_experience']; ?>">
            <input type="hidden" name="opportunity" value="<?php echo $_POST['opportunity']; ?>">

            <input type="hidden" name="reason" value="<?php echo $_POST['reason']; ?>">
            <input type="hidden" name="reason_other" value="<?php echo $_POST['reason_other']; ?>">
            <input type="hidden" name="understand" value="<?php echo $_POST['understand']; ?>">

            <input type="hidden" name="good_point" value="<?php echo $_POST['good_point']; ?>">
            <input type="hidden" name="good_point_other" value="<?php echo $_POST['good_point_other']; ?>">
            <input type="hidden" name="time_point" value="<?php echo $_POST['time_point']; ?>">

            <input type="hidden" name="envlonment" value="<?php echo $_POST['envlonment']; ?>">
            <input type="hidden" name="envlonment_reason" value="<?php echo $_POST['envlonment_reason']; ?>">
            <input type="hidden" name="occupation" value="<?php echo $_POST['occupation']; ?>">
            <input type="hidden" name="area" value="<?php echo $_POST['area']; ?>">

            <button type="submit">登録する</button>
            <button type="submit" disabled>修正する</button>
        </form>
    </div>
</body>

</html>