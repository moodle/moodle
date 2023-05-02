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
 * Japanese language strings.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

$string['pluginname'] = 'Microsoft 365の統合';
$string['acp_title'] = 'Microsoft 365管理コントロールパネル';
$string['acp_healthcheck'] = '状態チェック';
$string['acp_parentsite_name'] = 'Moodle';
$string['acp_parentsite_desc'] = 'Moodleコースデータ共有サイト。';
$string['calendar_user'] = 'パーソナル (ユーザ) カレンダー';
$string['calendar_site'] = 'サイト全体のカレンダー';
$string['erroracpauthoidcnotconfig'] = '初めにauth_oidcにアプリケーションの認証情報を設定してください。';
$string['erroracplocalo365notconfig'] = '初めにlocal_o365を設定してください。';
$string['errorhttpclientbadtempfileloc'] = 'ファイルを保存するための一時的な場所を開けませんでした。';
$string['errorhttpclientnofileinput'] = 'httpclient::putにファイルパラメータが指定されていません';
$string['errorcouldnotrefreshtoken'] = 'トークンを更新できませんでした';
$string['errorchecksystemapiuser'] = 'システムAPIユーザトークンを取得できませんでした。状態チェックを実行してMoodle cronが実行されていることを確認してください。必要に応じて、システムAPIユーザを更新してください。';
$string['erroro365apibadcall'] = 'API呼び出しエラー。';
$string['erroro365apibadcall_message'] = 'API呼び出しエラー: {$a}';
$string['erroro365apibadpermission'] = 'アクセス権が見つかりませんでした';
$string['erroro365apicouldnotcreatesite'] = 'サイト作成中の問題。';
$string['erroro365apicoursenotfound'] = 'コースが見つかりませんでした。';
$string['erroro365apiinvalidtoken'] = '無効または期限切れのトークン。';
$string['erroro365apiinvalidmethod'] = '無効なhttpmethodがapicallに渡されました';
$string['erroro365apinoparentinfo'] = '親フォルダの情報が見つかりませんでした';
$string['erroro365apinotimplemented'] = 'これは上書きする必要があります。';
$string['erroro365apinotoken'] = '所定のリソースまたはユーザのトークンがなく、取得もできませんでした。ユーザのリフレッシュトークンの有効期限が切れていませんか？';
$string['erroro365apisiteexistsnolocal'] = 'サイトは既に存在しますが、ローカルレコードが見つかりませんでした。';
$string['eventapifail'] = 'APIの失敗';
$string['eventcalendarsubscribed'] = 'ユーザはカレンダーに登録しました';
$string['eventcalendarunsubscribed'] = 'ユーザはカレンダーへの登録を解除しました';
$string['healthcheck_fixlink'] = '問題を修復するにはここをクリックしてください。';
$string['healthcheck_systemapiuser_title'] = 'システムAPIユーザ';
$string['healthcheck_systemtoken_result_notoken'] = 'Microsoft 365にシステムAPIユーザとして通信するためのトークンをMoodleが持っていません。これは通常、システムAPIユーザをリセットすることで解決します。';
$string['healthcheck_systemtoken_result_noclientcreds'] = 'OpenID Connectプラグインにアプリケーションの認証情報がありません。これらの認証情報がないとMoodleはMicrosoft 365と通信できません。ここをクリックして設定ページにアクセスし、認証情報を入力してください。';
$string['healthcheck_systemtoken_result_badtoken'] = 'Microsoft 365にシステムAPIユーザとして通信できませんでした。これは通常、システムAPIユーザをリセットすることで解決します。';
$string['healthcheck_systemtoken_result_passed'] = 'MoodleはMicrosoft 365にシステムAPIユーザとして通信できます。';
$string['settings_aadsync'] = 'ユーザをAzure ADと同期する';
$string['settings_aadsync_details'] = 'この機能を有効にすると、 MoodleとAzure ADのユーザは上記のオプションに従って同期されます。<br /><br /><b>注 : </b>同期ジョブはMoodle cronで実行し、一度に1000ユーザを同期します。デフォルトでは、毎日、サーバのあるタイムゾーンの午前1:00に実行されます。 大きなユーザセットを短時間で同期するには、[<b>ユーザをAzure ADと同期する</b>] のタスクの頻度を増やします (<a href="{$a}">スケジュールタスクの管理ページを使用)。</a><br /><br />詳細な手順は、<a href="https://docs.moodle.org/30/en/Office365#User_sync">ユーザ同期ドキュメント</a>をご覧ください。<br /><br />';
$string['settings_aadsync_create'] = 'Azure ADのユーザ向けにMoodleにアカウントを作成する';
$string['settings_aadsync_delete'] = 'Azure ADからアカウントが削除された場合、以前同期したMoodleのアカウントを削除する';
$string['settings_aadsync_match'] = '既存のMoodleユーザとAzure ADの同名のアカウントを一致させる<br /><small>これは、Microsoft 365のユーザ名とMoodleのユーザ名を検索し、一致を見つけようとします。一致では、大文字と小文字を区別せず、Microsoft 365テナントを無視します。例えば、MoodleのBoB.SmiThはbob.smith@example.onmicrosoft.comと一致します。一致するユーザは、MoodleアカウントとMicrosoft 365アカウントが連結され、すべてのMicrosoft 365/Moodle統合機能を使用できるようになります。ユーザの認証方法は、以下の設定が有効にならない限り、変更されません。</small>';
$string['settings_aadsync_matchswitchauth'] = '一致したユーザをMicrosoft 365 (OpenID Connect) 認証に切り替える<br /><small>ここでは、上の「一致」設定を有効にする必要があります。ユーザが一致する場合、この設定を有効にすると、認証方法がOpenID Connectに切り替わります。その後、Microsoft 365の資格情報を使用してMoodleにログインします。<b>注意: この設定を使用する場合は、OpenID Connect認証プラグインが有効であることを確認してください。</small>';
$string['settings_aadtenant'] = 'Azure ADテナント';
$string['settings_aadtenant_details'] = 'Azure AD内で組織を特定するために使用します。例 : "contoso.onmicrosoft.com"';
$string['settings_azuresetup'] = 'Azure設定';
$string['settings_azuresetup_details'] = 'このツールはAzureですべてが正しく設定されているかどうか確認します。また、一般的なエラーを修正することもできます。';
$string['settings_azuresetup_update'] = '更新';
$string['settings_azuresetup_checking'] = '確認しています...';
$string['settings_azuresetup_missingperms'] = '次のアクセス権がありません :';
$string['settings_azuresetup_permscorrect'] = 'アクセス権は正しいです。';
$string['settings_azuresetup_errorcheck'] = 'Azure設定の確認中にエラーが発生しました。';
$string['settings_azuresetup_unifiedheader'] = '統合API';
$string['settings_azuresetup_unifieddesc'] = '統合APIは、既存のアプリケーション固有APIと置き換わります。統合APIが利用可能な場合、今後に備えてAzureアプリケーションに追加する必要があります。最終的には、レガシーAPIと置き換わる予定です。';
$string['settings_azuresetup_unifiederror'] = '統合APIサポートの確認中にエラーが発生しました。';
$string['settings_azuresetup_unifiedactive'] = '統合APIがアクティブです。';
$string['settings_azuresetup_unifiedmissing'] = '統合APIは、このアプリケーションで見つかりませんでした。';
$string['settings_creategroups'] = 'ユーザグループを作成する';
$string['settings_creategroups_details'] = 'この機能を有効にした場合、サイト上のコースごとに、Microsoft 365で教師と学生のグループを作成して維持管理します。これにより、cronの実行ごとに必要なグループが作成され、現在のすべてのメンバーが追加されます。その後、グループのメンバーシップは、ユーザのMoodleコースへの登録/登録解除に従って維持管理されます。<br /><b>注 : </b>この機能を利用するには、Azureに追加されたアプリケーションにMicrosoft 365統合APIが追加されている必要があります。<a href="https://docs.moodle.org/30/en/Office365#User_groups">設定手順とドキュメント。</a>';
$string['settings_o365china'] = '中国向けMicrosoft 365';
$string['settings_o365china_details'] = '中国向けMicrosoft 365を使用している場合は、ここをチェックします。';
$string['settings_debugmode'] = 'デバッグメッセージを記録する';
$string['settings_debugmode_details'] = 'この機能を有効にすると、Moodleログに情報が記録されます。これは問題を特定するのに役立ちます。';
$string['settings_detectoidc'] = 'アプリケーションの認証情報';
$string['settings_detectoidc_details'] = 'Microsoft 365と通信するには、Moodleを識別するための認証情報が必要です。認証情報は"OpenID Connect"認証プラグインに設定されています。';
$string['settings_detectoidc_credsvalid'] = '認証情報が設定されました。';
$string['settings_detectoidc_credsvalid_link'] = '変更';
$string['settings_detectoidc_credsinvalid'] = '認証情報が設定されていないか、または不完全です。';
$string['settings_detectoidc_credsinvalid_link'] = '認証情報を設定する';
$string['settings_detectperms'] = 'アプリケーションのアクセス権';
$string['settings_detectperms_details'] = 'プラグイン機能を使用するには、Azure ADでアプリケーションの正しいアクセス権が設定されている必要があります。';
$string['settings_detectperms_nocreds'] = '初めにアプリケーションの認証情報を設定する必要があります。上記の設定を参照してください。';
$string['settings_detectperms_missing'] = '不足 :';
$string['settings_detectperms_errorfix'] = 'アクセス権の修復中にエラーが発生しました。Azureで手作業で設定してください。';
$string['settings_detectperms_fixperms'] = 'アクセス権を修復する';
$string['settings_detectperms_fixprereq'] = '自動的に修復するには、システムAPIユーザが管理者であることと、"Windows Azure Active Directory"アプリケーション向けに"組織のディレクトリへのアクセス"のアクセス権がAzureで有効になっている必要があります。';
$string['settings_detectperms_nounified'] = '統合APIが存在しません。新しい機能の一部が機能しない場合があります。';
$string['settings_detectperms_unifiednomissing'] = 'すべてのUnifiedアクセス権があります。';
$string['settings_detectperms_update'] = '更新';
$string['settings_detectperms_valid'] = 'アクセス権が設定されました。';
$string['settings_detectperms_invalid'] = 'Azure ADのアクセス権をチェックする';
$string['settings_header_setup'] = '設定';
$string['settings_header_options'] = 'オプション';
$string['settings_healthcheck'] = '状態チェック';
$string['settings_healthcheck_details'] = '何かが正しく機能していない場合、状態チェックを実行することで問題を特定し、解決策を表示することができます';
$string['settings_healthcheck_linktext'] = '状態チェックを実行する';
$string['settings_odburl'] = 'OneDrive for BusinessのURL';
$string['settings_odburl_details'] = 'OneDrive for Businessへのアクセスに使用するURLです。これは通常、Azure ADテナントによって決定されます。たとえば、Azure ADテナントが"contoso.onmicrosoft.com"の場合、通常は"contoso-my.sharepoint.com"になります。ドメイン名のみを入力し、http://やhttps://は含めないでください。';
$string['settings_serviceresourceabstract_valid'] = '{$a} は使用できます。';
$string['settings_serviceresourceabstract_invalid'] = 'この値は使用できない可能性があります。';
$string['settings_serviceresourceabstract_nocreds'] = '初めにアプリケーションの認証情報を設定してください。';
$string['settings_serviceresourceabstract_empty'] = '値を入力するか、[検出]をクリックして正しい値を検出します。';
$string['settings_systemapiuser'] = 'システムAPIユーザ';
$string['settings_systemapiuser_details'] = 'Azure ADユーザは任意ですが、管理者のアカウントまたは専用のアカウントのいずれかである必要があります。このアカウントはユーザ固有ではない操作を実行するのに使用されます。たとえば、コースSharePointサイトの管理などです。';
$string['settings_systemapiuser_change'] = 'ユーザの変更';
$string['settings_systemapiuser_usernotset'] = 'ユーザが設定されていません。';
$string['settings_systemapiuser_userset'] = '{$a}';
$string['settings_systemapiuser_setuser'] = 'ユーザの設定';
$string['spsite_group_contributors_name'] = '{$a} コントリビュータ';
$string['spsite_group_contributors_desc'] = 'コース {$a} のファイルを管理するためのアクセス権をもつすべてのユーザ';
$string['task_calendarsyncin'] = 'o365イベントをMoodleと同期する';
$string['task_coursesync'] = 'Microsoft 365にユーザグループを作成する';
$string['task_refreshsystemrefreshtoken'] = 'システムAPIユーザのリフレッシュトークンの更新';
$string['task_syncusers'] = 'ユーザをAzure ADと同期します。';
$string['ucp_connectionstatus'] = '接続ステータス';
$string['ucp_calsync_availcal'] = '利用可能なMoodleカレンダー';
$string['ucp_calsync_title'] = 'Outlookカレンダーの同期';
$string['ucp_calsync_desc'] = 'カレンダーをチェックすると、MoodleからOutlookカレンダーに同期されます。';
$string['ucp_connection_status'] = 'Microsoft 365の接続は次のとおりです。';
$string['ucp_connection_start'] = '&nbsp;365に接続する';
$string['ucp_connection_stop'] = 'Microsoft 365から接続解除する';
$string['ucp_features'] = 'Microsoft 365の機能';
$string['ucp_features_intro'] = '以下は、Microsoft 365でMoodleを機能強化する際に使用できる機能のリストです。';
$string['ucp_features_intro_notconnected'] = '一部の機能は、Microsoft 365に接続するまで使用できない可能性があります。';
$string['ucp_general_intro'] = 'ここでMicrosoft 365への接続を管理できます。';
$string['ucp_index_aadlogin_title'] = 'Microsoft 365のログイン';
$string['ucp_index_aadlogin_desc'] = 'Microsoft 365の認証情報を使用してMoodleにログインできます。 ';
$string['ucp_index_calendar_title'] = 'Outlookカレンダーの同期';
$string['ucp_index_calendar_desc'] = 'ここでは、MoodleとOutlook間のカレンダーの同期を設定できます。MoodleカレンダーのイベントをOutlookにエクスポートしたり、OutlookのイベントをMoodleに取り込んだりできます。';
$string['ucp_index_connectionstatus_connected'] = '現在Microsoft 365に接続されています';
$string['ucp_index_connectionstatus_matched'] = 'Microsoft 365ユーザ<small>"{$a}"</small>と一致しました。 この接続を完了するには、以下のリンクをクリックしてMicrosoft 365にログインしてください。';
$string['ucp_index_connectionstatus_notconnected'] = '現在Microsoft 365に接続されていません';
$string['ucp_index_onenote_title'] = 'OneNote';
$string['ucp_index_onenote_desc'] = 'OneNoteの統合により、Microsoft 365 OneNoteをMoodleで使用できます。OneNoteを使用して課題を完成させたり、コースで気軽にメモを取ったりできます。';
$string['ucp_notconnected'] = 'ここにアクセスする前に、Microsoft 365に接続してください。';
$string['settings_onenote'] = 'Microsoft 365 OneNoteを無効にする';
$string['ucp_status_enabled'] = 'アクティブ';
$string['ucp_status_disabled'] = '未接続';
$string['ucp_syncwith_title'] = '同期対象 :';
$string['ucp_syncdir_title'] = '同期の動作 :';
$string['ucp_syncdir_out'] = 'MoodleからOutlookへ';
$string['ucp_syncdir_in'] = 'OutlookからMoodleへ';
$string['ucp_syncdir_both'] = 'OutlookとMoodleを両方更新する';
$string['ucp_title'] = 'Microsoft 365 / Moodleコントロールパネル';
$string['ucp_options'] = 'オプション';
