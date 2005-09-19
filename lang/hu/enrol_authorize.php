<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.6 development (2005081700)


$string['adminreview'] = 'Rendelés ellenõrzése a hitelkártya használata elõtt.';
$string['anlogin'] = 'Authorize.net: felhasználónév';
$string['anpassword'] = 'Authorize.net: jelszó (nem szükséges)';
$string['anreferer'] = 'Adja meg itt az URL-hivatkozást, ha ezt beállítja az authorize.net fiókjában. Ezzel a weboldalkérésben egy \"Referer: URL\" fejléc továbbítódik.';
$string['antestmode'] = 'Authorize.net: ügyletek ellenõrzése';
$string['antrankey'] = 'Authorize.net: ügyletkulcs';
$string['ccexpire'] = 'Lejárat dátuma';
$string['ccexpired'] = 'A hitelkártya lejárt';
$string['ccinvalid'] = 'Érvénytelen kártyaszám';
$string['ccno'] = 'Hitelkártyaszám';
$string['cctype'] = 'Hitelkártyatípus';
$string['ccvv'] = 'Kártyaellenõrzés';
$string['ccvvhelp'] = 'Lásd a kártya túloldalán (3 számjegy)';
$string['choosemethod'] = 'Adja meg a kurzus beiratkozási kódját, ha ismeri; ellenkezõ esetben fizetnie kell a kurzus elvégzéséért.';
$string['chooseone'] = 'Az alábbi két mezõt vagy az egyiket töltse ki';
$string['description'] = 'Az Authorize.net modullal forgalmazók térítéses kurzusai hozhatók létre. Ha valamely kurzus ára nulla, a tanulóknak nem kell fizetni a belépéshez. Itt adható meg a portálra globálisan érvényes költség, valamint az egyes kurzusokhoz egyenként beállítható költség. A kurzusköltség felülírja a portálköltséget.';
$string['enrolname'] = 'Authorize.net: hitelkártyakapu';
$string['httpsrequired'] = 'Sajnos kérését jelenleg nem tudjuk feldolgozni. A portált nem lehetett megfelelõ módon beállítani.<br /><br />
Ne adja meg a hitelkártyaszámát, ha a böngészõ alján nem jelenik meg egy sárga lakat. Ez azt jelzi, hogy az ügyfél és a kiszolgáló között minden adat továbbítása kódoltan történik. Így a 2 számítógép közötti kapcsolat adatforgalma védve van és hitelkártyája számát nem lehet interneten keresztül levenni.';
$string['logindesc'] = 'Ezt az opciót be kell kapcsolni. <br /><br /> A Változók/Biztonság részben beállíthat egy <a href=\"$a->url\">loginhttps</a> opciót. <br /><br />
Ennek bekapcsolásakor a Moodle csak a bejelentkezési és fizetési oldalakon használ biztonságos https-csatlakozást.';
$string['nameoncard'] = 'Kártyán szereplõ név';
$string['reviewday'] = 'Automatikusan terhelje meg a hitelkártyát, ha egy tanár vagy egy rendszergazda  <b>$a</b> napon belül nem vizsgálja felül a rendelést. A CRON LEGYEN BEKAPCSOLVA.<br />(0 nap = automatikus terhelés kikapcsolása = tanár, rendszergazda kézi úton felülvizsgálja. Az ügylet törlõdik, ha kikapcsolja az automatikus terhelést, vagy ha 30 napon belül felülvizsgálja.)';
$string['reviewnotify'] = 'Fizetését ellenõrizzük. Néhány napon belül e-mail üzenetet kap a tanárától.';
$string['sendpaymentbutton'] = 'Pénz küldése';
$string['zipcode'] = 'Irányítószám';

?>
