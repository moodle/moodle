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
$string['pluginname'] = 'Turnitin Plagiarismプラグイン';
$string['turnitin'] = 'Turnitin';
$string['task_name'] = 'Turnitin Plagiarismプラグインのタスク';
$string['connecttesterror'] = 'Turnitinへの接続中にエラーが発生しました。エラーメッセージは以下の通りです。<br />';

// Assignment Settings.
$string['turnitin:enable'] = 'Turnitinを有効にする';
$string['excludebiblio'] = '参考文献の除外';
$string['excludequoted'] = '引用文献を除外';
$string['excludevalue'] = '小さな一致を除外する';
$string['excludewords'] = '語';
$string['excludepercent'] = 'パーセント';
$string['norubric'] = '採点なし';
$string['otherrubric'] = '他の講師に属するルーブリックを使用する';
$string['attachrubric'] = '採点をこの課題に添付する';
$string['launchrubricmanager'] = '採点マネジャーを起動する';
$string['attachrubricnote'] = '注意：学生は提出する前に、添付された採点およびその内容を閲覧できます。';
$string['anonblindmarkingnote'] = '注意：Turnitinの個別の匿名マーキングの設定が削除されました。Turnitinでは、Moodleのブラインドマーキングの設定を使って、匿名マーキングの設定を決定します。';
$string['transmatch'] = '翻訳一致機能';
$string["reportgen_immediate_add_immediate"] = "直ちにレポートを生成します。答案はすぐにリポジトリに追加されます（リポジトリが設定されている場合）。";
$string["reportgen_immediate_add_duedate"] = "直ちにレポートを生成します。答案は提出期限にリポジトリに追加されます（リポジトリが設定されている場合）。";
$string["reportgen_duedate_add_duedate"] = "提出期限にレポートを生成します。答案は提出期限にリポジトリに追加されます（リポジトリが設定されている場合）。";
$string['launchquickmarkmanager'] = 'Quickmarkマネジャーを起動する';
$string['launchpeermarkmanager'] = 'Peermarkマネジャーを起動する';
$string['studentreports'] = 'オリジナリティ レポートを学生に表示する';
$string['studentreports_help'] = 'Turnitinオリジナリティーレポートを受講生に表示することを許可する。はい、に設定すると、Turnitinにより作成されたオリジナリティーレポートは受講生により閲覧することができます。';
$string['submitondraft'] = '最初にアップロードされた際にファイルを提出する';
$string['submitonfinal'] = '受講生がマーキングに送信した際にファイルを提出する';
$string['draftsubmit'] = 'いつTurnitinにファイルを提出しますか？';
$string['allownonor'] = 'すべてのファイルタイプの提出物の提出を許可しますか？';
$string['allownonor_help'] = 'この設定では、すべてのファイルタイプで提出が可能になります。この設定を［はい］に設定すると、提出物のオリジナリティがチェックされ、ダウンロードが可能になり、また、GradeMarkフィードバックツールも利用できるようになります。';
$string['norepository'] = 'リポジトリなし';
$string['standardrepository'] = '標準リポジトリ';
$string['submitpapersto'] = '学生レポートを保存';
$string['institutionalrepository'] = '所属機関リポジトリ（適用する場合）';
$string['checkagainstnote'] = '注意：［～に対してチェックする］オプションのいずれにも［はい］を選択しなかった場合は、オリジナリティ レポートが生成されません。';
$string['spapercheck'] = '保存されている学生のレポートと比較する';
$string['internetcheck'] = 'インターネットでチェックする';
$string['journalcheck'] = 'ジャーナル、定期刊行物、<br />出版物をチェックする';
$string['compareinstitution'] = '提出されたファイルを教育機関内のレポートと比較する';
$string['reportgenspeed'] = '作成速度を報告する';
$string['locked_message'] = 'ロックのメッセージ';
$string['locked_message_help'] = 'ロックされている設定がある場合は、このメッセージでその理由を説明します。';
$string['locked_message_default'] = 'この設定はサイトレベルでロックされています';
$string['sharedrubric'] = '共有された採点';
$string['turnitinrefreshsubmissions'] = '提出物を更新';
$string['turnitinrefreshingsubmissions'] = '提出物を更新中';
$string['turnitinppulapre'] = 'Turnitinにファイルを提出するには、まずTurnitinのEULAに同意していただく必要があります。同意しない場合、ファイルはMoodleのみに提出されます。こちらをクリックしてEULAをお読みになり、同意してください。';
$string['noscriptula'] = '（ジャバスクリプトが作動されていないため、Turnitinユーザー使用規約に承諾した後、提出する前にこのページをマニュアル操作で更新する必要があります。）';
$string['filedoesnotexist'] = 'ファイルは削除されました';
$string['reportgenspeed_resubmission'] = 'この課題に対するレポートはすでに提出されており、その提出への類似性レポートが作成されました。レポートを再提出すると選択した場合、最初の提出と置き換えられ、新たなレポートが作成されます。{$a->num_resubmissions}再提出後は、新しい類似性レポートを見るのに再提出から{$a->num_hours}時間待つ必要があります。';

