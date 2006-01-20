<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.6 development (2005101200)


$string['adminauthorizeccapture'] = 'オーダーレビュー&オートキャプチャ設定';
$string['adminauthorizeemail'] = 'メール送信設定';
$string['adminauthorizesettings'] = 'Authorize.net 設定';
$string['adminauthorizewide'] = 'サイト全体の設定';
$string['adminavs'] = 'あなたのauthorize.netアカウントで住所確認システム ( AVS:Address Verification System ) を有効にした場合は、ここをチェックしてください。この設定により、ユーザが支払いフォームに入力するときに、市町村、県、国および郵便番号の入力が求められます。';
$string['admincronsetup'] = 'cron.phpメンテナンススクリプトが少なくとも24時間稼動していません。<br />オートキャプチャ機能を使用したい場合、Cronを有効にする必要があります。<br />再度 <a href=\"../doc/?frame=install.html&sub=cron\">cronの設定</a> または「an_review again」のチェックを外してください。<br />オートキャプチャを無効にすると、30日以内にトランザクションを検査しない場合、トランザクションはキャンセルされます。<br />30日以内に支払いを手動で支払いを受け付け/拒否したい場合は、「an_review」をチェックして、<br />「an_capture_day」フィールドにゼロを入力してください。';
$string['adminemailexpired'] = 'トランザクションが失効する <b>$a</b> 日前に何件の「認証 / 保留キャプチャ」トランザクションがあったか警告メールを管理者へ送信します。 (0 = メール送信を停止する、デフォルト = 2、最大 = 5 )<br />手動キャプチャを有効にしている場合、便利です (an_review = チェック、 an_capture_day = 0 )。';
$string['adminhelpcapture'] = '手動で支払いを受け付け/拒否するだけではなく、支払いのキャンセルを防ぐためにオートキャプチャを使用したいと思います。どうすれば良いですか?

- cronを設定してください。
- 「an_review」をチェックしてください。
- 「an_capture_day」フィールドに、1から29の間の数値を入力してください。あなたが「an_capture_day」内にキャプチャしない場合を除いて、クレジットカード情報は取得され、ユーザがコース登録されます。';
$string['adminhelpreview'] = '手動で支払いを受け付け/拒否するには?
- 「an_review」をチェックしてください。
- 「an_capture_day」フィールドにゼロを入力してください。

カード番号を入力するのと同時に学生をコース登録させるには?
- 「an_review」のチェックを外してください。';
$string['adminneworder'] = '新しい保留の注文が入りました:

注文ID: $a->orderid
トランザクションID: $a->transid
ユーザ: $a->user
コース: $a->course
金額: $a->amount

オートキャプチャ有効?: $a->acstatus

オートキャプチャが有効にされている場合、クレジットカード情報は $a->captureon 日で取得され、学生はコース登録されます。そうでない場合、$a->expireon 日で期限切れとなり、この日以降はカード情報の取得ができなくなります。

