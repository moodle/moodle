<?PHP // $Id$ 
      // auth.php - created with Moodle 1.4.2 nearly (2004083121)


$string['auth_dbdescription'] = '이 방식은 외부의 데이터베이스 테이블을 통해 사용자의 아이디와 비밀번호가 유효한 지 확인합니다. 다른 항목의 정보도 Moodle사의 데이터 베이스에 복사될 수 있도록 설정이 가능합니다.';
$string['auth_dbextrafields'] = '이 항목들은 선택 사항입니다. 외부 데이타베이스 항목으로부터 Moodle의 사용자 정보 항목으로 채울 수 있습니다. 사용자가 로그인 한 후 이 항목들을 수정할 수 있습니다.';
$string['auth_dbfieldpass'] = '비밀번호를 포함하는 필드명';
$string['auth_dbfielduser'] = '사용자 아이디를 포함하는 필드명';
$string['auth_dbhost'] = '데이타베이스 서버를 호스팅하는 컴퓨터';
$string['auth_dbname'] = '데이타베이스 자체의 이름';
$string['auth_dbpass'] = '사용자 아이디와 연결되는 비밀번호';
$string['auth_dbpasstype'] = '비밀번호 필드의 포멧을 구체적으로 적으세요. PostNuke와 같은 웹 프로그램으로 연결하기 위해서는 MD5 암호화를 사용하는 것이 유용합니다';
$string['auth_dbtable'] = '데이타베이스의 테이블명';
$string['auth_dbtitle'] = '외부 데이타베스 사용하기';
$string['auth_dbtype'] = '데이타베이스 유형(See the <a href=\"../lib/adodb/readme.htm#drivers\">ADOdb documentation</a>)에 대해 자세히 알고 싶으면';
$string['auth_dbuser'] = '데이타베이스의 읽기 권한을 가진 사용자명';
$string['auth_emaildescription'] = '이메일 확인 인증은 기본 인증 방법입니다. 사용자가 가입할 때, 새로운 사용자 아이디와 비밀번호를 선택하면, 사용자의 이메일 계정으로 확인 메일이 보내집니다. 이 메일에는 계정을 활성화할 수 있는 안전한 링크를 포함합니다. 다음 로그인할 경우에는 무들 데이타베이스에 저장된 값을 참고하게 됩니다.';
$string['auth_emailtitle'] = '이메일 기반 인증';
$string['auth_imapdescription'] = 'IMAP 서버를 사용하여 사용자의 이름과 패스워드의 유용성을 확인합니다';
$string['auth_imaphost'] = 'IMAP 서버의 주소. DNS 이름을 사용하지 않고 IP주소를 사용합니다.';
$string['auth_imapport'] = 'IMAP 서버의 포트 숫자를 나타냅니다. 보통 이 숫자는 143이거나 993입니다';
$string['auth_imaptitle'] = 'IMAP 서버의 사용';
$string['auth_imaptype'] = 'IMAP 서버의 유형. IMAP 서버는 다른 유형의 인증방법이나 신용방법을 사용 할 수 있습니다. ';
$string['auth_ldap_bind_dn'] = '만약 당신이 bind-user(운영자급 사용자를 지칭합니다)을 이용하여 사용자들을 찾길 바란다면 이곳에 자세한 것을 기록해야합니다. 예를 들면 \'cn=ldapuser,ou=public,o=org\' 이런 것들이 있습니다.';
$string['auth_ldap_bind_pw'] = 'Bind-user를 위한 패스워드';
$string['auth_ldap_contexts'] = '사용자들이 어디에 위치해있는지 나타내는 목록입니다. 다른 종류의 목록들은 예를 들어 \'ou=users,o=org; ou=others,o=org\'게 분류 합니다. ';
$string['auth_ldap_create_context'] = '만약 당신이 e mail확인으로 사용자를 생성시킬수 있다면 어디서 사용자들이 생성되었는지를 문맥상에 명시하시오. 이 문맥은 보안상의 문제를 막기위해 다른 사용자들과는 다르게 명기되어야 합니다. ldap_context-variable에 작성된 문맥을 포함할 필요가 없습니다. Moodle이 자동적으로 작성된 문맥에서 사용자를 찾아줄 것입니다. ';
$string['auth_ldap_creators'] = '새로운 코스들을 만드는 사람들의 목록입니다. 보통 \'cn=teachers,ou=staff,o=myorg\'형식으로 사람들을 분류합니다. ';
$string['auth_ldap_host_url'] = '\'ldap://ldap.myorg.com/\' 또는  \'ldaps://ldap.myorg.com/\' 식으로 URL상의 LDAP 호스트를 명기합니다. ';
$string['auth_ldap_memberattribute'] = '사용자들이 한 그룹안에 속해 있다면 보통 숫자를 사용하여 사용자를 명기하시오.';
$string['auth_ldap_search_sub'] = 'Subcontext(하위문맥)에서 사용자들을 찾고 싶다면  <> 0값을 넣으세요.';
$string['auth_ldap_update_userinfo'] = 'LDAP에서 Mooddle로 사용자의 정보를 갱신한다.(성, 이름, 주소 등.)  Mapping 정보를 위해 /auth/ldap/attr_mappings.php 이곳을 보십시오. ';
$string['auth_ldap_user_attribute'] = '이 속성은(보통 \'cn\' 입니다) 사용자들을 찾아내기 위해 이름을 사용합니다. ';
$string['auth_ldapdescription'] = '이 방법은 외부 LDAP서버에 대항해 인증을 해 줍니다. 만약 계정과 비밀번호가 유요하다면 Moodle은 데이터베이스 안에 새로운 사용자를 만듭니다. Moodle은 LDAP와 자체 필드에 미리 작성된 사용자의 특성을 읽을 수 있습니다. 로그인 방법을 따라야 계정과 비밀번호가 확인 되어 집니다. ';
$string['auth_ldapextrafields'] = '이 필드는 선택사항입니다. 당신이 여기에 명시한 LDAP서버에서 정보와 함께 Moodle 사용자 필드를 프리필(pre-fill)을 결정 할 수 있습니다. 만약 당신이 이 필드를 빈 공간으로 남겨둔다면, LDAP서버에서 아무것도 이동이 되지 않으며 Moodle 결점이 대신 사용 되어집니다. 어떠한 경우라도 사용자가 로그인을 한 후, 이 필드의 모든 것을 에디트 할 수 있습니다. ';
$string['auth_ldaptitle'] = 'LDAP 서버의 사용';
$string['auth_manualdescription'] = '이 방법은 사용자들이 그들의 계정을 생성하기 위해 다른 모든 방법들을 제거합니다. 모든 계정들은 운영자에 의해 손수 만들어져야 합니다. ';
$string['auth_manualtitle'] = '직접만든 계정만이 가능합니다. ';
$string['auth_multiplehosts'] = '여러명의 호스트들은 host1.com;host2.com;host3.com 식으로 명기되어 질 수 있습니다.';
$string['auth_nntpdescription'] = 'NNPP 서버를 사용하여 사용자의 이름과 패스워드의 유용성을 확인합니다';
$string['auth_nntphost'] = 'NNPP 서버의 주소. DNS가 아닌 IP 숫자를 사용합니다.';
$string['auth_nntpport'] = '서버 포트 (119가 가장 무난합니다)';
$string['auth_nntptitle'] = 'NNPP 서버를 사용합니다. ';
$string['auth_nonedescription'] = '사용자들은 외부 보안시스템을 거치지 않거나 이메일확인 작업 없이 즉시 계정을 만들 수 있습니다. 하지만 만들기 전에 이 문제가 가져올 수 있는 보안상, 등록상의 문제를 생각해 보시기 바랍니다. ';
$string['auth_nonetitle'] = '불인증';
$string['auth_pop3description'] = 'POP3 서버를 사용하여 사용자의 이름과 패스워드의 유용성을 확인합니다';
$string['auth_pop3host'] = 'POP3 서버의 주소. DNS가 아닌 IP 숫자를 사용합니다.';
$string['auth_pop3port'] = '서버 포트 (110이 가장 무난합니다)';
$string['auth_pop3title'] = 'POP3 서버를 사용합니다.';
$string['auth_pop3type'] = '서버의 형식. 만약 당신의 서버가 증면된 보안을 사용한다면 POP3cert를 선택하세요.';
$string['auth_user_create'] = '만들수 있는 사용자';
$string['auth_user_creation'] = '새로운 사용자는 외부 인증 소스 혹은 확인되어진 이메일을 통해 계정을 생성할 수 있습니다. 만약 당신이 이것이 가능하다면 사용자 생성을 위한 Moodle의 특별한 옵션을 형성하는 것을 기억하십시오.';
$string['auth_usernameexists'] = '선택하신 사용자명은 이미 존재합니다. 다른 이름의 사용자명을 선택하세요.';
$string['authenticationoptions'] = '인증 옵션들';
$string['authinstructions'] = '이곳에서 당신은 사용자들이 사용하고 있는 계정과 비밀번호의 정보를 제공할수 있습니다. 당신이 작성한 글자들은 로그인 페이지에 나타납니다. 하지만 만약 당신이 이곳을 빈칸으로 놔둔다면 로그인 페이지에 정보가 공개되지 않습니다. ';
$string['changepassword'] = '패스워드 URL을 바꾼다';
$string['changepasswordhelp'] = '만약 계정과 비밀번호를 잊어버렸다면 이곳에서 계정과 비밀번호를 찾거나 혹은 바꿀 수 있습니다. 이 형식은 로그인 페이지나 사용자 페이지에서  버튼형식으로 제공되어지지만  이곳을 빈칸으로 놓아둔다면 버튼은 웹페이지에 나타나지 않습니다. ';
$string['chooseauthmethod'] = '인증 방법 선택:';
$string['guestloginbutton'] = '손님 접속 버튼';
$string['instructions'] = '도움말';
$string['md5'] = 'MD5 인증 ';
$string['plaintext'] = '단순 텍스트';
$string['showguestlogin'] = '로그인 페이지에서 손님 로그인 버튼을 보이거나 숨길 수 있습니다.';
$string['thischarset'] = 'euc-kr';
$string['thisdirection'] = 'ltr';

?>
