<?PHP // $Id$ 
      // install.php - created with Moodle 1.6 development (2005072200)


$string['admindirerror'] = '設定されたadminディレクトリが間違っています。';
$string['admindirname'] = 'Adminディレクトリ';
$string['admindirsetting'] = '稀に、/adminディレクトリをコントロールパネルまたはその他の管理ツールにアクセスするためのURLとして使用しているウェブホストがあります。残念ながら、これはMoodle管理ページの標準的なロケーションと衝突します。インストールする時にadminディレクトリをリネームすることが可能です。ここに新しいディレクトリ名を入力してください。例: <br/> <br /><b>moodleadmin</b><br /> <br />

これはMoodleのadminリンクを変更します。';
$string['caution'] = '警告';
$string['chooselanguage'] = '言語を選択してください。';
$string['compatibilitysettings'] = 'PHP設定を確認しています ...';
$string['configfilenotwritten'] = 'インストールスクリプトは、自動的にあなたの設定を反映したconfig.phpファイルを作成することができませんでした。恐らくMoodleディレクトリに書き込み権が無いためだと思われます。下記のコードをconfig.phpという名称のファイルにコピーして、Moodleのルートディレクトリに入れてください。';
$string['configfilewritten'] = 'config.phpが正常に作成されました。';
$string['configurationcomplete'] = '設定が完了しました。';
$string['database'] = 'データベース';
$string['databasecreationsettings'] = 'ほとんどのMoodleデータが保存されるデータベース設定を行ってください。このデータベースはMoodle4Windowsインストーラーにより、下記の設定が指定された形で自動的に作成されます。<br />
<br /> <br />
<b>タイプ:</b> インストーラーにより「mysql」に固定されます。<br />
<b>ホスト:</b> インストーラーにより「localhost」に固定されます。<br />
<b>データベース名:</b> データベース名、例 moodle<br />
<b>ユーザ名:</b> インストーラーにより「root」に固定されます。<br />
<b>パスワード:</b> あなたのデータベースパスワードです。<br />
<b>テーブル接頭辞:</b> 全てのテーブル名に使用される任意の接頭辞です。';
$string['databasesettings'] = 'ほとんどのMoodleデータが保存されるデータベースの設定を行います。このデータベースは、アクセスするためのユーザ名およびパスワードと共に作成されている必要があります。<br/>
<br /> <br />
<b>タイプ:</b> mysql または postgres7<br />
<b>ホスト:</b> 例 localhost または db.isp.com<br />
<b>データベース名:</b> 例:moodle<br />
<b>ユーザ名:</b> データベースのユーザ名<br />
<b>パスワード:</b> データベースのパスワード<br />
<b>テーブル接頭辞:</b> 全てのテーブル名にオプションで使用する接頭辞';
$string['dataroot'] = 'データディレクトリ';
$string['datarooterror'] = 'あなたが指定した「データディレクトリ」が見つからないか、作成されませんでした。パスを訂正するか、ディレクトリを手動で作成してください。';
$string['dbconnectionerror'] = 'あなたが指定したデータベースに接続できませんでした。データベース設定を確認してください。';
$string['dbcreationerror'] = 'データベース作成エラー。設定で提示された名称のデータベースを作成できませんでした。';
$string['dbhost'] = 'ホストサーバ';
$string['dbpass'] = 'パスワード';
$string['dbprefix'] = 'テーブル接頭辞';
$string['dbtype'] = 'タイプ';
$string['directorysettings'] = '<p>Moodleのインストール先を確認してください。</p>

<p><b>ウェブアドレス:</b>
Moodleにアクセスする完全なウェブアドレスを指定してください。複数のURLよりアクセス可能な場合は、学生が利用する最も自然なURLを選択してください。最後にスラッシュを付けないでください。</p>

<p><b>Moodleディレクトリ:</b>
インストール先の完全なディレクトリパスを指定してください。大文字/小文字が間違っていないか確認してください。</p>

<p><b>データディレクトリ:</b>
Moodleがアップロードされたファイルを保存する場所が必要です。 このディレクトリは、ウェブサーバのユーザ ( 通常は「nobody」または「apache」 ) が読み込みおよび書き込みできるようにしてください。しかし、ウェブから直接アクセスできないようにしてください。</p>';
$string['dirroot'] = 'Moodleディレクトリ';
$string['dirrooterror'] = '「Moodleディレクトリ」設定が間違っているようです - インストール済みMoodleが見つかりませんでした。下記の値がリセットされました。';
$string['download'] = 'ダウンロード';
$string['fail'] = '失敗';
$string['fileuploads'] = 'ファイルアップロード';
$string['fileuploadserror'] = 'これは有効にしてください。';
$string['fileuploadshelp'] = '<p>あなたのサーバでは、ファイルのアップロードができないようです。</p>
<p>Moodleのインストールは可能ですが、ファイルのアップロードができない場合は、コースファイルやユーザプロフィールのイメージをアップロードすることができません。</p>
<p>ファイルアップロードを可能にするには、あなた ( またはシステム管理者 ) がメインphp.iniファイルを編集して、
<b>file_uploads</b> を \'1\'にする必要があります。</p>';
$string['gdversion'] = 'GDバージョン';
$string['gdversionerror'] = 'イメージの処理および作成を行うにはGDライブラリが必要です。';
$string['gdversionhelp'] = '<p>サーバにGDがインストールされていないようです。</p>