// Plugin settings.
$string['config'] = '設定';
$string['defaults'] = 'デフォルト設定';
$string['showusage'] = 'ダンプされたデータを表示';
$string['saveusage'] = 'ダンプされたデータを保存';
$string['errors'] = 'エラー';
$string['turnitinconfig'] = 'Turnitin Plagiarismプラグインの構成';
$string['tiiexplain'] = 'Turnitinは商用製品であり、このサービスを利用するにはサービス料のお支払いが必要です。詳しくは、<a href=http://docs.moodle.org/en/Turnitin_administration>http://docs.moodle.org/en/Turnitin_administration</a>をご覧ください。';
$string['useturnitin'] = 'Turnitinを有効にする';
$string['useturnitin_mod'] = 'Turnitinを有効にする： {$a}';
$string['turnitindefaults'] = 'Turnitin Plagiarismプラグインデフォルト設定';
$string['defaultsdesc'] = '次の設定はアクティビティーモジュール内のTurnitinを有効にする際のデフォルト設定です';
$string['turnitinpluginsettings'] = 'Turnitin Plagiarismプラグイン設定';
$string['pperrorsdesc'] = '以下のファイルをTurnitinにアップロード中に問題が発生しました。ファイルを再提出するには、再提出するファイルを選択し、［再提出］ボタンをクリックしてください。これらは、次回Cronを実行したときに処理されます。';
$string['pperrorssuccess'] = '選択したファイルが再提出され、Cronで処理されます。';
$string['pperrorsfail'] = '選択したファイルのいくつかで問題が発生しました。これらには新しいCronイベントを生成できませんでした。';
$string['resubmitselected'] = '選択したファイルを再提出する';
$string['deleteconfirm'] = 'この提出物を削除しますか？\n\n　いったん削除すると、元に戻すことはできません。';
$string['deletesubmission'] = '提出物を削除';
$string['semptytable'] = '検索結果がありません。';
$string['configupdated'] = '設定が更新されました';
$string['defaultupdated'] = 'Turnitin デフォルトが更新されました';
$string['notavailableyet'] = '利用できません';
$string['resubmittoturnitin'] = 'Turnitinに再提出する';
$string['resubmitting'] = '再提出中';
$string['id'] = 'ID';
$string['student'] = '学生';
$string['course'] = 'コース';
$string['module'] = 'モジュール';

// Grade book/View assignment page.
$string['turnitin:viewfullreport'] = 'オリジナリティ レポートを閲覧';
$string['launchrubricview'] = 'マーキングに使用された採点を閲覧する';
$string['turnitinppulapost'] = 'あなたのファイルはTurnitinに提出されませんでした。こちらをクリックして、EULAに同意してください。';
$string['ppsubmissionerrorseelogs'] = 'このファイルはTurnitinに提出されていません。詳しくは、システム管理者にお問い合わせください。';
$string['ppsubmissionerrorstudent'] = 'このファイルはTurnitinに提出されていません。更なる詳細に関しては、チューターまでご相談ください。';

