<?php // $Id$ 
      // enrol_ldap.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2004112400)


$string['description'] = '<p>ユーザ登録をコントロールするために、LDAPサーバを使用することができます。LDAPの使用は、あなたのLDAPツリーがコースに登録されているグループを含んでいること、それぞれのグループ/コース内に学生に対応したメンバー登録があることを前提とします。</p>
<p>コースはLDAP内でグループとして定義され、ユニークなユーザ識別を含む複数のメンバーシップフィールド(<em>member</em> または <em>memberUid</em>)を持っていることを前提とします。</p>

<p>LDAPを使用したユーザ登録を使用するには、ユーザが有効なIDナンバーフィールドを<strong>持つ必要</strong>があります。LDAPグループは、ユーザがコースに登録できるように、メンバーフィールドの中にIDナンバーを持つ必要があります。あなたが既にLDAP認証を使用しているのでしたら、通常これらは正常に動作します。</p>

<p>ユーザ登録内容は、ユーザのログイン時に更新されます。登録情報の同期をとるためのスクリプトを実行させることもできます。<em>enrol/ldap/enrol_ldap_sync.php</em>をご覧ください。</p>

<p>このプラグインでは、新しいグループがLDAP内に作成された時に、自動的に新しいコースを作成することもできます。</p>';
$string['enrol_ldap_autocreate'] = 'Moodleに存在しないコースに登録された場合、自動的にコースを作成します。';
$string['enrol_ldap_autocreation_settings'] = 'コース自動作成設定';
$string['enrol_ldap_bind_dn'] = 'bindユーザをユーザ検索に使用したい場合は、ここで指定してください。「 cn=ldapuser,ou=public,o=org 」のようになります。';
$string['enrol_ldap_bind_pw'] = 'bindユーザのパスワード。';
$string['enrol_ldap_category'] = '自動作成コースのカテゴリ。';
$string['enrol_ldap_course_fullname'] = 'オプション: 「名称」を取得するLDAPフィールド';
$string['enrol_ldap_course_idnumber'] = 'LDAPのユニークなidentifierにマップしてください。通常は <em>cn</em> または <em>uid</em>です。コース自動作成を使用する場合は値を固定してください。 ';
$string['enrol_ldap_course_settings'] = 'コース登録設定';
$string['enrol_ldap_course_shortname'] = 'オプション: 「省略名」を取得するLDAPフィールド';
$string['enrol_ldap_course_summary'] = 'オプション: 「概要」を取得するLDAPフィールド';
$string['enrol_ldap_editlock'] = 'ロック値';
$string['enrol_ldap_host_url'] = ' 「 ldap://ldap.myorg.com/ 」または「 ldaps://ldap.myorg.com/ 」のようにLDAPホストをURLの形式で指定してください。';
$string['enrol_ldap_objectclass'] = 'コース検索に使用するオブジェクトクラス。通常は「 posixGroup 」';
$string['enrol_ldap_server_settings'] = 'LDAPサーバ設定';
$string['enrol_ldap_student_contexts'] = '学生の登録時に割り当てられるグループリストのコンテキストです。コンテキストは「 ; 」で区切ってください。例: 「  ou=courses,o=org; ou=others,o=org 」 ';
$string['enrol_ldap_student_memberattribute'] = 'ユーザがグループに属して(登録されて)いる場合のメンバー属性。通常、「 member 」または「 memberUid 」';
$string['enrol_ldap_student_settings'] = '学生登録設定';
$string['enrol_ldap_teacher_contexts'] = '教師の登録時に割り当てられるグループリストのコンテキストです。コンテキストは「 ; 」で区切ってください。例: 「  ou=courses,o=org; ou=others,o=org 」 ';
$string['enrol_ldap_teacher_memberattribute'] = 'ユーザがグループに属して(登録されて)いる場合のメンバー属性。通常、「 member 」または「 memberUid 」';
$string['enrol_ldap_teacher_settings'] = '教師登録設定';
$string['enrol_ldap_template'] = 'オプション: 自動作成コースが設定をコピーするテンプレートコース。';
$string['enrol_ldap_updatelocal'] = 'ローカルデータの更新';
$string['enrol_ldap_version'] = 'あなたのサーバで使用しているLDAPプロトコルのバージョン。';
$string['enrolname'] = 'LDAP';
$string['thischarset'] = 'UTF-8';
$string['thisdirection'] = 'ltr';
$string['thislanguage'] = 'Japanese';

?>
