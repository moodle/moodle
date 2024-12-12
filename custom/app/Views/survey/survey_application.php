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
            flex-wrap: wrap;
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
    <form action="/custom/app/Views/survey/survey_confirm.php" method="post">
        <input type="hidden" name="event_id" value="<?php echo $eventId ?>">
        <label class="label_name" for="name">本日の講義内容について、ご意見・ご感想をお書きください </label>
        <textarea row="20px" name="feel"></textarea>
        <label class="label_name" for="participation_experience">今までに大阪大学公開講座のプログラムに参加されたことはありますか</label>
        <div class="radio-group">
            <label>
                <input type="radio" checked name="participation_experience" value="はい">はい
            </label><br>
            <label>
                <input type="radio" name="participation_experience" value="いいえ">いいえ
            </label><br>
        </div>
        <label class="label_name" for="opportunity">本日のプログラムをどのようにしてお知りになりましたか</label>
        <div class="checkbox-group">
            <label>
                <input type="checkbox" checked name="opportunity[]" value="チラシ(その他の欄にどこでご覧になったかをご記入ください)"><span>チラシ(その他の欄にどこでご覧になったかをご記入ください)</span>
            </label><br>
            <label>
                <input type="checkbox" name="opportunity[]" value="ウェブサイト(その他の欄にウェブサイト名をご記入ください)"><span>ウェブサイト(その他の欄にウェブサイト名をご記入ください)</span>
            </label><br>
            <label>
                <input type="checkbox" name="opportunity[]" value="本プラットフォームからのメール"><span>本プラットフォームからのメール</span>
            </label><br>
            <label>
                <input type="checkbox" name="opportunity[]" value="SNS(X,Instagram,Facebookなど)"><span>SNS(X,Instagram,Facebookなど)</span>
            </label><br>
            <label>
                <input type="checkbox" name="opportunity[]" value="21世紀懐徳堂からのメールマガジン"><span>21世紀懐徳堂からのメールマガジン</span>
            </label><br>
            <label>
                <input type="checkbox" name="opportunity[]" value="大阪大学卒業生メールマガジン"><span>大阪大学卒業生メールマガジン</span>
            </label><br>
            <label>
                <input type="checkbox" name="opportunity[]" value="Peatixからのメール"><span>Peatixからのメール</span>
            </label><br>
            <label>
                <input type="checkbox" name="opportunity[]" value="知人からの紹介"><span>知人からの紹介</span>
            </label><br>
            <label>
                <input type="checkbox" name="opportunity[]" value="講師・スタッフからの紹介"><span>講師・スタッフからの紹介</span>
            </label><br>
            <label>
                <input type="checkbox" name="opportunity[]" value="自治体の広報・掲示"><span>自治体の広報・掲示</span>
            </label><br>
            <label>
                <input type="checkbox" name="opportunity[]" value="スマートニュース広告"><span>スマートニュース広告</span>
            </label><br>
        </div>
        <label class="label_name" for="opportunity_other">その他</label>
        <textarea row="20px" name="feel"></textarea>
        <label class="label_name" for="reason">本日のテーマを受講した理由は何ですか</label>
        <div class="checkbox-group">
            <label>
                <input type="checkbox" checked name="reason[]" value="テーマに関心があったから"><span>テーマに関心があったから</span>
            </label><br>
            <label>
                <input type="checkbox" name="reason[]" value="本日のプログラム内容に関心があったから"><span>本日のプログラム内容に関心があったから</span>
            </label><br>
            <label>
                <input type="checkbox" name="reason[]" value="本日のゲストに関心があったから"><span>本日のゲストに関心があったから</span>
            </label><br>
            <label>
                <input type="checkbox" name="reason[]" value="大阪大学のプログラムに参加したかったから"><span>大阪大学のプログラムに参加したかったから</span>
            </label><br>
            <label>
                <input type="checkbox" name="reason[]" value="教養を高めたいから"><span>教養を高めたいから</span>
            </label><br>
            <label>
                <input type="checkbox" name="reason[]" value="仕事に役立つと思われたから"><span>仕事に役立つと思われたから</span>
            </label><br>
            <label>
                <input type="checkbox" name="reason[]" value="日常生活に役立つと思われたから"><span>日常生活に役立つと思われたから</span>
            </label><br>
            <label>
                <input type="checkbox" name="reason[]" value="余暇を有効に利用したかったから"><span>余暇を有効に利用したかったから</span>
            </label><br>
        </div>
        <label class="label_name" for="reason_other">その他</label>
        <textarea row="20px" name="reason_other"></textarea>
        <label class="label_name" for="satisfaction">本日のプログラムの満足度について、あてはまるものを1つお選びください</label>
        <div class="radio-group">
            <label>
                <input type="radio" checked name="satisfaction" value="非常に満足"><span>非常に満足</span>
            </label><br>
            <label>
                <input type="radio" name="satisfaction" value="満足"><span>満足</span>
            </label><br>
            <label>
                <input type="radio" name="satisfaction" value="ふつう"><span>ふつう</span>
            </label><br>
            <label>
                <input type="radio" name="satisfaction" value="不満"><span>不満</span>
            </label><br>
            <label>
                <input type="radio" name="satisfaction" value="非常に不満"><span>非常に不満</span>
            </label><br>
        </div>
        <label class="label_name" for="understand">本日のプログラムの理解度について、あてはまるものを1つお選びください</label>
        <div class="radio-group">
            <label>
                <input type="radio" checked name="understand" value="よく理解できた"><span>よく理解できた</span>
            </label><br>
            <label>
                <input type="radio" name="understand" value="理解できた"><span>理解できた</span>
            </label><br>
            <label>
                <input type="radio" name="understand" value="ふつう"><span>ふつう</span>
            </label><br>
            <label>
                <input type="radio" name="understand" value="理解できなかった"><span>理解できなかった</span>
            </label><br>
            <label>
                <input type="radio" name="understand" value="全く理解できなかった"><span>全く理解できなかった</span>
            </label><br>
        </div>
        <label class="label_name" for="good_point">本日のプログラムで特に良かった点について教えてください。以下に当てはまるものがあれば一つお選びください<br>
            あてはまるものがなければ、「その他」の欄に記述してください。
        </label>
        <div class="radio-group">
            <label>
                <input type="radio" checked name="good_point" value="テーマについて考えを深めることができた"><span>テーマについて考えを深めることができた</span>
            </label><br>
            <label>
                <input type="radio" name="good_point" value="最先端の研究について学べた"><span>最先端の研究について学べた</span>
            </label><br>
            <label>
                <input type="radio" name="good_point" value="大学の研究者と対話ができた"><span>大学の研究者と対話ができた</span>
            </label><br>
            <label>
                <input type="radio" name="good_point" value="大学の講義の雰囲気を味わえた"><span>大学の講義の雰囲気を味わえた</span>
            </label><br>
            <label>
                <input type="radio" name="good_point" value="全く理解できなかった"><span>大阪大学について知ることができた</span>
            </label><br>
            </label><br>
            <label>
                <input type="radio" name="good_point" value="身の回りの社会課題に対する解決のヒントが得られた"><span>身の回りの社会課題に対する解決のヒントが得られた</span>
            </label><br>
        </div>
        <label class="label_name" for="reason_other">その他</label>
        <textarea row="20px" name="reason_other"></textarea>
        <label class="label_name" for="satisfaction">本日のプログラムの開催時間(〇〇分)について,あてはまるものを1つお選びください。</label>
        <div class="radio-group">
            <label>
                <input type="radio" checked name="satisfaction" value="適当である"><span>適当である</span>
            </label><br>
            <label>
                <input type="radio" name="satisfaction" value="満足"><span>長すぎる</span>
            </label><br>
            <label>
                <input type="radio" name="satisfaction" value="ふつう"><span>短すぎる</span>
            </label><br>
            <label>
        </div>
        <label class="label_name" for="envilonment">本日のプログラムの開催環境について、あてはまるものを1つお選びください<br>
            「あまり快適ではなかった」「全く回出来ではなかった」と回答された方は次の質問にその理由を教えてください
        </label>
        <div class="radio-group">
            <label>
                <input type="radio" checked name="envilonment" value="とても快適だった"><span>とても快適だった</span>
            </label><br>
            <label>
                <input type="radio" name="envilonment" value="快適だった"><span>快適だった</span>
            </label><br>
            <label>
                <input type="radio" name="envilonment" value="ふつう"><span>ふつう</span>
            </label><br>
            <label>
                <input type="radio" checked name="envilonment" value="あまり快適ではなかった"><span>あまり快適ではなかった</span>
            </label><br>
            <label>
                <input type="radio" name="envilonment" value="全く快適ではなかった"><span>全く快適ではなかった</span>
            </label><br>
        </div>
        <label class="label_name" for="satisfaction">問9で「あまり快適ではなかった」「」全く快適ではなかったと回答された方は
            その理由を教えてください
        </label>
        <textarea row="20px" name="reason_other"></textarea>

        <?php echo $responce['passage'] ?><br>

        <button type="submit">確認画面へ</button>
    </form>
</div>
</body>
<?php include('/var/www/html/moodle/custom/app/Views/common/footer.php'); ?>

</html>