<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.6 development (2005090100)


$string['adminreview'] = '신용카드 번호를 저장하기 전에  확인합니다.';
$string['anlogin'] = 'Authorize.net: 접속 이름';
$string['anpassword'] = 'Authorize.net: 비밀번호';
$string['anreferer'] = '만약 당신이 authorize.net 계정을 설정했다면 그 사이트의 주소가 보여질 것입니다. 이것은 그 사이트의 주소가 인터넷의  요청에 의해 새로운 줄로 보내질 것입니다. ';
$string['antestmode'] = '테스트 모드로만 이용하실수 있습니다. (no money will be drawn)';
$string['antrankey'] = 'Authorize.net: 처리 열쇠 ';
$string['ccexpire'] = '기한 만료 날짜';
$string['ccexpired'] = '이 신용카드는 만기되었습니다.';
$string['ccinvalid'] = '카드 번호가 잘못되었습니다.';
$string['ccno'] = '신용카드 번호 ';
$string['cctype'] = '신용카드 종류';
$string['ccvv'] = '카드 확인';
$string['ccvvhelp'] = '카드의 뒷 부분을 보십시오.';
$string['choosemethod'] = '만약 당신이 교육과정 등록 키를 알고 있고 등록했다면 당신은 이 교육과정의 사용 비용을 지불해야만 합니다.';
$string['chooseone'] = '아래의 두 부분의 한 부분이나 모든 부분을 채우시오';
$string['description'] = 'Authorize.net 모듈은 유료교육과정을 신용카드 공급자로부터 지불하는것을 허락할것입니다. 만약 무료교육과정이라면 학생들은 수업을 들으려면 돈을 내야 하는지 물어봐야 할 필요가 없습니다. 당신은 이 사이트를 처음 설정된 값으로 전체 사이트 이용비용이 정할수 있고 각 코스마다 개인적으로 가격을 이용비용을 정할수 있습니다. 교육과정의 이용비용은 사이트 이용비용보다 우위에 있습니다.';
$string['enrolname'] = 'Authorize.net 신용카드 입구';
$string['httpsrequired'] = '저희는 당신이 요구가 수월하게 처리되지 못한점을 알리게 되어 죄송하게 생각합니다. 이 사이트의 설정이 현재 제대로 설정되지 않았습니다. 
<br /><br />
우리는 당신이 인터넷 브라우저의 아래부분에서 노란 자물쇠 그림이 보일때까지 신용카드 번호를 입력하지 않기를 바랍니다.
이것은 모든 데이터가 클라이언트와 서버사이에 암호화됨을 의미합니다. 그래서 두대의 컴퓨터 사이에 이동되는 모든 정보가 보호되고 당신의 신용카드 번호는 인터넷에 알려지지 않게 됩니다.';
$string['logindesc'] = '이 옵선은 반드시 켜져야 합니다.<br /><br />
변경/보안 섹션에서 <a href=\"$a->url\">loginhttps</a>옵션을 설정 할수 있습니다.
<br /><br />
이 옵션을 킨다면 무들은 접속 페이지와 지불 페이지에 보안 프로그램을 사용할것입니다.';
$string['nameoncard'] = '카드에 적힌 이름';
$string['reviewday'] = '<b>$a</b>일 내에 선생님이나 관리자가 주문을 확인하지 않으면 자동적으로 신용카드를 회수합니다. cron이 반드시 활성화 되어 있어야 합니다.<br />(0일 = 불가능, autocapture = 교사나 관리자가 수동으로 접수. 만일 30일 이내에 접수하지 않거나 자동 수납을 불가능 으로 해 놓으면 송금은 취소될 것입니다) ';
$string['reviewnotify'] = '결제가 확인되었습니다. 선생님으로부터 며칠안으로 메일이 갈것입니다.';
$string['sendpaymentbutton'] = '지불하기';
$string['zipcode'] = '국가 코드';

?>
