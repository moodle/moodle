<?PHP // $Id$ 
      // install.php - created with Moodle 1.6 development (2005101200)


$string['admindirerror'] = '명시된 관리 디렉토리가 맞지 않습니다';
$string['admindirname'] = '관리 디렉토리';
$string['admindirsetting'] = '간혹 웹호스트 업체는 당신이 관리 구획에 접근할 수 있도록하기 위해 특별한 URL을 사용하거나 관리한다. 불행히도 이것은 모들관리페이지를 위한 표준위치와 충돌한다. 당신은 이것을 고칠수있다. 설치과정에 적어넣은 관리 디렉토리를  새로 바꿔 적어 넣으면 된다.
예를 들어  <br /> <br /><b>moodleadmin</b><br /> <br />
그러면 무들에 있는 관리 링크가 고쳐질 것이다.';
$string['caution'] = '주의';
$string['chooselanguage'] = '언어를 선택하시오';
$string['compatibilitysettings'] = 'PHP 설정을 검사하는 중 ..';
$string['configfilenotwritten'] = '설치 스크립트는 당신이 선택한 설정으로 config.php파일을 자동적으로 생성할수 없습니다. 아마도 무들의 경로에 쓰기 허용이 되어 있지 않은 모양입니다.  당신은 수동적으로 다음의 코드를 무들의 루트디렉토리안의 config.php파일로 복사할수 있습니다.';
$string['configfilewritten'] = '성공적으로 contig.php가 생성되었음.';
$string['configurationcomplete'] = '초기 설정 완료';
$string['database'] = '데이타 베이스';
$string['databasecreationsettings'] = '지금 당신은 모든 무들 데이터가 저장될 데이터 베이스를 설정할 필요가 있습니다. 이 데이터 베이스는  아래에 있는 특별한 설정의 윈도우 설치프로그램으로써 무들 프로그램에 의해 자동 설치 될것입니다.<br />
<br /> <br />
<b>Type:</b> fixed to \"mysql\" by the installer<br />
<b>Host:</b> fixed to \"localhost\" by the installer<br />
<b>Name:</b> database name, eg moodle<br />
<b>User:</b> fixed to \"root\" by the installer<br />
<b>Password:</b> your database password<br />
<b>Tables Prefix:</b> optional prefix to use for all table names';
$string['databasesettings'] = '지금 당신은 대부분의 무들 정보가 저장될 데이터 베이스를 설정할 필요가 있습니다. 이 데이터베이스는 미리 생성되어있어야 하며, 데이터 베이스에 접근하기위한 사용자이름과 비밀번호가 만들어져 있어야 합니다.<br />
<br /> <br />
<b>Type:</b> mysql or postgres7<br />
<b>Host:</b> eg localhost or db.isp.com<br />
<b>Name:</b> database name, eg moodle<br />
<b>User:</b> your database username<br />
<b>Password:</b> your database password<br />
<b>Tables Prefix:</b> 모든 이름을 위한 사용의 선택적인 접두사';
$string['dataroot'] = '데이타 디렉토리';
$string['datarooterror'] = '당신이 지정한 \'데이타 디렉토리\'가 없거나 생성되지 않았습니다. 정확한 경로를 적거나 수동으로 그 디렉토리를 생성해 놓으시오.';
$string['dbconnectionerror'] = '지정한 데이터베이스에 연결할 수 없습니다. 데이타베이스의 설정을 점검하시오.';
$string['dbcreationerror'] = '데이터베이스 생성 오류. 주어진 설정값으로 데이터베이스 이름을 생성할 수 없습니다.';
$string['dbhost'] = '호스트 서버';
$string['dbpass'] = '비밀번호';
$string['dbprefix'] = '접두사';
$string['dbtype'] = '형태';
$string['directorysettings'] = '<p>무들을 설치할 위치를 확인하세요.</p>
<p><b>웹주소:</b>
무들이 접근할 전체 웹 주소 명기하세요
만약 당신의 웹 사이트가 복합적인 URLs를 경유하여 접근가능하다면 당신의 학생들이 사용할 가장 자연스러운 것을 선택하세요.  마지막에 슬레시를 넣지 마세요.</p>

