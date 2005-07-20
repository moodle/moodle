<?PHP // $Id$



$string['admindirerror'] = '명시된 관리 디렉토리가 맞지 않습니다';
$string['admindirname'] = '관리 디렉토리';
$string['admindirsetting'] = '매우 적은 웹주인들은  당신이 관리 구회에 접근하기 위해서 특별한 URL을 사용하거나 관리한다.   불행히도 이것은 모들관리페이지를 위한 표준위치와 충돌한다. 당신은 이것을 고칠수있다. 너의 설치에 있는 관리 주소성명록에 재이름을 붙임으로써 그리고 새로운 이름을 여기에 입력함으로써.
예를 들어  <br /> <br /><b>moodleadmin</b><br /> <br />
이럿은 관리 링크를 모들에서 고칠것이다.';
$string['caution'] = '주의';
$string['chooselanguage'] = '언어를 선택하시오';
$string['compatibilitysettings'] = '당신의 PHP 위치를 검사하시오';
$string['configfilenotwritten'] = '그 장치 필기문자는 config.php파일을 자동적으로 생성할수 없다. 당신의 선택한 장소들을 포함하여. 아마도 그 모듈주소성명록은 쓸수있지않다.  당신은 수동적으로 모듈의 원래의 근본 주소성명록안에서 따르는 부호를 config.php파일로 복사할수 있다. ';
$string['configfilewritten'] = 'contig.php는 성공적으로 생성되었다.';
$string['configurationcomplete'] = '와성된 구성배열';
$string['database'] = '데이타 베이스';
$string['databasecreationsettings'] = '지금 당신은 모든 무들 데이터가 저장될 데이터 베이스를 설정할 필요가 있습니다. 이 데이터 베이스는  아래에 있는 특별한 설정의 윈도우 설치프로그램으로써 무들 프로그램에 의해 자동 설치 될것입니다.<br />
<br /> <br />
<b>Type:</b> fixed to \"mysql\" by the installer<br />
<b>Host:</b> fixed to \"localhost\" by the installer<br />
<b>Name:</b> database name, eg moodle<br />
<b>User:</b> fixed to \"root\" by the installer<br />
<b>Password:</b> your database password<br />
<b>Tables Prefix:</b> optional prefix to use for all table names';
$string['databasesettings'] = '지금 당신은 대부분의 모들 정보가 저장될 데이터 베이스를 형성할 필요가 있다. 이 데이터베이스는 이미 형성되어있어야 한다. 그리고 사용자이름과 비밀번호는 접근하기위해서 생성되어야 한다.
.<br />
<br /> <br />
<b>Type:</b> mysql or postgres7<br />
<b>Host:</b> eg localhost or db.isp.com<br />
<b>Name:</b> database name, eg moodle<br />
<b>User:</b> your database username<br />
<b>Password:</b> your database password<br />
<b>Tables Prefix:</b> 모든 이름을 위한 사용의 선택적인 접두사';
$string['dataroot'] = '정보집';
$string['datarooterror'] = '당신이 특별화한 \'정보집\'발견되거나 생성되지 않을 수 있다. 정확한 경로 또는 수동적으로 그 주소성명록을 생성하는것';
$string['dbconnectionerror'] = '우리는 당신이 특별화한 데이터베이스에 연결할 수 없다. 당신의 데이타베이스 위치를 체크해라.';
$string['dbcreationerror'] = '데이터베이스 생성 오류.  주어진 데이테 베이스 이름을 제공된 장소에서 생성할 수 없다.';
$string['dbhost'] = '주인 서버';
$string['dbpass'] = '비밀번호';
$string['dbprefix'] = '접두사';
$string['dbtype'] = '형태';
$string['directorysettings'] = '이 모들 장치의 위치를 확인하세요
<p><b웹주소:</b>
모들이 접근할 전체 웹 주소 명기하세요
만약 당신의 웹 사이트가 복합적인 URLs를 경유하여 접근가능하다면 당신의 학생들이 사용할 가장 자연스러운 것을 선택하세요.  긴 슬레시를 포함하지 마세요.</p>

<p><b>모들 주소성명록:</b>
완전한 주소성명록 경로를 이 장치에 명기하세요  위아래의 경우는 옳바르다는 것을 확인하세요.

