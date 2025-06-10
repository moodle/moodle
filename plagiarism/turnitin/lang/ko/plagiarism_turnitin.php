<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC
 */

/*
 * To change this template, choose Tools | Templates.
 * and open the template in the editor.
 */

// General.
$string['pluginname'] = 'Turnitin 표절 플러그인';
$string['turnitin'] = 'Turnitin';
$string['task_name'] = 'Turnitin 표절 플러그인 작업';
$string['connecttesterror'] = 'Turnitin에 연결하는 데 오류가 생겼습니다. 오류 메시지가 아래에 있습니다.<br />';

// Assignment Settings.
$string['turnitin:enable'] = 'Turnitin 활성화 하기';
$string['excludebiblio'] = '참고 문헌 제외';
$string['excludequoted'] = '인용 자료 제외';
$string['excludevalue'] = '사소한 일치 제외';
$string['excludewords'] = '단어';
$string['excludepercent'] = '퍼센트';
$string['norubric'] = '채점표 없음';
$string['otherrubric'] = '다른 강사의 채점표 사용';
$string['attachrubric'] = '본 과제에 채점표 첨부하기';
$string['launchrubricmanager'] = '채점표 관리자 시작하기';
$string['attachrubricnote'] = '주의: 학생들은 첨부된 채점표와 자신들의 내용을 제출 전에 볼 수 있습니다.';
$string['anonblindmarkingnote'] = '참고: 별도의 Turnitin 익명 채점 설정은 삭제됩니다. Turnitin은 Moodle의 블라인드 채점 환경을 이용해 익명 채점 설정을 결정합니다.';
$string['transmatch'] = '번역된 일치';
$string["reportgen_immediate_add_immediate"] = "보고서를 즉시 생성합니다. 제출물이 데이터베이스에 바로 추가됩니다(데이터베이스가 설정된 경우).";
$string["reportgen_immediate_add_duedate"] = "보고서를 즉시 생성합니다. 제출물이 마감일에 데이터베이스에 추가됩니다(데이터베이스가 설정된 경우).";
$string["reportgen_duedate_add_duedate"] = "보고서를 마감일에 생성합니다. 제출물이 마감일에 데이터베이스에 추가됩니다(데이터베이스가 설정된 경우).";
$string['launchquickmarkmanager'] = 'Quickmark 관리자 개시하기';
$string['launchpeermarkmanager'] = 'Peermark 관리자 개시하기';
$string['studentreports'] = '학생들에게 독창성 보고서 공개';
$string['studentreports_help'] = 'Turnitin 독창성 보고서를 학생 사용자가 볼 수 있도록 허용합니다. "예"라고 지정될 경우 Turnitin에서 작성된 독창성 보고서를 학생 사용자가 볼 수 있습니다.';
$string['submitondraft'] = '처음 업로드시 파일 제출';
$string['submitonfinal'] = '학생이 제출하여 채점을 요구할 때 파일 제출';
$string['draftsubmit'] = '파일을 언제 Turnitin에 제출하여야 합니까?';
$string['allownonor'] = '모든 파일 유형의 제출을 허용하시겠습니까?';
$string['allownonor_help'] = '본 환경은 모든 파일 유형의 제출을 가능케 합니다. &#34;예&#34;로 옵션을 설정한 경우, 제출물은 가능한 부분에 대해 독창성을 검토 받게 되며, 제출물들은 다운로드 가능하고 GradeMark 도구들은 가능 범위 내에서 사용할 수 있습니다.';
$string['norepository'] = '보관소 없음';
$string['standardrepository'] = '표준 보관소';
$string['submitpapersto'] = '학생 보고서 보관';
$string['institutionalrepository'] = '기관 보관소(적용 가능시)';
$string['checkagainstnote'] = '참고: 아래 “비교” 옵션 중 하나 이상에 “예”를 선택하지 않으면 독창성 보고서가 생성되지 않습니다.';
$string['spapercheck'] = '보관된 학생 보고서들과 비교하기';
$string['internetcheck'] = '인터넷 내용과 비교';
$string['journalcheck'] = '저널,<br />정기 간행물, 출판물들과 비교';
$string['compareinstitution'] = '제출된 파일들을 본 기관내에 제출된 보고서들과 비교';
$string['reportgenspeed'] = '보고서 생성 속도';
$string['locked_message'] = '잠긴 메시지';
$string['locked_message_help'] = '설정이 잠기면 이유를 설명하는 이 메시지가 표시됩니다.';
$string['locked_message_default'] = '이 설정은 사이트 수준에서 잠깁니다.';
$string['sharedrubric'] = '공유 채점표';
$string['turnitinrefreshsubmissions'] = '제출물 새로 고침';
$string['turnitinrefreshingsubmissions'] = '제출물 새로 고침';
$string['turnitinppulapre'] = '파일을 Turnitin에 제출하려면 먼저 당사의 EULA에 동의해야 합니다. EULA에 동의하지 않는 것으로 선택하면 파일이 Moodle로만 제출됩니다. 계약을 읽고 동의하려면 여기를 클릭하십시오.';
$string['noscriptula'] = '(Javascript가 활성화되지 않았으므로 Turnitin 사용자 동의서에 동의하고 본 페이지를 수동적으로 새로고침한 후 제출을 하여야 합니다)';
$string['filedoesnotexist'] = '파일이 삭제되었습니다.';
$string['reportgenspeed_resubmission'] = '이 과제에 대한 보고서를 이미 제출하셨으며, 제출물에 대한 유사성 보고서가 생성되었습니다. 보고서를 다시 제출하도록 선택하는 경우 이전 제출물이 대체되고, 새 보고서가 생성됩니다. {$a->num_resubmissions}의 재제출 이후, 새로운 유사성 보고서를 보려면 재제출 후 {$a->num_hours}시간 동안 기다려야 합니다.';

