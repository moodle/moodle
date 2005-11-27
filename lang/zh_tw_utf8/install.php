<?PHP // $Id$ 
      // install.php - created with Moodle 1.5.3+ (2005060230)


$string['admindirerror'] = '系統管理目錄指定不正確';
$string['admindirname'] = '管理者目錄';
$string['admindirsetting'] = '有極少數的伺服器使用特定的URL作為系統管理的頁面控制,不幸地是與MOODLE預設的管理頁面衝突.您可以重新命名ADMIN目錄以修正這個問題.例如: <br /> <br /><b>moodleadmin</b><br /> <br />
如此可能可以修復MOODLE中的系統管理連結';
$string['caution'] = '注意';
$string['chooselanguage'] = '選擇語系';
$string['compatibilitysettings'] = '檢查PHP設定';
$string['configfilenotwritten'] = '本安裝程式無法自動產生conifg.php檔案,此檔案包含您的個人設定,有可能是您的moodle資料夾無寫入權限,您可以手動複製以下的程式碼,存成conifg.php,然後把它放在MOODLE的根目錄中';
$string['configfilewritten'] = 'CONFIG.PHP成功建立';
$string['configurationcomplete'] = '設定完成';
$string['database'] = '資料庫';
$string['databasecreationsettings'] = '現在,您必須設定儲存MOODLE資料的資料庫.此資料庫可以經由以下的設定,由Moodle4Windows安裝程式建立.<br />
<br /> <br />
<b>型式:</b>預設為\"mysql\"<br />
<b>伺服器:</b>預設為\"localhost\"<br />
<b>資料庫名稱:</b>資料庫名稱, 例:moodle<br />
<b>使用者:</b> 預設為\"root\"<br />
<b>密碼:</b>您資料庫的操作密碼<br />
<b>資料表前置字元:</b>資料表前置名稱的預設選項';
$string['databasesettings'] = '現在,您必須設定儲存MOODLE資料的資料庫.此資料庫必須事先建立,而且必須有一組可以存取的帳號和密碼.<br />
<br /> <br />
<b>型式:</b>mysql或postgres7<br />
<b>伺服器:</b>localhost或URL<br />
<b>資料庫名稱:</b>資料庫名稱, 例:moodle<br />
<b>使用者:</b>您的資料庫操作帳號<br />
<b>密碼:</b>您資料庫的操作密碼<br />
<b>資料表前置字元:</b>資料表前置名稱的預設選項';
$string['dataroot'] = '資料目錄';
$string['datarooterror'] = '您指定的\"資料目錄\"找不到或無法建立,請修正路徑或手動建立該目錄';
$string['dbconnectionerror'] = '無法連到您指定的資料庫,請查檢您的資料庫設定';
$string['dbcreationerror'] = '建立資料庫錯誤,無法以您給的資料庫名稱建立資料表';
$string['dbhost'] = '主機位址';
$string['dbpass'] = '密碼';
$string['dbprefix'] = '資料表前置字元';
$string['dbtype'] = '型式';
$string['directorysettings'] = '<p>請確認MOODLE安裝的位置</p>
><b>網路存取路徑:</b>
指定存取MOODLE的完整路徑,如果您的網站可經由多重的URL存取,請選擇最自然,學生會使用的網址,請勿包含最後的斜線.</p>

<p><b>Moodle 目錄:</b>
指定安裝MOODLE的目錄,請確認大小寫是正確的.</p>

