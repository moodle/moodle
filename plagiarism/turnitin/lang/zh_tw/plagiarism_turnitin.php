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
$string['pluginname'] = 'Turnitin 剽竊 Plugin';
$string['turnitin'] = 'Turnitin';
$string['task_name'] = 'Turnitin 剽竊 Plugin 工作';
$string['connecttesterror'] = '連線至 Turnitin 時發生錯誤，傳回的錯誤訊息如下：<br />';

// Assignment Settings.
$string['turnitin:enable'] = '啟用 Turnitin';
$string['excludebiblio'] = '不含參考書目';
$string['excludequoted'] = '不含引用資料內容';
$string['excludevalue'] = '排除小型相符處';
$string['excludewords'] = '字';
$string['excludepercent'] = '百分比';
$string['norubric'] = '沒有評語';
$string['otherrubric'] = '使用屬於其他指導教師的評語';
$string['attachrubric'] = '將評語附加至此作業';
$string['launchrubricmanager'] = '啟動評語管理工具';
$string['attachrubricnote'] = '注意：學生將可以在提交前，檢視附加的評語及其內容。';
$string['anonblindmarkingnote'] = '注意：個別的 Turnitin 匿名標示設定已遭到移除。Turnitin 將使用 Moodle 的盲標設定來決定匿名標示設定。';
$string['transmatch'] = '已翻譯的相符功能';
$string["reportgen_immediate_add_immediate"] = "立即產生報告。 提交將立即新增至存放庫 (如果已設定存放庫)。";
$string["reportgen_immediate_add_duedate"] = "立即產生報告。提交將在截止日期新增至存放庫 (如果已設定存放庫)。";
$string["reportgen_duedate_add_duedate"] = "在截止日期產生報告。報告將在截止日期新增至存放庫 (如果已設定存放庫)。";
$string['launchquickmarkmanager'] = '啟動 Quickmark 管理工具';
$string['launchpeermarkmanager'] = '啟動 Peermark 管理工具';
$string['studentreports'] = '向學生顯示原創性報告';
$string['studentreports_help'] = '允許您向學生使用者顯示 Turnitin 原創性報告。若設定為「確定」，學生將可以檢視 Turnitin 產生的原創性報告。';
$string['submitondraft'] = '在初次上傳時提交檔案';
$string['submitonfinal'] = '將學生送交評分時提交檔案';
$string['draftsubmit'] = '應何時將檔案提交至 Turnitin？';
$string['allownonor'] = '允許任何檔案類型的提交物件？';
$string['allownonor_help'] = '此設定將允許提交任何檔案類型。若此選項設為「是」，將會在可行時，檢查提交物件的原創性，可以下載提交物件，以及使用 GradeMark 反饋工具。';
$string['norepository'] = '沒有存放庫';
$string['standardrepository'] = '標準存放庫';
$string['submitpapersto'] = '儲存學生報告';
$string['institutionalrepository'] = '機構存放庫 (適用時)';
$string['checkagainstnote'] = '注意：如果下面的「比較對象」選項當中，至少有一個未選擇「是」，就不會產生原創性報告。';
$string['spapercheck'] = '與已儲存的學生報告比較';
$string['internetcheck'] = '與網際網路比較';
$string['journalcheck'] = '與雜誌、<br />期刊與出版物比較';
$string['compareinstitution'] = '將提交的檔案和此機構內提交的報告比較';
$string['reportgenspeed'] = '產生報告速度';
$string['locked_message'] = '已鎖定訊息';
$string['locked_message_help'] = '若有任何設定遭到鎖定，會顯示此訊息說明原因。';
$string['locked_message_default'] = '於網站層級鎖定此設定';
$string['sharedrubric'] = '共用的評語';
$string['turnitinrefreshsubmissions'] = '重新整理提交物件';
$string['turnitinrefreshingsubmissions'] = '正在重新整理提交物件';
$string['turnitinppulapre'] = '若要提交檔案至 Turnitin，您首先必須接受我們的 EULA。如果選擇不接受我們的 EULA，則您的檔案將只能夠提交至 Moodle。請按一下此處以閱讀並接受「協議」。';
$string['noscriptula'] = '(因為您並未啟用 Javascript，在接受 Turnitin 使用者協議後，您必須手動更新此頁面，然後才能提交物件)';
$string['filedoesnotexist'] = '檔案已遭到刪除';
$string['reportgenspeed_resubmission'] = '您已經繳交文稿至此作業，我們已針對您繳交的文稿作成相似度報告。如果選擇重新繳交文稿，新文稿會覆蓋較早的文稿，並作成新報告。{$a->num_resubmissions} 重新繳交後，您必須等候 {$a->num_hours} 小時才能看到新的相似度報告。';