下記のリンクで、学生がコース登録するための支払いを承認/拒否することもできます:
$a->url';
$string['adminnewordersubject'] = '$a->course: 新しい未決注文 ( $a->orderid )';
$string['adminpendingorders'] = 'あなたはオートキャプチャ機能を停止しています。<br />あなたがチェックしない場合、ステータスAN_STATUS_AUTHの合計 $a->count　件のトランザクションがキャンセルされます。<br />支払いを受け付け/拒否するには <a href=\'$a->url\'>支払い管理</a>ページにアクセスしてください。';
$string['adminreview'] = 'クレジットカード処理手続きの前に注文を検査する。';
$string['amount'] = '金額';
$string['anlogin'] = 'Authorize.net: ログイン名';
$string['anpassword'] = 'Authorize.net: パスワード ( 不要 )';
$string['anreferer'] = '必要な場合は、リファラURLを設定してください。これは、ウェブリクエストの「Referer: URL」ヘッダを送信します。';
$string['antestmode'] = 'Authorize.net: テストトランザクション';
$string['antrankey'] = 'Authorize.net: トランザクションキー';
$string['authcaptured'] = '認証完了 / キャプチャ完了';
$string['authorizedpendingcapture'] = '認証完了 / キャプチャ未了';
$string['avsa'] = '住所は合致しますが、郵便番号が合致しません。';
$string['avsb'] = '住所情報を入力されていません。';
$string['avse'] = '住所確認システムエラー';
$string['avsg'] = '米国以外のカード発行銀行';
$string['avsn'] = '住所および郵便番号が合致しません。';
$string['avsp'] = '住所確認システムを使用できません。';
$string['avsr'] = 'リトライ - システム利用不可またはタイムアウト';
$string['avsresult'] = 'AVS結果';
$string['avss'] = '発行人よりサービスがサポートされていません。';
$string['avsu'] = '住所情報を利用できません。';
$string['avsw'] = '9桁の郵便番号は合致しますが、住所が合致しません。';
$string['avsx'] = '住所および9桁の郵便番号が合致しません。';
$string['avsy'] = '住所および5桁の郵便番号が合致しません。';
$string['avsz'] = '5桁の郵便番号は合致しますが、住所が合致しません。';
$string['canbecredit'] = '$a->upto に返金可能';
$string['cancelled'] = 'キャンセル完了';
$string['capture'] = 'キャプチャ';
$string['capturedpendingsettle'] = '認証完了 / 確定未了';
$string['capturedsettled'] = '認証完了 / 確定';
$string['capturetestwarn'] = 'キャプチャに問題がありますが、テストモードではレコードは更新されません。';
$string['captureyes'] = 'クレジットカード情報が取得され、学生がコース登録されます。本当によろしいですか?';
$string['ccexpire'] = '有効期限';
$string['ccexpired'] = 'クレジットカードの期限が切れています。';
$string['ccinvalid'] = 'クレジットカードが間違っています。';
$string['ccno'] = 'クレジットカード番号';
$string['cctype'] = 'クレジットカードタイプ';
$string['ccvv'] = 'CV2';
$string['ccvvhelp'] = 'カードの裏面 ( 3桁の数字 ) をご覧ください。';
$string['choosemethod'] = 'コースの登録キーを知っている場合は、入力してください。コースの登録キーを知らない場合は、このコースに利用料を支払う必要があります。';
$string['chooseone'] = '次の2つのフィールドの1つまたは両方に入力してください。';
$string['credittestwarn'] = 'クレジットは正常に動作しているようですが、テストモードではデータベースにレコードはインサートされません。';
$string['cutofftime'] = 'トランザクションカットオフ時間。何時に最終のトランザクションを確定のために取得しますか?';
$string['delete'] = '無効化';
$string['description'] = 'Authorize.netモジュールでは、クレジットカード経由でコースの支払いを行うことができます。コースの受講料がゼロの場合、学生に対して受講登録に関する支払いは求められません。サイト全体の利用料をデフォルトとしてここで設定して、コースごとに受講料を設定することができます。コース受講料を設定した場合、コース受講料はサイト利用料に優先します。';
$string['enrolname'] = 'Authorize.net クレジットカード・ゲイトウェイ';
$string['expired'] = '期限切れ';
$string['howmuch'] = 'いくらですか?';
$string['httpsrequired'] = '申し訳ございません、あなたのリクエストは現在処理することができません。このサイトの設定は正常に行われませんでした。
<br /><br />
ブラウザの下部に黄色の鍵マークが表示されない場合は、あなたのクレジットカード番号を入力しないでください。これは単にクライアントとサーバ間で送信されるすべてのデータが暗号化されることを意味します。ですから、2台のコンピュータ間のトランザクション情報は保護され、あなたのクレジットカード番号がインターネット上で盗まれることはありません。';
$string['logindesc'] = 'このオプションは「ON」にする必要があります。<br /><br />管理 >> 詳細設定 >> セキュリティ で <a href=\"$a->url\">loginhttps が「Yes」になっていること</a>を確認してください。 <br /><br />この設定を「Yes」にすることで、Moodleはログインおよび支払いページに関してセキュアhttps接続を使用します。';
$string['missingaddress'] = '住所を入力してください。';
$string['missingcc'] = 'カード番号を入力してください。';
$string['missingccexpire'] = '有効期限を入力してください。';
$string['missingcctype'] = 'カードタイプを入力してください。';
$string['missingcvv'] = '郵便番号を入力してください。';
$string['missingzip'] = 'カード名義を入力してください。';
$string['nameoncard'] = 'クレジットカード名義';
$string['noreturns'] = '返金無し!';
$string['notsettled'] = '未確定';
$string['orderid'] = '注文ID';
$string['paymentmanagement'] = '支払い管理';
$string['paymentpending'] = 'このコースに関するあなたの支払いは保留中です。注文番号は、 $a->orderid です。';
$string['pendingordersemail'] = '支払いを受け付けない場合、トランザクション $a->pending は、$a->days 日で期限が切れます。