// Plugin settings.
$string['config'] = '구성';
$string['defaults'] = '기본 환경';
$string['showusage'] = '데이타 덤프 표시하기';
$string['saveusage'] = '데이타 덤프 저장하기';
$string['errors'] = '오류들';
$string['turnitinconfig'] = 'Turnitin 표절 Plugin 구성';
$string['tiiexplain'] = 'Turnitin은 상업 제품이며 이 서비스를 사용하려면 사용료를 지불하여야 합니다. 자세한 사항은 <a href=http://docs.moodle.org/en/Turnitin_administration>http://docs.moodle.org/en/Turnitin_administration</a>를 참조하십시오.';
$string['useturnitin'] = 'Turnitin 활성화 하기';
$string['useturnitin_mod'] = 'Turnitin 활성화 대상 {$a}';
$string['turnitindefaults'] = 'Turnitin 표절 플러그인 기본 환경';
$string['defaultsdesc'] = '다음의 환경들은 Turnitin을 활동 모듈에서 활성화시킬 때의 기본 세트입니다';
$string['turnitinpluginsettings'] = 'Turnitin 표절 플러그인 환경';
$string['pperrorsdesc'] = 'Turnitin에 아래 파일들을 업로드하는 데 문제가 발생하였습니다. 다시 제출하려면 원하는 파일을 선택하고 다시 제출 버튼을 누르십시오. 그러면 다음에 cron이 실행될 때 처리됩니다.';
$string['pperrorssuccess'] = '선택한 파일이 다시 제출되고 cron에 의해 처리됩니다.';
$string['pperrorsfail'] = '선택한 파일 중 일부에 문제가 있었습니다. 해당 파일에 대해 새로운 cron 이벤트를 생성할 수 없었습니다.';
$string['resubmitselected'] = '선택한 파일 다시 제출';
$string['deleteconfirm'] = '이 제출물을 정말 삭제하시겠습니까?\n\n이는 되돌릴 수 없습니다.';
$string['deletesubmission'] = '제출물 삭제';
$string['semptytable'] = '발견된 결과가 없음';
$string['configupdated'] = '구성 업데이트';
$string['defaultupdated'] = 'Turnitin 기본 업데이트';
$string['notavailableyet'] = '이용할 수 없습니다';
$string['resubmittoturnitin'] = 'Turnitin에 다시 제출';
$string['resubmitting'] = '다시 제출 중';
$string['id'] = '아이디';
$string['student'] = '학생';
$string['course'] = '코스';
$string['module'] = '모듈';

