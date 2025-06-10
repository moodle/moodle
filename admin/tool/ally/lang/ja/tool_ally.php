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

$string['adminurl'] = '起動URL';
$string['adminurldesc'] = 'アクセシビリティレポートにアクセスするために使用されるLTI起動URL。';
$string['allyclientconfig'] = 'Ally設定';
$string['ally:clientconfig'] = 'クライアント設定のアクセスと更新';
$string['ally:viewlogs'] = 'Allyログビューア';
$string['clientid'] = 'クライアントID';
$string['clientiddesc'] = 'AllyクライアントID';
$string['code'] = 'ソースコード';
$string['contentauthors'] = 'コンテンツの作者';
$string['contentauthorsdesc'] = '選択されたロールに割り当てられた管理者とユーザは、アップロードされたコースファイルのアクセシビリティについて評価されます。ファイルにはアクセシビリティの評価が与えられます。評価が低いということは、そのファイルのアクセシビリティを改善するために変更が必要であることを意味します。';
$string['contentupdatestask'] = 'コンテンツの更新タスク';
$string['curlerror'] = 'cURLエラー：{$a}';
$string['curlinvalidhttpcode'] = '不正なHTTPステータスコード：{$a}';
$string['curlnohttpcode'] = 'HTTPステータスコードを確認できません';
$string['error:invalidcomponentident'] = '不正なコンポーネント識別子{$a}';
$string['error:pluginfilequestiononly'] = 'このURLでは問題コンポーネントのみサポートされています';
$string['error:componentcontentnotfound'] = '{$a} のコンテンツが見つかりません';
$string['error:wstokenmissing'] = 'ウェブサービストークンがありません。管理者ユーザは自動設定を実行する必要があるかもしれません。';
$string['excludeunused'] = '未使用のファイルを除外する';
$string['excludeunuseddesc'] = 'HTMLコンテンツに添付されていて、HTML内でリンク/参照されているファイルを除外します。';
$string['filecoursenotfound'] = '渡されたファイルはどのコースにも属していません';
$string['fileupdatestask'] = 'ファイルの更新をAllyにプッシュ';
$string['id'] = 'ID';
$string['key'] = 'キー';
$string['keydesc'] = 'LTIコンシューマーキー。';
$string['level'] = 'レベル';
$string['message'] = 'メッセージ';
$string['pluginname'] = 'Ally';
$string['pushurl'] = 'ファイル更新URL';
$string['pushurldesc'] = 'ファイル更新に関する通知をこのURLにプッシュします。';
$string['queuesendmessagesfailure'] = 'AWS SQSへのメッセージ送信中にエラーが発生しました。エラーデータ：$a';
$string['secret'] = 'プライベートキー';
$string['secretdesc'] = 'LTIのプライベートキー。';
$string['showdata'] = 'データを表示する';
$string['hidedata'] = 'データを非表示にする';
$string['showexplanation'] = '説明を表示する';
$string['hideexplanation'] = '説明を非表示にする';
$string['showexception'] = '例外を表示する';
$string['hideexception'] = '例外を非表示にする';
$string['usercapabilitymissing'] = '指定されたユーザは、このファイルを削除することができません。';
$string['autoconfigure'] = 'Allyウェブサービスを自動設定する';
$string['autoconfiguredesc'] = 'Allyのために自動的にウェブサービスロールとユーザを作成します。';
$string['autoconfigureconfirmation'] = 'Allyのために自動的にウェブサービスロールとユーザを作成します。次のアクションが実行されます。<ul><li>「ally_webservice」というロールと「ally_webuser」というユーザ名のユーザを作成します</li><li>「ally_webuser」ユーザを「ally_webservice」ロールに追加します</li><li>ウェブサービスを有効にします</li><li>ウェブサービスプロトコルを有効にします</li><li>Allyウェブサービスを有効にします</li><li>「ally_webuser」アカウントのトークンを作成します</li></ul>';
$string['autoconfigsuccess'] = '成功 - Allyウェブサービスが自動的に設定されました。';
$string['autoconfigtoken'] = 'ウェブサービストークンは次のとおりです：';
$string['autoconfigapicall'] = '次のURLを介してウェブサービスが機能しているかどうかをテストできます：';
$string['privacy:metadata:files:action'] = 'ファイルに対して行われたアクション、例：作成、更新、または削除。';
$string['privacy:metadata:files:contenthash'] = '一意性を判断するためのファイルのコンテンツハッシュ。';
$string['privacy:metadata:files:courseid'] = 'ファイルが所属するコースID。';
$string['privacy:metadata:files:externalpurpose'] = 'Allyと統合するには、ファイルをAllyと交換する必要があります。';
$string['privacy:metadata:files:filecontents'] = '実際のファイルのコンテンツは、アクセシビリティを評価するためAllyに送信されました。';
$string['privacy:metadata:files:mimetype'] = 'ファイルMIMEタイプ、例：テキスト/プレイン、イメージ/jpegなど。';
$string['privacy:metadata:files:pathnamehash'] = '一意に識別するためのファイルのパス名ハッシュ。';
$string['privacy:metadata:files:timemodified'] = 'フィールドが最後に修正された日時。';
$string['cachedef_annotationmaps'] = 'コースの注釈データを保存する';
$string['cachedef_fileinusecache'] = '使用中の関連ファイルのキャッシュ';
$string['cachedef_pluginfilesinhtml'] = 'HTML内の関連ファイルのキャッシュ';
$string['cachedef_request'] = 'Allyフィルタリクエストのキャッシュ';
$string['pushfilessummary'] = 'Allyファイル更新の要約。';
$string['pushfilessummary:explanation'] = 'Allyに送信された更新の要約。';
$string['section'] = 'セクション {$a}';
$string['lessonanswertitle'] = '授業「{$a}」の解答';
$string['lessonresponsetitle'] = '授業「{$a}」の回答';
$string['logs'] = 'Allyログ';
$string['logrange'] = 'ログ範囲';
$string['loglevel:none'] = 'なし';
$string['loglevel:light'] = '小';
$string['loglevel:medium'] = '中';
$string['loglevel:all'] = 'すべて';
$string['logcleanuptask'] = 'Allyログのクリーンアップタスク';
$string['loglifetimedays'] = 'この日数の間ログを保持する';
$string['loglifetimedaysdesc'] = 'この日数の間Allyログを保持します。0に設定して、決してログを削除しないようにします。スケジュールタスクは毎日実行するように (デフォルトで) 設定され、この日数より古くなったログエントリを削除するようにします。';
$string['logger:filtersetupdebugger'] = 'Allyフィルタ設定ログ';
$string['logger:pushtoallysuccess'] = 'Allyエンドポイントへのプッシュ成功';
$string['logger:pushtoallyfail'] = 'Allyエンドポイントへのプッシュ失敗';
$string['logger:pushfilesuccess'] = 'Allyエンドポイントへのファイルのプッシュ成功';
$string['logger:pushfileliveskip'] = 'ライブファイルプッシュ失敗';
$string['logger:pushfileliveskip_exp'] = '通信上の問題のためライブファイルプッシュをスキップしています。ライブファイルプッシュは、ファイル更新タスクが成功したときにリストアされます。設定を確認してください。';
$string['logger:pushfileserror'] = 'Allyエンドポイントへのプッシュ失敗';
$string['logger:pushfileserror_exp'] = 'Allyサービスへのコンテンツ更新のプッシュに関連するエラー。';
$string['logger:pushcontentsuccess'] = 'Allyエンドポイントへのコンテンツのプッシュ成功';
$string['logger:pushcontentliveskip'] = 'ライブコンテンツプッシュ失敗';
$string['logger:pushcontentliveskip_exp'] = '通信上の問題のためライブコンテンツプッシュをスキップしています。ライブコンテンツプッシュは、コンテンツ更新タスクが成功したときにリストアされます。設定を確認してください。';
$string['logger:pushcontentserror'] = 'Allyエンドポイントへのプッシュ失敗';
$string['logger:pushcontentserror_exp'] = 'Allyサービスへのコンテンツ更新のプッシュに関連するエラー。';
$string['logger:addingconenttoqueue'] = 'コンテンツをプッシュキューに追加';
$string['logger:annotationmoderror'] = 'Allyモジュールコンテンツ注釈が失敗しました。';
$string['logger:annotationmoderror_exp'] = 'モジュールが正しく識別されませんでした。';
$string['logger:failedtogetcoursesectionname'] = 'コースセクション名の取得に失敗しました';
$string['logger:moduleidresolutionfailure'] = 'モジュールIDの解決に失敗しました';
$string['logger:cmidresolutionfailure'] = 'コースモジュールIDの解決に失敗しました';
$string['logger:cmvisibilityresolutionfailure'] = 'コースモジュールの可視性の解決に失敗しました';
$string['courseupdatestask'] = 'Allyにコースイベントをプッシュする';
$string['logger:pushcoursesuccess'] = 'Allyエンドポイントへのコースイベントのプッシュに成功しました';
$string['logger:pushcourseliveskip'] = 'ライブコースイベントプッシュ失敗';
$string['logger:pushcourseerror'] = 'ライブコースイベントプッシュ失敗';
$string['logger:pushcourseliveskip_exp'] = '通信上の問題のためライブコースイベントプッシュをスキップしています。ライブコースイベントプッシュは、コースイベント更新タスクが成功したときにリストアされます。設定を確認してください。';
$string['logger:pushcourseserror'] = 'Allyエンドポイントへのプッシュ失敗';
$string['logger:pushcourseserror_exp'] = 'Allyサービスへのコース更新プッシュに関連するエラー。';
$string['logger:addingcourseevttoqueue'] = 'コースイベントをプッシュキューに追加';
$string['logger:cmiderraticpremoddelete'] = 'コースモジュールIDに事前削除の問題があります。';
$string['logger:cmiderraticpremoddelete_exp'] = 'モジュールが正しく識別できませんでした。セクションが削除されたために存在しないか、他の要因で削除フックがトリガされ、見つからなくなりました。';
$string['logger:servicefailure'] = 'サービスの使用中に失敗しました。';
$string['logger:servicefailure_exp'] = '<br>クラス：{$a->class}<br>パラメータ：{$a->params}';
$string['logger:autoconfigfailureteachercap'] = 'ally_webserviceロールに教師アーキタイプケイパビリティを割り当て中に失敗しました。';
$string['logger:autoconfigfailureteachercap_exp'] = '<br>ケイパビリティ：{$a->cap}<br>パーミッション：{$a->permission}';
$string['deferredcourseevents'] = '保留されたコースイベントを送信する';
$string['deferredcourseeventsdesc'] = 'Allyとの通信エラーの間に蓄積した、保存されたコースイベントを送信できるようにします。';