// Plugin settings.
$string['config'] = '配置';
$string['defaults'] = '預設設定';
$string['showusage'] = '顯示資料傾印';
$string['saveusage'] = '儲存資料傾印';
$string['errors'] = '錯誤';
$string['turnitinconfig'] = 'Turnitin 剽竊 Plugin 配置';
$string['tiiexplain'] = 'Turnitin 為商務產品。您必須付費訂閱，才能使用此服務。如需更多資訊，請參閱　<a href=http://docs.moodle.org/en/Turnitin_administration>http://docs.moodle.org/en/Turnitin_administration</a>';
$string['useturnitin'] = '啟用 Turnitin';
$string['useturnitin_mod'] = '啟用 Turnitin 的對象 {$a}';
$string['turnitindefaults'] = 'Turnitin 剽竊 Plugin 預設設定';
$string['defaultsdesc'] = '以下設定是在活動單元內啟用 Turnitin時的預設集';
$string['turnitinpluginsettings'] = 'Turnitin 剽竊 Plugin 設定';
$string['pperrorsdesc'] = '嘗試將以下檔案上傳至 Turnitin 時出現問題。若要重新提交，請選擇您要重新提交的檔案，然後按「重新提交」按鈕。這些檔案會在下次 Cron 執行時處理。';
$string['pperrorssuccess'] = '已重新提交您選擇的檔案，並將由 Cron 負責處理。';
$string['pperrorsfail'] = '您選擇的檔案有一些出現問題，無法為其建立新的 Cron 事件。';
$string['resubmitselected'] = '重新提交所選檔案';
$string['deleteconfirm'] = '確定要刪除此提交物件嗎？\n\n此動作將無法復原。';
$string['deletesubmission'] = '刪除提交物件';
$string['semptytable'] = '未找到任何結果。';
$string['configupdated'] = '配置已更新';
$string['defaultupdated'] = 'Turnitin 預設值已更新';
$string['notavailableyet'] = '無法使用';
$string['resubmittoturnitin'] = '重新提交至 Turnitin';
$string['resubmitting'] = '正在重新提交';
$string['id'] = 'ID';
$string['student'] = '學生';
$string['course'] = '課程';
$string['module'] = '單元';

// Grade book/View assignment page.
$string['turnitin:viewfullreport'] = '檢視原創性報告';
$string['launchrubricview'] = '檢視標示用的評分表';
$string['turnitinppulapost'] = '並未將您的檔案提交至 Turnitin。請按一下這裡，以接受我們的 EULA。';
$string['ppsubmissionerrorseelogs'] = '這個檔案並未提交至 Turnitin，請連絡您的系統管理員';
$string['ppsubmissionerrorstudent'] = '這個檔案並未提交至 Turnitin，請連絡您的指導教師，以取得更多詳細資料';

// Receipts.
$string['messageprovider:submission'] = 'Turnitin 剽竊 Plugin 電子回條通知';
$string['digitalreceipt'] = '電子回條';
$string['digital_receipt_subject'] = '這是您的 Turnitin 電子回條';
$string['pp_digital_receipt_message'] = '{$a->firstname} {$a->lastname} 您好：<br /><br />您已在 <strong>{$a->submission_date}</strong> 上成功將檔案 <strong>{$a->submission_title}</strong> 提交至課程 <strong>{$a->course_fullname}</strong> 中的作業 <strong>{$a->assignment_name}{$a->assignment_part}</strong>。您的提交 ID 為 <strong>{$a->submission_id}</strong>。您可以從文件檢視工具列印/下載按鈕，來檢視或列印完整的電子回條。<br /><br />感謝您使用 Turnitin，<br /><br />Turnitin 團隊敬上';