// Grade book/View assignment page.
$string['turnitin:viewfullreport'] = '독창성 보고서 보기';
$string['launchrubricview'] = '채점에 사용된 채점표 보기';
$string['turnitinppulapost'] = '귀하의 파일은 Turnitin에 제출되지 않았습니다. 당사 EULA를 수락하려면 여기를 클릭하십시오.';
$string['ppsubmissionerrorseelogs'] = '이 파일은 Turnitin에 제출되지 않았습니다. 시스템 관리자에게 문의하십시오.';
$string['ppsubmissionerrorstudent'] = '이 파일은 Turnitin에 제출되지 않았습니다. 자세한 내용은 담당 튜터와 상의하십시오.';

// Receipts.
$string['messageprovider:submission'] = 'Turnitin 표절 플러그인 디지털 수령 알림';
$string['digitalreceipt'] = '디지털 수령증';
$string['digital_receipt_subject'] = '귀하의 Turnitin 디지털 수령증입니다.';
$string['pp_digital_receipt_message'] = '{$a->firstname} {$a->lastname}님,<br /><br /><strong>{$a->submission_date}</strong>에 수강하신 <strong>{$a->course_fullname}</strong> 클래스의 <strong>{$a->assignment_name}{$a->assignment_part}</strong> 과제에 해당하는 <strong>{$a->submission_title}</strong> 파일을 성공적으로 제출하였습니다. 제출 ID는 <strong>{$a->submission_id}</strong>입니다. 디지털 수령증은 문서보기 창의 프린트/다운로드 버튼을 눌러 확인 및 인쇄할 수 있습니다.<br /><br />Turnitin을 이용해 주셔서 감사합니다.<br /><br />Turnitin 팀';

// Paper statuses.
$string['turnitinid'] = 'Turnitin 아이디';
$string['turnitinstatus'] = 'Turnitin 상태';
$string['pending'] = '보류 중';
$string['similarity'] = '유사성';
$string['notorcapable'] = '이 파일에 대해 독창성 보고서를 생성할 수 없습니다.';
$string['grademark'] = 'GradeMark';
$string['student_read'] = '학생이 보고서를 본 날짜:';
$string['student_notread'] = '학생이 이 보고서를 보지 않았습니다.';
$string['launchpeermarkreviews'] = 'Peermark 평가 개시하기';

// Cron.
$string['ppqueuesize'] = '표절 플러그인 이벤트 queue의 이벤트 수';
$string['ppcronsubmissionlimitreached'] = '이 cron 실행당 {$a}개의 제출물만 처리되므로 더 이상 Turnitin에 제출물이 전송되지 않습니다.';
$string['cronsubmittedsuccessfully'] = '{$a->coursename} 코스의 {$a->assignmentname} 과제에 대한 제출물: {$a->title}(TII ID: {$a->submissionid})이(가) Turnitin에 성공적으로 제출되었습니다.';
$string['pp_submission_error'] = 'Turnitin이 귀하의 제출에 대한 오류 메시지를 반환했습니다.';
$string['turnitindeletionerror'] = 'Turnitin 제출물 삭제에 실패하였습니다.로컬 Moodle 복사본은 제거되었지만 Turnitin 상의 제출물을 삭제할 수 없었습니다.';
$string['ppeventsfailedconnection'] = 'Turnitin에 대한 접속을 구성할 수 없기 때문에 이 cron 실행으로는 Turnitin 표절 플러그인에 의한 이벤트 처리가 불가능합니다.';

