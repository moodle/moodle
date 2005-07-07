<?PHP // $Id$ 
      // install.php - created with Moodle 1.5 + (2005060201)


$string['admindirerror'] = 'Belirtilen yönetici dizini hatalý';
$string['admindirname'] = 'Yönetici Dizini';
$string['admindirsetting'] = 'Bir kaç web hosting, kontrol paneline ulaþmak için /admin olarak belirtilmiþ bir URL kullanýyor. Maalesef, bu Moodle yönetici sayfalarýyla bir karýþýklýk ortaya çýkarmaktadýr. Yönetici dizininin ismini kurulum sýrasýnda deðiþtirerek bu sorunu ortadan kaldýrabilirsiniz. Örnek: <br /><br />><b>moodleadmin</b><br /> <br />Bu Moodle içinde yönetici linklerini düzeltecektir.';
$string['caution'] = 'Dikkat';
$string['chooselanguage'] = 'Bir dil seçin';
$string['compatibilitysettings'] = 'PHP ayarlarýnýz kontrol ediliyor...';
$string['configfilenotwritten'] = 'Kurulum programý, Moodle dizini yazýlabilir olmadýðýndan dolayý seçtiðiniz ayarlarý içeren bir config.php dosyasý oluþturamýyor.  Aþaðýdaki kodu kopyalayýp bu kodu config.php dosyasý içine yapýþtýrýp Moodle kök dizinine oluþturduðunuz dosyayý yükleyebilirsiniz.';
$string['configfilewritten'] = 'config.php dosyasý baþarýyla oluþturuldu';
$string['configurationcomplete'] = 'Yapýlandýrma tamamlandý';
$string['database'] = 'Veritabaný';
$string['databasecreationsettings'] = 'Þimdi, Moodle verilerinin saklanacaðý veritabanýný
oluþturmanýz gerekiyor. Bu veritabaný Moodle4Windows kurulumu tarafýndan aþaðýdaki ayarlara göre otomatik olarak oluþturulacak.<br />
<br /> <br />
<b>Tipi:</b> kurulum tarafýndan mysql olarak sabitlendi<br />
<b>Sunucu:</b> kurulum tarafýndan localhost olarak sabitlendi<br />
<b>Adý:</b> veritabaný adý, ör: moodle<br />
<b>Kullanýcý:</b> kurulum tarafýndan root olarak sabitlendi<br />
<b>Þifre:</b> kullanýcý þifresi<br />
<b>Tablo öneki:</b> tüm tablo isimleri için isteðe baðlý önek';
$string['databasesettings'] = 'Þimdi, Moodle verilerinin saklanacaðý veritabanýný
oluþturmanýz gerekiyor. Bu veritabaný önceden oluþturulmalý
ve bu veritabanýna eriþmek için kullanýcý adý - þifre ayarlanmalý.<br />
<br /><br />
<b>Tipi:</b> mysql veya postgres7<br />
<b>Sunucu:</b> ör: localhost veya db.iss.com<br />
<b>Adý:</b> veritabaný adý, ör: moodle<br />
<b>Kullanýcý:</b> veritabaný kullanýcýsý<br />
<b>Þifre:</b> kullanýcý þifresi<br />
<b>Tablo öneki:</b> tüm tablo isimleri için isteðe baðlý önek';
$string['dataroot'] = 'Veri Dizini';
$string['datarooterror'] = 'Belirtilen \'Veri Dizini\' bulunamadý veya oluþturulamadý. Dizin yolunu düzenleyin veya bu dizini kendiniz oluþturun.';
$string['dbconnectionerror'] = 'Belirtiðiniz veritabanýna baðlantý kuramadýk. Lütfen veritabaný ayarlarýný kontrol edin.';
$string['dbcreationerror'] = 'Veritabaný oluþturma hatasý. Belirtilen ayarlardan saðlanan isimle bir veritabaný oluþturulamadý.';
$string['dbhost'] = 'Veritabaný Sunucusu';
$string['dbpass'] = 'Þifre';
$string['dbprefix'] = 'Tablo öneki';
$string['dbtype'] = 'Tipi';
$string['directorysettings'] = '<p>Lütfen, Bu Moodle kurulumu için yollarý onaylayýn.</p>

