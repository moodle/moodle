<?PHP // $Id$ 
      // enrol_flatfile.php - created with Moodle 1.5 ALPHA (2005042300)


$string['description'] = '在这种方式中，Moodle重复地检查并处理一个特殊格式的文本文件，它的位置由您指定。文件格式如下：
<pre>
add, student, 5, CF101
add, teacher, 6, CF101
add, teacheredit, 7, CF101
del, student, 8, CF101
del, student, 17, CF101
add, student, 21, CF101, 1091115000, 1091215000
</pre>';
$string['enrolname'] = '普通文件';
$string['filelockedmail'] = 'cron进程无法删除您用于选课的文本文件。请设置好权限以便Moodle删除这个文件，否则它将会被重复处理。';
$string['filelockedmailsubject'] = '重要错误：选课文件';
$string['location'] = '文件位置';
$string['mailadmin'] = '通过E-mail提醒管理员';
$string['mailusers'] = '通过E-mail提醒用户';

?>
