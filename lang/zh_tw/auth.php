<?PHP // $Id$ 
      // auth.php - created with Moodle 1.0.8.1 (2003011200)


$string['auth_dbdescription'] = "從外界資料庫檢查 帳號名稱與密碼是否相符.  若是一個新帳號資料庫中其他資料也會被拷貝到本系統.";
$string['auth_dbextrafields'] = "選填資料.  你可以選擇將使用者帳號部分資料從<B>外界資料庫</B> 中擷取填為預設值. <P>若是不填, 將以本系統預設值為準.<P>無論如何使用者皆可以登入後修改.";
$string['auth_dbfieldpass'] = "包含密碼的欄位名稱";
$string['auth_dbfielduser'] = "包含帳號名稱的欄位名稱";
$string['auth_dbhost'] = "資料庫所在電腦.";
$string['auth_dbname'] = "資料庫名稱";
$string['auth_dbpass'] = "密碼與帳號名稱相符合";
$string['auth_dbpasstype'] = "Specify the format that the password field is using. MD5 encryption is useful for connecting to other common web applications like PostNuke";
$string['auth_dbtable'] = "資料庫中資料表名稱";
$string['auth_dbtitle'] = "使用外界資料庫";
$string['auth_dbtype'] = "資料庫格式 (進一步說明請參見<A HREF=../lib/adodb/readme.htm#drivers>ADOdb 說明文件</A>)";
$string['auth_dbuser'] = "可讀取資料庫的使用者名稱";
$string['auth_emaildescription'] = "以電子郵件確認帳號是系統預設認證方式.  當使用者申請帳號時, 選擇帳號名稱與密碼, 系統將以電子郵件送出確認訊息. 申請者須閱讀電子郵件後按下內容內確認連結後啟動帳號使用權. 以上動作只要一次即可,之後可要帳號名稱與密碼相符便可登入.";
$string['auth_emailtitle'] = "電子郵件確認";
$string['auth_imapdescription'] = "本方式使用 IMAP 伺服器 檢查帳號名稱與密碼是否相符.";
$string['auth_imaphost'] = "IMAP 伺服器網址. 請使用 IP 號碼, 而不是名稱資料.";
$string['auth_imapport'] = "IMAP 伺服器的連接埠. 通常是 143 或 993.";
$string['auth_imaptitle'] = "使用IMAP 伺服器";
$string['auth_imaptype'] = "IMAP 伺服器型態.  有不同認證方式.";
$string['auth_ldap_bind_dn'] = "If you want to use bind-user to search users, specify it here. Someting like 'cn=ldapuser,ou=public,o=org'";
$string['auth_ldap_bind_pw'] = "Password for bind-user.";
$string['auth_ldap_contexts'] = "List of contexts where users are located. Separate different contexts with ';'. For example: 'ou=users,o=org; ou=others,o=org'";
$string['auth_ldap_host_url'] = "Specify LDAP host in URL-form like 'ldap://ldap.myorg.com/' or 'ldaps://ldap.myorg.com/' ";
$string['auth_ldap_search_sub'] = "Put value &lt;&gt; 0 if  you like to search users from subcontexts.";
$string['auth_ldap_update_userinfo'] = "Update user information (firstname, lastname, address..) from LDAP to Moodle. Look at /auth/ldap/attr_mappings.php for mapping information";
$string['auth_ldap_user_attribute'] = "The attribute used to name/search users. Usually 'cn'.";
$string['auth_ldapdescription'] = "This method provides authentication against an external LDAP server.
                                  If the given username and password are valid, Moodle creates a new user 
                                  entry in its database. This module can read user attributes from LDAP and prefill 
                                  wanted fields in Moodle.  For following logins only the username and 
                                  password are checked.";
$string['auth_ldapextrafields'] = "These fields are optional.  You can choose to pre-fill some Moodle user fields with information from the <B>LDAP fields</B> that you specify here. <P>If you leave these fields blank, then nothing will be transferred from LDAP and Moodle defaults will be used instead.<P>In either case, the user will be able to edit all of these fields after they log in.";
$string['auth_ldaptitle'] = "Use an LDAP server";
$string['auth_nntpdescription'] = "This method uses an NNTP server to check whether a given username and password is valid.";
$string['auth_nntphost'] = "The NNTP server address. Use the IP number, not DNS name.";
$string['auth_nntpport'] = "Server port (119 is the most common)";
$string['auth_nntptitle'] = "Use an NNTP server";
$string['auth_nonedescription'] = "Users can sign in and create valid accounts immediately, with no authentication against an external server and no confirmation via email.  Be careful using this option - think of the security and administration problems this could cause.";
$string['auth_nonetitle'] = "No authentication";
$string['auth_pop3description'] = "This method uses a POP3 server to check whether a given username and password is valid.";
$string['auth_pop3host'] = "POP3 伺服器網址. 輸入IP 而不是名稱.";
$string['auth_pop3port'] = "Server port (110 is the most common)";
$string['auth_pop3title'] = "使用 POP3 伺服器";
$string['auth_pop3type'] = "Server type. If your server uses certificate security, choose pop3cert.";
$string['authenticationoptions'] = "Authentication options";
$string['authinstructions'] = "Here you can provide instructions for your users, so they know which username and password they should be using.  The text you enter here will appear on the login page.  If you leave this blank then no instructions will be printed.";
$string['changepassword'] = "更改密碼的網址";
$string['changepasswordhelp'] = "請輸入當使用者忘記密碼時可以設定新密碼的網址. 此網址將提供於登入畫面中, 若是並未提供網址則不會出現此按鈕.";
$string['chooseauthmethod'] = "選擇登入認證方式Choose an authentication method: ";
$string['guestloginbutton'] = "訪客 登入按鈕";
$string['instructions'] = "指引";
$string['md5'] = "MD5 encryption";
$string['plaintext'] = "純文字內容";
$string['showguestlogin'] = "你可以選擇登入網頁中是否顯示訪客登入按鈕.";

?>