// Receipts.
$string['messageprovider:submission'] = 'Turnitin Plagiarismプラグインのデジタル受領書に関する通知';
$string['digitalreceipt'] = 'デジタル受領書';
$string['digital_receipt_subject'] = 'これはあなたのTurnitinのデジタル受領書です';
$string['pp_digital_receipt_message'] = '{$a->lastname} {$a->firstname}様、<br /><br />あなたは<strong>{$a->submission_date}</strong>に、<strong>{$a->course_fullname}</strong>クラスの課題<strong>{$a->assignment_name}{$a->assignment_part}</strong>にファイル<strong>{$a->submission_title}</strong>を提出しました。提出IDは<strong>{$a->submission_id}</strong>です。デジタル受領書はすべて、文書閲覧内にある印刷やダウンロードボタンを使って閲覧および印刷することができます。<br /><br />Turnitinをご利用いただき、ありがとうございます。<br /><br />Turnitinチーム一同';

// Paper statuses.
$string['turnitinid'] = 'Turnitin ID';
$string['turnitinstatus'] = 'Turnitinのステータス';
$string['pending'] = '保留中';
$string['similarity'] = '類似性';
$string['notorcapable'] = 'このファイルに対してオリジナリティ レポートを作成することができません。';
$string['grademark'] = 'GradeMark';
$string['student_read'] = 'レポートの閲覧日：';
$string['student_notread'] = '受講生はこのレポートをまだ閲覧していません。';
$string['launchpeermarkreviews'] = 'Peermarkレビューを起動する';

// Cron.
$string['ppqueuesize'] = 'Plagiarismプラグインのイベントキューにあるイベント数';
$string['ppcronsubmissionlimitreached'] = 'Cronは一度に{$a}件までの提出物しか処理しないので、これ以上Turnitinに提出物を送れません。';
$string['cronsubmittedsuccessfully'] = '{$a->coursename}コースの課題{$a->assignmentname}に対して、提出物：{$a->title}（TII ID：{$a->submissionid}）が正しくTurnitinに送信されました。';
$string['pp_submission_error'] = 'Turnitinから次の提出物についてエラーが返されました。';
$string['turnitindeletionerror'] = 'Turnitinの提出物削除に失敗しました。ローカル Moodle コピーは削除されましたが、Turnitin内の提出物を削除することはできませんでした。';
$string['ppeventsfailedconnection'] = 'Turnitinに接続していないので、Turnitin PlagiarismプラグインはCronでイベントを処理できません。';

