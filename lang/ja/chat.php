<?PHP // $Id$ 
      // chat.php - created with Moodle 1.6 development (2005101200)


$string['beep'] = 'ビープ';
$string['chatintro'] = '紹介文';
$string['chatname'] = 'チャットルーム名';
$string['chatreport'] = 'チャットセッション';
$string['chattime'] = '次回のチャットタイム';
$string['configmethod'] = '通常のチャットメソッドでは、クライアントは定期的にサーバにアクセスして内容を更新します。このメソッドは設定を必要とせず、どこでも使うことができますが、チャット参加者が多くなればサーバに対する多大な負担が生じます。サーバデーモンを使用する場合は、Unixのシェルアクセスが必要ですが、軽快なチャット環境を提供することができます。';
$string['configoldping'] = 'ユーザの応答がなくなってから、どれくらいの時間 ( 秒数 ) で退室したと見なしますか? これは単に上限であり、通常退出は非常に速く検出されます。あなたのサーバには、更に小さな値を設定する必要があります。通常のメソッドを使用している場合、2* chat_refresh_room より小さな値を<strong>設定しないでください</strong>。';
$string['configrefreshroom'] = 'どれくらいのタイミング ( 秒数 ) でチャットルームをリフレッシュしますか?この通知を小さくすればチャットルームはレスポンスが良いように見えますが、多くの人がチャットをする場合、サーバにかかる負担が大きくなります。';
$string['configrefreshuserlist'] = 'どれくらいのタイミング ( 秒数 ) でユーザリストをリフレッシュしますか?';
$string['configserverhost'] = 'サーバデーモンが稼動しているホスト';
$string['configserverip'] = '上記ホスト名に関するIPアドレス';
$string['configservermax'] = '最大クライアント数';
$string['configserverport'] = 'デーモンに使用するサーバのポート';
$string['currentchats'] = 'アクティブ・チャットセッション';
$string['currentusers'] = '現在のユーザ';
$string['deletesession'] = 'セッションを削除する';
$string['deletesessionsure'] = '本当にこのセッションを削除してもよろしいですか?';
$string['donotusechattime'] = 'チャット時間を公開しない';
$string['enterchat'] = 'チャットルームに入室';
$string['errornousers'] = 'ユーザが見つかりませんでした!';
$string['explaingeneralconfig'] = 'これらの設定は、<strong>常に</strong>反映されます。';
$string['explainmethoddaemon'] = 'これらの設定は、チャットメソッドに「Chatサーバデーモン」を選択した時<strong>のみ</strong>影響します。';
$string['explainmethodnormal'] = 'これらの設定は、チャットメソッドに「ノーマルメソッド」を選択した時<strong>のみ</strong>影響します。';
$string['generalconfig'] = '一般設定';
$string['helpchatting'] = 'チャットヘルプ';
$string['idle'] = 'アイドル';
$string['messagebeepseveryone'] = '$a が全員にビープします!';
$string['messagebeepsyou'] = '$a があなたにビープしました!';
$string['messageenter'] = '$a が入室しました。';
$string['messageexit'] = '$a が退室しました。';
$string['messages'] = 'メッセージ';
$string['methoddaemon'] = 'Chatサーバデーモン';
$string['methodnormal'] = 'ノーマルメソッド';
$string['modulename'] = 'チャット';
$string['modulenameplural'] = 'チャット';
$string['neverdeletemessages'] = 'メッセージを削除しない';
$string['nextsession'] = '次のスケジュールセッション';
$string['noguests'] = 'ゲストはこのチャットを利用できません。';
$string['nomessages'] = 'メッセージがありません。';
$string['repeatdaily'] = '毎日同じ時間に';
$string['repeatnone'] = '繰り返し無し - 指定した時間にのみ公開';
$string['repeattimes'] = 'セッションの繰り返し';
$string['repeatweekly'] = '毎週同じ時間に';
$string['savemessages'] = 'セッションの保存期間';
$string['seesession'] = 'このセッションを見る';
$string['sessions'] = 'チャットセッション';
$string['strftimemessage'] = '%%H:%%M';
$string['studentseereports'] = 'すべての人が過去のセッションを見ることができる';
$string['viewreport'] = '過去のチャットセッションを表示';

?>
