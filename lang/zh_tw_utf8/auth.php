<?PHP // $Id$ 
      // auth.php - created with Moodle 1.3 development (2004041800)


$string['auth_dbdescription'] = '該方法使用一個外部資料庫來檢驗使用者名和密碼是否有效。如果是一個新帳號，該帳號其他欄位的資訊將一起複製到本系統中。';
$string['auth_dbextrafields'] = '這些欄位是可選的。你在此指定的<B>外部資料庫欄位</B>將預先填入本系統的使用者資料庫中。<P>如果你留空不填，將使用系統預設值。<P>無論以上哪種情況，使用者在登錄後都可以改寫這些欄位。';
$string['auth_dbfieldpass'] = '包含密碼的欄位名稱';
$string['auth_dbfielduser'] = '包含帳號名稱的欄位名稱';
$string['auth_dbhost'] = '資料庫所在主機';
$string['auth_dbname'] = '資料庫名稱';
$string['auth_dbpass'] = '密碼與帳號名稱相符合';
$string['auth_dbpasstype'] = '指定密碼欄位所用的格式。MD5編碼可用於與其他通用WEB應用如PostNuke相聯接';
$string['auth_dbtable'] = '資料庫中資料表名稱';
$string['auth_dbtitle'] = '使用外界資料庫';
$string['auth_dbtype'] = '資料庫格式 (進一步說明請參見<A 資料庫類型（詳情請看<A HREF=../lib/adodb/readme.htm#drivers>ADOdb幫助文檔</A>）';
$string['auth_dbuser'] = '可讀取資料庫的使用者名稱';
$string['auth_emaildescription'] = '以電子郵件確認帳號是系統預設認證方式.  當使用者申請帳號時, 選擇帳號名稱與密碼, 系統將以電子郵件送出確認訊息. 申請者須閱讀電子郵件後按下內容內確認連結後啟動帳號使用權. 以上動作只要一次即可,之後可要帳號名稱與密碼相符便可登入.';
$string['auth_emailtitle'] = '電子郵件確認';
$string['auth_imapdescription'] = '本方式使用 IMAP 伺服器 檢查帳號名稱與密碼是否相符.';
$string['auth_imaphost'] = 'IMAP 伺服器網址. 請使用 IP 號碼, 而不是名稱資料.';
$string['auth_imapport'] = 'IMAP 伺服器的連接埠. 通常是 143 或 993.';
$string['auth_imaptitle'] = '使用IMAP 伺服器';
$string['auth_imaptype'] = 'IMAP 伺服器型態.  有不同認證方式.';
$string['auth_ldap_bind_dn'] = '如果你想用綁定使用者來搜索使用者，在此指定。就象：‘cn=ldapuser,ou=public,o=org’';
$string['auth_ldap_bind_pw'] = '綁定使用者的密碼。';
$string['auth_ldap_contexts'] = '使用者背景列表。以‘;’分隔。例如：‘ou=users,o=org; ou=others,o=org’';
$string['auth_ldap_create_context'] = '如果你允許根據email資訊創建使用者,指定創建使用者的內容.該值應該有別於別的使用者';
$string['auth_ldap_creators'] = '列出可創建新課程的組.用';
$string['auth_ldap_host_url'] = '以URL形式指定LDAP主機，類似於：‘ldap://ldap.myorg.com/’或‘ldaps://ldap.myorg.com/’or ldaps://ldap.myorg.com/ ';
$string['auth_ldap_memberattribute'] = '指定從屬於某個組的使用者屬性,一般是member';
$string['auth_ldap_search_sub'] = '如果你想從次級上下文中搜索使用者，設值&lt;&gt; 0。';
$string['auth_ldap_update_userinfo'] = '從LDAP向本系統更新使用者資訊（姓名、位址……）要查看映射資訊，請看/auth/ldap/attr_mappings.php';
$string['auth_ldap_user_attribute'] = '用於命名/搜索使用者的屬性。通常為‘cn’。';
$string['auth_ldap_version'] = '你目前LDAP 伺服器的使用版本';
$string['auth_ldapdescription'] = '該方法利用一個外部的LDAP伺服器進行身份驗證。 如果使用者名和密碼是有效的，本系統據此在資料庫中創建一個新使用者。 該模組可以從LDAP中讀取使用者屬性，並把指定的欄位預先填入本系統資料庫。 此後的登錄只需檢驗使用者名和密碼。';
$string['auth_ldapextrafields'] = '這些欄位是可選的。你可以在此指定這些<B>LDAP欄位</B>複製到本系統的資料庫中。 <P>如果你不選，將使用本系統預設值。<P>無論以上何種情況，使用者在登錄之後都可以修改這些欄位。';
$string['auth_ldaptitle'] = '使用一個LDAP伺服器';
$string['auth_manualdescription'] = '該方法不允許使用者以任何方式創建帳號。所有帳號只能由管理員手工創建。';
$string['auth_manualtitle'] = '只允許手工添加帳號';
$string['auth_multiplehosts'] = '多個不同的主機可以被指定(例如host1.com;host2.com;host3.com)';
$string['auth_nntpdescription'] = '該方法使用一個NNTP伺服器來檢驗使用者名和密碼是否有效。';
$string['auth_nntphost'] = 'NNTP伺服器位址。用IP地址，不要用功能變數名稱。';
$string['auth_nntpport'] = '伺服器埠（通常是119）';
$string['auth_nntptitle'] = '使用一個NNTP伺服器';
$string['auth_nonedescription'] = '使用者可以即刻進入本系統並創建一個有效帳號，不需要任何身份驗證，也不需要電子郵件確認。慎用該方法——考慮一下安全性和管理上的問題。';
$string['auth_nonetitle'] = '沒有身份驗證';
$string['auth_pop3description'] = '該方法使用一個POP3伺服器來檢驗使用者名和密碼。';
$string['auth_pop3host'] = 'POP3 伺服器網址. 輸入IP 而不是名稱.';
$string['auth_pop3port'] = '伺服器埠（通常是110）';
$string['auth_pop3title'] = '使用 POP3 伺服器';
$string['auth_pop3type'] = '伺服器類型。如果你的POP3伺服器使用安全驗證，請選擇pop3cert。';
$string['auth_user_create'] = '啟動使用者創建功能';
$string['auth_user_creation'] = '新的(匿名)使用者可以在外部身份驗證源中創建新使用者帳號，並通過email確認。如果你啟動了這個功能，請記住同時也為使用者創建功能設置一下模組特定選項';
$string['auth_usernameexists'] = '選中的使用者名已經存在。請選擇一個新的。';
$string['authenticationoptions'] = '身份驗證選項';
$string['authinstructions'] = '你在這裏可以給你的使用者提供使用說明，讓他們知道該用哪個使用者名和密碼。你在這裏輸入的文本將出現在登錄頁面。如果留空不填，登錄頁面將不會出現使用說明。';
$string['changepassword'] = '更改密碼的網址';
$string['changepasswordhelp'] = '請輸入當使用者忘記密碼時可以設定新密碼的網址. 此網址將提供於登入畫面中, 若是並未提供網址則不會出現此按鈕.';
$string['chooseauthmethod'] = '選擇一個身份驗證方法：';
$string['guestloginbutton'] = '訪客 登入按鈕';
$string['instructions'] = '使用說明';
$string['md5'] = 'MD5加密';
$string['plaintext'] = '純文字內容';
$string['showguestlogin'] = '你可以選擇登入網頁中是否顯示訪客登入按鈕.';

?>