<p><b>무들 경로:</b>
완전한 디렉토리 경로를 명기하세요  대소문자를 정확히 구별하여 기재하세요.</p>

<p><b>데이터 경로:</b>
무들이 업로드된 파일을 저장할 수 있는 장소가 필요합니다. 이 디렉토리는 웹 서버의 사용자(보통 \"none\" 또는 \"apache\" )에 의해서 \'읽고쓰기 가능\' 권한을 보유하여야 합니다. 그러나 그것은 직접적으로 웹을 경유해서 접근할 수 있어서는 안됩니다.</p> ';
$string['dirroot'] = '무들 경로';
$string['dirrooterror'] = '무들 경로의 위치가 바르지 않은 것 같습니다 - 모들설치 프로그램을 찾을 수 없습니다. 아래의 값들은 초기화 되었습니다.';
$string['download'] = '다운로드';
$string['fail'] = '실패';
$string['fileuploads'] = '업로드된 파일 ';
$string['fileuploadserror'] = '이것은 켜져야 합니다.';
$string['fileuploadshelp'] = '<p> 당신의 서버에서 파일 업로딩이 불가능해 보입니다.</p>

<p>무들은 설치될 수 있지만 파일 업로딩 할 수 없는 상태에서는 당신은 배움터의 파일이나 사진을 업로드 할 수 없을 것입니다.</p>

<p>파일 업로딩이 가능하게 하기 위해서는 당신(또는 당신의 시스템 관리자)가 php.ini 파일 속의  <b>file_upload</b>을 \'1\'로 설정해야 할 것입니다.</p>';
$string['gdversion'] = 'GD 의 버전';
$string['gdversionerror'] = 'GD 라이브러리는 진행과정과 사진을 만드는 것을 보여주어야 합니다.';
$string['gdversionhelp'] = '<P>당신의 서버는 GD가 설치된 것으로 보여지지 않습니다.

<P>GD는 무들이 그림을 처리할 수 있도록 PHP에 의해서 요구되는 라이브러리 입니다.{사용자 아이콘과 같은}그리고 새로운 이미지를 생성하는{함수 그래프와 같은} 모들은 여전히 GD의 이런 기능없이 작동할 것이고 이런 GD의 기능들은 단지 당신에게는 사용할 수 없을 것입니다.';
$string['globalsquotes'] = '전역변수 조작 안전성 결여';
$string['globalsquoteserror'] = '여러분의 PHP 설정을 다음과 같이 고쳤습니다:  register_globals 및 enable magic_quotes_gpc 을 껐습니다.';
$string['globalsquoteshelp'] = '<p>Combination of disabled Magic Quotes GPC and enabled Register Globals both at the same time is not recommended.</p>

<p>권장하는 여러분의 php.ini 설정은 <b>magic_quotes_gpc = On</b> 과 <b>register_globals = Off</b> 입니다.</p>

<p>만일 여러분이 php.ini 에 접근할 수 없다면, 무들 디렉토리안에 아래의 내용이 담긴 .htaccess 파일을 넣어 두십시오.
<blockquote>php_value magic_quotes_gpc On</blockquote>
<blockquote>php_value register_globals Off</blockquote>
</p> ';
$string['installation'] = '설치';
$string['magicquotesruntime'] = '매직 코트 실행 시간';
$string['magicquotesruntimeerror'] = '이것은 꺼져야 합니다.';
$string['magicquotesruntimehelp'] = '<p>매직 코트 실행시간은 모들이 적절한 기능을 하기 위해 꺼져야 합니다.</p>

<p>일반적으로 이것은 디폴트값에 의해 꺼집니다. 당신의 php.ini파일에 있는 매직 코트 실행시간을 조절하세요.</p>

<p>만약에 당신의 php.ini파일에 접속하지 않았다면 당신은 요구되는 파일안의 라인을 따르는 곳에 위치할 수 있을 것입니다. 당신의 모들 주소성명록대로 접속하세요<blockquote>php_value magic_quotes_runtime off<blockquote></p>';
$string['memorylimit'] = '기억 제한';
$string['memorylimiterror'] = '이 php 기억 제한은 매우 낮게 설치되어 있습니다. 당신은 후에 문제를 닥치게 될 것입니다.';
$string['memorylimithelp'] = '<p>서버의 PHP 메모리한계가 최근 $a 로 설정되었습니다.</p>

<p>이것은 아마 무들이 나중에 기억의 문제를 갖게 되는 것을 야기시킬것입니다. 특히 만약 당신이 사용할 수 있는 많은 무들을 가지고 있거나 그런 사용자를 가지고 있다면 그러할 것입니다.</p>

<p>우리는 당신이 16M 와 같은 가능하면 더 높은 제한을 가지고 있는 PHP를 형성하기를 요구합니다.
당신이 시도 할 수 잇는 여러가지 방법들이 있습니다.</p>
<ol>
<li>만약 당신이 할 수 있다면 기억제한이 가능한 파일과 함께 PHP를 번역하세요. 이것은 무들이 자기 스스로 기억제한을 설치하는데 허락할 것입니다</li>

<li>만약 다신이 php.ini.파일에 접속했다면 당신은 <b>memory_limit</b>을  16M와 같은 어떤 것으로 바꿔설치 할 수 있을것입니다. 만약 당신이 접속하지 않았다면 이것을 실행하기 위한 당신의관리자에게 문의하실 수 있습니다.</li>

<li>당신이 만들어 낼수 있는 몇개의 PHP서버들에서 무들 주소성명록에 있는 접속파일은 이 라인을 포함하고 있습니다.
<P><blockquote>php_value memory_limit 16M<blockquote></p>
<p>그러나 모든 php페이지가 (당신이 페이지를 살펴보았을때 문제를 찾을 것이다) 일하는 것으로부터 방해 할 이 몇개의 서버들이 있기때문에 당신은 .htaccess 를 제거해야 할 것입니다.</p></li></ol>';
$string['mysqlextensionisnotpresentinphp'] = 'php는 php가 mysql과 연결할 수 있기 위해서 적절하게 형성됐다. 당신의 php.ini 파일이나 recompile php를 확인하세요.';
$string['pass'] = '통과하세요.';
$string['phpversion'] = 'php버젼';
$string['phpversionerror'] = 'php 버젼은 틀림없이 적어도 4.1.0.이어야합니다.';
$string['phpversionhelp'] = '무들은 적어도 4.1.0의 php버젼을 요구합니다.';
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = '아마 Safe Mode가 작동되어서 문제가 있을 것입니다.';
$string['safemodehelp'] = '<p>무들은 아마 safe mode on 문제를 가지고 있을수 있습니다. 적어도 그 문제는 아마 새로운 파일을 만드는 것을 허락하지 않을 것입니다.</p>';
$string['sessionautostart'] = '세션의 활동을 시작하세요.';
$string['sessionautostarterror'] = '이것은 종료되어야 합니다.';
$string['sessionautostarthelp'] = '<p>무들은 세션의 지원을 요구하고 무들은 그것 없이는 기능하지 않을 것 입니다.</p>
<P>세션은 php 안에서 ini 파일이 될 수 있습니다. 세션의 자동적 활동의 시작을 위한 매개변수를 찾으세요.</p>';
$string['wwwroot'] = '웹 주소';
$string['wwwrooterror'] = '이 웹 주소는 유효한 것 같지 않습니다 - 무들 설치 프로그램이 거기에 없습니다.';

?>
