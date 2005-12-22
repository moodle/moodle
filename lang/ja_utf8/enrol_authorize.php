<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.6 development (2005101200)


$string['adminauthorizeccapture'] = 'オーダーレビュー&オートキャプチャ設定';
$string['adminauthorizeemail'] = 'メール送信設定';
$string['adminauthorizesettings'] = 'Authorize.net 設定';
$string['adminauthorizewide'] = 'サイト全体の設定';
$string['adminreview'] = 'クレジットカード処理手続きの前に注文を検査する。';
$string['anlogin'] = 'Authorize.net: ログイン名';
$string['anpassword'] = 'Authorize.net: パスワード ( 不要 )';
$string['anreferer'] = '必要な場合は、リファラURLを設定してください。これは、ウェブリクエストの「Referer: URL」ヘッダを送信します。';
$string['antestmode'] = 'Authorize.net: テストトランザクション';
$string['antrankey'] = 'Authorize.net: トランザクションキー';
$string['authorizedpendingcapture'] = '認証完了 / キャプチャ未了';
$string['cancelled'] = 'キャンセル完了';
$string['capture'] = 'キャプチャ';
$string['capturedpendingsettle'] = '認証完了 / 確定未了';
$string['capturedsettled'] = '認証完了 / 確定';
$string['ccexpire'] = '有効期限';
$string['ccexpired'] = 'クレジットカードの期限が切れています。';
$string['ccinvalid'] = 'クレジットカードが間違っています。';
$string['ccno'] = 'クレジットカード番号';
$string['cctype'] = 'クレジットカードタイプ';
$string['ccvv'] = 'CV2';
$string['ccvvhelp'] = 'カードの裏面 ( 3桁の数字 ) をご覧ください。';
$string['choosemethod'] = 'コースの登録キーを知っている場合は、入力してください。コースの登録キーを知らない場合は、このコースに利用料を支払う必要があります。';
$string['chooseone'] = '次の2つのフィールドの1つまたは両方に入力してください。';
$string['cutofftime'] = 'トランザクションカットオフ時間。何時に最終のトランザクションを確定のために取得しますか?';
$string['description'] = 'Authorize.netモジュールでは、クレジットカード経由でコースの支払いを行うことができます。コースの受講料がゼロの場合、学生に対して受講登録に関する支払いは求められません。サイト全体の利用料をデフォルトとしてここで設定して、コースごとに受講料を設定することができます。コース受講料を設定した場合、コース受講料はサイト利用料に優先します。';
$string['enrolname'] = 'Authorize.net クレジットカード・ゲイトウェイ';
$string['expired'] = '期限切れ';
$string['httpsrequired'] = '申し訳ございません、あなたのリクエストは現在処理することができません。このサイトの設定は正常に行われませんでした。
<br /><br />
ブラウザの下部に黄色の鍵マークが表示されない場合は、あなたのクレジットカード番号を入力しないでください。これは単にクライアントとサーバ間で送信される全てのデータが暗号化されることを意味します。ですから、2台のコンピュータ間のトランザクション情報は保護され、あなたのクレジットカード番号がインターネット上で盗まれることはありません。';
$string['logindesc'] = 'このオプションは「ON」にする必要があります。<br /><br />管理 >> 詳細設定 >> セキュリティ で <a href=\"$a->url\">loginhttps</a> が「Yes」になっていることを確認してください。 <br /><br />この設定を「Yes」にすることで、Moodleはログインおよび支払いページに関してセキュアhttps接続を使用します。';
$string['nameoncard'] = 'クレジットカード名義';
$string['orderid'] = 'オーダーID';
$string['paymentmanagement'] = '支払い管理';
$string['paymentpending'] = 'このコースに関するあなたの支払いは保留中です。注文番号は、 $a->orderid です。';
$string['refund'] = '払い戻し';
$string['refunded'] = '払い戻し完了';
$string['returns'] = '返金';
$string['reviewday'] = '教師または管理者が、<b>$a</b> 日以内に注文を検査しない場合、自動的にクレジットカード情報を取得する。CRONを有効にする必要があります。( 0日 = 自動取得を無効にする = 教師、管理者が手動で検査を行う。自動取得が無効にされた場合、30日以内にトランザクションを検査しない場合は、トランザクションはキャンセルされます。 )';
$string['reviewnotify'] = 'あなたの支払いが確認されました。数日中に先生からメールが送信されますのでお待ちください。';
$string['sendpaymentbutton'] = '支払いの送信';
$string['settled'] = '確定済み';
$string['settlementdate'] = '確定年月日';
$string['transid'] = 'トランザクションID';
$string['void'] = '無効';
$string['zipcode'] = '郵便番号';

?>
