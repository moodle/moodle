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
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['adminurl'] = '啟動 URL';
$string['adminurldesc'] = '用於存取易存取性報告的 LTI 啟動 URL。';
$string['allyclientconfig'] = 'Ally 配置';
$string['ally:clientconfig'] = '存取和更新用戶端配置';
$string['ally:viewlogs'] = 'Ally 記錄檢視器';
$string['clientid'] = '用戶端代號';
$string['clientiddesc'] = 'Ally 用戶端代號';
$string['code'] = '代碼';
$string['contentauthors'] = '內容作者';
$string['contentauthorsdesc'] = '對於指派給選取的這些角色的管理員和使用者，將評估其上傳的課程檔案的易存取性。會給予這些檔案易存取性評價。較低評價表示檔案需要變更以便於存取。';
$string['contentupdatestask'] = '內容更新工作';
$string['curlerror'] = 'cURL 錯誤：{$a}';
$string['curlinvalidhttpcode'] = '無效的 HTTP 狀態代碼：{$a}';
$string['curlnohttpcode'] = '無法驗證 HTTP 狀態代碼';
$string['error:invalidcomponentident'] = '無效的元件識別碼 {$a}';
$string['error:pluginfilequestiononly'] = '此 URL 僅支援問題元件';
$string['error:componentcontentnotfound'] = '針對 {$a} 找不到任何內容';
$string['error:wstokenmissing'] = '缺少 Web 服務 Token。管理使用者可能需要執行自動配置？';
$string['excludeunused'] = '排除未使用的檔案';
$string['excludeunuseddesc'] = '略過附加於 HTML 內容，但已在 HTML 中連結/參照的檔案。';
$string['filecoursenotfound'] = '傳入的檔案不屬於任何課程';
$string['fileupdatestask'] = '推送檔案更新至 Ally';
$string['id'] = '編號';
$string['key'] = '金鑰';
$string['keydesc'] = 'LTI 使用者金鑰。';
$string['level'] = '層級';
$string['message'] = '訊息';
$string['pluginname'] = 'Ally';
$string['pushurl'] = '檔案更新 URL';
$string['pushurldesc'] = '推送檔案更新相關通知至此 URL。';
$string['queuesendmessagesfailure'] = '傳送訊息至 AWS SQS 時發生錯誤。錯誤資料：$a';
$string['secret'] = '密鑰';
$string['secretdesc'] = 'LTI 密鑰。';
$string['showdata'] = '顯示資料';
$string['hidedata'] = '隱藏資料';
$string['showexplanation'] = '顯示說明';
$string['hideexplanation'] = '隱藏說明';
$string['showexception'] = '顯示例外情況';
$string['hideexception'] = '隱藏例外情況';
$string['usercapabilitymissing'] = '提供的使用者無權刪除此檔案。';
$string['autoconfigure'] = '自動配置 Ally Web 服務';
$string['autoconfiguredesc'] = '自動為 Ally 建立 Web 服務角色和使用者。';
$string['autoconfigureconfirmation'] = '自動建立 Ally 網路服務角色和使用者，並啟用網路服務。系統將進行下列動作：<ul><li>建立標題為「ally_webservice」的角色和使用者名稱為「ally_webuser」的使用者</li><li>將使用者「ally_webuser」新增至「ally_webservice」角色</li><li>啟用網路服務</li><li>啟用其餘網路服務傳輸協定</li><li>啟用 Ally 網路服務</li><li>為「ally_webuser」帳戶建立 Token</li></ul>';
$string['autoconfigsuccess'] = '成功 - 已自動配置 Ally Web 服務。';
$string['autoconfigtoken'] = 'Web 服務 Token 如下：';
$string['autoconfigapicall'] = '您可以透過以下 URL 測試 Web 服務是否正常運作：';
$string['privacy:metadata:files:action'] = '對檔案執行的動作，例如：已建立、已更新或已刪除。';
$string['privacy:metadata:files:contenthash'] = '用於確定唯一性的檔案內容雜湊。';
$string['privacy:metadata:files:courseid'] = '檔案所屬的課程編號。';
$string['privacy:metadata:files:externalpurpose'] = '若要與 Ally 整合，必須與 Ally 交換檔案。';
$string['privacy:metadata:files:filecontents'] = '實際檔案內容會傳送至 Ally，以便評估其易存取性。';
$string['privacy:metadata:files:mimetype'] = '檔案 MIME 類型，例如：text/plain、image/jpeg 等。';
$string['privacy:metadata:files:pathnamehash'] = '用於唯一識別檔案的檔案路徑名稱。';
$string['privacy:metadata:files:timemodified'] = '上次修改此欄位的時間。';
$string['cachedef_annotationmaps'] = '儲存課程的註解資料';
$string['cachedef_fileinusecache'] = '使用快取內的 Ally 檔案';
$string['cachedef_pluginfilesinhtml'] = 'HTML 快取內的 Ally 檔案';
$string['cachedef_request'] = 'Ally 篩選器請求快取';
$string['pushfilessummary'] = 'Ally 檔案更新摘要。';
$string['pushfilessummary:explanation'] = '傳送至 Ally 的檔案更新摘要。';
$string['section'] = '第 {$a} 節';
$string['lessonanswertitle'] = '「{$a}」課程的答案';
$string['lessonresponsetitle'] = '對「{$a}」課程的回應';
$string['logs'] = 'Ally 記錄';
$string['logrange'] = '記錄範圍';
$string['loglevel:none'] = '無';
$string['loglevel:light'] = '輕度';
$string['loglevel:medium'] = '中';
$string['loglevel:all'] = '全部';
$string['logcleanuptask'] = 'Ally 日誌清除工作';
$string['loglifetimedays'] = '在此天數內保留日誌';
$string['loglifetimedaysdesc'] = '在此天數內保留 Ally 日誌。設為 0 表示從不刪除日誌。排定的工作 (預設) 設為每天執行，並且將移除比此天數更久的日誌項目。';
$string['logger:filtersetupdebugger'] = 'Ally 篩選器設定日誌';
$string['logger:pushtoallysuccess'] = '成功推送至 Ally 端點';
$string['logger:pushtoallyfail'] = '未成功推送至 Ally 端點';
$string['logger:pushfilesuccess'] = '成功推送檔案至 Ally 端點';
$string['logger:pushfileliveskip'] = '即時檔案推送失敗';
$string['logger:pushfileliveskip_exp'] = '由於發生通訊問題，已略過即時檔案推送。檔案更新工作成功時，即時檔案推送會還原。請檢閱您的配置。';
$string['logger:pushfileserror'] = '未成功推送至 Ally 端點';
$string['logger:pushfileserror_exp'] = '與推送至 Ally 服務的內容更新相關的錯誤。';
$string['logger:pushcontentsuccess'] = '成功推送內容至 Ally 端點';
$string['logger:pushcontentliveskip'] = '即時內容推送失敗';
$string['logger:pushcontentliveskip_exp'] = '由於發生通訊問題，已略過即時內容推送。內容更新工作成功時，即時內容推送會還原。請檢閱您的配置。';
$string['logger:pushcontentserror'] = '未成功推送至 Ally 端點';
$string['logger:pushcontentserror_exp'] = '與推送至 Ally 服務的內容更新相關的錯誤。';
$string['logger:addingconenttoqueue'] = '正在新增內容至推送佇列';
$string['logger:annotationmoderror'] = 'Ally 模組內容批註失敗。';
$string['logger:annotationmoderror_exp'] = '未正確識別模組。';
$string['logger:failedtogetcoursesectionname'] = '無法取得課程章節名稱';
$string['logger:moduleidresolutionfailure'] = '無法解析模組編號';
$string['logger:cmidresolutionfailure'] = '無法解析課程模組編號';
$string['logger:cmvisibilityresolutionfailure'] = '無法解析課程模組可見性';
$string['courseupdatestask'] = '推送課程事件至 Ally';
$string['logger:pushcoursesuccess'] = '成功推送課程事件至 Ally 端點';
$string['logger:pushcourseliveskip'] = '即時課程事件推送失敗';
$string['logger:pushcourseerror'] = '即時課程事件推送失敗';
$string['logger:pushcourseliveskip_exp'] = '由於發生通訊問題，已略過即時課程事件推送。課程事件更新工作成功時，即時課程事件推送會還原。請檢閱您的配置。';
$string['logger:pushcourseserror'] = '未成功推送至 Ally 端點';
$string['logger:pushcourseserror_exp'] = '與推送至 Ally 服務的課程更新相關的錯誤。';
$string['logger:addingcourseevttoqueue'] = '正在新增課程事件至推送佇列';
$string['logger:cmiderraticpremoddelete'] = '課程模組編號預先刪除發生問題。';
$string['logger:cmiderraticpremoddelete_exp'] = '未正確識別模組；模組因刪除區段而不存在，或有其他因素觸發刪除而使其找不到。';
$string['logger:servicefailure'] = '取用服務時失敗。';
$string['logger:servicefailure_exp'] = '<br>類別：{$a->class}<br>參數：{$a->params}';
$string['logger:autoconfigfailureteachercap'] = '指派講師 archetype 功能給 ally_webservice 角色時失敗。';
$string['logger:autoconfigfailureteachercap_exp'] = '<br>能力：{$a->cap}<br>權限：{$a->permission}';
$string['deferredcourseevents'] = '傳送延後的課程事件';
$string['deferredcourseeventsdesc'] = '允許傳送儲存的課程事件，這些事件在與 Ally 通訊失敗期間累積';
