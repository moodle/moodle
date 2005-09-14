<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.6 development (2005081700)


$string['adminreview'] = 'Kredi kartýndan çekmeden önce sipariþi incele.';
$string['anlogin'] = 'Authorize.net: Kullanýcý adý';
$string['anpassword'] = 'Authorize.net: Þifre';
$string['anreferer'] = 'Authorize.net hesabýnýzda URL referer ayarý yaptýysanýz buraya yazýnýz. Bu, web isteðinde \"Referer: URL\" baþlýðýný gönderir.';
$string['antestmode'] = 'Ýþlemleri deneme modunda çalýþtýr (para çekilmez)';
$string['antrankey'] = 'Authorize.net: Ýþlem Anahtarý (Transaction Key)';
$string['ccexpire'] = 'Geçerlilik Tarihi';
$string['ccexpired'] = 'Kredi kartýnýn süresi geçmiþ';
$string['ccinvalid'] = 'Geçersiz kart numarasý';
$string['ccno'] = 'Kredi Kartý No';
$string['cctype'] = 'Kredi Kartý Tipi';
$string['ccvv'] = 'Onay Kodu';
$string['ccvvhelp'] = 'Kartýn arkasýna bakýnýz (son 3 rakam)';
$string['choosemethod'] = 'Kursun kayýt anahtarýný biliyorsanýz giriniz. Diðer durumda bu kurs için ödeme yapmanýz gerekiyor.';
$string['chooseone'] = 'Aþaðýdaki iki alandan birini veya ikisini doldurun';
$string['description'] = 'Authorize.net modülü Kredi Kartý saðlayýcýlarýyla ücretli kurslar ayarlamanýza olanak verir. Bir kursun ücreti sýfýr ise öðrencilere ödeme yapmalarý için bir istekte bulunulmaz. Sitenin geneli için ayarlayabileceðiniz varsayýlan bir tutar vardýr ve her bir dersin ücretini tek tek de ayarlayabilirsiniz. Kurs ücreti ayarlanýrsa site genelindeki ücret yoksayýlýr.';
$string['enrolname'] = 'Authorize.net Kredi Kartý Saðlayýcýsý';
$string['httpsrequired'] = 'Üzgünüz, isteðinizi þu anda yerine getiremiyoruz. Bu sitenin ayarý doðru yapýlandýrýlmamýþ.
<br /><br />
Tarayýcýnýzýn alt tarafýnda sarý bir kilit görmüyorsanýz kredi kartý numaranýzý girmeyiniz. Bu, sizinle sunucu arasýnda gidip gelen verinin þifrelendiði anlamýna gelir. Böylece 2 bilgisayar arasýnda akan bilgi korunmuþ olur ve kredi kartý numaranýz internet üzerinden yakalanamaz.';
$string['logindesc'] = 'Bu seçenek AÇIK olmalý.
<br /><br />
<a href=\"$a->url\">Loginhttps</a> seçeneðini Deðiþkenler/Güvenlik bölümünden ayarlayabilirsiniz.
<br /><br />
Bu seçenek aktif ise sadece giriþ ve ödeme sayfalarý için güvenli baðlantý (https) kullanýlacaktýr.';
$string['nameoncard'] = 'Kart Üzerindeki Ýsim';
$string['reviewday'] = '<b>$a</b> gün içinde eðitimci veya yönetici sipariþi incelemezse kredi kartýndan otomatik olarak parayý çek. CRON ETKÝN OLMALI. <br /> (0 gün = otomatik çekme aktif deðil = Eðimci veya yönetici sipariþi kendisi inceleyecek. Otomatik çekmeyi etinleþtirmezseniz veya 30 gün içinde sipariþi incelemezseniz iþlem iptal edilir.)';
$string['reviewnotify'] = 'Ödemeniz incelenecek. Bir kaç gün içinde eðitimcinizden bir email bekleyin.';
$string['sendpaymentbutton'] = 'Ödemeyi Yap';
$string['zipcode'] = 'Posta Kodu';

?>
