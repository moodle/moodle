<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.6 development (2005060201)


$string['adminreview'] = 'Rebyuhin ang order bago icapture ang credit card.';
$string['anlogin'] = 'Authorize.net: Pangalan na panglog-in';
$string['anpassword'] = 'Authorize.net: Password';
$string['anreferer'] = 'Itype dito ang URL referer, kung isinaayos mo ito sa iyong authorize.net account.  Ipapadala nito ang linya na \"Referer: URL\" na nakaembed sa web request.';
$string['antestmode'] = 'Patakbuhin ang transaksiyon sa mode na pagsubok lamang (walang perang kukunin)';
$string['antrankey'] = 'Authorize.net: Susi ng transaksiyon';
$string['ccexpire'] = 'Petsa ng Pagkapasó';
$string['ccexpired'] = 'Pasó na ang credit card';
$string['ccinvalid'] = 'Ditanggap na bilang ng card';
$string['ccno'] = 'Bilang ng Credit Card';
$string['cctype'] = 'Uri ng Credit Card';
$string['ccvv'] = 'Beripikasyon ng Card';
$string['ccvvhelp'] = 'Tingnan ang likod ng card (huling 3 numero)';
$string['choosemethod'] = 'Kung alam mo ang susi sa pag-enrol sa kurso, ipasok ito; kundi ay kailangan mong magbayad sa kursong ito.';
$string['chooseone'] = 'Punan ang isa o pareho sa sumusunod na dalawang puwang';
$string['description'] = 'Ang Authorize.net na modyul ay pinahihintulutan kang magsaayos ng may-bayad na kurso sa pamamagitan ng mga negosyante.  Kung ang halaga ng anumang kurso ay sero, ang mga mag-aaral ay hindi na sisingilin para makapasok.  May pangbuong site na halaga na itatakda mo rito bilang default para sa buong site at pagkatapos ay isang kaayusang pangkurso na itatakda mo para sa bawat kurso.  Nananaig ang halaga ng kurso sa halaga ng site.';
$string['enrolname'] = 'Gateway ng Authorize.net Credit Card ';
$string['httpsrequired'] = 'Ikinalulungkot naming ipaalam sa inyo na hindi puwedeng iproseso ang kahilingan mo sa kasalukuyan.   Hindi maisaayos ang kompigurasyon ng site na ito.
<br /><br />
Huwag pong ipapasok ang credit card number ninyo hangga\'t di kayo nakakakita ng dilaw na kandado sa ibaba ng browser.  Ang ibig sabihin nito, ay ieencrypt ang lahat ng datos na ipapadala sa pagitan ng client at server.  Kaya ang impormasyon sa panahon ng transaksiyon sa pagitan ng dalawang kompyuter ay protektado at ang credit card number ninyo ay hindi mananakaw sa internet.';
$string['logindesc'] = 'Kailangang naka-ON ang opsiyong ito.
<br /><br />
Maaari kang magsaayos ng 
<a href=\"$a->url\">loginhttps</a> na opsiyon sa Baryabol/Seguridad na seksiyon.
<br /><br />
Kapag binuhay ito ang Moodle ay gagamit ng ligtas na https na koneksiyon para lamang sa pahinang panglog-in at pagbabayad.';
$string['nameoncard'] = 'Pangalan sa card';
$string['reviewday'] = 'Awtomatikong icapture ang credit card maliban na lamang kapag nirebyu ng guro o administrador ang order sa loob ng <b>$a</b> araw.  KAILANGANG BUHAYIN ANG CRON.<br />(0 araw = patayin ang awtocapture = rerebyuhin ito ng guro, admin nang mano-mano.  Ang transaksiyon ay kakanselahin kapag pinatay mo ang awtocapture o kapag hindi mo nirebyu ito sa loob ng 30 araw.)';
$string['reviewnotify'] = 'Rerebyuhin ang kabayaran mo.  Umasa ka na may email na ipapadala sa iyo ang guro mo sa loob ng ilang araw.';
$string['sendpaymentbutton'] = 'Ipadala ang Bayad';
$string['zipcode'] = 'Zip code';

?>