<p><b>Web Adresi:</b>
Moodle\'a eriþilecek olan tam web adresini belirtin.
Web siteniz bir çok URL\'den eriþilebiliyorsa, öðrencilerinizin
en sýk kullanacaðý bir tanesini seçin.
Sonuna / (slash) ekleMEyin.</p>

<p><b>Moodle Dizini:</b>
Bu kurulama ait tam fiziksel klasör yolunu belirtin.
BÜYÜK/küçük harflerin doðru olduðundan emin olun.</p>

<p><b>Veri Dizini:</b>
Siteye yüklenen dosyalarýn nereye kaydedileceðini belirtin.
Bu dizin sunucu kullanýcýsý tarafýndan okunabilir ve
YAZILABÝLÝR olmalý. (genellikle \'nobody\',\'apache\',\'www\' olur)
Ancak, bu dizine direkt olarak webden eriþim olMAMAlý.</p>';
$string['dirroot'] = 'Moodle Dizini';
$string['dirrooterror'] = '\'Moodle Dizini\' ayarlarý hatalý görünüyor - Burada bir Moodle kurulumu bulunamadý. Aþaðýdaki deðer sýfýrlandý.';
$string['download'] = 'Ýndir';
$string['fail'] = 'Hata';
$string['fileuploads'] = 'Dosya Göndermeleri';
$string['fileuploadserror'] = 'Bu açýk olmalý';
$string['fileuploadshelp'] = '<p>Bu sunucuda, Dosya yüklemesi etkinleþtirilmemiþ görünüyor.</p>

<p>Moodle hala kurulabilir, fakat bu özellik olmadan, yeni kullanýcý
resimleri ve kurslara dosya gönderilemez.</p>

<p>Dosya yüklemesini etkinleþtirmeniz için (veya sistem yöneticiniz)
sisteminizin php.ini dosyasýnýndaki <b>file_uploads</b> ayarý \'1\'
olarak deðiþtirilmeli.</p>';
$string['gdversion'] = 'GD sürümü';
$string['gdversionerror'] = 'GD kütüphanesi resimleri oluþturma ve iþleme özelliði sunmalý';
$string['gdversionhelp'] = '<p>Sunucunuzda GD kütüphanesi kurulu görülmüyor.</p>

<p>Moodle\'ýn resimleri iþlemesi ve yeni resim oluþturmasý için
GD kütüphanesi PHP kurulumu sýrasýnda gereklidir. Örneðin,
Moodle bu kütüphane sayesinde kullanýcý resimlerinin týrnak
resimlerini çýkartýr ve loglarla ilgili grafikler oluþturur.
Moodle GD olmadan da çalýþýr, ancak yukarýda bahsedilen
özelliklerden yararlanamazsýnýz.</p>

<p>Unix altýnda PHP\'ye GD desteðini saðlamak için, PHP\'yi --with-gd parametresiyle derleyin.</p>

<p>Windows altýnda php.ini dosyasýný düzenler ve libgd.dll\'yi referans eden satýrdaki yorumlarý kaldýrýrsýnýz.</p>';
$string['installation'] = 'Kurulum';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'Bu kapalý olmalý';
$string['magicquotesruntimehelp'] = '<p>Magic quotes runtime ayarý, Moodle\'ýn iþlevsel çalýþmasý için kapalý olmalý.</p>

<p>Normalde de zaten bu varsayýlan olarak kapalýdýr ...  php.ini dosyasýndaki <b>magic_quotes_runtime</b> ayarýna bakýn.</p>

<p>php.ini dosyasýna eriþim hakkýnýz yoksa, Moodle klasöründe yer alan .htaccess isimli dosyada þu ayarý yapýn:

<blockquote>php_value magic_quotes_runtime Off</blockquote>
</p> ';
$string['memorylimit'] = 'Bellek Limiti';
$string['memorylimiterror'] = 'PHP bellek limiti ayarý çok düþük... Daha sonra bu ayardan dolayý bazý sorunlar oluþabilir.';
$string['memorylimithelp'] = '<p>Sunucunuz için PHP bellek limiti þu anda $a olarak ayarlanmýþ durumda.</p>

<p>Özellikle bir çok modülü etkinleþtirilmiþ ve/veya çok fazla kullanýcýnýz
varsa bu durum daha sonra bazý bellek sorunlarýna sebep olabilir.</p>

<p>Mümkünse size PHP\'e daha yüksek limitli bir bellek ayarý yapmanýzý,
örneðin, 16M, öneriyoruz. Ýþte bunu yapabilmeniz için size bir kaç yol:</p>

<ol>
<li>Bunu yapmaya yetkiliyseniz, PHP\'yi <i>--enable-memory-limit</i> ile yeniden derleyin.
Bu, Moodle\'nýn kendi kendine bellek limitini ayarlasýna izin verecek.</li>

<li>php.ini dosyasýna eriþim hakkýnýz varsa, <b>memory_limit</b> ayarýný 16M gibi
bir ayarla deðiþtirin. Eriþim hakkýnýz yoksa, bunu sistem yöneticinizden sizin
için yapmasýný isteyin.</li>

<li>Bazý PHP sunucularýnda Moodle klasörü içinde þu ayarý içeren bir
.htaccess dosyasý oluþturabilirsiniz:
<p><blockquote>php_value memory_limit 16M</blockquote></p>
<p>Ancak, bazý sunucularda bu durum çalýþan <b>bütün</b> PHP sayfalarýný engelleyecektir.
(sayfanýz altýna baktýðýnýzda bazý hatalar göreceksiniz)
Böyle bir durumda .htaccess dosyasýný silmeniz gerekiyor.</p></li>
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'PHP, büyük ihtimal MySQL uzantýsýyla birlikte yapýlandýrýlmamýþ. Bu yüzden MySQL ile baðlantý kurulamýyor. php.ini dosyasýný kontrol edin veya PHP\'yi tekrar derleyin.';
$string['pass'] = 'Geçti';
$string['phpversion'] = 'PHP sürümü';
$string['phpversionerror'] = 'PHP sürümü en az 4.1.0 olmalý';
$string['phpversionhelp'] = '<p>Moodle, PHP sürümünün en az 4.1.0 olmasýný gerektirir.</p>
<p>Þu anda bu sürümü çalýþýyor: $a</p>
<p>PHP\'yi güncellemeli veya PHP\'nin yeni sürümünü kullananan bir hostinge taþýnmalýsýnýz!</p>';
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = 'Moodle, safe mode\'ýn açýk olmasý durumunda bazý sorunlar çýkartabilir';
$string['safemodehelp'] = '<p>Moodle, safe mode\'un açýk olmasý durumunda bazý sorunlar çýkartabilir.
   Moodle tarafýndan en azýndan bazý dosyalarýn oluþturulmasý gerekiyor,
   ama bu mod yeni dosyalarýn oluþturulmasýna izin vermiyor.</p>

<p>Safe mode sadece paranoyak web hostinglerince kullanýlmaktadýr. Bu durumda
Moodle için baþka bir web hosting firmasý bulmanýz gerekiyor.</p>

<p>Ýsterseniz devam edebilirsiniz, ama daha sonra bir çok sorunla karþýlaþýrsýnýz.</p>   ';
$string['sessionautostart'] = 'Otomatik Oturum Baþlama';
$string['sessionautostarterror'] = 'Bu kapalý olmalý';
$string['sessionautostarthelp'] = '<p>Moodle, oturum desteði gerektirir ve bu olmadan iþlevsel çalýþamaz.</p>

<p>Oturum desteði php.ini dosyasýndan ayarlanabilir ... session.auto_start parametresine bakýn.</p>';
$string['wwwroot'] = 'Web adresi';
$string['wwwrooterror'] = 'Web adresi doðru ayarlanmýþ görünmüyor. Moodle kurulumu belirtilen yerde görünmüyor.';

?>