<p><b>정보집:</b>
당신은 모들이 업로드된 파일을 저장할 수 있는 장소가 필요하다. 이 주소성명록은 웹근무자(보통 \"아무도\" 또는 \"깡패\" )에 의해서 AND WRITEA로 읽을 수 있어야 한다, 그러나 그것은 직접적으로 웹을 경유해서</p>접근하지 말아야 한다. ';
$string['dirroot'] = '모들 주소성명록';
$string['dirrooterror'] = '모들 주소성명록의 위치는 옳바르지 않다-우리는 모들설비를 거기에서 찾을수 없다. 그 밑의 가치는 다시 설치되었다.';
$string['download'] = '다운로드';
$string['fail'] = '파일';
$string['fileuploads'] = '업로드된 파일 ';
$string['fileuploadserror'] = '이것은 켜져야 합니다.';
$string['fileuploadshelp'] = '<p> 당신의 서버에서 파일 업로딩이 불가능해 보입니다.

모들은 여전히 설치되어있지만 이 능력없이 당신은 진행파일이나 사진을 업로드 할 수 없을 것입니다.

파일 업로딩이 가능하게 하기 위해서는 당신(또는 당신의 시스템 관리자)가 main php.ini.파일을 당신의 서버에서 편집하고 <b>file_upload</b>을 \'1.</p>으로 위치시켜야 할 것입니다.';
$string['gdversion'] = 'GD방식';
$string['gdversionerror'] = 'GD 라이브러리는 진행과정과 사진을 만드는 것을 보여주어야 합니다.';
$string['gdversionhelp'] = '<P>당신의 서버는 GD가 설치된 것으로 보여지지 않습니다.

<P>GD는 모들이 사진을 만드는 과정을 허락하는 PHP에 의해서 요구되는 라이브러리 입니다.{사용자 아이콘과 같은}그리고 새로운 이미지를 창조하는{함수 그래프와 같은} 모들은 여전히 GD의 이런 특징들없이 작동할 것이고 이런 GD의 특징드은 단지 당신에게는 사용할 수 없을 것입니다.';
$string['installation'] = '설치, 설비';
$string['magicquotesruntime'] = '매직 코트 실행 시간';
$string['magicquotesruntimeerror'] = '이것은 꺼져야 합니다.';
$string['magicquotesruntimehelp'] = '매직 코트 실행시간은 모들이 적절한 기능을 하기 위해 꺼져야 합니다.

일반적으로 이것은 디폴트값에 의해 꺼집니다. 당신의 php.ini파일에 있는 매직 코트 실행시간을 조절하세요.

만약에 당신의 php.ini파일에 접속하지 않았다면 당신은 요구되는 파일안의 라인을 따르는 곳에 위치할 수 있을 것입니다. 당신의 모들 주소성명록대로 접속하세요<blockquote>php_value magic_quotes_runtime off<blockquote>';
$string['memorylimit'] = '기억 제한';
$string['memorylimiterror'] = '이 php 기억 제한은 매우 낮게 설치되어 있습니다. 당신은 후에 문제를 닥치게 될 것입니다.';
$string['memorylimithelp'] = '당신의 서버의 PHP기억제한이 최근에 Sa.</p>에 설치되었습니다.

이것은 아마 무들이 나중에 기억의 문제를 갖게 되는 것을 야기시킬것입니다. 특히 만약 당신이 사용할 수 있는 많은 무들을 가지고 있거나 그런 사용자를 가지고 있다면 그러할 것입니다.

우리는 당신이 16M 와 같은 가능하면 더 높은 제한을 가지고 있는 PHP를 형성하기를 요구합니다.
당신이 시도 할 수 잇는 여러가지 방법들이 있습니다.

만약 당신이 할 수 있다면 기억제한이 가능한 파일과 함께 PHP를 번역하세요. 이것은 무들이 자기 스스로 기억제한을 설치하는데 허락할 것입니다.

만약 다신이 php.ini.파일에 접속했다면 당신은 <b>memory_limit</b>을  16M와 같은 어떤 것으로 바꿔설치 할 수 있을것입니다. 만약 당신이 접속하지 않았다면 이것을 실행하기 위한 당신의관리자에게 문의하 실 수 있습니다.

당신이 만들어 낼수 있는 몇개의PHP서버들에서 무들 주소성명록에 있는 접속파일은 이 라인을 포함하고 있습니다.
<P><blockquote>php_value memory_limit 16M<blockquote></p>
<p>그러나 모든 php페이지가 (당신이 페이지를 살펴보았을때 문제를 찾을 것이다) 일하는 것으로부터 방해 할 이 몇개의 서버들이 있기때문에 당신은 접속 파일 </p></li></ol>를 제거해야 할 것입니다.';
$string['mysqlextensionisnotpresentinphp'] = 'php는 php가 mysql과 연결할 수 있기 위해서 적절하게 형성됐다. 당신의 php.ini 파일이나 recompile php를 확인하세요.';
$string['pass'] = '통과하세요.';
$string['phpversion'] = 'php버젼';
$string['phpversionerror'] = 'php 버젼은 틀림없이 적어도 4.1.0.이어야합니다.';
$string['phpversionhelp'] = '무들은 적어도 4.1.0의 php버젼을 요구합니다.';
$string['safemode'] = '안전한 상태';
$string['safemodeerror'] = '무들은 아마 안전한 상태에서 문제를 가지고 있을 것입니다.';
$string['safemodehelp'] = '<p>무들은 아마 안전한 상태에서의 문제를 가지고 있을수 있습니다. 적어도 그 문제는 아마 새로운 파일을 만드는 것을 허락하지 않을 것입니다.</p>';
$string['sessionautostart'] = '세션의 활동을 시작하세요.';
$string['sessionautostarterror'] = '이것은 종료되어야 합니다.';
$string['sessionautostarthelp'] = '<p>무들은 세션의 지원을 요구하고 무들은 그것 없이는 기능하지 않을 것 입니다.<P>세션은 php 안에서 ini 파일이 될 수 있습니다. 세션의 자동적 활동의 시작을 위한 매개변수를 찾으세요.';
$string['wwwroot'] = '웹 주소';
$string['wwwrooterror'] = '이 유효한 무들 설비들 인 것 같지 않은 이 웹 주소는 있는 것 같지 않습니다.';
?>
