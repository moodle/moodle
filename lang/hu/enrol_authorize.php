<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.6 development (2005101200)

$string['adminauthorizeccapture'] = 'Megrendelés ellenõrzése és beállítások automatikus rögzítése';
$string['adminauthorizeemail'] = 'E-mail küldésének beállításai';
$string['adminauthorizesettings'] = 'Authorize.net beállításai';
$string['adminauthorizewide'] = 'Portálra érvényes beállítások';
$string['adminneworder'] = ' Tisztelt Rendszergazda!
                
  Új elbírálandó megrendelést kapott:

   Rendelésazonosító: $a->orderid
   Ügyletazonosító: $a->transid
   Felhasználó: $a->user
   Kurzus: $a->course
   Összeg: $a->amount
               
   AUTOMATIKUS MEGTERHELÉS BEKAPCSOLVA?: $a->acstatus
                
  Ha az automatikus megterhelés be van kapcsolva, $a->captureon esetén megtörténik a hitelkártya megterhelése, 
  és a tanulót beiratkoztatja a kurzusra. Ellenkezõ esetben  $a->expireon dátummal lejár, és ezt követõen már nem lehet megterhelni.
                
  Lehetõsége van a tanulót beiratkoztató fizetés elfogadására/elutasítására az alábbi ugrópontot követve:
  $a->url';
$string['adminnewordersubject'] = '$a->course: Új elbírálandó megrendelés($a->orderid)';
$string['adminreview'] = 'Rendelés ellenõrzése a hitelkártya használata elõtt.';
$string['amount'] = 'Összeg';
$string['anlogin'] = 'Authorize.net: felhasználónév';
$string['anpassword'] = 'Authorize.net: jelszó (nem szükséges)';
$string['anreferer'] = 'Adja meg itt az URL-hivatkozást, ha ezt beállítja az authorize.net fiókjában. Ezzel a weboldalkérésben egy \"Referer: URL\" fejléc továbbítódik.';
$string['antestmode'] = 'Authorize.net: ügyletek ellenõrzése';
$string['antrankey'] = 'Authorize.net: ügyletkulcs';
$string['authorizedpendingcapture'] = 'Engedélyezett / Folyamatban lévõ megterhelés';
$string['canbecredit'] = 'Nem téríthetõ vissza ide:  $a->upto';
$string['cancelled'] = 'Törölve';
$string['capture'] = 'Megterhelés';
$string['capturedpendingsettle'] = 'Megterhelve / Rendezés folyamatban';
$string['capturedsettled'] = 'Megterhelve / Rendezve';
$string['capturetestwarn'] = 'A megterhelés mûködni látszik, de tesztelõ módban nem történt rekordfrissítés';
$string['captureyes'] = 'A hitelkártyát megterheljük és a tanulót beiratkoztatjuk. Biztosan ezt akarja?';
$string['ccexpire'] = 'Lejárat dátuma';
$string['ccexpired'] = 'A hitelkártya lejárt';
$string['ccinvalid'] = 'Érvénytelen kártyaszám';
$string['ccno'] = 'Hitelkártyaszám';
$string['cctype'] = 'Hitelkártyatípus';
$string['ccvv'] = 'Kártyaellenõrzés';
$string['ccvvhelp'] = 'Lásd a kártya túloldalán (3 számjegy)';
$string['choosemethod'] = 'Adja meg a kurzus beiratkozási kódját, ha ismeri; ellenkezõ esetben fizetnie kell a kurzus elvégzéséért.';
$string['chooseone'] = 'Az alábbi két mezõt vagy az egyiket töltse ki';
$string['credittestwarn'] = 'A hitel mûködni látszik, de tesztelõ módban nem került rekord az adatbázisba';
$string['cutofftime'] = 'Ügylet megszüntetésének ideje. Mikor kerül sor az utolsó ügylet rendezésére?';
$string['description'] = 'Az Authorize.net modullal forgalmazók térítéses kurzusai hozhatók létre. Ha valamely kurzus ára nulla, a tanulóknak nem kell fizetni a belépéshez. Itt adható meg a portálra globálisan érvényes költség, valamint az egyes kurzusokhoz egyenként beállítható költség. A kurzusköltség felülírja a portálköltséget.';
$string['enrolname'] = 'Authorize.net: hitelkártyakapu';
$string['expired'] = 'Lejárt';
$string['howmuch'] = 'Mennyi?';
$string['httpsrequired'] = 'Sajnos kérését jelenleg nem tudjuk feldolgozni. A portált nem lehetett megfelelõ módon beállítani.<br /><br /> Ne adja meg a hitelkártyaszámát, ha a böngészõ alján nem jelenik meg egy sárga lakat. Ez azt jelzi, hogy az ügyfél és a kiszolgáló között minden adat továbbítása kódoltan történik. Így a 2 számítógép közötti kapcsolat adatforgalma védve van és hitelkártyája számát nem lehet interneten keresztül levenni.';
$string['logindesc'] = 'Ezt az opciót be kell kapcsolni. <br /><br /> A Rendszergazda / Változók / Biztonság részben ellenõrizze, be van-e kapcsolva a <a href=\"$a->url\">loginhttps</a> opció. <br /><br />
Ennek bekapcsolásakor a Moodle csak a bejelentkezési és fizetési oldalakon használ biztonságos https-csatlakozást.';
$string['nameoncard'] = 'Kártyán szereplõ név';
$string['noreturns'] = 'Nincs visszatérítés!';
$string['notsettled'] = 'Nincs rendezve';
$string['orderid'] = 'Rendelés azonosítója';
$string['paymentmanagement'] = 'Fizetés kezelése';
$string['paymentpending'] = 'Ezen kurzushoz tartozó fizetésének rendezése folyamatban ezzel a rendelésazonosítóval: $a->orderid.';
$string['refund'] = 'Visszatérít';
$string['refunded'] = 'Visszatérítve';
$string['returns'] = 'Visszatérítések';
$string['reviewday'] = 'Automatikusan terhelje meg a hitelkártyát, ha egy tanár vagy egy rendszergazda  <b>$a</b> napon belül nem vizsgálja felül a rendelést. A CRON LEGYEN BEKAPCSOLVA.<br />(0 nap = automatikus terhelés kikapcsolása = tanár, rendszergazda kézi úton felülvizsgálja. Az ügylet törlõdik, ha kikapcsolja az automatikus terhelést, vagy ha 30 napon belül felülvizsgálja.)';
$string['reviewnotify'] = 'Fizetését ellenõrizzük. Néhány napon belül e-mail üzenetet kap a tanárától.';
$string['sendpaymentbutton'] = 'Pénz küldése';
$string['settled'] = 'Rendezve';
$string['settlementdate'] = 'Rendezés dátuma';
$string['subvoidyes'] = 'A visszatérített $a->transid ügylet törölve lesz és $a->amount összeget jóváírunk a számláján. Biztosan ezt akarja?';
$string['tested'] = 'Elenõrizve';
$string['testmode'] = '[ELLENÕRZÉSI MÓD]';
$string['transid'] = 'Ügyletazonosító';
$string['unenrolstudent'] = 'A tanulót kiiratkoztatja?';
$string['void'] = 'Érvénytelen';
$string['voidtestwarn'] = 'Az érvénytelen mûködni látszik, de tesztelõ módban nem történt rekordfrissítés';
$string['voidyes'] = 'Az ügyletet töröljük. Biztosan ezt akarja?';
$string['zipcode'] = 'Irányítószám';

?>
