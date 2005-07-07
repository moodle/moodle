<?PHP // $Id$ 
      // admin.php - created with Moodle 1.5 + (2005060201)


$string['adminseesallevents'] = 'Yöneticiler bütün olaylarý görür';
$string['adminseesownevents'] = 'Yöneticiler diðer kullanýcýlar gibidir';
$string['blockinstances'] = 'Kullaným';
$string['blockmultiple'] = 'Çoklu';
$string['cachetext'] = 'Yazý önbelleði ömrü';
$string['calendarsettings'] = 'Takvim';
$string['change'] = 'deðiþtir';
$string['configallowunenroll'] = 'Bu seçenek \'Evet\' ise öðrenciler istedikleri zaman kendi kendilerine kurstan kayýtlarýný sildirebilirler. Diðer durumda buna izin verilmez ve sadece yöneticiler ve eðitimciler bu iþi yapmalýdýr.';
$string['configclamactlikevirus'] = 'Dosya VÝRÜS olarak muamele görsün';
$string['configclamdonothing'] = 'Dosya SAÐLAM olarak muamele görsün';
$string['configclamfailureonupload'] = 'Clam\'ý yüklenen dosyalarý taramasý için yapýlandýrdýysanýz, fakat yol yanlýþ belirtilir veya programýn çalýþmasý sýrasýnda bilinmeyen bir sebepten dolayý hata oluþursa nasýl davranýlacak? \'Dosya VÝRÜS olarak muamele görsün\'ü seçerseniz dosya karantina klasörüne taþýnýr ya da silinir. \'Dosya SAÐLAM olarak muamele görsün\'ü seçerseniz dosya normal þekilde yüklenir. Ayný zamanda yöneticilere clam programýnda hata oluþtuðu bildirilir. \'Dosya VÝRÜS olarak muamele görsün\'ü seçer ve bazý sebeplerden dolayý clamýn çalýþmasý hata ile sonuçlanýrsa (genellikle pathtoclam yolu yanlýþ girilirse olur), TÜM dosyalar belirtilen karantina klasörüne taþýnýr ya da silinir. Bu ayarý deðiþtirirken DÝKKATLÝ olun.';
$string['configcountry'] = 'Buradan bir ülke seçerseniz, yeni kullanýcýlar için bu ülke varsayýlan olarak seçili olacaktýr. Ülke seçmeyi zorunlu tutmak istiyorsanýz, bu seçeneði ayarlamayýn.';
$string['configdebug'] = 'Bu seçeneði açýk tutarsanýz PHP\'deki error_reporting metodu daha fazla uyarý mesajý gösterecektir. Bu, geliþtiriciler için kullanýþlýdýr.';
$string['configdeleteunconfirmed'] = 'Bu, email yetkilendirmesi kullanýyorsanýz, kullanýcýnýn ne kadar sürede bu emali onaylamasý gerektiðini belirtir. Bu süreden sonra, onyalanmaýþ eski hesaplar silinecektir.';
$string['configdisplayloginfailures'] = 'Bu, seçilen kullanýcýnýn önceden yapmýþ olduðu giriþ hatalarý hakkýnda ekranda bilgi gösterir.';
$string['configextendedusernamechars'] = 'Öðrencilerin kullanýcý adlarýnda isteði herhangi bir karakteri seçebilmesini istiyorsanýz bu ayarý etkinleþtirin. (Not: Adý ve soyadýný etkilemez, giriþ için kullanýlan kullanýcý adýný etkiler) Bu ayar \'hayýr\' ise sadece ingilizceki alfanümerik karakterler kullanýlabilecektir.';
$string['configgdversion'] = 'Kurulu olan GD sürümünü seçiniz. Varsayýlan olarak seçilen otomatik olarak algýlanmýþtýr. Ne yaptýðýnýzý bilmiyorsanýz burayý deðiþtirmeyiniz.';
$string['configintrotimezones'] = 'Bu sayfa dünya zaman dilimleri (yaz saati uygulamasý dahil) hakkýnda yeni bilgiyi arayacak ve yerel veritabanýný bu bilgi ile güncelleyecek. Bu kontrol þu sýraya göre yapýlacak: $a Bu iþlem genel olarak çok güvenlidir ve normal kurulumlarý bozmaz. Þimdi zaman dilimlerini güncellemek ister misiniz?';
$string['configlang'] = 'Sitenin tamamýnda geçerli olan varsayýlan bir dil seçin. Kullanýcýlar daha sonra istedikleri dili seçebilirler.';
$string['configlanglist'] = 'Kurulumla birlikte gelen dillerin herhangi birinin seçilebilmesi için burayý boþ býrakýn. Ancak dil menüsünü kýsýtlamak istiyorsanýz buraya dil listesini virgülle ayýrarak girin. Örnek: tr,fr,de,en_us';
$string['configlangmenu'] = 'Ana sayfa, giriþ sayfasý vb. yerlerde dil menüsünün görünüp görünmeyeceðini belirtin. Bu, kullanýcýnýn kendi profilinde düzenleyebileceði dil tercihini etkilemeyecektir.';
$string['configlocale'] = 'Sitenin tamamýnda geçerli olan yerelleþtirme kodunu girin. Bu, gün biçimini ve dilini etkileyecektir. Ýþletim sisteminde bu yerelleþtirmenin var olmasý gerekmektedir. Eðer neyi seçeneðinizi bilmiyorsanýz boþ býrakýnýz.
<br /> Örnekler: Linux için: de_DE, en_US, tr_TR; Windows için: turkish, german, spanish';
$string['configmessaging'] = 'Sitede kullanýcýlar arasý mesajlaþma etkinleþtirilsin mi?';
$string['configopentogoogle'] = 'Bu ayar etkinleþtirilirse, Google, siteye konuk kullanýcý olarak giriþ yapabilecektir. Ek olarak, sitenize Google aracýlýðýyla gelen kullanýcýlar da konuk kullanýcý olarak giriþ yapabileceklerdir. Not: Bu, zaten ziyaretçi giriþine açýk olan kurslara eriþimi Google açýsýndan þeffaflaþtýrýr.';
$string['configpathtoclam'] = 'Clam AV\'in yolu. Büyük ihtimal /usr/bin/clamscan veya /usr/bin/clamdscan olmasý gerekiyor. Clam AV\'in çalýþmasý için bunu belirtmeniz gerekir.';
$string['configquarantinedir'] = 'Clam AV\'in virüs bulaþmýþ dosyalarý karantina klasörüne taþýmasýný istiyorsanýz buraya yolu yazýnýz. Bu klasör Web sunucu tarafýndan yazýlabilir olmalý. Burayý boþ býrakýrsanýz veya olmayan bir klasörü girerseniz ya da klasör yazýlabilir deðilse, virüslü dosyalar silinir. Klasörün sonuna slash (/) ekleMEyin.';
$string['configrunclamonupload'] = 'Dosya yüklemelerinde Clam AV çalýþtýrýlsýn mý? Bunun için doðru bir pathtoclam yolu belirtmeniz gerekiyor. (Clam AV, http://www.clamav.net/ sitesinden indirebileceðiniz ücretsiz bir virüs tarama programýdýr.)';
$string['configsectioninterface'] = 'Arayüz';
$string['configsectionmail'] = 'Mail';
$string['configsectionmaintenance'] = 'Bakým';
$string['configsectionmisc'] = 'Çeþitli';
$string['configsectionoperatingsystem'] = 'Ýþletim Sistemi';
$string['configsectionpermissions'] = 'Ýzinler';
$string['configsectionsecurity'] = 'Güvenlik';
$string['configsectionuser'] = 'Kullanýcý';
$string['configsessioncookie'] = 'Bu seçenek Moodle oturumlarý için kullanýlan çerezlerin adýný ayarlar. Bu seçenek isteðe baðlýdýr, ancak ayný anda ayný web sitesi birden çok moodle kopyasý ile çalýþýyorsa bu seçenek oluþan karýþýklýðý ortadan kaldýrýr.';
$string['configsessiontimeout'] = 'Bu siteye giriþ yapan kullanýcýlar uzun süre iþlem yapmazlarsa (sayfalarý gezinmezse) ne kadar süre içinde oturum sona erecek?';
$string['configsmtphosts'] = 'Moodle\'nin email göndermesi için bir veya birden fazla SMTP sunucu girebilirsiniz (ör: \'mail.a.com\' veya \'mail.a.com;mail.b.com\'). Bu seçeneði boþ býrakýrsanýz PHP\'nin email gönderirken kullandýðý varsayýlan metot kullanýlacaktýr.';
$string['configsmtpuser'] = 'Yukarýda bir SMTP sunucu belirttiyseniz ve bu sunucu yetki istiyorsa buraya sunucu için kullanýcý adý ve þifreyi giriniz.';
$string['configunzip'] = 'Unzip programýnýn yerini belirtin (Sadece Unix için, isteðe baðlýdýr). Belirtilirse, sunucuda zip arþivini açmak için bu kullanýlacaktýr. Boþ býrakýrsanýz, zip arþivini açmak için dahili iþlemler kullanýlacaktýr.';
$string['configvariables'] = 'Deðiþkenler';
$string['configwarning'] = 'Bu ayarlarý deðiþtirirken dikkatli olun. Bilmediðiniz deðerleri girmeniz sorunlara sebep olabilir.';
$string['configzip'] = 'Zip programýnýn yerini belirtin (Sadece Unix için, isteðe baðlýdýr). Belirtilirse, sunucuda zip arþivi oluþturmak için bu kullanýlacaktýr. Boþ býrakýrsanýz, zip arþivi oluþturmak için dahili iþlemler kullanýlacaktýr.';
$string['confirmation'] = 'Onay';
$string['cronwarning'] = '<a href=\"cron.php\">cron.php bakým programý</a> son 24 saattir çalýþmýyor. <br /><a href=\"../doc/?frame=install.html&amp;sub=cron\">Kurulum belgesi</a> bunu nasýl otomatikleþtireceðinizi açýklýyor.';
$string['edithelpdocs'] = 'Yardým belgelerini düzenle';
$string['editstrings'] = 'Ýfadeleri düzenle';
$string['filterall'] = 'Tüm ifadeleri filtrele';
$string['filteruploadedfiles'] = 'Tüm gönderilen dosyalarý filtrele';
$string['helpadminseesall'] = 'Yöneticiler tüm olaylarý mý yoksa sadece kendine ait olaylarý mý görsün?';
$string['helpcalendarsettings'] = 'Moodle\'a iliþkin tarih/saat ve takvim ayarlarýný yapýlandýrýn';
$string['helpforcetimezone'] = 'Kullanýcýlarýn bireysel olarak zaman dilimlerini seçmelerine izin verebilir ya da herkesin ayný zaman dilimini kullanmasýný zorunlu tutabilirsiniz.';
$string['helpsitemaintenance'] = 'Güncellemeler ve diðer çalýþmalar için';
$string['helpstartofweek'] = 'Takvimde hafta hangi günle baþlýyor?';
$string['helpupcomingmaxevents'] = 'Varsayýlan olarak en fazla kaç tane yaklaþan olay kullanýcýlara gösterilecek?';
$string['helpweekenddays'] = 'Hangi günler \"Hafta sonu\" olarak deðerlendirilecek ve farklý bir renkte görünecek?';
$string['importtimezones'] = 'Zaman dilimleri listesinin tamamýný güncelle';
$string['importtimezonescount'] = '$a->source \'dan $a->count kayýt çýkartýldý';
$string['importtimezonesfailed'] = 'Hiç kaynak bulunamadý! (Kötü haber)';
$string['optionalmaintenancemessage'] = 'Ýsteðe baðlý bakým mesajý';
$string['sitemaintenance'] = 'Bu siteye þu anda bakým yapýlýyor ve þimdilik eriþilemez';
$string['sitemaintenancemode'] = 'Bakým modu';
$string['sitemaintenanceoff'] = 'Bakým modu pasifleþtirildi ve site þu anda tekrar normal çalýþýyor';
$string['sitemaintenanceon'] = 'Siteniz þu anda bakým modunda (sadece yöneticiler girþ yapabilir ve siteyi kullanabilir)';
$string['sitemaintenancewarning'] = 'Siteniz þu anda bakým modunda (sadece yöneticiler girþ yapabilir). Bu siteyi normal haline döndürmek için <a href=\"maintenance.php\">bakým modunu pasifleþtirin</a>.';
$string['timezoneforced'] = 'Bu site yöneticisi tarafýndan zorunlu tutuldu';
$string['timezoneisforcedto'] = 'Bütün kullanýcýlarý kullanmaya zorunlu tut';
$string['timezonenotforced'] = 'Kullanýcýlar kendi zaman dilimini seçebilsin';

?>