<p>GDは、Moodleがイメージ ( ユーザプロフィールアイコン等 ) を処理したり、新しいイメージ ( ロググラフ等 ) を作成するためにPHPが必要とするライブラリです。Moodleは、GD無しでも動作します -  イメージ処理が使用できないだけです。</p>

<p>Unix環境下で、GDをPHPにインストールするには、PHPを --with-gd パラメータでコンパイルしてください。</p>

<p>Windows環境下では、php.iniでlibgd.dllを参照している行のコメントアウトを取り除いてください。</p>';
$string['installation'] = 'インストレーション';
$string['magicquotesruntime'] = 'Magic Quotesランタイム';
$string['magicquotesruntimeerror'] = 'これは無効にしてください。';
$string['magicquotesruntimehelp'] = '<p>Moodleを正常に動作させるためには、Magic quotesランタイムを無効にする必要があります。</p>

<p>通常はデフォルトで無効にされています ... php.iniの <b>magic_quotes_runtime</b> 設定を確認してください。</p>

<p>php.iniファイルにアクセスできない場合は、Moodleディレクトリの.htaccessファイルに次の行を追加してください:
<blockquote>php_value magic_quotes_runtime Off</blockquote>
</p>';
$string['memorylimit'] = 'メモリー制限';
$string['memorylimiterror'] = 'PHPのメモリー制限が低すぎます ... 問題が発生する可能性があります。';
$string['memorylimithelp'] = '<p>現在、サーバのPHPメモリー制限が $a に設定されています。</p>
<p>この設定では、Moodleのメモリーに関わるトラブルが発生します。 特に多くのモジュールを使用したり、多くのユーザがMoodleを使用する場合に、トラブルが発生します。</p>
<p>可能でしたら、PHPのメモリー制限上限を16M以上に設定されることをお勧めします。この設定を実現するために、幾つかの方法があります:
<ol>
<li>コンパイル可能な場合は、PHPを<i>--enable-memory-limit</i>オプションでコンパイルしてください。
これにより、Moodle自身がメモリー制限を設定することが可能になります。
<li>php.iniファイルにアクセスできる場合は、<b>memory_limit</b>設定を16Mのように変更することができます。php.iniファイルにアクセスできない場合は、管理者に変更を依頼してください。
<li>次の行を含む.htaccessファイルをMoodleディレクトリに作成することができるサーバもあります:
<p><blockquote>php_value memory_limit 16M</blockquote></p>
<p>しかし、この設定が<b>全ての</b>PHPページの動作を妨げる場合もあります。ページを閲覧時にエラーが表示される場合は、.htaccessファイルを削除してください。</p>
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'MySQLと通信できるようにPHPのMySQL extension設定が正しく設定されていません。php.iniを確認するか、PHPを再度コンパイルしてください。';
$string['pass'] = 'パス';
$string['phpversion'] = 'PHPバージョン';
$string['phpversionerror'] = 'PHPバージョンは4.1.0以上をお使いください。';
$string['phpversionhelp'] = '<p>MoodleではPHPバージョン4.1.0以上が必要です。</p>
<p>現在、バージョン $a が動作しています。</p>
<p>PHPをアップグレードするか、新しいバージョンがインストールされているホストに移動してください!</p>';
$string['safemode'] = 'セーフモード';
$string['safemodeerror'] = 'セーフモードが有効の場合、Moodleに問題が発生する場合があります。';
$string['safemodehelp'] = '<p>セーフモードが有効にされている場合、Moodleには様々な問題が発生します。 特に新しいファイルを作成することができません。</p>
<p>セーフモードは通常被害妄想を持ったウェブホストで有効にされています。Moodleサイト用に別の新しいウェブホスティング会社を探してください。</p>
<p>セーフモード環境下で、インストール作業を続けることも可能ですが、後でいくつかの問題が発生することが予想されます。</p>';
$string['sessionautostart'] = 'セッション自動スタート';
$string['sessionautostarterror'] = 'これは無効にしてください。';
$string['sessionautostarthelp'] = '<p>Moodleはセッションサポートを必要とします。また、セッションサポート無しでは動作しません。.</p>
<p>セッションは、php.iniファイルで使用可にすることができます ... session.auto_startパラメータを探してください。</p>';
$string['wwwroot'] = 'ウェブアドレス';
$string['wwwrooterror'] = 'ウェブアドレスが間違っています - インストール済みMoodleはここに表示されません。';

?>