<p><b>資料目錄:</b>
您必須指定一個讓MOODLE存放上傳檔案的目錄,此目錄必須能讓網頁伺服器存取,並執行寫入動作(通常是\'nobody\'或 \'apache\'),但不可以讓使用者直接透過網頁存取.</p>';
$string['dirroot'] = 'Moodle目錄';
$string['dirrooterror'] = '\"Moodle目錄\"的設定似乎有誤-無法找到MOODLE的安裝,下列的值已重設';
$string['download'] = '下載';
$string['fail'] = '失敗';
$string['fileuploads'] = '檔案上傳';
$string['fileuploadserror'] = '這應該開啟';
$string['fileuploadshelp'] = '<p>伺服器似乎取消檔案上傳功能.</p>
<p>MOODLE仍然可以繼續安裝,但無此功能,您無法上傳檔案或圖片</P>
<p>要啟動檔案上傳功能,您(或系統管理者)必須編輯main.php這個檔案,把 <b>file_uploads</b>設為 \'1\'.</p>';
$string['gdversion'] = 'GD版本';
$string['gdversionerror'] = 'GD程式庫應該存在,以便處理並建立影像';
$string['gdversionhelp'] = '<p>您的伺服器似乎未安裝GD</P>

<p>GD是PHP的一套繪圖程式庫,可允許MOODLE處理圖形(像是使用者的個人圖像)或自動產生新圖形(像是產生統計圖).沒有GD,MOODLE仍可正常運作,但上述的功能就會無法使用</P>

<P>要在UNIX中,把GD功能加到PHP中,您必須用--with-gd 參數重新編譯PHP</P>
<P>在WINDOWS中,您可以編輯PHP.INI這個檔案,將libgd.dll前的#號取消</p>';
$string['globalsquotes'] = '不安全的全域變數處理';
$string['globalsquoteserror'] = '修正您的PHP設定： 停用register_globals且/或啟用magic_quotes_gpc';
$string['globalsquoteshelp'] = '<p>不建議同時停用 Magic Quotes GPC 與啟用 Register Globals 。</p>

<p>建議的 php.ini 設定是 <b>magic_quotes_gpc = On</b> 且 <b>register_globals = Off</b> </p>

<p>如果您無法修改 php.ini 的設定，您也許可以嘗試在  Moodle 安裝目錄下放置一個名為 .htaccess 的檔案，裡面輸入：
<blockquote>php_value magic_quotes_gpc On</blockquote>
<blockquote>php_value register_globals Off</blockquote>
</p> ';
$string['installation'] = '系統安裝';
$string['magicquotesruntime'] = 'Magic Quotes 動態執行程式庫';
$string['magicquotesruntimeerror'] = '這應該關閉';
$string['magicquotesruntimehelp'] = '<p>要正確執行moodle,Magic Quotes 動態執行程式庫應該關閉</p>
<p>通常預設值是關閉的,詳見在您php.ini檔案中<b>magic_quotes_runtime</b>的設定</p>
<p>如果您無法存取php.ini,您可能要把 <blockquote>php_value magic_quotes_runtime Off</blockquote>
寫入到.htaccess這個檔案,且放在moodle根目錄中</p>';
$string['memorylimit'] = '記憶體限制';
$string['memorylimiterror'] = 'PHP 執行之記憶體設定過低,您可能稍後會遇到一些問題';
$string['memorylimithelp'] = '<p>您伺服器的記憶體限制目前設為$a.</p>

<p>這可能會影響往後moodle運作時記憶體的使用,尤其是您有許多模組和(或)使用者的時候</p>

<p>我們建議您在php中的記憶體儘量設定較高的記憶體使用,如16MB,有幾種方式可以進行:</p>
<ol>
<li>如果您可以重新編譯PHP請使用 <i>--enable-memory-limit</i>.
這可以讓MOODLE 自行設定記憶體上限.</li>
<li>如果您可以存取php.ini,您可以變更 <b>memory_limit</b>
將它設為16M或更高.如果您無法存取,可洽您的系統管理員,幫您調整設定.</li>
<li>在一些PHP 伺服器中您可以在MOODL目錄建立一個 .htaccess 檔案,檔案內容包含:
<p><blockquote>php_value memory_limit 16M</blockquote></p>
<p>但是,在一些伺服器上,這樣的設定會使<b>所有</b> PHP 頁面無法正常動作(瀏覽時會發生錯誤),此時您必須移除 .htaccess 檔案.</p></li>
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'PHP並未正確設定支援MySQL,請確認您的php.ini檔案設定或重新編譯PHP.';
$string['pass'] = '測試通過';
$string['phpversion'] = 'PHP版本';
$string['phpversionerror'] = 'PHP版本至少必須在4.1.0以上';
$string['phpversionhelp'] = '<p>Moodle 需要至少4.1.0.的PHP版本 </p>
<p>您目前執行的是$a 版</p>
<p>您必須更新您的 PHP 或在有更新版本的主機進行安裝!</p>';
$string['safemode'] = '安全模式';
$string['safemodeerror'] = 'moodle 在安全模式啟動時可能會發生錯誤';
$string['safemodehelp'] = '<p>在安全模式下,MOODLE會產生許多問題,至少可能無法允許您建立新檔案</P>

<p>有一些hosting業者可能會啟動安全模式,此時您可能要換一家提供網頁空間的公司,以安裝您的MOODLE</p>

<p>您仍然可以試著裝看看,不過稍後可能會遇到一些問題</p>';
$string['sessionautostart'] = 'Session自動啟動';
$string['sessionautostarterror'] = '這應該關閉';
$string['sessionautostarthelp'] = '<p>Moodle 需要支援session功能,沒有它無法正確動作 .</p>
<p>Sessions 可以在 php.ini 檔案中啟動,請尋找 session.auto_start 參數.</p>';
$string['wwwroot'] = 'www路徑';
$string['wwwrooterror'] = '指定網路位址不存在-這個moodle 系統並不在您指定的地方';

?>