あなたがオートキャプチャを有効にしていないため、これは警告メッセージです。支払いを手動で受け付けるか、拒否してください。

保留の支払いを受け付け/拒否するには次のページへ:
$a->url

オートキャプチャを有効にすると、あなたは警告メッセージを受信しなくなります。設定は次のページへ:
$a->enrolurl';
$string['reason11'] = '重複トランザクションが送信されました。';
$string['reason13'] = 'マーチャントログインIDが無効またはアカウントがアクティブではありません。';
$string['reason16'] = 'トランザクションが見つかりませんでした。';
$string['reason17'] = 'マーチャントは、このタイプのクレジットカードを受付けません。';
$string['reason27'] = 'AVSのトランザクション結果が一致しません。提供された住所がカード所有者の住所と合致しません。';
$string['reason28'] = 'マーチャントは、このタイプのクレジットカードを受付けません。';
$string['reason30'] = 'プロセッサの設定が正しくありません。マーチャントサービスプロバイダにご連絡ください。';
$string['reason39'] = '提供された通貨コードが正しくないか、サポートされていないか、マーチャントに許可されていないか、為替レートがありません。';
$string['reason43'] = 'プロセッサでマーチャントが正しく設定されていません。マーチャントサービスプロバイダにご連絡ください。';
$string['reason44'] = 'このトランザクションは拒否されました。カード番号フィルタエラー!';
$string['reason45'] = 'このトランザクションは拒否されました。カード番号 / AVSフィルタエラー!';
$string['reason47'] = '決済にリクエストされた金額が、認証された最初の金額よりも大きくないかもしれません。';
$string['reason5'] = '有効な金額を入力してください。';
$string['reason50'] = 'このトランザクションは決済処理中ですので、払い戻しはできません。';
$string['reason51'] = 'このトランザクションに関する全てのクレジットの合計が、最初のトランザクションの金額よりも多くなっています。';
$string['reason54'] = '参照トランザクションはクレジット発行の基準に適合しません。';
$string['reason55'] = '参照トランザクションに対するクレジット合計は、最初のクレジット金額を超えます。';
$string['refund'] = '払い戻し';
$string['refunded'] = '払い戻し完了';
$string['returns'] = '返金';
$string['reviewday'] = '教師または管理者が、<b>$a</b> 日以内に注文を検査しない場合を除いて、自動的にクレジットカード情報を取得します。CRONを有効にする必要があります。( 0日 = オートキャプチャを無効にする = 教師、管理者が手動で検査を行う。オートキャプチャを無効にすると、30日以内にトランザクションを検査しない場合、トランザクションはキャンセルされます。 )';
$string['reviewnotify'] = 'あなたの支払いが確認されました。数日中に先生からメールが送信されますのでお待ちください。';
$string['sendpaymentbutton'] = '支払いの送信';
$string['settled'] = '確定済み';
$string['settlementdate'] = '確定年月日';
$string['subvoidyes'] = '返金済みトランザクション $a->transid はキャンセルされ、$a->amount があなたの口座に振り込まれます。本当によろしいですか?';
$string['tested'] = 'テスト済み';
$string['testmode'] = '[ テストモード ]';
$string['transid'] = 'トランザクションID';
$string['unenrolstudent'] = '学生を登録解除しますか?';
$string['void'] = '取り消し';
$string['voidtestwarn'] = '取り消しに問題がありますが、テストモードではレコードは更新されません。';
$string['voidyes'] = 'トランザクションがキャンセルされます。本当によろしいですか?';
$string['zipcode'] = '郵便番号';

?>
