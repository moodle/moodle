<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.6 development (2005101200)


$string['adminauthorizeccapture'] = 'Sipariþi Ýnceleme ve Otomatik-Çekme Ayarlarý';
$string['adminauthorizeemail'] = 'Email Gönderme Ayarlarý';
$string['adminauthorizesettings'] = 'Authorize.net Ayarlarý';
$string['adminauthorizewide'] = 'Site Geneli Ayarlarý';
$string['adminneworder'] = 'Deðerli Yönetici,

Yeni bir bekleyen sipariþ aldýnýz:

Sipariþ no: $a->orderid
Ýþlem ID: $a->transid
Kullanýcý: $a->user
Kurs: $a->course
Miktar: $a->amount

OTOMATÝK-ÇEKME ETKÝN MÝ?: $a->acstatus

Otomatik çekme etkinse kredi kartýndan $a->captureon tarihinde çekilecek ve öðrencinin derse kaydý yapýlacak. Diðer durumda $a->expireon tarihinde süresi dolacak ve bu tarihten sonra çekilemeyecek.

Ayrýca aþaðýdaki linki týklayarak ödemeyi derhal kabul veya reddedebilir ve öðrenciyi derse kaydedebilirsiniz:
$a->url';
$string['adminnewordersubject'] = '$a->course: Bekleyen Yeni Sipariþ($a->orderid)';
$string['adminreview'] = 'Kredi kartýndan çekmeden önce sipariþi incele.';
$string['amount'] = 'Miktar';
$string['anlogin'] = 'Authorize.net: Kullanýcý adý';
$string['anpassword'] = 'Authorize.net: Þifre';
$string['anreferer'] = 'Authorize.net hesabýnýzda URL referer ayarý yaptýysanýz buraya yazýnýz. Bu, web isteðinde \"Referer: URL\" baþlýðýný gönderir.';
$string['antestmode'] = 'Ýþlemleri deneme modunda çalýþtýr (para çekilmez)';
$string['antrankey'] = 'Authorize.net: Ýþlem Anahtarý (Transaction Key)';
$string['authorizedpendingcapture'] = 'Onaylanmýþ / Çekilmeyi Bekliyor';
$string['canbecredit'] = '$a->upto\'a kadar geri ödenebilir';
$string['cancelled'] = 'Ýptal edilmiþ';
$string['capture'] = 'Çek';
$string['capturedpendingsettle'] = 'Çekilmiþ / Ödeme Bekleniyor';
$string['capturedsettled'] = 'Çekilmiþ / Ödenmiþ';
$string['capturetestwarn'] = 'Çekme çalýþýyor olarak görünüyor fakat deneme modunda kayýt güncellenmedi';
$string['captureyes'] = 'Para kredi kartýndan çekilecek ve öðrencinin derse kaydý yapýlacak. Emin misiniz?';
$string['ccexpire'] = 'Geçerlilik Tarihi';
$string['ccexpired'] = 'Kredi kartýnýn süresi geçmiþ';
$string['ccinvalid'] = 'Geçersiz kart numarasý';
$string['ccno'] = 'Kredi Kartý No';
$string['cctype'] = 'Kredi Kartý Tipi';
$string['ccvv'] = 'Onay Kodu';
$string['ccvvhelp'] = 'Kartýn arkasýna bakýnýz (son 3 rakam)';
$string['choosemethod'] = 'Kursun kayýt anahtarýný biliyorsanýz giriniz. Diðer durumda bu kurs için ödeme yapmanýz gerekiyor.';
$string['chooseone'] = 'Aþaðýdaki iki alandan birini veya ikisini doldurun';
$string['credittestwarn'] = 'Geri ödeme çalýþýyor olarak görünüyor fakat deneme modunda yeni kayýt eklenmedi';
$string['cutofftime'] = 'Hesap Kesim Zamaný. Hesap kesimi en son ne zaman yapýlacak?';
$string['description'] = 'Authorize.net modülü Kredi Kartý saðlayýcýlarýyla ücretli kurslar ayarlamanýza olanak verir. Bir kursun ücreti sýfýr ise öðrencilere ödeme yapmalarý için bir istekte bulunulmaz. Sitenin geneli için ayarlayabileceðiniz varsayýlan bir tutar vardýr ve her bir dersin ücretini tek tek de ayarlayabilirsiniz. Kurs ücreti ayarlanýrsa site genelindeki ücret yoksayýlýr..<br /><br /><b>Not:</b> Kurs ayarlarýnda kayýt anahtarýný girdiyseniz öðrenciler bu anahtara göre de kayýt olma seçeneðine sahip olabileceklerdir. Bu, öðrecilerden bazýlarýnýn ödeme yaparak bazýlarýnýn da kayýt anahtarýna göre kayýt olmasýný istiyorsanýz kullanýþlýdýr.';
$string['enrolname'] = 'Authorize.net Kredi Kartý Saðlayýcýsý';
$string['expired'] = 'Süresi dolmuþ';
$string['howmuch'] = 'Ne kadar?';
$string['httpsrequired'] = 'Üzgünüz, isteðinizi þu anda yerine getiremiyoruz. Bu sitenin ayarý doðru yapýlandýrýlmamýþ.
<br /><br />
Tarayýcýnýzýn alt tarafýnda sarý bir kilit görmüyorsanýz kredi kartý numaranýzý girmeyiniz. Bu, sizinle sunucu arasýnda gidip gelen verinin þifrelendiði anlamýna gelir. Böylece 2 bilgisayar arasýnda akan bilgi korunmuþ olur ve kredi kartý numaranýz internet üzerinden yakalanamaz.';
$string['logindesc'] = 'Bu seçenek AÇIK olmalý.
<br /><br />
<a href=\"$a->url\">Loginhttps</a> seçeneðini Deðiþkenler/Güvenlik bölümünden ayarlayabilirsiniz.
<br /><br />
Bu seçenek aktif ise sadece giriþ ve ödeme sayfalarý için güvenli baðlantý (https) kullanýlacaktýr.';
$string['nameoncard'] = 'Kart Üzerindeki Ýsim';
$string['noreturns'] = 'Geri ödeme yok';
$string['notsettled'] = 'Faturalandýrýlmamýþ';
$string['orderid'] = 'Sipariþ ID';
$string['paymentmanagement'] = 'Ödeme Yönetimi';
$string['paymentpending'] = '$a->orderid numaralý ödemeniz bu kurs için onay bekliyor.';
$string['refund'] = 'Geri Öde';
$string['refunded'] = 'Geri ödenmiþ';
$string['returns'] = 'Geri ödemeler';
$string['reviewday'] = '<b>$a</b> gün içinde eðitimci veya yönetici sipariþi incelemezse kredi kartýndan otomatik olarak parayý çek. CRON ETKÝN OLMALI. <br /> (0 gün otomatik çekme aktif deðil anlamýna gelir ve ayný zamanda eðitimci veya yöneticinin sipariþi kendisi inceleyeceðini zorunlu tutar. Otomatik çekmeyi etinleþtirmezseniz veya 30 gün içinde sipariþi incelemezseniz iþlem iptal edilir.)';
$string['reviewnotify'] = 'Ödemeniz incelenecek. Bir kaç gün içinde eðitimcinizden bir email bekleyin.';
$string['sendpaymentbutton'] = 'Ödemeyi Yap';
$string['settled'] = 'Faturalandýrýlmýþ';
$string['settlementdate'] = 'Hesap Kesim Tarihi';
$string['subvoidyes'] = 'Geri ödenen $a->transid nolu iþlem iptal edilecek ve hesabýnýza $a->amount yüklenecek. Emin misiniz?';
$string['tested'] = 'Denendi';
$string['testmode'] = '[DENEME MODU]';
$string['transid'] = 'Ýþlem ID';
$string['unenrolstudent'] = 'Öðrencinin ders kaydýný sil?';
$string['void'] = 'Ýptal et';
$string['voidtestwarn'] = 'Ýptal etme çalýþýyor olarak görünüyor fakat deneme modunda kayýt güncellenmedi';
$string['voidyes'] = 'Ýþlem iptal edilecek. Emin misiniz?';
$string['zipcode'] = 'Posta Kodu';

?>
