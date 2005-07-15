<?PHP // $Id$ 
      // auth.php - created with Moodle 1.6 development (2005060201)


$string['auth_dbdescription'] = '從外界資料庫檢查 帳號名稱與密碼是否相符.  若是一個新帳號資料庫中其他資料也會被拷貝到本系統.';
$string['auth_dbextrafields'] = '選填資料.  你可以選擇將使用者帳號部分資料從<b>外界資料庫</b> 中擷取填為預設值. <br />若是不填, 將以本系統預設值為準.<br />無論如何使用者皆可以登入後修改.';
$string['auth_dbfieldpass'] = '包含密碼的欄位名稱';
$string['auth_dbfielduser'] = '包含帳號名稱的欄位名稱';
$string['auth_dbhost'] = '資料庫所在電腦.';
$string['auth_dbname'] = '資料庫名稱';
$string['auth_dbpass'] = '密碼與帳號名稱相符合';
$string['auth_dbpasstype'] = 'Specify the format that the password field is using. MD5 encryption is useful for connecting to other common web applications like PostNuke';
$string['auth_dbtable'] = '資料庫中資料表名稱';
$string['auth_dbtitle'] = '使用外界資料庫';
$string['auth_dbtype'] = '資料庫格式 (進一步說明請參見<a href=\"../lib/adodb/readme.htm#drivers\">ADOdb 說明文件</a>)';
$string['auth_dbuser'] = '可讀取資料庫的使用者名稱';
$string['auth_emaildescription'] = '以電子郵件確認帳號是系統預設認證方式.  當使用者申請帳號時, 選擇帳號名稱與密碼, 系統將以電子郵件送出確認訊息. 申請者須嬝疚q子郵件後按下內容內確認連結後啟動帳號使用權. 以上動作只要一次即可,之後可要帳號名稱與密碼相符便可登入.';
$string['auth_emailtitle'] = '電子郵件確認';
$string['auth_imapdescription'] = '本方式使用 IMAP 伺服器 檢查帳號名稱與密碼是否相符.';
$string['auth_imaphost'] = 'IMAP 伺服器網址. 請使用 IP 號碼, 而不是名稱資料.';
$string['auth_imapport'] = 'IMAP 伺服器的連接埠. 通常是 143 或 993.';
$string['auth_imaptitle'] = '使用IMAP 伺服器';
$string['auth_imaptype'] = 'IMAP 伺服器型態.  有不同認證方式.';
$string['auth_pop3host'] = 'POP3 伺服器網址. 輸入IP 而不是名稱.';
$string['auth_pop3title'] = '使用 POP3 伺服器';
$string['changepassword'] = '更改密碼的網址';
$string['changepasswordhelp'] = '請輸入當使用者忘記密碼時可以設定新密碼的網址. 此網址將提供於登入畫面中, 若是並未提供網址則不會出現此按鈕.';
$string['chooseauthmethod'] = '選擇登入認證方式Choose an authentication method: ';
$string['guestloginbutton'] = '訪客 登入按鈕';
$string['instructions'] = '指引';
$string['md5'] = 'MD5 encryption';
$string['plaintext'] = '純文字內容';
$string['showguestlogin'] = '你可以選擇登入網頁中是否顯示訪客登入按鈕.';

?>