// Error codes.
$string['tii_submission_failure'] = '자세한 내용은 담당 튜터나 시스템 관리자와 상의하십시오.';
$string['faultcode'] = '착오 코드';
$string['line'] = '열';
$string['message'] = '메세지';
$string['code'] = '코드';
$string['tiisubmissionsgeterror'] = 'Turnitin에서 본 과제의 제출물들을 가져오는데 오류가 발생하였습니다';
$string['errorcode0'] = '이 파일은 Turnitin에 제출되지 않았습니다. 시스템 관리자에게 문의하십시오.';
$string['errorcode1'] = '이 파일은 독창성 보고서를 생성할 만큼 충분한 내용을 담고 있지 않아 Turnitin에 제출되지 않았습니다.';
$string['errorcode2'] = '이 파일은 {$a->maxfilesize} 크기 한도를 초과하기 때문에 Turnitin에 제출되지 않을 것입니다.';
$string['errorcode3'] = '사용자가 Turnitin 최종 사용자 라이센스 계약을 수락하지 않았기 때문에 이 파일은 Turnitin에 제출되지 않았습니다.';
$string['errorcode4'] = '이 과제에 지원되는 형식의 파일을 업로드해야 합니다. 허용되는 파일 형식은 .doc, .docx, .ppt, .pptx, .pps, .ppsx, .pdf, .txt, .htm, .html, .hwp, .odt, .wpd, .ps, .rtf입니다.';
$string['errorcode5'] = '제출을 허용하지 않는 Turnitin에서 모듈을 생성하는 동안 문제가 발생하여 Turnitin에 이 파일이 제출되지 않았습니다. 자세한 정보는 해당 API 로그를 참조하십시오.';
$string['errorcode6'] = '제출을 허용하지 않는 Turnitin에서 모듈 환경을 수정하는 동안 문제가 발생하여 Turnitin에 이 파일이 제출되지 않았습니다. 자세한 정보는 해당 API 로그를 참조하십시오.';
$string['errorcode7'] = '제출을 허용하지 않는 Turnitin에서 사용자를 생성하는 동안 문제가 발생하여 Turnitin에 이 파일이 제출되지 않았습니다. 자세한 정보는 해당 API 로그를 참조하십시오.';
$string['errorcode8'] = '임시 파일을 생성하는 동안 문제가 발생하여 Turnitin에 이 파일이 제출되지 않았습니다. 잘못된 파일 이름 때문일 가능성이 높습니다. 파일 이름을 바꾸고 제출 수정을 이용하여 다시 업로드하십시오.';
$string['errorcode9'] = '제출할 파일 풀에 액세스 가능한 내용이 없어 파일이 제출되지 않았습니다.';
$string['coursegeterror'] = '코스 데이터를 가져올 수 없었습니다.';
$string['configureerror'] = '코스에서 이 모듈을 사용하기 전에 관리자로서 이를 완전히 구성해야 합니다. 귀하의 Moodle 관리자에게 문의하시기 바랍니다.';
$string['turnitintoolofflineerror'] = '일시적인 문제가 발생했습니다. 잠시 후 다시 시도하십시오.';
$string['defaultinserterror'] = '데이타베이스에 기본 환경값을 삽입하는 데 오류가 생겼습니다.';
$string['defaultupdateerror'] = '데이타베이스에 있는 기본 환경값을 업데이트하는 데 오류가 생겼습니다.';
$string['tiiassignmentgeterror'] = 'Turnitin에서 과제를 가져오는데 오류가 발생하였습니다';
$string['assigngeterror'] = 'Turnitin 데이타를 취득할 수 없었음';
$string['classupdateerror'] = 'Turnitin 클래스 데이타를 업데이트할 수 없었습니다';
$string['pp_createsubmissionerror'] = 'Turnitin에서 제출물을 생성하는데 오류가 발생하였습니다';
$string['pp_updatesubmissionerror'] = 'Turnitin에 대해 제출물을 재제출하는데 오류가 발생하였습니다';
$string['tiisubmissiongeterror'] = 'Turnitin에서 제출물을 가져오는데 오류가 발생하였습니다';

