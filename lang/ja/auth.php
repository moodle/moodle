<?PHP // $Id$ 
      // auth.php - created with Moodle 1.3 development (2004041800)


$string['auth_dbdescription'] = 'ユーザ名とパスワードを確認するために外部のデータベースを使用します。新しいアカウントを作成する場合、他のフィールドの情報がMoodleへ複製されます。';
$string['auth_dbextrafields'] = 'これらのフィールドは任意項目です。<B>外部データベースフィールド</B>より事前に入力されたMoodleユーザフィールドを選択することも可能です。<P>空白の場合は初期値が使用されます。<P>どちらの場合でも、ユーザはログイン後に全てのフィールドを編集可能です。';
$string['auth_dbfieldpass'] = 'パスワードを含んだフィールド名';
$string['auth_dbfielduser'] = 'ユーザ名を含んだフィールド名';
$string['auth_dbhost'] = 'データベースサーバが稼動しているコンピュータ';
$string['auth_dbname'] = 'データベース名';
$string['auth_dbpass'] = '上記ユーザ名に合致するパスワード';
$string['auth_dbpasstype'] = 'パスワードフィールドで使用するフォーマットを特定してください。MD5暗号化はPostNukeのような他の一般的なウェブアプリケーションへの接続に便利です。';
$string['auth_dbtable'] = 'データベースのテーブル名';
$string['auth_dbtitle'] = '外部データベースを使用';
$string['auth_dbtype'] = 'データベースタイプ(詳細は<A HREF=../lib/adodb/readme.htm#drivers>ADOdb documentation</A>をご覧ください';
$string['auth_dbuser'] = 'データベースアクセス用のユーザ名';
$string['auth_emaildescription'] = 'メールによる確定は認証方法の初期値です。ユーザが新しいユーザ名とパスワードを選択してサインアップした場合、確定用メールがユーザのメールアドレスに送信されます。このメールにはユーザがアカウントを確定するためのリンクが記入されています。アカウント確定後のログインではMoodleデータベースに保存されているユーザ名とパスワードのみを確認します。';
$string['auth_emailtitle'] = 'Emailベースの認証';
$string['auth_imapdescription'] = 'ユーザ名とパスワードを確認するためにIMAPサーバを使用します。';
$string['auth_imaphost'] = 'IMAPサーバーアドレスです。IPアドレスではなくドメイン名を使用してください。';
$string['auth_imapport'] = 'IMAPサーバポート番号です。通常は143又は993です。';
$string['auth_imaptitle'] = 'IMAPサーバを使用';
$string['auth_imaptype'] = 'IMAPサーバタイプです。IMAPサーバは異なる認証及びネゴシエーションを利用することが可能です。';
$string['auth_ldap_bind_dn'] = 'ユーザ検索にbindユーザを利用したい場合は、ここに明示してください。例 \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'bindユーザ用のパスワード';
$string['auth_ldap_contexts'] = 'ユーザが配置されているコンテキスト一覧です。異なるコンテキストは「;」で分けてください。例 \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'ユーザ作成をメールによる認証で行う場合、ユーザが作成されるコンテキストを特定してください。セキュリティーの観点から、このコンテキストは各ユーザ毎に異なるものでなければなりません。Moodleが自動的にコンテキストからユーザを探しますので、ldap_context-vaiableをこのコンテキストに追加する必要はありません。';
$string['auth_ldap_creators'] = 'メンバーが新しいコースの作成を許されているグループのリストです。複数のグループは「;」で分けられています。通常は\'cn=teachers,ou=staff,o=myorg\'のようになります。';
$string['auth_ldap_host_url'] = 'LDAPホストのURLを下記のように明示してください。