// Paper statuses.
$string['turnitinid'] = 'Turnitin ID';
$string['turnitinstatus'] = 'Turnitin 狀態';
$string['pending'] = '等待中';
$string['similarity'] = '相似度';
$string['notorcapable'] = '無法爲此檔案製作原創性報告。';
$string['grademark'] = 'GradeMark';
$string['student_read'] = '學生檢視報告於：';
$string['student_notread'] = '學生尚未檢視此報告。';
$string['launchpeermarkreviews'] = '啟動 Peermark 評論';

// Cron.
$string['ppqueuesize'] = '剽竊 Plugin 事件佇列中的事件數';
$string['ppcronsubmissionlimitreached'] = '因為每次執行這個 Corn，只能處理 {$a} 個，所以不會再將提交物件傳送至 Turnitin。';
$string['cronsubmittedsuccessfully'] = '課程 {$a->coursename} 上作業 {$a->assignmentname} 的提交物件：{$a->title} (TII ID：{$a->submissionid}) 已成功提交至 Turnitin。';
$string['pp_submission_error'] = 'Turnitin 已傳回有關提交物件的錯誤：';
$string['turnitindeletionerror'] = 'Turnitin 提交物件刪除失敗。本機的 Moodle 複本已遭到移除，但無法刪除 Turnitin 內的提交物件。';
$string['ppeventsfailedconnection'] = '因為無法與 Turnitin 建立連接，所以執行此 Cron，Turnitin 剽竊 Plugin 也不會處理任何事件。';

// Error codes.
$string['tii_submission_failure'] = '請連絡您的指導教師或系統管理員，以取得更多詳細資料';
$string['faultcode'] = '錯誤代號';
$string['line'] = '列';
$string['message'] = '訊息';
$string['code'] = '代號';
$string['tiisubmissionsgeterror'] = '嘗試從 Turnitin 內取得此作業的提交物件時發生錯誤';
$string['errorcode0'] = '這個檔案並未提交至 Turnitin，請連絡您的系統管理員';
$string['errorcode1'] = '因為這個檔案的內容不足，無法產生原創性報告，所以並未提交至 Turnitin。';
$string['errorcode2'] = '這個檔案超過允許的檔案大小上限 {$a->maxfilesize}，因此將不會提交至 Turnitin';
$string['errorcode3'] = '因為使用者尚未接受 Turnitin 使用者授權協議，所以不會將此檔案提交至 Turnitin。';
$string['errorcode4'] = '您必須為此作業上傳支援的檔案類型。可接受的檔案類型如下：.doc、.docx、.ppt、.pptx、.pps、.ppsx、.pdf、.txt、.htm、.html、.hwp、.odt、.wpd、.ps 及 .rtf';
$string['errorcode5'] = '這個檔案並未提交至 Turnitin，因為在 Turnitin 中建立單元時出現問題，故無法提交。請參閱 API 記錄檔以取得進一步資訊';
$string['errorcode6'] = '這個檔案並未提交至 Turnitin，因為在 Turnitin 中建立單元時出現問題，故無法提交。請參閱 API 記錄檔以取得進一步資訊';
$string['errorcode7'] = '這個檔案並未提交至 Turnitin，因為在 Turnitin 中建立使用者時出現問題，故無法提交。請參閱 API 記錄檔以取得進一步資訊';
$string['errorcode8'] = '因為建立暫存檔案時出現問題，所以並未提交這個檔案。原因很可能是檔案名稱無效。請重新命名該檔案，然後使用「編輯提交物件」來重新上傳';
$string['errorcode9'] = '因為檔案集區中沒有可存取的內容可供提交，所以無法提交該檔案。';
$string['coursegeterror'] = '無法取得課程資料';
$string['configureerror'] = '在課程內使用此單元之前，您必須先以管理員的身分進行完整設定。請聯繫您的 Moodle 管理員。';
$string['turnitintoolofflineerror'] = '發生暫時性問題。請稍後再試。';
$string['defaultinserterror'] = '嘗試將預設的設定值插入資料庫時發生錯誤';
$string['defaultupdateerror'] = '嘗試更新資料庫中預設的設定值時發生錯誤';
$string['tiiassignmentgeterror'] = '嘗試從 Turnitin 內取得作業時發生錯誤';
$string['assigngeterror'] = '無法取得 Turnitin 資料';
$string['classupdateerror'] = '無法更新 Turnitin 課程資料';
$string['pp_createsubmissionerror'] = '嘗試在 Turnitin 內建立提交物件時發生錯誤';
$string['pp_updatesubmissionerror'] = '嘗試將您的提交物件重新提交至 Turnitin 時發生錯誤';
$string['tiisubmissiongeterror'] = '嘗試從 Turnitin 內取得提交物件時發生錯誤';

