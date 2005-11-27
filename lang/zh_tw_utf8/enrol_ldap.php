<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.5.2 (2005060220)


$string['description'] = '<p>您可以使用LDAP伺服器控制您的課程登記.</br>
假定您的LDAP目錄樹包含對應到課程的群組,而每個群組/課程將會有對應到學生的會員資料.</p>
<p>這是假定您的課程在LDAP中是以群組方式定義,而每個群組有多重的會員欄位如
(<em>member</em> 或 <em>memberUid</em>)可以包含使用者的一個唯一識別項(unique identification).</p>
<p>要使用LDAP登記,您的用者<strong>必須</strong> 
擁有一個有效的idnumber欄位.LDAP群組必須在會員欄位中有該idnumber以讓使用者能登記到課程中.
如果您己擁有LDAP認識時,此功能通常可以正常運作.</p>
<p>選課登記當使用者登入時將會自動更新,您也可以執行一個script讓選課登記同步化.請參照<em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
<p>此外掛也可以設為當LDAP中有新群組產生時自動建立新課程.</p>';
$string['enrol_ldap_autocreate'] = '如果還沒有人在Moodle中登記一個課程的話,課程可以自動化產生';
$string['enrol_ldap_autocreation_settings'] = '課程自動化產生設定';
$string['enrol_ldap_bind_dn'] = '如果您要讓嵌入的使用者(bind-user)搜尋其他使用者,請在此設定,如:\'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = '嵌入使用者(bind-user)的密碼';
$string['enrol_ldap_category'] = '自動建立(auto-created )課程的類別';
$string['enrol_ldap_course_fullname'] = '選項:取得全名的LDAP欄位';
$string['enrol_ldap_course_idnumber'] = 'LDAP中的唯一識別項地圖,通常是<em>cn</em>或<em>uid</em>.建議當您使用自動建立課程功能時,鎖定這個值. ';
$string['enrol_ldap_course_settings'] = '課程登記設定';
$string['enrol_ldap_course_shortname'] = '選項:取得簡稱的LDAP欄位';
$string['enrol_ldap_course_summary'] = '選項:取得摘要的LDAP欄位';
$string['enrol_ldap_editlock'] = '鎖定值';
$string['enrol_ldap_general_options'] = '一般選項';
$string['enrol_ldap_host_url'] = '以URL格式指定LDAP主機如:\'ldap://ldap.myorg.com/\' 
或 \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_objectclass'] = '用來搜尋課程的物件類別,通常是\'posixGroup\'.';
$string['enrol_ldap_search_sub'] = '從子節點搜尋群組成員';
$string['enrol_ldap_server_settings'] = 'LDAP 伺服器設定';
$string['enrol_ldap_student_contexts'] = '存放學生登記群組的節點(context)列表,以\";\"號分割不同的節點,如:\'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = '學生會員屬性,當使用者屬於(加入)群組時,通常是\'member\'
或\'memberUid\'.';
$string['enrol_ldap_student_settings'] = '學生登記設定';
$string['enrol_ldap_teacher_contexts'] = '存放教師登記群組的節點(context)列表,以\";\"號分割不同的節點,如:\'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = '教師會員屬性,當使用者屬於(加入)群組時,通常是\'member\'
或\'memberUid\'.';
$string['enrol_ldap_teacher_settings'] = '教師登記設定';
$string['enrol_ldap_template'] = '選擇性:自動建立的課程可以從樣板課程中複製他們的設定.';
$string['enrol_ldap_updatelocal'] = '更新本地資料';
$string['enrol_ldap_version'] = '您伺服器目前使用的LDAP協定版本 ';
$string['enrolname'] = 'LDAP';

?>
