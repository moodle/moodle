<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.5 ALPHA (2005051500)


$string['description'] = '<p>您可以是用LDAP服务器来控制选课方面的信息。在这种情况下，您的LDAP树上的组会被映射为课程，而在这些组中应该包含的成员应该可以映射到学生。</p>

<p>假设课程是作为组来定义的，每个组中都有多个成员字段(<em>member</em>或<em>memberUid</em>)，而字段中则包含中用户的唯一标识。</p>

<p>要使用LDAP选可，您的用户<strong>必须</strong>都有一个唯一的标识字段。LDAP组则中的用户则是已经选课的学生。如果您已经是用LDAP认证，这通常会工作地很好。</p>

<p>当用户登录时选课信息会更新。您也可以运行一个小的脚本来让选可信息保持同步。看一下 <em>enrol/ldap/enrol_ldap_sync.php</em>。</p>

<p>这个插件可以在LDAP中有新组出现时自动创建课程，当然这需要进行相应设置。</p>';
$string['enrol_ldap_autocreate'] = '如果已经有人选课，但课程尚不存在于Moodle中，可以自动创建课程。';
$string['enrol_ldap_autocreation_settings'] = '自动创建课程设置';
$string['enrol_ldap_bind_dn'] = '如果希望是用bind-user来搜索用户，请在此指定，如“cn=ldapuser,ou=public,o=org”。';
$string['enrol_ldap_bind_pw'] = 'bind-user的密码';
$string['enrol_ldap_category'] = '自动创建课程的类别';
$string['enrol_ldap_course_fullname'] = '可选：从哪个LDAP字段获取全名。';
$string['enrol_ldap_course_idnumber'] = 'LDAP中的唯一标识，通常是<em>cn</em>或<em>uid</em>。';
$string['enrol_ldap_course_settings'] = '课程设置';
$string['enrol_ldap_course_shortname'] = '可选：从哪个LDAP字段中获取简称信息。';
$string['enrol_ldap_course_summary'] = '可选：从哪个LDAP字段中获取概要信息。';
$string['enrol_ldap_editlock'] = '上锁';
$string['enrol_ldap_host_url'] = '以链接形式指定LDAP主机，如“ldap://ldap.myorg.com/”或“ldaps://ldap.myorg.com/”。';
$string['enrol_ldap_objectclass'] = '用于搜索课程的objectClass，通常是“posixGroup”。';
$string['enrol_ldap_search_sub'] = '在下属目录中搜索组的归属信息。';
$string['enrol_ldap_server_settings'] = 'LDAP服务器设置';
$string['enrol_ldap_student_contexts'] = '在哪里可以找到包含着选课信息的组。不同的位置之间应当以分号“;”分割。例如：“ou=courses,o=org; ou=others,o=org”。';
$string['enrol_ldap_student_memberattribute'] = '当用户属于一个组时使用的成员属性。通常是“member”或“memberUid”。';
$string['enrol_ldap_student_settings'] = '学生选课设置';
$string['enrol_ldap_teacher_contexts'] = '在哪里可以找到包含着任课教师信息的组。不同的位置之间应当以分号“;”分割。例如：“ou=courses,o=org; ou=others,o=org”。';
$string['enrol_ldap_teacher_memberattribute'] = '当用户属于一个组时使用的成员属性。通常是“member”或“memberUid”。';
$string['enrol_ldap_teacher_settings'] = '教师任教设置';
$string['enrol_ldap_template'] = '可选：自动创建的课程可以从一个作为模板的课程中复制设置。';
$string['enrol_ldap_updatelocal'] = '更新本地数据';
$string['enrol_ldap_version'] = '您的服务器使用的LDAP协议的版本';
$string['enrolname'] = 'LDAP';
$string['enrol_ldap_general_options'] = "常规选项";

?>
