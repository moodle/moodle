<?PHP // $Id$ 
      // enrol_database.php - created with Moodle 1.5.3+ (2005060230)


$string['autocreate'] = '如果Moodle中某课程不存在，则当有人选修课程时系统会自动创建课程。';
$string['category'] = '自动创建的课程所属类别';
$string['dbhost'] = '数据库服务器主机名';
$string['dbname'] = '指定数据库';
$string['dbpass'] = '访问服务器的密码';
$string['dbtable'] = '数据库中的表格';
$string['dbtype'] = '数据库服务器类型';
$string['dbuser'] = '访问服务器的用户名';
$string['description'] = '您可以是用外部的数据库(任意类型)来控制选课信息。在这种情况下，通常假设您的外部数据库包含了一个字段对应着课程的ID和一个字段对应着用户的ID。它们将与您选择的本地课程和用户表中的字段相关联。';
$string['enrolname'] = '外部数据库';
$string['field_mapping'] = '字段映射';
$string['general_options'] = '通用选项';
$string['localcoursefield'] = 'course表格中用于和远程数数据匹配的字段名(如idnumber)';
$string['localuserfield'] = '本地user表格中用于和远程数据匹配的字段名(如idnumber)';
$string['remotecoursefield'] = '在远程数据库中能找到课程ID的字段';
$string['remoteuserfield'] = '在远程数据库中能找到用户ID的字段';
$string['server_settings'] = '服务器设置';
$string['template'] = '选项：自动创建的课程可以从模板课程中拷贝设置。';

?>
