<?PHP // $Id$ 
      // install.php - created with Moodle 1.4.3 + (2004083131)


$string['admindirerror'] = 'Odreðeni administratorski direktorij je nepravilan';
$string['admindirname'] = 'Administratorski direktorij';
$string['admindirsetting'] = 'Veoma nekoliko ili priližno nekoliko webhostova koristi administrator kao specijalni URL za Vaš pristup kontrolnoj ploèi. Na nesreæu ovo je konflikt sa standardnom lokacijom za Moodle administratorsku stranicu. Možete ovo fiksirati preimenovanjem administratorskog direktorija u Vašoj instalaciji, postavijajuæi ovdje novo ime. Na primjer: <br /> <br /><b>moodleadmin</b><br /> <br />
Ovo æe fiksirati administratorski link na Moodle.';
$string['caution'] = 'Pažnja';
$string['chooselanguage'] = 'Izaberite jezik';
$string['compatibilitysettings'] = 'Provjerite Vaša PHP podešavanja ...';
$string['configfilenotwritten'] = 'Instalacijska skripta nije u moguænosti da automatski kreira config.php datoteku koja obuhvata Vaše izabrano podešavanje, vjerovatno zbog Moodle direktorija koji nije pisan. Možete ruèno kopirati prateæi kod u ime fajla config.php unutar osnovnog direktorija za Moodle.';
$string['configfilewritten'] = 'config.php je bio uspješno kreiran';
$string['configurationcomplete'] = 'Konfiguracija je kompletirana';
$string['database'] = 'Baza podataka';
$string['databasesettings'] = 'Sada Vam je potrebno da konfigurišete bazu podataka gdje æe i veæina Moodle podataka biti pohranjena. Ova baza podataka veæ mora biti kreirana i korisnièko ime i lozinku kreirajte da je dostupno.<br />
<br /> <br />
<b>Tip:</b> mysql or postgres7<br />
<b>Glavni:</b> eg lokalhost ili db.isp.com<br />
<b>Ime:</b> ime baze podataka, eg moodle<br />
<b>Korisnik:</b> Vaše korisnièko ime baze podataka<br />
<b>Lozinka:</b> your database password<br />
<b>Tabelarni prefiks:</b> alternativni prefiks da koristi svim imenima tabela';
$string['dataroot'] = 'Direktorij podataka';
$string['datarooterror'] = '\'Directorij podataka\' koji ste naveli ne može biti pronaðen ili kreiran.  Svaka korekcija puta ili pravljenja ruèno tog direktorija.';
$string['dbconnectionerror'] = 'Ne možemo se spojiti na bazu podataka koju ste naveli. Molimo Vas da provjerite svoja podešavanja baze podataka.';
$string['dbcreationerror'] = 'Greška prilikom pravljenja baze podataka. Ne možete napraviti ime date baze podataka sa predviðenim podešavanjima';
$string['dbhost'] = 'Glavni server';
$string['dbpass'] = 'Lozinka';
$string['dbprefix'] = 'Lista prefiksa';
$string['dbtype'] = 'Tip';
$string['directorysettings'] = '<p>Molim Vas potvrdite lokaciju ove Moodle instalacije.</p>

<p><b>Web Adresa:</b>
Navedite punu web adresu gdje æe Moodle biti dostupan. Ako je Vaš web sajt dostupan preko više URLa tada izaberite što prirodniji jedan od onih koje æe Vaši studenti koristiti. Ne ukljuèujte prateæu crticu.</p>

<p><b>Moodle Directorij:</b>
Navedite punu putanju direktorija za ovu instalaciju. Budite sigurni da je gornji/donji sluèaj taèan.</p>

