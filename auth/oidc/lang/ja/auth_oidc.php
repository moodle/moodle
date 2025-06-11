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
 * @package auth_oidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

// phpcs:disable moodle.Files.LangFilesOrdering.IncorrectOrder
// phpcs:disable moodle.Files.LangFilesOrdering.UnexpectedComment

$string['pluginname'] = 'OpenID Connect';
$string['auth_oidcdescription'] = 'OpenID Connectプラグインは、設定可能なアイデンティティプロバイダを使用してシングルサインオン機能を提供します。';
$string['cfg_authendpoint_key'] = '認証エンドポイント';
$string['cfg_authendpoint_desc'] = 'アイデンティティプロバイダが使用する認証エンドポイントのURIです。';
$string['cfg_autoappend_key'] = '自動付加';
$string['cfg_autoappend_desc'] = 'ユーザがユーザ名/パスワードのログインフローを使用してログインした場合、自動的にこの文字列を付加します。これは、アイデンティティプロバイダが共通のドメインを求めているのものの、ユーザにログイン時に入力してほしくない場合に便利です。たとえば、完全なOpenID Connectユーザが"james@example.com"である場合、ここに"@example.com"と入力しておくと、ユーザはユーザ名として"james"を入力するだけで済みます。<br /><b>注 :</b> ユーザ名が競合する場合、つまり同じ名前のMoodleユーザが存在する場合、認証プラグインの優先順位を使用してユーザが決定されます。';
$string['cfg_clientid_key'] = 'クライアントID';
$string['cfg_clientid_desc'] = 'アイデンティティプロバイダに登録したクライアントID。';
$string['cfg_clientsecret_key'] = 'クライアント秘密鍵';
$string['cfg_clientsecret_desc'] = 'アイデンティティプロバイダに登録したクライアント秘密鍵です。プロバイダによっては、キーと呼ばれることもあります。';
$string['cfg_err_invalidauthendpoint'] = '無効な認証エンドポイント';
$string['cfg_err_invalidtokenendpoint'] = '無効なトークンエンドポイント';
$string['cfg_err_invalidclientid'] = '無効なクライアントID';
$string['cfg_err_invalidclientsecret'] = '無効なクライアント秘密鍵';
$string['cfg_icon_key'] = 'アイコン';
$string['cfg_icon_desc'] = 'ログインページでプロバイダ名の横に表示されるアイコンです。';
$string['cfg_iconalt_o365'] = 'Microsoft 365アイコン';
$string['cfg_iconalt_locked'] = 'ロック済みアイコン';
$string['cfg_iconalt_lock'] = 'ロックアイコン';
$string['cfg_iconalt_go'] = '緑の丸';
$string['cfg_iconalt_stop'] = '赤の丸';
$string['cfg_iconalt_user'] = 'ユーザアイコン';
$string['cfg_iconalt_user2'] = '別のユーザアイコン';
$string['cfg_iconalt_key'] = 'キーアイコン';
$string['cfg_iconalt_group'] = 'グループアイコン';
$string['cfg_iconalt_group2'] = '別のグループアイコン';
$string['cfg_iconalt_mnet'] = 'MNETアイコン';
$string['cfg_iconalt_userlock'] = 'ロック付きユーザアイコン';
$string['cfg_iconalt_plus'] = 'プラスアイコン';
$string['cfg_iconalt_check'] = 'チェックマークアイコン';
$string['cfg_iconalt_rightarrow'] = '右向き矢印アイコン';
$string['cfg_customicon_key'] = 'カスタムアイコン';
$string['cfg_customicon_desc'] = '独自のアイコンを使用する場合は、ここにアップロードします。これにより、上記で選択していたアイコンは上書きされます。<br /><br /><b>カスタムアイコンを使用する際の注意 : </b><ul><li>この画像はログインページではサイズ変更<b>されません</b>。このため、35x35ピクセル以下の画像をアップロードすることをお勧めします。</li><li>カスタムアイコンをアップロードした後で標準のアイコンに戻す場合は、上のボックスのカスタムアイコンをクリックします。次に[削除]、[OK]をクリックし、最後にフォームの下部にある[変更の保存]をクリックします。これにより、選択された標準アイコンがMoodleログインページに表示されます。</li></ul>';
$string['cfg_debugmode_key'] = 'デバッグメッセージを記録する';
$string['cfg_debugmode_desc'] = '有効にすると、Moodleログに情報が記録されます。これは問題を特定するのに役立つことがあります。';
$string['cfg_loginflow_key'] = 'ログインフロー';
$string['cfg_loginflow_authcode'] = '認証リクエスト';
$string['cfg_loginflow_authcode_desc'] = 'このフローでは、ユーザはMoodleログインページでアイデンティティプロバイダの名前 (上記の「プロバイダ名」を参照) をクリックします。ユーザはプロバイダにリダイレクトされ、そこでログインします。ログインが成功したら、ユーザはMoodleにリダイレクトされ、透過的にMoodleログインが行われます。これは最も標準化され、最もセキュアなユーザのログイン方法です。';
$string['cfg_loginflow_rocreds'] = 'ユーザ名/パスワード認証';
$string['cfg_loginflow_rocreds_desc'] = 'このフローでは、手動によるログインと同様、ユーザはMoodleのログインフォームにユーザ名とパスワードを入力します。これらの認証情報はバックグラウンドでアイデンティティプロバイダに渡され、認証を取得します。ユーザはアイデンティティプロバイダと直接やり取りしないので、このフローはユーザに最も透過的です。すべてのアイデンティティプロバイダがこのフローをサポートしているわけではない点にご注意ください。';
$string['cfg_oidcresource_key'] = 'リソース';
$string['cfg_oidcresource_desc'] = 'リクエストを送る、OpenID Connectのリソース。';
$string['cfg_oidcscope_key'] = '範囲';
$string['cfg_oidcscope_desc'] = '使用するOIDCスコープ。';
$string['cfg_opname_key'] = 'プロバイダ名';
$string['cfg_opname_desc'] = 'これはユーザがログインするために使用する必要がある認証情報の種類を識別するラベルで、エンドユーザに表示されます。このラベルはプロバイダを識別するために、このプラグインのユーザに表示されるすべての部分で使用されます。';
$string['cfg_redirecturi_key'] = 'リダイレクトURI';
$string['cfg_redirecturi_desc'] = 'これは"リダイレクトURI"として登録するURIです。OpenID Connectアイデンティティプロバイダは、クライアントとしてMoodleを登録するときにこれを要求します。<br /><b>注意:</b> これは、ここに表示されているとおり「正確」にOpenID Connectプロバイダに入力する必要があります。違いがあると、OpenID Connectを使用してログインできません。';
$string['cfg_tokenendpoint_key'] = 'トークエンドポイント';
$string['cfg_tokenendpoint_desc'] = 'アイデンティティプロバイダが使用する、トークンエンドポイントのURIです。';
$string['event_debug'] = 'デバッグメッセージ';
$string['errorauthdisconnectemptypassword'] = 'パスワードは空白にできません。';
$string['errorauthdisconnectemptyusername'] = 'ユーザ名は空白にできません。';
$string['errorauthdisconnectusernameexists'] = 'このユーザ名は既に使用されています。別のユーザ名を選択してください。';
$string['errorauthdisconnectnewmethod'] = 'ログイン方法を使用する';
$string['errorauthdisconnectinvalidmethod'] = '無効なログイン方法を受信しました。';
$string['errorauthdisconnectifmanual'] = '手動によるログインを利用する場合は、以下に認証情報を入力します。';
$string['errorauthinvalididtoken'] = 'Invalid id_tokenを受信しました。';
$string['errorauthloginfailednouser'] = '無効なログイン : Moodleでユーザが見つかりませんでした';
$string['errorauthnoauthcode'] = '認証コードを受信していません。';
$string['errorauthnocreds'] = 'OpenID Connectクライアント認証情報を設定してください。';
$string['errorauthnoendpoints'] = 'OpenID Connectサーバエンドポイントを設定してください。';
$string['errorauthnohttpclient'] = 'HTTPクライアントを設定してください。';
$string['errorauthnoidtoken'] = 'OpenID接続のid_tokenを受信していません。';
$string['errorauthunknownstate'] = '不明な状態です。';
$string['errorauthuseralreadyconnected'] = '既に別のOpenID Connectユーザに接続しています。';
$string['errorauthuserconnectedtodifferent'] = '認証したOpenID Connectユーザは既にMoodleユーザに接続されています。';
$string['errorbadloginflow'] = '無効なログインフローが指定されました。注 : インストールまたはアップグレードを最近行った場合は、Moodleキャッシュをクリアしてください。';
$string['errorjwtbadpayload'] = 'JWTペイロードを読み取れませんでした。';
$string['errorjwtcouldnotreadheader'] = 'JWTヘッダーを読み取れませんでした';
$string['errorjwtempty'] = '空のJWT、または文字列以外のJWTを受信しました。';
$string['errorjwtinvalidheader'] = '無効なJWTヘッダー';
$string['errorjwtmalformed'] = '無効な形式のJWTを受信しました。';
$string['errorjwtunsupportedalg'] = 'JWS AlgまたはJWEがサポートされていません';
$string['erroroidcnotenabled'] = 'OpenID Connect認証プラグインが有効になっていません。';
$string['errornodisconnectionauthmethod'] = 'フォールバックする有効な認証プラグインがないため、接続解除できません (ユーザの以前のログイン方法または手動ログイン方法)。';
$string['erroroidcclientinvalidendpoint'] = '無効なエンドポイントURIを受信しました。';
$string['erroroidcclientnocreds'] = 'クライアントの認証情報と秘密鍵を設定してください';
$string['erroroidcclientnoauthendpoint'] = '認証エンドポイントが設定されていません。$this->setendpointsを使用して設定してください。';
$string['erroroidcclientnotokenendpoint'] = 'トークンエンドポイントが設定されていません。$this->setendpointsを使用して設定してください。';
$string['erroroidcclientinsecuretokenendpoint'] = 'トークンエンドポイントはこのためにSSL/TLSを使用している必要があります。';
$string['errorucpinvalidaction'] = '無効なアクションを受信しました。';
$string['erroroidccall'] = 'OpenID接続のエラーが発生しました。詳細については、ログを確認してください。';
$string['erroroidccall_message'] = 'OpenID接続のエラー : {$a}';
$string['eventuserauthed'] = 'ユーザをOpenID Coonectで認証しました';
$string['eventusercreated'] = 'ユーザをOpenID Connectで作成しました';
$string['eventuserconnected'] = 'ユーザをOpenID Connectに接続しました';
$string['eventuserloggedin'] = 'ユーザはOpenID Connectにログインしました';
$string['eventuserdisconnected'] = 'ユーザはOpenID Connectから接続解除されました';
$string['oidc:manageconnection'] = 'OpenID Connect接続を管理する';
$string['ucp_general_intro'] = 'ここでは、{$a} への接続を管理できます。有効にした場合、個別のユーザ名とパスワードを使用する代わりに、{$a} アカウントを使用してMoodleにログインできます。接続後は、Moodleのユーザ名とパスワードを覚えておく必要がなくなります。すべてのログインは {$a} が処理します。';
$string['ucp_login_start'] = '{$a} を使用してMoodleへのログインを開始する';
$string['ucp_login_start_desc'] = 'アカウントが {$a} を使用してMoodleにログインするよう切り替わります。有効にした場合、{$a} の認証情報を使用してログインするようになります。現在のMoodleユーザ名とパスワードは機能しなくなります。いつでもアカウントの接続を解除し、通常のログイン方法に戻ることができます。';
$string['ucp_login_stop'] = '{$a} を使用したMoodleへのログインを停止する';
$string['ucp_login_stop_desc'] = '現在 {$a} を使用してMoodleにログインしています。[{$a} ログインの停止]をクリックすると、 Moodleアカウントが {$a} から接続解除されます。{$a} アカウントを使用してMoodleにログインできなくなります。 ユーザ名とパスワードを作成するように求められます。その後は、Moodleに直接ログインできるようになります。';
$string['ucp_login_status'] = '{$a} ログインは :';
$string['ucp_status_enabled'] = '有効';
$string['ucp_status_disabled'] = '無効';
$string['ucp_disconnect_title'] = '{$a} 接続解除';
$string['ucp_disconnect_details'] = 'Moodleアカウントを {$a} から接続解除します。Moodleにログインするには、ユーザ名とパスワードを作成する必要があります。';
$string['ucp_title'] = '{$a} 管理';

// phpcs:enable moodle.Files.LangFilesOrdering.IncorrectOrder
// phpcs:enable moodle.Files.LangFilesOrdering.UnexpectedComment