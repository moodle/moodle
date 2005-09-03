<?PHP // $Id$ 
      // auth.php - created with Moodle 1.6 development (2005060201)


$string['alternatelogin'] = '만약 당신이 URL을 들어와 여기에 있다면, 이 사이트를 위한 로그인 페이지에서 사용될 것이다. 이 페이지는 행동 자산 설치 <strong>\'$a\'</strong> 를 가진 형태를 포함하여야 하고 <strong> 사용자 이름 </strong>  <strong>password</strong> 분야를 다시 돌려야 한다. 
<br /> 당신이 이 사이트에서 나갈때에 정확하지 않은 URL 로 들어오는 것을 주의하십시오. 이 로그인 페이지의 초기 설정을 사용하는 설치 빈칸을 남겨두십시오. ';
$string['alternateloginurl'] = '로그인 URL을 교체하십시오.';
$string['auth_cas_baseuri'] = '서버의 URL (기초 uri가 없는 어느것) 
<br /> 예를 들면, 만약 CAS 서버가 호스트에게 응답한다면, 도메인 fr/CAS/ then<br />cas_baseuri = CAS/';
$string['auth_cas_create_user'] = '만약 당신이 CAS를 삽입하고 싶다면, 모듈 데이터 베이스에서 사용자를 확증하십시오. 만약 단지 모듈 데이터 베이스에서 이미 존재하는 사용자가 아니라면, 로그인 할 수 있다. ';
$string['auth_cas_enabled'] = '만약 당신이 CAS 인증을 원한다면 이것을 켜십시오.';
$string['auth_cas_hostname'] = 'CAS 서버의 후원자이름 <br />eg: host.domain.fr';
$string['auth_cas_invalidcaslogin'] = '유감스럽지만, 당신의 로그인은 실패하였다 - 당신은 저술할 수 없다. ';
$string['auth_cas_language'] = '선택된 언어';
$string['auth_cas_logincas'] = '안전한 연결 접근';
$string['auth_cas_port'] = 'CAS 서버의 포트';
$string['auth_cas_server_settings'] = 'CAS 서버 배열';
$string['auth_cas_text'] = '안전한 연결';
$string['auth_cas_version'] = 'CAS의 버전';
$string['auth_casdescription'] = '이 방법은 환경에서의 개개의 사인에서 사용자를 확증하기 위해 CAS 서버(중앙 확증 서비스)를 사용한다. 당신은 또한 단순한 LDAP 확증을 사용한다. 만약 주어진 사용자 이름과 비밀 번호가 CAS에 따라 근거가 확실하다면, 모듈은 그것의 데이터베이스에서 새로운 사용자 엔트리를 창작하고, 만약 요구되면 LDAP로부터 사용자 변경을 취한다. 로그인을 따르는 것에서 단지 사용자 이름과 비밀번호는 체크된다. ';
$string['auth_castitle'] = 'CAS 서버(SSO)를 사용하시오.';
$string['auth_common_settings'] = '일반설정';
$string['auth_data_mapping'] = '데이타 계획';
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
$string['auth_fccreators'] = '회원들이 있는 그룹의 리스트는 새로운 코스를 만들기 위해 허락된다. 분류된 다수의 그룹들은 함께 \';\' 이름들은 반드시 정확하게 최고급 서버에 철자가 쓰여져야 한다. 시스템은 case-sensitive이다. ';
$string['auth_fcdescription'] = '이 방법은 정해진 사용자성명인지 그리고 비밀번호가 유효한지 확인하기 위해서 펄스트클래스 서버를 사용합니다.';
$string['auth_fcfppport'] = '서버 포트(3333이 가장 일반적입니다.)';
$string['auth_fchost'] = '펄스트클래스 서버 주소. IP 주소나 DNS 이름을 입력하세요';
$string['auth_fcpasswd'] = '상층부 설명을 위한 비밀번호';
$string['auth_fctitle'] = '펄스트클래스 서버를 이용하세요.';
$string['auth_fcuserid'] = '최고급을 위해 \'Subadministrator\' 설치 특권과 함께 설명한다. ';
$string['auth_fieldlock'] = '사용자 정보 잠김.';
$string['auth_fieldlock_expl'] = '<p><b>사용자 정보 잠김:</b> 만약 보기가 허락된다면 무들 프로그램은 사용자 정보 분야를 바로 편집해서 정보 유출을 방지할것입니다. 이 옵션을 사용하면 사용자 정보 파일은 외부 시스템으로 부터 지속적으로 지킬수 있습니다. </p>';
$string['auth_fieldlocks'] = '사용자 분야 잠금';
$string['auth_fieldlocks_help'] = '<p>당신은 사용자 정보 분야를 잠굴수 있습니다. 이 기능은 사용자 정보 기록이나 사용자 정보를 사이트에서 운영자가 직접 유지 관리하는데 유용합니다. 무들 프로그램에 의해 잠긴 분야가 있다면 사용자 계정을 새로만들거나 하지 않으면 계정은 사용할수 없게 될 것입니다.</p><p>이런 문제가 생기지 않길 바란다면 설정을 칸이 비워져있을땐 잠기지 않음으로 설정하십시오.</p>';
$string['auth_imapdescription'] = 'IMAP 서버를 사용하여 사용자의 이름과 패스워드의 유용성을 확인합니다';
$string['auth_imaphost'] = 'IMAP 서버의 주소. DNS 이름을 사용하지 않고 IP주소를 사용합니다.';
$string['auth_imapport'] = 'IMAP 서버의 포트 숫자를 나타냅니다. 보통 이 숫자는 143이거나 993입니다';
$string['auth_imaptitle'] = 'IMAP 서버의 사용';
$string['auth_imaptype'] = 'IMAP 서버의 유형. IMAP 서버는 다른 유형의 인증방법이나 신용방법을 사용 할 수 있습니다. ';
$string['auth_ldap_bind_dn'] = '만약 당신이 bind-user(운영자급 사용자를 지칭합니다)을 이용하여 사용자들을 찾길 바란다면 이곳에 자세한 것을 기록해야합니다. 예를 들면 \'cn=ldapuser,ou=public,o=org\' 이런 것들이 있습니다.';
$string['auth_ldap_bind_pw'] = 'Bind-user를 위한 패스워드';
$string['auth_ldap_bind_settings'] = '설정 확정';
$string['auth_ldap_contexts'] = '사용자들이 어디에 위치해있는지 나타내는 목록입니다. 다른 종류의 목록들은 예를 들어 \'ou=users,o=org; ou=others,o=org\'게 분류 합니다. ';
$string['auth_ldap_create_context'] = '만약 당신이 e mail확인으로 사용자를 생성시킬수 있다면 어디서 사용자들이 생성되었는지를 문맥상에 명시하시오. 이 문맥은 보안상의 문제를 막기위해 다른 사용자들과는 다르게 명기되어야 합니다. ldap_context-variable에 작성된 문맥을 포함할 필요가 없습니다. Moodle이 자동적으로 작성된 문맥에서 사용자를 찾아줄 것입니다. ';
$string['auth_ldap_creators'] = '새로운 코스들을 만드는 사람들의 목록입니다. 보통 \'cn=teachers,ou=staff,o=myorg\'형식으로 사람들을 분류합니다. ';
$string['auth_ldap_expiration_desc'] = '무력한 만기 비밀 번호 확인이나 LDAP로부터 정확하게 비밀번호 만기 시간을 읽는 LDAP를 아무것도 선택하지 마십시오.';
$string['auth_ldap_expiration_warning_desc'] = '비밀번호 만기 경고 전에 얼마간의 날들의 여유가 있다.';
$string['auth_ldap_expireattr_desc'] = '선택 사항 : 비밀 번호 만기 시간이 저장되는 속성이 우선한다. 비밀번호 만기 시간';
$string['auth_ldap_graceattr_desc'] = '선택사항 : ';
$string['auth_ldap_gracelogins_desc'] = 'LDAP 유예기간 로그인 지원이 가능하다. 비밀번호가 만기된 후에 사용자는 유예기간 고르인이 0이 되기 전까지 로그인 가능하다. 만약 비밀 번호가 만기된다면 이 설치가 가능한 것은 유예 기간 고르인 메세지를 보여준다. ';
$string['auth_ldap_host_url'] = '\'ldap://ldap.myorg.com/\' 또는  \'ldaps://ldap.myorg.com/\' 식으로 URL상의 LDAP 호스트를 명기합니다. ';
$string['auth_ldap_login_settings'] = '로그인 설정하기';
$string['auth_ldap_memberattribute'] = '사용자들이 한 그룹안에 속해 있다면 보통 숫자를 사용하여 사용자를 명기하시오.';
$string['auth_ldap_objectclass'] = '선택 사항 : 대상의 계급이 ldap_user_type에서 이름이나 사용자 찾기에 사용되는 것이 우선이다. 보통 당신은 이것에 부담을 느낄 필요가 없다. ';
$string['auth_ldap_opt_deref'] = '다른 경우 탐색 동안에 얼마나 통제되는 것인지 결정하십시오. 다음의 평가 중 하나를 선택하십시오.
\"아니오\" (LDAP_DEREF_NEVER) or 
\"예\" (LDAP_DEREF_ALWAYS) ';
$string['auth_ldap_passwdexpire_settings'] = 'LDAP 비밀번호의 만료 설정';
$string['auth_ldap_preventpassindb'] = '비밀번호 노출 방지를 위해서 무들의 데이터 베이스에 저장에 대한 물음에 \"네\" 로 설정해 주십시오.';
$string['auth_ldap_search_sub'] = 'Subcontext(하위문맥)에서 사용자들을 찾고 싶다면  <> 0값을 넣으세요.';
$string['auth_ldap_server_settings'] = 'LDAP 서버 설정';
$string['auth_ldap_update_userinfo'] = 'LDAP에서 Mooddle로 사용자의 정보를 갱신한다.(성, 이름, 주소 등.)  Mapping 정보를 위해 /auth/ldap/attr_mappings.php 이곳을 보십시오. ';
$string['auth_ldap_user_attribute'] = '이 속성은(보통 \'cn\' 입니다) 사용자들을 찾아내기 위해 이름을 사용합니다. ';
$string['auth_ldap_user_settings'] = '사용자 검색 설정';
$string['auth_ldap_user_type'] = '어떻게 사용자가 LDAP에서 저장되는 지를 선택하십시오. 이 설치는 또한 얼마나 특정한 로그인이 만기되었는지, 사용자 창작이 작동 될 것이다. ';
$string['auth_ldap_version'] = '당신 서버 LDAP 프로토콜 버전을 사용하고 있습니다.';
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
$string['auth_pamdescription'] = '이 방법은 이 서버에서 원래 사용자 이름에 접근하는 PAM을 사용한다. 당신은 이 모듈을 사용하기 위해 
<a href=\"http://www.math.ohio-state.edu/~ccunning/pam_auth/\" target=\"_blank\">PHP4 PAM Authentication</a> 를 설치해야 한다. ';
$string['auth_pamtitle'] = 'PAM(';
$string['auth_passwordisexpired'] = '당신의 비밀번호가 만료되었습니다. 지금 비밀번호를 바꾸시겠습니까?';
$string['auth_passwordwillexpire'] = '당신의 비밀번호가 $a요일에 만료됩니다. 지금 비밀번호를 바꾸시겠습니까?';
$string['auth_pop3description'] = 'POP3 서버를 사용하여 사용자의 이름과 패스워드의 유용성을 확인합니다';
$string['auth_pop3host'] = 'POP3 서버의 주소. DNS가 아닌 IP 숫자를 사용합니다.';
$string['auth_pop3mailbox'] = '연결 시도를 위한 우편함의 이름 (보통 INBOX)';
$string['auth_pop3port'] = '서버 포트 (110이 가장 무난합니다)';
$string['auth_pop3title'] = 'POP3 서버를 사용합니다.';
$string['auth_pop3type'] = '서버의 형식. 만약 당신의 서버가 증면된 보안을 사용한다면 POP3cert를 선택하세요.';
$string['auth_shib_convert_data'] = 'API에 대한 변경 정보를 수집하십시오.';
$string['auth_shib_convert_data_description'] = '당신은 이 좀더 암호에 의해 제공된 데이터를 변경하기 위해 이 API를 사용할 수 있다. 좀 더 많은 규정을 위해
<a href=\"../auth/shibboleth/README.txt\" target=\"_blank\">README</a> 을 읽어보십시오.';
$string['auth_shib_convert_data_warning'] = '웹서버 진행에 의해 파일이 존재하지 않거나 읽을 수 없다. ';
$string['auth_shib_instructions'] = '만약 당신의 규정이 그것을 지지 한다면 암호에 접근하기 위해 <a href=\"$a\"> 암호 로그인 </a>을 사용하라. .<br />그렇지 않으면 보여지는 지금에서부터 정상적인 로그인을 사용십시오.';
$string['auth_shib_instructions_help'] = '여기에 당신은 암호를 설명하기 위한 당신의 사용자 관습 명령을 제공하여야 한다. 그것은 명령 섹션의 로그인 페이지에 보여질 것이다. 그것은 암호가 보호되는 고쳐쓰는 사용자의 공급원 링크를 \"<b>$a</b>\" 암호 사용자가 모듈에 로그인 할 수 있기 위해 포함하여야 한다. 만약 당신이 이 빈칸을 남겨둔다면, 표준 명령이 사용될 것이다 (특정 암호가 아니라)';
$string['auth_shib_only'] = '단지 암호말';
$string['auth_shib_only_description'] = '만약 암호 확증이 강요된다면 이 옵션을 체크하십시오.';
$string['auth_shib_username_description'] = '모듈 사용자 이름으로 사용되어야 하는 다양한 웹서버 암호 환경의 이름을 지어라. ';
$string['auth_shibboleth_login'] = '암호말 고르인';
$string['auth_shibboleth_manual_login'] = '수동 로그인';
$string['auth_shibbolethdescription'] = '이 방법을 사용하는 사용자는 창조적이며 <a href=\"http://shibboleth.internet2.edu/\" target=\"_blank\">Shibboleth</a>을 사용하는 것을 증명한다. 
<br> <a href=\"../auth/shibboleth/README.txt\" target=\"_blank\">을 읽는 것을 확신하라. 
어떻게 당신의 모듈을 비빌먼호와 함께 설치하는 가에 대해
README</a>';
$string['auth_shibbolethtitle'] = '암호';
$string['auth_updatelocal'] = '내부 데이터의 개정';
$string['auth_updatelocal_expl'] = '<p><b> 내부 데이터의 개정 :</b> 만약 가능 하다면, 분야는 (외부 근거로부터) 사용자가 로그인하는 모든 시간이나 사용자가 동기화가 있을때 업데이트 될 것이다. 분야는 위치상으로 잠겨 있어야 하며 업데이트 된다. ';
$string['auth_updateremote'] = '외부데이터의 업데이트';
$string['auth_updateremote_expl'] = '<p><b> 외부 데이터를 업데이트 하시오 :</b> 만약 가능하다면, 외부의 근거는 사용자 기록이 업데이트 될때 업데이트 될것이다. 분야들은 편집을 허락하는 것이 허락되어야 한다. ';
$string['auth_updateremote_ldap'] = '<p><b>주목:</b> 외부의 LDAP 데이터를 업데이트 하는 것은 당신이 모든 사용자가 기록되는 특권을 편집하는 묶여있는 사용자를 요구한다. 현재는 많은 가치를 지닌 속성을 보존하지 않으며 여분의 가치들은 업데이트로 제거된다.</p> ';
$string['auth_user_create'] = '만들수 있는 사용자';
$string['auth_user_creation'] = '새로운 사용자는 외부 인증 소스 혹은 확인되어진 이메일을 통해 계정을 생성할 수 있습니다. 만약 당신이 이것이 가능하다면 사용자 생성을 위한 Moodle의 특별한 옵션을 형성하는 것을 기억하십시오.';
$string['auth_usernameexists'] = '선택하신 사용자명은 이미 존재합니다. 다른 이름의 사용자명을 선택하세요.';
$string['authenticationoptions'] = '인증 옵션들';
$string['authinstructions'] = '이곳에서 당신은 사용자들이 사용하고 있는 계정과 비밀번호의 정보를 제공할수 있습니다. 당신이 작성한 글자들은 로그인 페이지에 나타납니다. 하지만 만약 당신이 이곳을 빈칸으로 놔둔다면 로그인 페이지에 정보가 공개되지 않습니다. ';
$string['changepassword'] = '패스워드 URL을 바꾼다';
$string['changepasswordhelp'] = '만약 계정과 비밀번호를 잊어버렸다면 이곳에서 계정과 비밀번호를 찾거나 혹은 바꿀 수 있습니다. 이 형식은 로그인 페이지나 사용자 페이지에서  버튼형식으로 제공되어지지만  이곳을 빈칸으로 놓아둔다면 버튼은 웹페이지에 나타나지 않습니다. ';
$string['chooseauthmethod'] = '인증 방법 선택하세요:';
$string['createchangepassword'] = '파일이 없다면 만드시오 - 강제 변경';
$string['createpassword'] = '파일이 없다면 만드시오';
$string['forcechangepassword'] = '암호변경 강요';
$string['forcechangepassword_help'] = '다음 무들 사용 로그인시 비밀번호를 바꿀 것을 사용자에게 요청합니다.';
$string['forcechangepasswordfirst_help'] = '사용자에게 무들에 처음 로그인 할 때 비밀번호를 변경할 것을요청합니다.';
$string['guestloginbutton'] = '손님 접속 버튼';
$string['infilefield'] = '이 분야는 파일이 필요합니다.';
$string['instructions'] = '도움말';
$string['locked'] = '잠겨있음';
$string['md5'] = 'MD5 인증 ';
$string['passwordhandling'] = '비밀번호 분야 다루기.';
$string['plaintext'] = '단순 텍스트';
$string['showguestlogin'] = '로그인 페이지에서 손님 로그인 버튼을 보이거나 숨길 수 있습니다.';
$string['stdchangepassword'] = '비밀번호 페이지를 사용하기';
$string['stdchangepassword_expl'] = '만약 외부의 인증 시스템이 모듈을 통해 비밀 번호 변경을 허락한다면, 이것을 Yes 로 바꾸십시오. 이 장치는 \'URL 비밀 번호 변경\'을 변경한다.';
$string['stdchangepassword_explldap'] = '주목 : 만약 LDAP 서버가 제거되었다면, 당신이 사용하는 SSL 위의 SSL 은 터널(ldaps://)로 암호화 되는 것을 추천한다. ';
$string['unlocked'] = '잠기지 않음';
$string['unlockedifempty'] = '비어있다면 잠기지 않음';
$string['update_never'] = '불가';
$string['update_oncreate'] = '제작하다';
$string['update_onlogin'] = '모든 접속';
$string['update_onupdate'] = '업데이트';

?>
