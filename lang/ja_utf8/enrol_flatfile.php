<?PHP // $Id$ 
      // enrol_flatfile.php - created with Moodle 1.5 unstable development (2004092000)


$string['description'] = 'この方法は、あなたが指定した場所にある特別にフォーマットされたテキストファイルを繰り返しチェックします。ファイルのフォーマットは下記のようになります:
<pre>
add, student, 5, CF101
add, teacher, 6, CF101
add, teacheredit, 7, CF101
del, student, 8, CF101
del, student, 17, CF101
add, student, 21, CF101, 1091115000, 1091215000
</pre>';
$string['enrolname'] = 'フラットファイル';
$string['filelockedmail'] = 'ファイルベースのユーザ登録で使用しているファイル($a)はcronプロセスによる削除は行われません。通常、ファイルパーミッションの問題により削除されません。Moodleが削除できるようにファイルのパーミッションを変更してください。変更しない場合は、この処理が繰り返し行われます。';
$string['filelockedmailsubject'] = '重大なエラー:エンロールメントファイル';
$string['location'] = 'ファイルの場所';
$string['mailadmin'] = '管理者にメール通知';
$string['mailusers'] = 'ユーザにメール通知';
$string['thischarset'] = 'UTF-8';
$string['thisdirection'] = 'ltr';
$string['thislanguage'] = 'Japanese';

?>