// Javascript.
$string['closebutton'] = '닫기';
$string['loadingdv'] = 'Turnitin 문서보기 창 로드 중...';
$string['changerubricwarning'] = '채점표를 변경하거나 분리하면 이미 채점이 된 점수카드를 포함하여 해당 과제에 있는 보고서들의 기존 채점표 채점사항이 모두 제거됩니다.';
$string['messageprovider:submission'] = 'Turnitin 표절 플러그인 디지털 수령 알림';

// Turnitin Submission Status.
$string['turnitinstatus'] = 'Turnitin 상태';
$string['deleted'] = '삭제됨';
$string['pending'] = '보류 중';
$string['because'] = '관리자가 처리 queue에 보류 중인 과제를 삭제하고 Turnitin에 대한 제출을 중단했기 때문입니다.<br /><strong>파일이 아직 Moodle에 있습니다. 담당 강사에게 문의하십시오.</strong><br />오류 코드는 아래에서 확인하십시오.';
$string['submitpapersto_help'] = '<strong>보관소 없음: </strong><br />Turnitin은 제출된 문서를 데이터베이스에 저장하지 않습니다. 보고서는 초기 유사성 검사를 수행할 목적으로만 처리됩니다.<br /><br /><strong>표준 보관소: </strong><br />Turnitin은 제출된 문서의 사본을 표준 데이터베이스에만 저장합니다. 이 옵션을 선택하면 Turnitin은 저장된 문서만 사용하여 향후 제출되는 모든 문서에 대해 유사성 검사를 수행합니다.<br /><br /><strong>기관 보관소(적용 가능시): </strong><br />이 옵션을 선택하면 Turnitin이 제출된 문서를 기관의 개인 데이터베이스에만 추가합니다.  제출된 문서의 유사성 검사는 기관 내의 다른 강사가 수행합니다.';
$string['errorcode12'] = '이 파일은 Turnitin에 제출되지 않았습니다. 삭제된 코스의 과제물에 속해 있기 때문입니다. 행 ID: ({$a->id}) | 코스 모듈 ID: ({$a->cm}) | 사용자 ID: ({$a->userid})';
$string['errorcode15'] = '이 파일은 해당 파일이 속해 있는 활동 모듈을 찾을 수 없기 때문에 Turnitin에 제출되지 않았습니다.';
$string['tiiaccountconfig'] = 'Turnitin 계정 구성';
$string['turnitinaccountid'] = 'Turnitin 계정 아이디';
$string['turnitinsecretkey'] = 'Turnitin 공유 키';
$string['turnitinapiurl'] = 'Turnitin API URL';
$string['tiidebugginglogs'] = '디버깅 및 로깅';
$string['turnitindiagnostic'] = '진단 모드를 활성화 시키기';
$string['turnitindiagnostic_desc'] = '<b>[주의]</b><br />Turnitin API의 문제들을 찾아내기 위해서만 진단 모드를 활성화 시킵니다.';
$string['tiiaccountsettings_desc'] = '이 설정은 귀하의 Turnitin 계정에서 구성한 설정과 일치해야 합니다. 그렇지 않으면 과제 생성 및/또는 학생 제출물에 문제가 발생할 수 있습니다.';
$string['tiiaccountsettings'] = 'Turnitin 계정 설정';
$string['turnitinusegrademark'] = 'GradeMark 사용하기';
$string['turnitinusegrademark_desc'] = '제출물을 채점하는 데 GradeMark를 사용할 지 Moodle을 사용할 지 결정하십시오.<br /><i>(이는 계정에 GradeMark가 구성되어 있는 분만 사용할 수 있습니다)</i>';
$string['turnitinenablepeermark'] = 'Peermark 과제 활성화 하기';
$string['turnitinenablepeermark_desc'] = 'Peermark 과제의 생성을 허용할 것인지를 선택하십시오.<br/><i>(T이는 계정에 Peermark 구성이 있을 경우에만 사용가능합니다)</i>';
$string['transmatch_desc'] = '번역된 일치를 과제 셋업 스크린에서 설정으로 사용할 것인지 결정하십시오.<br /><i>(Turnitin 계정에서 번역된 일치를 활성화한 경우에만 이 옵션을 활성화하십시오)</i>';
$string['repositoryoptions_0'] = '강사에게 Turnitin 보관소 보관 옵션을 활성화하기';
$string['repositoryoptions_1'] = '강사 확장 보관소 옵션 활성화';
$string['repositoryoptions_2'] = '표준 보관소에 모든 보고서를 제출하십시오';
$string['repositoryoptions_3'] = '보관소에 어떤 보고서도 제출하지 마십시오';
$string['turnitinrepositoryoptions'] = '보고서 보관소 설정';
$string['turnitinrepositoryoptions_desc'] = 'Turnitin 과제의 보관소 옵션을 선택하십시오.<br /><i>(기관 사용자 계정이 활성화된 사용자만 기관 보관소 이용 가능)</i>';
$string['tiimiscsettings'] = '기타 플러그인 설정';
$string['pp_agreement_default'] = '이 박스에 체크함으로써 본 제출물이 나의 저작임을 확인하며 이의 제출 결과로 발생할 수 있는 모든 저작권 침해에 대해 책임을 받아들입니다.';
$string['pp_agreement_desc'] = '<b>[선택]</b><br />제출물에 대한 동의 확인 문장을 입력하십시오.<br />(<b>주의:</b> 본 동의서가 공백일 경우, 제출 도중 학생에 의한 동의 확인이 요구되지 않을 것입니다)';
$string['pp_agreement'] = '면책조항/동의';
$string['studentdataprivacy'] = '학생 데이타 개인 정보 환경';
$string['studentdataprivacy_desc'] = 'API를 통해 Turnitin으로 학생의 개인 데이타가 전송되지 않도록 하려면 다음 설정을 구성할 수 있습니다.';
$string['enablepseudo'] = '학생 개인 정보 보호 활성화하기';
$string['enablepseudo_desc'] = '본 옵션을 선택할 경우, 학생 이메일들이 Turnitin API 콜을 위해 가명으로 전환될 것입니다.<br /><i>(<b>참고:</b>  본 옵션은 Moodle 사용자 데이타가 벌써 동시화된 경우 변경할 수 없습니다)</i>';
$string['pseudofirstname'] = '학생 가명 (이름)';
$string['pseudofirstname_desc'] = '<b>[옵션]</b><br />Turnitin의 문서보기 창에 나타날 학생의 이름';
$string['pseudolastname'] = '학생 가명 (성)';
$string['pseudolastname_desc'] = 'Turnitin의 문서보기 창에 나타날 학생의 (가족) 성';
$string['pseudolastnamegen'] = '(가족) 성 자동 작성하기';
$string['pseudolastnamegen_desc'] = '가명 (가족) 성에 대해 동의하고, 이를 사용자 프로파일 필드에 설정한 경우, 본 필드는 자동적으로 고유한 식별력을 갖게 됩니다.';
$string['pseudoemailsalt'] = '가 암호 Salt';
$string['pseudoemailsalt_desc'] = '<b>[옵션]</b><br />학생의 가이메일의 복합성을 높이기 위한 salt 옵션.<br />(<b>참고:</b> 지속적인 가이메일을 유지하기 위해서는 이 salt를 변경할 수 없습니다)';
$string['pseudoemaildomain'] = '가 이메일 도메인';
$string['pseudoemaildomain_desc'] = '<b>[옵션]</b><br />가이메일 주소의 옵션 도메인. (비워둘 경우 기본 값인 @tiimoodle.com으로 설정)';
$string['pseudoemailaddress'] = '가 이메일 주소';
$string['connecttest'] = 'Turnitin 접속을 시험';
$string['connecttestsuccess'] = 'Moodle이 Turnitin에 성공적으로 연결되었습니다.';
$string['diagnosticoptions_0'] = '꺼짐';
$string['diagnosticoptions_1'] = '표준';
$string['diagnosticoptions_2'] = '디버깅';
$string['repositoryoptions_4'] = '기관 보관소에 모든 보고서 제출';
$string['turnitinrepositoryoptions_help'] = '<strong>강사에게 Turnitin 보관소 보관 옵션을 활성화하기: </strong><br />강사는 문서를 표준 데이터베이스에 추가하거나, 기관의 개인 데이터베이스에 추가하거나, 데이터베이스에 추가하지 않도록 Turnitin에 지정할 수 있습니다.<br /><br /><strong>강사 확장 보관소 옵션 활성화: </strong><br />강사는 이 옵션을 사용하여 과제 설정을 보고 Turnitin에서  학생들이 문서 저장 위치를 지정하도록 허용할 수 있습니다. 학생들은 문서를 표준 학생 데이터베이스에 추가할지 아니면 기관의 개인 데이터베이스에 추가할지를 선택할 수 있습니다.<br /><br /><strong>표준 보관소에 모든 보고서를 제출하십시오: </strong><br />기본적으로 모든 문서는 표준 학생 데이터베이스에 추가됩니다.<br /><br /><strong>보관소에 어떤 보고서도 제출하지 마십시오: </strong><br />문서는 Turnitin에서 초기 검사를 수행하여 강사에게 채점 결과를 표시하는 데에만 사용됩니다.<br /><br /><strong>기관 보관소에 모든 보고서 제출: </strong><br />Turnitin은 기관의 보고서 데이터베이스 내에 모든 보고서를 저장합니다. 제출된 문서에 대한 유사성 검사는 기관 내의 다른 강사에 의해서만 수행됩니다.';
$string['turnitinuseanon'] = '익명 채점 사용하기';
$string['createassignmenterror'] = 'Turnitin에서 과제를 생성하는데 오류가 발생하였습니다';
$string['editassignmenterror'] = 'Turnitin에 있는 과제를 수정하는데 오류가 발생하였습니다';
$string['ppassignmentediterror'] = '모듈{$a->title}(TII ID: {$a->assignmentid})은 Turnitin에서 편집되지 못했습니다. 자세한 정보는 해당 API 로그를 참조하십시오.';
$string['pp_classcreationerror'] = '이 클래스는 Turnitin에서 생성되지 못했습니다. 자세한 정보는 해당 API 로그를 참조하십시오.';
$string['unlinkusers'] = '사용자 링크 취소하기';
$string['relinkusers'] = '사용자 재연결하기';
$string['unlinkrelinkusers'] = 'Turnitin 사용자 연결 취소/재연결하기';
$string['nointegration'] = '통합이 없음';
$string['sprevious'] = '이전';
$string['snext'] = '다음';
$string['slengthmenu'] = '보기_MENU_입력사항';
$string['ssearch'] = '검색:';
$string['sprocessing'] = '처리중...';
$string['szerorecords'] = '기록이 없음';
$string['sinfo'] = '_START_ to _END_ 의 _TOTAL_ 사항 표시';
$string['userupdateerror'] = '사용자 데이타를 업데이트할 수 없었음';
$string['connecttestcommerror'] = 'Turnitin에 연결할 수 없었습니다. API URL 환경을 재확인하시기 바랍니다.';
$string['userfinderror'] = 'Turnitin에 있는 사용자를 찾는 데 오류가 발생하였습니다';
$string['tiiusergeterror'] = 'Turnitin에서 사용자 상세사항을 가져오는 데 오류가 발생하였습니다';
$string['usercreationerror'] = 'Turnitin 사용자 생성 실패';
$string['ppassignmentcreateerror'] = '이 모듈은 Turnitin에서 생성되지 못했습니다. 자세한 정보는 해당 API 로그를 참조하십시오.';
$string['excludebiblio_help'] = '본 환경은 학생 보고서의 참고문헌, 인용 저작물이나 인용 섹션들이 독창성 보고서 생성시 비교검색 대상에서 제외되도록 강사가 선택할 수 있게 해줍니다. 이 환경은 개별 독창성 보고서마다 취소할 수 있습니다.';
$string['excludequoted_help'] = '본 환경은 본문에 있는 인용문들이 독창성 보고서 생성시 비교검색 대상에서 제외되도록 강사가 선택할 수 있게 해줍니다. 이 환경은 개별 독창성 보고서마다 취소할 수 있습니다.';
$string['excludevalue_help'] = '본 환경은 강사가 너무 짧은 일치 사항(강사가 판단한 바)이 독창성 보고서 생성시 반영되지 않도록 선택할 수 있게 해줍니다. 이 환경은 개별 독창성 보고서마다 취소할 수 있습니다.';
$string['spapercheck_help'] = '보고서에 대한 독창성 보고서 처리시 Turnitin 학생 보고서 보관소에 대해 비교검색. 이것이 선택되지 않은 경우 유사성 지표 퍼센트가 감소할 수 있음.';
$string['internetcheck_help'] = '보고서에 대한 독창성 보고서 처리시 Turnitin 인터넷 보관소에 대해 비교검색. 이것이 선택되지 않은 경우 유사성 지표 퍼센트가 감소할 수 있음.';
$string['journalcheck_help'] = '보고서에 대한 독창성 보고서 처리시 Turnitin 저널, 계간지 그리고 발행 저작물 보관소에 대해 비교 검색. 이것이 선택되지 않은 경우 유사성 지표 퍼센트가 감소할 수 있음.';
$string['reportgenspeed_help'] = '과제물 설정을 위한 3가지 옵션: &#39;보고서를 즉시 생성합니다. 제출물이 마감일에 데이터베이스에 추가됩니다(데이터베이스가 설정된 경우).&#39;, &#39;보고서를 즉시 생성합니다. 제출물이 데이터베이스에 바로 추가됩니다(데이터베이스가 설정된 경우).&#39;, &#39;보고서를 마감일에 생성합니다. 제출물이 마감일에 데이터베이스에 추가됩니다(데이터베이스가 설정된 경우).&#39;<br /><br />&#39;보고서를 즉시 생성합니다. 제출물이 마감일에 데이터베이스에 추가됩니다(데이터베이스가 설정된 경우).&#39; 옵션은 학생이 보고서를 제출하면 바로 독창성 보고서를 생성합니다. 이 옵션을 선택하면 학생들은 과제물을 다시 제출할 수 없습니다.<br /><br />재제출을 허용하려면 &#39;보고서를 즉시 생성합니다. 제출물이 데이터베이스에 바로 추가됩니다(데이터베이스가 설정된 경우).&#39;을 선택하십시오. 이 옵션을 선택하면 학생들이 기한까지 계속해서 과제 논문을 재제출할 수 있습니다. 재제출 시 독창성 보고서 처리 시간은 약 24시간 정도 걸릴 수 있습니다.<br /><br />&#39;보고서를 마감일에 생성합니다. 제출물이 마감일에 데이터베이스에 추가됩니다(데이터베이스가 설정된 경우).&#39; 옵션을 선택하면 제출 마감일에만 독창성 보고서를 생성합니다. 이 설정으로 독창성 보고서가 생성되면 제출된 모든 보고서를 서로 비교할 수 있습니다.';
$string['turnitinuseanon_desc'] = '제출물을 채점할 때 익명 채점을 허용할 것인지 결정하십시오.<br /><i>(이는 계정에 익명 채점이 구성되어 있는 분만 사용할 수 있습니다)</i>';
