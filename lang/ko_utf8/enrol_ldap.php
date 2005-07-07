<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.5 ALPHA (2005051500)


$string['description'] = '<p>LDAP서버를 이용하여 당신은 등록자를 관리 할수 있습니다. . 
LDAP목차는 과목 내용(map)의 
그룹을 포함하고 있고. 각각의 그룹/강좌은 학생들용 내용(mpa)의 회원제 접속허가를 가지고 있다고 가정할수 있다.<p>
<p>또한 각 강좌는 LDAP의 구분에 의하여 나누어지고 각 그룹은 여려개의 활동영혁을 가지게 된다(<em>member</em> or <em>memberUid</em>) 그것은 각 유저마다 서로 다른 ID를 가지게 한다.</p>
<p>LDAP등록을 이용하려면 사용자들은 <strong>꼭!</strong> 
유요한 ID값을 가지고 있어야 한다. 
또한 LDAP그룹은 과목으로의 등록을 위해서 각 사용자의 영역에 맞는 ID값을 가지고 있어야 한다. 
만약 LDAP 인증을 사용하고 있다면 이러한 것들은 잘 작동될것이다. 
.</p>
<p>등록은 사용자가 로그인할때 업데이트 된다.
또한 등록 서류를 싱크시키기 위해서 스크립트를 사용할수도 있다. 
다음 파일을 참고 하라 
<em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
<p>이 플러그인은 새 그룹이 LDAP에 들옥되면 자동적으로 새 강좌를 생성한다.
</p>';
$string['enrol_ldap_autocreate'] = '만일 Moodle에 들록되지 않은 코스가 등록되면 자동으로 그 코스를 생성할 것이다. ';
$string['enrol_ldap_autocreation_settings'] = '자동 생성 코스 설정';
$string['enrol_ldap_bind_dn'] = '만일 각 search사용자들에 대해 bind-user 를 사용하고 싶다면 다음을 설정하십시오. ex) \'cn=ldapuser,ou=public,o=org\' ';
$string['enrol_ldap_bind_pw'] = 'bind-user를 위한 패스워드';
$string['enrol_ldap_category'] = '자동 생성 코스의 분류(카테고리)';
$string['enrol_ldap_course_fullname'] = '옵션: 전체이름을 위한 LDAP 창';
$string['enrol_ldap_course_idnumber'] = 'LDAP에서의 서로다른 identifier을 위한 지도, 대부분
<em>cn</em>나 <em>uid</em>. 만일 자동 코스 생성기능을 사용하면 값 수정을 막아 놓는 것을 추천 합니다.';
$string['enrol_ldap_course_settings'] = '코스 등록 설정';
$string['enrol_ldap_course_shortname'] = '옵션: 별명(shortname)을 위한 LDAP 창';
$string['enrol_ldap_course_summary'] = '옵션: 간단한 정리를 위한 LDAP 창';
$string['enrol_ldap_editlock'] = '값수정 잠금';
$string['enrol_ldap_host_url'] = 'Specify LDAP host in URL-창의 LDAP호스트 값을 다음과 같이 입력하시오
\'ldap://ldap.myorg.com/\' 
or \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_objectclass'] = 'objectClass 가 search courses로 사용되었다.대부분
\'posixGroup\'';
$string['enrol_ldap_search_sub'] = '하부내용에서 그룹 회원 찾기';
$string['enrol_ldap_server_settings'] = 'LDAP 서버 설정';
$string['enrol_ldap_student_contexts'] = '그룹과 학생들의 등록서류가 있는 곳의 내용 목록

다음 내용과는 별도의 것이다 \';\'. 예를 들어: 
\'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = '회원 속성, 사용자들이 (등록루) 그룹에속해 있다면
 . 대부분 \'member\'
나 \'memberUid\'.일것이다';
$string['enrol_ldap_student_settings'] = '학생 등록 설정';
$string['enrol_ldap_teacher_contexts'] = '그룹과 선생들의 등록 서류가 있는 곳의 내용 목록
. 다음 내용과는 별도의 것이다 \';\'. 예를 들어: 
\'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = '회원 속성, 사용자들이 (등록루) 그룹에속해 있다면
 . 대부분 \'member\'
나 \'memberUid\'.일것이다';
$string['enrol_ldap_teacher_settings'] = '선생 등록 설정';
$string['enrol_ldap_template'] = '옵션: 자동 생성 코스는 그들의 설정값을 template코스에서 가져온다.';
$string['enrol_ldap_updatelocal'] = '현재 시간 업데이트';
$string['enrol_ldap_version'] = '당신의서버가 사용하고 있는 LDAP 프로토콜의 버젼';
$string['enrolname'] = 'LDAP';

?>