\'ldap://ldap.myorg.com/\' 又は \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_memberattribute'] = 'ユーザがグループに属性している場合、ユーザの属性を特定してください。通常は\'member\'です。
';
$string['auth_ldap_search_sub'] = 'サブコンテキストからユーザを検索する場合は、  &lt;&gt; 0 のように入力してください。';
$string['auth_ldap_update_userinfo'] = 'LDAPよりMoodleの情報(名前、名字、住所等)を更新します。マッピング情報に関しては /auth/ldap/attr_mappings をご覧ください。';
$string['auth_ldap_user_attribute'] = 'name/searchユーザに使われる属性です。通常は\'cn\'です。';
$string['auth_ldap_version'] = 'サーバで使用しているLDAPプロトコルのバージョン';
$string['auth_ldapdescription'] = '外部のLDAPサーバに対して認証を行います。ユーザ名とパスワードが正しい場合、Moodleは新しいユーザをデータベースに作成します。このモジュールはユーザ属性をLDAPから取得してMoodleのフィールドに入力します。認証後のログインではユーザ名とパスワードのみが確認されます。

';
$string['auth_ldapextrafields'] = 'これらのフィールドは任意項目です。<B>LDAPフィールド</B>より事前に入力されたMoodleユーザフィールドを選択することも可能です。<P>空白の場合はLDAPよりデータの転送は行われずにMoodleの初期値が使用されます<P>どちらの場合でも、ユーザはログイン後に全てのフィールドを編集可能です。';
$string['auth_ldaptitle'] = 'LDAPサーバを使用';
$string['auth_manualdescription'] = 'この方法ではユーザによるユーザアカウント作成機能を停止します。全てのアカウント作成は管理者により手動で行う必要があります。';
$string['auth_manualtitle'] = '手動アカウント作成のみ';
$string['auth_multiplehosts'] = '複数のホストを設定出来ます(例 host1.com;host2.com;host3.com)';
$string['auth_nntpdescription'] = 'ユーザ名とパスワードを確認するためにNNTPサーバを使用します。';
$string['auth_nntphost'] = 'NNTPサーバーアドレスです。IPアドレスではなくドメイン名を使用してください。';
$string['auth_nntpport'] = 'サーバーポート(119が一般的です)';
$string['auth_nntptitle'] = 'NNTPサーバを使用';
$string['auth_nonedescription'] = 'ユーザはログインして外部サーバ及びメールによる認証無しにアカウントを直ちに作成できます。このオプションを使用するときは十分に注意してください - セキュリティー及び管理上の問題が発生するかもしれないことを考えてください。';
$string['auth_nonetitle'] = '認証無し';
$string['auth_pop3description'] = 'ユーザ名とパスワードを確認するためにPOP3サーバを使用します。';
$string['auth_pop3host'] = 'POP3サーバーアドレスです。IPアドレスではなくドメイン名を使用してください。';
$string['auth_pop3port'] = 'サーバーポート (110が一般的です)';
$string['auth_pop3title'] = 'POP3サーバを使用';
$string['auth_pop3type'] = 'サーバタイプです。もし認証が必要な場合はpop3certを選択してください。';
$string['auth_user_create'] = 'ユーザの作成を許可する';
$string['auth_user_creation'] = '新しい(匿名の)ユーザは外部認証によりユーザアカウントを作成することが出来ます。ユーザの確定はメールによって行われます。このオプションを有効にした場合、module-specificオプションも同時に有効にする必要があります。';
$string['auth_usernameexists'] = 'このユーザ名は既に存在します。新しいものを選んでください。';
$string['authenticationoptions'] = '認証オプション';
$string['authinstructions'] = 'どのようなユーザ名やパスワードを使用したらよいのかユーザに説明します。ここに入力した文章はログインページに表示されます。空白の場合、何も表示されません。';
$string['changepassword'] = 'パスワードのURLを変更する';
$string['changepasswordhelp'] = 'ユーザがユーザ名/パスワードを忘れたときに回復又は変更するためのボタンをログインページに表示します。空白の場合、ボタンは表示されません。

';
$string['chooseauthmethod'] = '認証方法の選択:';
$string['guestloginbutton'] = 'ゲストログインボタン';
$string['instructions'] = '説明';
$string['md5'] = 'MD5暗号化';
$string['plaintext'] = 'テキスト';
$string['showguestlogin'] = 'ログインページのゲストログインボタンを表示／非表示に出来ます。';

?>