// Error codes.
$string['tii_submission_failure'] = '詳しくは、チューターかシステム管理者にお問い合わせください。';
$string['faultcode'] = 'フォルトコード';
$string['line'] = 'ライン';
$string['message'] = 'メッセージ';
$string['code'] = 'コード';
$string['tiisubmissionsgeterror'] = '提出物をTurnitinからこの課題へ入手する際にエラーが発生しました';
$string['errorcode0'] = 'このファイルはTurnitinに提出されていません。詳しくは、システム管理者にお問い合わせください。';
$string['errorcode1'] = 'このファイルはオリジナリティ レポートを作成するコンテンツが不足しているので、Turnitinに送信されていません。';
$string['errorcode2'] = 'このファイルは許容されるサイズの上限{$a->maxfilesize}を超えているため、Turnitinに提出できません。';
$string['errorcode3'] = 'このファイルはユーザーが、Turnitinのユーザーライセンス契約に同意していないため、Turnitinへ提出することができません';
$string['errorcode4'] = 'この課題に対応しているファイルの種類でアップロードする必要があります。アップロード可能なファイルは、.doc、.docx、.ppt、.pptx、.pps、.ppsx、.pdf、.txt、.htm、.html、.hwp、.odt、.wpd、.ps、.rtfです。';
$string['errorcode5'] = 'Turnitin内でのモジュールの作成に問題があるため、このファイルはTurnitinに提出されていません。詳しくは、APIログを参照してください。';
$string['errorcode6'] = 'Turnitin内でのモジュール設定の編集に問題があるため、このファイルはTurnitinに提出されていません。詳しくは、APIログを参照してください。';
$string['errorcode7'] = 'Turnitin内でのユーザーの作成に問題があるため、このファイルはTurnitinに提出されていません。詳しくは、APIログを参照してください。';
$string['errorcode8'] = '一時ファイルの作成に問題があるため、このファイルはTurnitinに提出されていません。ファイル名が無効である可能性があります。［提出物の編集］を使ってファイルの名前を変更してからもう一度アップロードしてください。';
$string['errorcode9'] = 'このファイルは、ファイルプールにアクセス可能なコンテンツがないため、送信できません。';
$string['coursegeterror'] = 'コースデータを取得できませんでした';
$string['configureerror'] = 'このコースの使用を開始する前に、管理者がこのモジュールを設定する必要があります。Moodle管理者までお問い合わせください。';
$string['turnitintoolofflineerror'] = '現在一時的な問題が発生しています。後ほど再度試みてください。';
$string['defaultinserterror'] = 'データベースのデフォルト設定値を挿入中にエラーが発生しました';
$string['defaultupdateerror'] = 'データベースのデフォルト設定値を更新中にエラーが発生しました';
$string['tiiassignmentgeterror'] = 'Turnitinから課題を入手する際にエラーが発生しました';
$string['assigngeterror'] = 'Turnitinデータを取得できませんでした';
$string['classupdateerror'] = 'Turnitinクラスのデータを更新できませんでした';
$string['pp_createsubmissionerror'] = 'Turnitinで提出物を作成する際にエラーが発生しました';
$string['pp_updatesubmissionerror'] = '提出物をTurnitinへ再提出する際にエラーが発生しました';
$string['tiisubmissiongeterror'] = '提出物をTurnitinから入手する際にエラーが発生しました';

// Javascript.
$string['closebutton'] = '閉じる';
$string['loadingdv'] = 'Turnitin文書閲覧を読み込み中...';
$string['changerubricwarning'] = '採点を変更したり解除したりすると、スコアカードを含めてこの課題のレポートに既に存在する採点がすべて削除されます。以前に採点されたレポートの全体評価は残ります。';
$string['messageprovider:submission'] = 'Turnitin Plagiarismプラグインのデジタル受領書に関する通知';