<p><b>Directorij podataka:</b>
Potrebno Vam je mjesto gdje Moodle možete spasiti uèitavajuæi datoteke. Ovaj direktorij bi trebao biti èitljiv i pisan od web server korisnika (obièno \'niko\' ili \'apache\'), ali to neæe biti direktno pristupaèno od weba.</p>';
$string['dirroot'] = 'Direktorij Moodla';
$string['dirrooterror'] = 'Podešavanje \'Direktorija Moodla\' izgleda je netaèno - ne možemo tamo naæi Moodle instalaciju. Niža vrijednost æe biti ponovo dovedena na poèetni položaj.';
$string['download'] = 'Preuzeti';
$string['fail'] = 'Izostati';
$string['fileuploads'] = 'Katalog za uèitavanja datoteka';
$string['fileuploadserror'] = 'Ovo bi trebalo biti ukljuèeno';
$string['fileuploadshelp'] = '<p>Katalog za uèitavanje datoteka izgleda da je nedostupan na Vašem serveru.</p>

<p>Moodle još uvijek može biti instaliran, ali bez ove moguænosti, neæete biti u moguænosti da uèitavate datoteke kursa ili duplikate novih korisnièkih profila.</p>

<p>Da uèitavanje datoteke bude dostupno Vi (ili Vaš sistem administratora) trebat æete promijeniti svoju php.ini datoteku na Vašem sistemu i promijeniti podešavanja za <b>katalog_ucitavanja_datoteka</b> to \'1\'.</p>';
$string['gdversion'] = 'GD verzija';
$string['gdversionerror'] = 'GD datoteka sa izvornim kodom trebala bi prezentirati proces i kreirati duplikate';
$string['gdversionhelp'] = '<p>Vaš server neæe izgledati isto imajuæi GD instalaciju.</p>

<p>GD je datoteka sa izvornim kodom što je potrebno da PHP dozvoli Moodle da izradi duplikate (kao što je ikona korisnièkog profila) i da kreira nove duplikate (kao što je operativni registar slika).  Moodle æe još uvijek raditi bez GD a ova lica jednostavno neæe biti dostupna vama.</p>

<p>Da dodate GD u PHP na osnovu Unixa, kompajlirate PHP koristeæi se gd parametrom.</p>

<p>Na osnovu Windows obièno možete podesiti php.ini i ne bilježiti liniju referencirajuæi libgd.dll.</p>';
$string['installation'] = 'Instalacija';
$string['magicquotesruntime'] = 'Èari naznake vremenskog kretanja';
$string['magicquotesruntimeerror'] = 'Ovo bi trebalo biti iskljuèeno';
$string['magicquotesruntimehelp'] = '<p>Èini kvote vremena pokretanja trebalo bi iskljuèiti da Moodle propisno funkcioniše.</p>

<p>Normalno ovo je iskljuèeno po podrazumijevanoj vrijednosti ... pogledaj podešavanje<b>cini_kvote_vremena_pokretanja </b> na Vašoj php.ini datoteci.</p>

<p>Ako nemate pristu na Vaš php.ini, moæi æete biti u moguænosti da ocjenite prateæi liniju u datoteci pozivajuæi .htapristup unutar Vašeg Moodle direktorija:  <blocquote>php_vrijednost_cina_kvote_vremena_pokretanja iskljuèeno</blockquote>
</p>   
   ';
$string['memorylimit'] = 'Ogranièenje memorije';
$string['memorylimiterror'] = 'PHP ogranièenje je podešeno na potpuno malo memorije ... kasnije se možete kretati unutar problema.';
$string['memorylimithelp'] = '<p>PHP ogranièenje memorije za Vaš server je trenutno podešeno na $a.</p>

<p>Ovo možda prouzrokuje Moodlu da kasnije ima problema sa memorijom, posebno ako imate mnogo dozvoljenih modula i /ili mnogo korisnika.</p>

<p>Preporuèujemo Vam da konfigurišete PHP sa  visokim ogranièenjem ako je moguæe, kao 16M. Èineæi ovo tamo je nekoliko naèina pa možete pokušati: </p><ol>
<li>Ako ste, opet kompajlisati PHP sa <i>--dostupnim-memorijskim-ogranièenjem</i>. Ovo æe dozvoliti Moodle da postavi memorijsko ogranièenje sam za sebe.</li>
<li>Ako imate pristup na Vašu php.ini datoteku, možete promijeniti <b>memorijsko_ogranièenje</b> podešavanje u nešto kao ovo 16M. Ako nemate pristup možete pitati svog administratora da to uradi za Vas.</li>
<li>Na nekim PHP serverima možete kreirati  a.ht pristupnu datoteku u Moodle direktoriju koji se sadrži na ovoj liniji:<br /><blockquote>php_vrijednost memorijskog_ogranièenja 16M</blockquote></li>
<br />Kakogod, na istom serveru ovo izbjegavajte <b>sve</b> PHP stranice za rad (vidjet æete grešku prilkom pregleda stranice) æete na njima morati izbrisati .htpristupnu datoteku. </li></ol>';
$string['mysqlextensionisnotpresentinphp'] = 'PHP neæe biti propisno konfigurisan sa MySQL ekstenzijom tako da može komunicirati sa  MySQL.  Molimo Vas da provjerite svoju php.ini datoteku ili opet kompajlišite PHP.';
$string['pass'] = 'Proæi';
$string['phpversion'] = 'PHP verzija';
$string['phpversionerror'] = 'PHP verzija mora biti najmanje 4.1.0';
$string['phpversionhelp'] = '<p>Moodle zahtijeva najmanju PHP verziju 4.1.0.</p>
<p>Trenutno pokreæete verziju $a</p>
<p>Možete nadograditi PHP ili premjestiti na glavnu sa novijom verzijom PHP!</p>';
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = 'Moodle može imati problema sa ukljuèenim safe mode-om';
$string['safemodehelp'] = '<p>Moodle može imati razlièite probleme sa ukljiuèenim safe modom, ni najmanji taj problem vjerovatno neæe dozvoliti kreiranje novih datoteka.</p>
   
<p>Safe mode je obièno jedino dozvoljen od paranoiène javnosti web gostiju, tako da Vi možete imati jednostavnu potražnju nove web gostujuæe kompanije za Vaš Moodle sajt.</p>
   
<p>Možete pokušati nastaviti sa instalacijom ako želite, ali oèekujte nekoliko problema kasnije.</p>';
$string['sessionautostart'] = 'Automatski poèetak akcije';
$string['sessionautostarterror'] = 'Ovo bi trebalo biti iskljuèeno';
$string['sessionautostarthelp'] = '<p>Moodle zahtijeva podršku za postupanje i neæe funcionisati bez toga.</p>

<p>Etapa u radu može biti dozvoljena u php.ini datoteci ... pogledajte za postupanje.auto_start parameter.</p>';
$string['wwwroot'] = 'Web adresa';
$string['wwwrooterror'] = 'Web adresa nije jasna da bi bila validna - ovom Moodle instalacija je nejasna da bila tu.';

?>