// Javascript.
$string['closebutton'] = '關閉';
$string['loadingdv'] = '正在載入 Turnitin 文件檢視工具...';
$string['changerubricwarning'] = '變更或分離評語，會將從此作業內的報告中移除現有的所有評語分數，包括之前標示的計分卡。之前已評分報告的整體成績則會予以保留。';
$string['messageprovider:submission'] = 'Turnitin 剽竊 Plugin 電子回條通知';

// Turnitin Submission Status.
$string['turnitinstatus'] = 'Turnitin 狀態';
$string['deleted'] = '已刪除';
$string['pending'] = '等待中';
$string['because'] = '這是因為管理員從處理佇列中刪除等待中的作業，並中止提交至Turnitin。<br /><strong>檔案仍存在 Moodle 中，請連絡您的指導教師。</strong><br />如需任何錯誤代碼，請參閱下面資訊：';
$string['submitpapersto_help'] = '<strong>沒有存放庫: </strong><br />Turnitin 被設定為不將上傳文件儲存至任何知識庫。文件僅用於初始查重。<br /><br /><strong>標準存放庫: </strong><br />Turnitin 將只在標準知識庫中儲存上傳文件的副本。選擇此選項，Turnitin 對日後上傳文件的查重工作將只使用已儲存文件。<br /><br /><strong>機構存放庫 (適用時): </strong><br />選擇此選項，將 Turnitin 設定為只添加文件至您機構的私有知識庫。上傳文件的查重工作將只由您機構的其他教員完成。';
$string['errorcode12'] = '此文件未能上傳至 Turnitin 因其所在任務課程已刪除。行 ID: ({$a->id}) | 課程模塊 ID: ({$a->cm}) | 用戶 ID: ({$a->userid})';
$string['errorcode15'] = '此檔案已提交至 Turnitin，因為找不到其所屬的活動模組';
$string['tiiaccountconfig'] = 'Turnitin 帳戶配置';
$string['turnitinaccountid'] = 'Turnitin 帳戶 ID';
$string['turnitinsecretkey'] = 'Turnitin 共用金鑰';
$string['turnitinapiurl'] = 'Turnitin API URL';
$string['tiidebugginglogs'] = '偵錯和記錄';
$string['turnitindiagnostic'] = '啟用診斷模式';
$string['turnitindiagnostic_desc'] = '<b>[警告]</b><br />啟用診斷模式，只能追蹤 Turnitin API 的問題。';
$string['tiiaccountsettings_desc'] = '請確定這些設定和 Turnitin 帳戶中配置的設定相符，否則您可能會遇到有關作業建立和/或學生提交的問題。';
$string['tiiaccountsettings'] = 'Turnitin 帳戶設定';
$string['turnitinusegrademark'] = '使用 GradeMark';
$string['turnitinusegrademark_desc'] = '選擇要使用 Turnitin GradeMark 或 Moodle 來為提交物件評分。<br /><i>(唯有帳戶設定 GradeMark 的使用者才可使用此功能)</i>';
$string['turnitinenablepeermark'] = '啟用 PeerMark 作業';
$string['turnitinenablepeermark_desc'] = '選擇是否允許建立 Peermark 作業。<br/><i>(唯有帳戶已配置 Peermark 的使用者，才會有此選項)</i>';
$string['transmatch_desc'] = '決定是否要在作業設定畫面上提供「已翻譯的相符功能」設定。<br /><i>(唯有 Turnitin 帳戶啟用「已翻譯的相符功能」時，才能啟用此選項)</i>';
$string['repositoryoptions_0'] = '啟用指導教師標準存放庫選項';
$string['repositoryoptions_1'] = '啟用指導教師的擴充存放庫選項';
$string['repositoryoptions_2'] = '將所有報告提交至標準存放庫';
$string['repositoryoptions_3'] = '不要將任何報告提交至存放庫';
$string['turnitinrepositoryoptions'] = '報告存放庫作業';
$string['turnitinrepositoryoptions_desc'] = '選擇 Turnitin 作業的存放庫選項.<br /><i>(唯有帳戶啟用此功能的使用者才可使用機關存放庫)</i>';
$string['tiimiscsettings'] = '其他 Plugin 設定';
$string['pp_agreement_default'] = '我確認所提交物件是我自己的作品，同時接受所有可能因提交此物件造成著作權侵權之責任。';
$string['pp_agreement_desc'] = '<b>[選擇性]</b><br />輸入一個提交時協議確認聲明。<br />(<b>注意：</b>如果完全空白的協議，學生在提交時就不需要確認協議)';
$string['pp_agreement'] = '免責聲明/協議';
$string['studentdataprivacy'] = '學生資料隱私設定';
$string['studentdataprivacy_desc'] = '可以配置以下設定，確保學生的個人資料不會透過 API 傳送至 Turnitin。';
$string['enablepseudo'] = '啟用學生隱私';
$string['enablepseudo_desc'] = '若選擇此選項，會將學生的電子郵件地址轉變為 Turnitin API 呼叫的虛擬對等物件。<br /><i>(<b>注意：</b>若有任何 Moodle 用戶資料已經與 Turnitin 同步的話，就不能變更此選項)</i>';
$string['pseudofirstname'] = '學生的假名';
$string['pseudofirstname_desc'] = '<b>[選擇性]</b><br />要在Turnitin 文件檢視工具內顯示的學生名字';
$string['pseudolastname'] = '學生的假姓';
$string['pseudolastname_desc'] = '要在Turnitin 文件檢視工具內顯示的學生姓氏';
$string['pseudolastnamegen'] = '自動產生姓氏';
$string['pseudolastnamegen_desc'] = '如果設定為「是」，就會對使用者設定檔欄位設定假姓，然後自動將唯一的識別碼填入該欄位。';
$string['pseudoemailsalt'] = '虛擬加密 Salt';
$string['pseudoemailsalt_desc'] = '<b>[選擇性]</b><br />選擇性的 Salt 可增加所產生虛擬學生電子郵件地址的複雜性。<br />(<b>注意：</b>Salt 應該維持不變，以讓虛擬的電子郵件地址保持一致)';
$string['pseudoemaildomain'] = '虛擬電子郵件網域';
$string['pseudoemaildomain_desc'] = '<b>[選擇性]</b><br />虛擬電子郵件地址的選擇性網域 (如果留下空白，預設為 @tiimoodle.com)';
$string['pseudoemailaddress'] = '虛擬電子郵件地址';
$string['connecttest'] = '測試 Turnitin 連線';
$string['connecttestsuccess'] = 'Moodle 已成功連線至 Turnitin。';
$string['diagnosticoptions_0'] = '關閉';
$string['diagnosticoptions_1'] = '標準';
$string['diagnosticoptions_2'] = '偵錯';
$string['repositoryoptions_4'] = '將所有文件上傳至機構資源庫';
$string['turnitinrepositoryoptions_help'] = '<strong>啟用指導教師標準存放庫選項: </strong><br />教員可將 Turnitin 設置為添加文件至標準知識庫、機構私有知識庫或不添加至知識庫。<br /><br /><strong>啟用指導教師的擴充存放庫選項: </strong><br />這一選項將允許教員查看作業設置，該作業設置允許學生通過 Turnitin 設定文件的儲存位置。學生可以選擇添加文件至標準學生知識庫或添加至您機構的私有知識庫。<br /><br /><strong>將所有報告提交至標準存放庫: </strong><br />所有文件都默認添加至標準學生知識庫。<br /><br /><strong>不要將任何報告提交至存放庫: </strong><br />文件通過 Turnitin 將只用于供教員查看評分和初始查重。<br /><br /><strong>將所有文件上傳至機構資源庫: </strong><br />Turnitin 被設定為將所有論文儲存至機構論文知識庫。上傳文件的查重工作將由您機構內的其他教員完成。';
$string['turnitinuseanon'] = '使用匿名標示';
$string['createassignmenterror'] = '嘗試在 Turnitin 內建立作業時發生錯誤';
$string['editassignmenterror'] = '嘗試在 Turnitin 內編輯作業時發生錯誤';
$string['ppassignmentediterror'] = '無法在 Turnitin 上編輯單元 {$a->title} (TII ID：{$a->assignmentid})，請參閱您的 API 記錄檔以取得進一步資訊';
$string['pp_classcreationerror'] = '無法在 Turnitin 上建立這個課程，請參閱您的 API 記錄檔以取得進一步資訊';
$string['unlinkusers'] = '解除連接使用者';
$string['relinkusers'] = '重新連接使用者';
$string['unlinkrelinkusers'] = '解除連接/重新連接 Turnitin 使用者';
$string['nointegration'] = '無整合';
$string['sprevious'] = '上一個';
$string['snext'] = '下一個';
$string['slengthmenu'] = '顯示 _MENU_ Entries';
$string['ssearch'] = '搜尋：';
$string['sprocessing'] = '正在從 Turnitin 載入資料...';
$string['szerorecords'] = '無法顯示任何記錄。';
$string['sinfo'] = '顯示 _START_ 到 _END_，總共 _TOTAL_ 個項目。';
$string['userupdateerror'] = '無法更新使用者資料';
$string['connecttestcommerror'] = '無法連線至 Turnitin。請再次檢查 API URL 設定。';
$string['userfinderror'] = '嘗試在 Turnitin 內尋找使用者時發生錯誤';
$string['tiiusergeterror'] = '嘗試從 Turnitin 內取得使用者詳細資料時發生錯誤';
$string['usercreationerror'] = 'Turnitin 使用者建立失敗';
$string['ppassignmentcreateerror'] = '無法在 Turnitin 上建立這個單元，請參閱您的 API 記錄檔以取得進一步資訊';
$string['excludebiblio_help'] = '產生原創性報告時，此設定允許指導教師選擇排除出現在學生報告中的參考書目部分的內文，使其不被檢查相符處。此設定可以在各個原創性報告內撤銷。';
$string['excludequoted_help'] = '產生原創性報告時，此設定允許指導教師選擇排除出現在學生報告中的引述內文，使其不被檢查相符處。此設定可以在各個原創性報告內撤銷。';
$string['excludevalue_help'] = '產生原創性報告時，此設定允許指導教師選擇排除不夠長的相符處 (長度由指導教師決定)。此設定可以在各個原創性報告內撤銷。';
$string['spapercheck_help'] = '當為報告處理原創性報告時，與 Turnitin 學生報告存放庫比較。若取消選取的話，相似度指標百分比可能會降低。';
$string['internetcheck_help'] = '為報告處理原創性報告時，與 Turnitin 網際網路存放庫比較。若取消選取的話，相似度指標百分比可能會降低。';
$string['journalcheck_help'] = '為報告處理原創性報告時，與 Turnitin 雜誌、期刊與出版物存放庫比較。若取消選取的話，相似度指標百分比可能會降低。';
$string['reportgenspeed_help'] = '此作業設定共有三個選項：「立即產生報告。提交將在截止日期新增至存放庫 (如果已設定存放庫)。」、「立即產生報告。 提交將立即新增至存放庫 (如果已設定存放庫)。」和「在截止日期產生報告。報告將在截止日期新增至存放庫 (如果已設定存放庫)。」<br /><br />「立即產生報告。提交將在截止日期新增至存放庫 (如果已設定存放庫)。」這個選項會在學生提交物件時，馬上產生原創性報告。若選擇這個選項，學生將無法重新提交至作業。<br /><br />若要允許重新提交，請選擇「立即產生報告。 提交將立即新增至存放庫 (如果已設定存放庫)。」選項。這允許學生在截止日期前，隨時皆可將報告重新提交至作業。處理重新提交物件的原創性報告，最多可能會需要 24 小時的時間。<br /><br />「在截止日期產生報告。報告將在截止日期新增至存放庫 (如果已設定存放庫)。」這個選項，只有在作業的截止日期當天，才會產生原創性報告。準備原創性報告時，此設定將會互相比較所有提交至作業的報告。';
$string['turnitinuseanon_desc'] = '選擇在為提交物件評分時是否允許匿名標示。<br /><i>(唯有帳戶設定可匿名標示的使用者才可使用此功能)</i>';