// Turnitin Submission Status.
$string['turnitinstatus'] = 'Turnitinのステータス';
$string['deleted'] = '削除されました';
$string['pending'] = '保留中';
$string['because'] = 'これは、管理者が保留中の課題をプロセスキューから削除し、Turnitinへの提出を中止したためです。<br /><strong>ファイルはMoodleに残ります。インストラクタにお問い合わせください。</strong><br />エラーコードは次の通りです。';
$string['submitpapersto_help'] = '<strong>リポジトリなし: </strong><br />Turnitin は、提出された文書を他のレポジトリに保管するように指定されていません。Turnitin によるレポートの処理は、初回の類似性チェックのときにのみ行われます。<br /><br /><strong>標準リポジトリ: </strong><br />Turnitin は提出された文書のコピーを標準のレポジトリにのみ保管します。このオプションを選択すると、Turnitin は将来提出されるすべての文書への類似性チェックを行うときにだけ、保管された文書を使用します。<br /><br /><strong>所属機関リポジトリ（適用する場合）: </strong><br />このオプションを選択すると、Turnitin は提出された文書を所属機関専用のレポジトリにのみ追加します。提出された文書への類似性チェックは、所属機関内の別の講師によってのみ行われます。';
$string['errorcode12'] = '削除されたコースの課題であるため、このファイルは Turnitin に提出されませんでした。列 ID: ({$a->id}) | コース モジュール ID: ({$a->cm}) | ユーザー ID: ({$a->userid})';
$string['errorcode15'] = '属しているアクティビティモジュールが見つからないため、このファイルはTurnitinに提出されていません。';
$string['tiiaccountconfig'] = 'Turnitinアカウントの構成';
$string['turnitinaccountid'] = 'TurnitinアカウントID';
$string['turnitinsecretkey'] = 'Turnitin共有キー';
$string['turnitinapiurl'] = 'Turnitin API URL';
$string['tiidebugginglogs'] = 'デバッグとログ';
$string['turnitindiagnostic'] = '診断モードをオンにする';
$string['turnitindiagnostic_desc'] = '<b>[注意]</b><br />Turnitin APIを使用して診断モードを有効にするのは、問題を追跡するときにだけにしてください。';
$string['tiiaccountsettings_desc'] = 'これらの設定がTurnitinアカウントでの構成と一致していることを確認してください。一致していないと、課題の作成や学生の提出物に問題が発生することがあります。';
$string['tiiaccountsettings'] = 'Turnitinアカウントの設定';
$string['turnitinusegrademark'] = 'GradeMarkを使用する';
$string['turnitinusegrademark_desc'] = 'GradeMarkを使用して提出物を評価するかどうかを選択してください。<br /><i>（このオプションは、アカウントでGradeMarkの使用を設定している方にのみ利用可能です）</i>';
$string['turnitinenablepeermark'] = 'Peermark課題のみを有効にする';
$string['turnitinenablepeermark_desc'] = 'Peermark課題の作成を許可するかどうかを選択してください。<br/><i>（Peermarkがアカウントに設定されている場合のみに使用可能です）</i>';
$string['transmatch_desc'] = '課題の設定画面で翻訳一致機能を利用するかどうかを決めます。<br /><i>（Turnitinアカウントで翻訳一致機能を有効にしている場合にのみ、このオプションを使用してください）</i>';
$string['repositoryoptions_0'] = 'インストラクタの標準リポジトリ オプションを有効にする';
$string['repositoryoptions_1'] = '講師の拡大リポジトリオプションを有効にする';
$string['repositoryoptions_2'] = 'すべてのレポートを標準リポジトリに提出する';
$string['repositoryoptions_3'] = 'レポートをリポジトリに提出しない';
$string['turnitinrepositoryoptions'] = 'レポートリポジトリの課題';
$string['turnitinrepositoryoptions_desc'] = 'Turnitin課題のリポジトリ オプションを選択してください。<br /><i>（所属機関リポジトリは、アカウントで有効にしている場合にのみ利用可能です）</i>';
$string['tiimiscsettings'] = 'その他のプラグインの設定';
$string['pp_agreement_default'] = 'このボックスをチェックすることによって、私はこの提出物が私自身の物であることを確認し、この提出物に関しての全ての著作権侵害の責任を負うこと誓います。';
$string['pp_agreement_desc'] = '<b>[オプション]</b><br />提出物への同意確認を入力してください。<br />（<b>注意：</b>同意欄が空白のまま残されると、学生の提出時に同意確認を要求しません）';
$string['pp_agreement'] = '免責事項／契約';
$string['studentdataprivacy'] = '受講生データプライバシー設定';
$string['studentdataprivacy_desc'] = '次の設定では、API経由で学生の個人情報をTurnitinに送信しないことを選択できます。';
$string['enablepseudo'] = '学生プライバシーを有効にする';
$string['enablepseudo_desc'] = 'このオプションを選択すると、学生の電子メール アドレスがTurnitin APIコールに変更されます。<br /><i>（<b>注意：</b>このオプションは、MoodleのユーザーデータがすでにTurnitinと同期されている場合は、変更できません）</i>';
$string['pseudofirstname'] = '学生の疑似の名前';
$string['pseudofirstname_desc'] = '<b>[オプション]</b><br />Turnitinの文書閲覧に学生の名だけが表示されます。';
$string['pseudolastname'] = '学生の疑似の姓';
$string['pseudolastname_desc'] = '文書閲覧に学生の名字が表示されます。';
$string['pseudolastnamegen'] = '名字を自動作成する';
$string['pseudolastnamegen_desc'] = 'これを有効にすると、疑似の姓がユーザーのプロファイルフィールドに設定され、このフィールドが固有のIDとして自動的に入力されます。';
$string['pseudoemailsalt'] = '擬似暗号化ソルト';
$string['pseudoemailsalt_desc'] = '<b>[オプション]</b><br />作成された学生の疑似電子メール アドレスをさらに複雑にするために、ソルトを使用できます。<br />（<b>注意：</b>疑似電子メール アドレスの一貫性を保つためには、ソルトを変更しないでください）';
$string['pseudoemaildomain'] = '疑似電子メールドメイン';
$string['pseudoemaildomain_desc'] = '<b>[オプション]</b><br />疑似電子メール アドレスのオプションドメイン（空白のままにすると、デフォルトで@tiimoodle.comに設定されます）';
$string['pseudoemailaddress'] = '疑似電子メール アドレス';
$string['connecttest'] = 'Turnitin接続のテスト';
$string['connecttestsuccess'] = 'MoodleはTurnitinに正しく接続しました。';
$string['diagnosticoptions_0'] = 'オフ';
$string['diagnosticoptions_1'] = '標準';
$string['diagnosticoptions_2'] = 'デバッグ';
$string['repositoryoptions_4'] = 'すべてのレポートを機関レポジトリに提出';
$string['turnitinrepositoryoptions_help'] = '<strong>インストラクタの標準リポジトリ オプションを有効にする: </strong><br />講師は、文書の追加先として標準のレポジトリを使う、所属機関専用のレポジトリを使う、またはレポジトリを使わないことを Turnitin で指定できます。<br /><br /><strong>講師の拡大リポジトリオプションを有効にする: </strong><br />このオプションは、講師が課題の設定を表示し、受講生が文書をどこに保管するかを Turnitin で指定できるようにするためのものです。受講生は自分の文書を標準の受講生レポジトリに追加することも、所属機関の専用レポジトリに保管することもできます。<br /><br /><strong>すべてのレポートを標準リポジトリに提出する: </strong><br />すべての文書は、既定で標準の受講者レポジトリに追加されます。<br /><br /><strong>レポートをリポジトリに提出しない: </strong><br />文書が使用されるのは、Turnitin での初回チェック時と講師による評価時のみです。<br /><br /><strong>すべてのレポートを機関レポジトリに提出: </strong><br />Turnitin は、すべてのレポートを所属機関のレポートレポジトリ内に保管するように指定されています。同様に、提出された文書のチェックは、所属機関内の別の講師によってのみ行われます。';
$string['turnitinuseanon'] = '匿名コメント記入（マーキング）を使用';
$string['createassignmenterror'] = '課題をTurnitinで作成する際にエラーが発生しました';
$string['editassignmenterror'] = '課題をTurnitinで編集する際にエラーが発生しました';
$string['ppassignmentediterror'] = 'モジュール{$a->title}（TII ID：{$a->assignmentid}）をTurnitin上で作成できませんでした。詳しい情報については、APIログをご覧ください。';
$string['pp_classcreationerror'] = 'このクラスをTurnitin上で作成できませんでした。詳しい情報については、APIログをご覧ください。';
$string['unlinkusers'] = 'ユーザーのリンクを削除する';
$string['relinkusers'] = 'ユーザーを再リンクする';
$string['unlinkrelinkusers'] = 'ユーザーのリンクを削除／再リンクする';
$string['nointegration'] = '統合無し';
$string['sprevious'] = '前';
$string['snext'] = '次';
$string['slengthmenu'] = '表示 _MENU_ 項目';
$string['ssearch'] = '検索：';
$string['sprocessing'] = 'Turnitinからデータをロード中...';
$string['szerorecords'] = '表示できる記録がありません。';
$string['sinfo'] = '_START_～_END_（全_TOTAL_）エントリを表示';
$string['userupdateerror'] = 'ユーザーデータを講師できませんでした';
$string['connecttestcommerror'] = 'Turnitinに接続できませんでした。APIのURL設定を確認してください。';
$string['userfinderror'] = 'Turnitinのユーザーを検索中にエラーが発生しました';
$string['tiiusergeterror'] = 'ユーザー情報をTurnitinから入手する際にエラーが発生しました';
$string['usercreationerror'] = 'Turnitinユーザー作成に失敗';
$string['ppassignmentcreateerror'] = 'このモジュールをTurnitin上で作成できませんでした。詳しい情報については、APIログをご覧ください。';
$string['excludebiblio_help'] = 'この設定では、オリジナリティ レポートが作成される際、一致の検索から参考文献、引用文、または参照文を除外するかどうかをインストラクタが選択できます。この設定は、個人のオリジナリティ レポートでは無効にすることができます。';
$string['excludequoted_help'] = 'この設定では、オリジナリティ レポートが作成される際、一致の検索から引用文章を除外するかどうかをインストラクタが選択できます。この設定は、個人のオリジナリティ レポートでは無効にすることができます。';
$string['excludevalue_help'] = 'この設定では、インストラクタはオリジナリティ レポートが作成される際、比較するのに長さが十分でない（講師によって決定される）一致を除外することを選択できます。この設定は、個人のオリジナリティ レポートでは無効にすることができます。';
$string['spapercheck_help'] = 'レポートのオリジナリティーレポートを処理する際にTurnitin受講生レポートリポジトリに対してチェックします。これを選択しない場合は、類似性指標が減少する場合があります。';
$string['internetcheck_help'] = 'レポートのオリジナリティ レポートを処理する際にTurnitinインターネットリポジトリに対してチェックします。選択されないと、類似性指標は減少する場合があります。';
$string['journalcheck_help'] = 'レポートのオリジナリティ レポートを処理する際にTurnitinの学術誌、定期刊行物、および出版物リポジトリに対してチェックします。選択されないと、類似性指標は減少する場合があります。';
$string['reportgenspeed_help'] = 'この課題の設定には、&#39;直ちにレポートを生成します。答案は提出期限にリポジトリに追加されます（リポジトリが設定されている場合）。&#39;、&#39;直ちにレポートを生成します。答案はすぐにリポジトリに追加されます（リポジトリが設定されている場合）。&#39;、&#39;提出期限にレポートを生成します。答案は提出期限にリポジトリに追加されます（リポジトリが設定されている場合）。&#39;の、3つのオプションがあります。<br /><br />&#39;直ちにレポートを生成します。答案は提出期限にリポジトリに追加されます（リポジトリが設定されている場合）。&#39;のオプションは、学生が提出すると、オリジナリティレポートが即座に作成されます。このオプションを選択すると、学生はこの課題への再提出ができません。<br /><br />再提出を許可するには、&#39;直ちにレポートを生成します。答案はすぐにリポジトリに追加されます（リポジトリが設定されている場合）。&#39;を選択してください。このオプションを選択すると、学生は期限日までレポートを再提出することができます。再提出物に対するオリジナリティ レポートの作成には、24時間ほどかかります。<br /><br />また、&#39;提出期限にレポートを生成します。答案は提出期限にリポジトリに追加されます（リポジトリが設定されている場合）。&#39;のオプションでは、オリジナリティ レポートが課題の提出期限日に作成されます。この設定では、オリジナリティ レポートが作成される際、この課題に対して提出されたすべてのレポートが互いの提出物に対して比較されます。';
$string['turnitinuseanon_desc'] = '提出物評価時に匿名コメントの記入（マーキング）を許可するかどうかを設定してください。<br /><i>（このオプションは、アカウントで匿名コメントの記入を設定している方にのみ利用可能です）</i>';
