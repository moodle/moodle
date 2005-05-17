<?PHP // $Id$ 
      // install.php - created with Moodle 1.4.4 + (2004083140)


$string['admindirerror'] = 'Valitud adminni kataloog on vale';
$string['admindirname'] = 'Administraatori kataloog';
$string['admindirsetting'] = 'Väga vähesed administraatorid kasutavad spetsiaalset URL-i et saada ligipääs kontroll paneeli või mingisse muusse kohta. Kahjuks läheb see konflikti Moodle standartse asukohaga. Sa saad seda viga parandada kui nimetad oma adminni kataloogi ümber. Näiteks nagu: : <br /> <br /><b>moodleadmin</b><br /> <br /> See teeb adminni lingid Moodles korda';
$string['caution'] = 'Hoiatus';
$string['chooselanguage'] = 'Vali keel';
$string['compatibilitysettings'] = 'Kontrollin teie PHP sätteid ...';
$string['configfilenotwritten'] = 'Installatsiooni skript ei suutnud automaatselt tekitada config.php faili mis sisaldasid sinu valitud seadeid. Arvatavasti sellepärast ,et sinu Moodel kataloog ei ole kirjutatav. Sa saad manuaalselt kopeerida järgnevat koodi faili mille nimeks on config.php mis asub Moodle põhikataloogis';
$string['configfilewritten'] = 'config.php on edukalt loodud';
$string['configurationcomplete'] = 'Seadistamine lõpetatud';
$string['database'] = 'Andmekogu';
$string['databasesettings'] = 'Sa pead konfigureerima admebaasi kus suurem osa Moodle andmetest asub. See andmebaas peab juba olema loodud ja kasutajanimi ja parool peavad eksisteerima.
br />
<br /> <br />
<b>Type:</b> mysql or postgres7<br />
<b>Host:</b> eg localhost or db.isp.com<br />
<b>Name:</b> andmebaasi nimi, eg moodle<br />
<b>User:</b> sinu andmebaasi kasutajanimi<br />
<b>Password:</b> sinu andmebaasi parool<br />
<b>Tables Prefix:</b> optional prefix to use for all table names';
$string['dataroot'] = 'Andmete kataloog';
$string['datarooterror'] = 'Andme kataloog mis täpsustasid ei suudetud luua. Paranda teekond või tee ise manuaalselt';
$string['dbconnectionerror'] = 'Me ei suutnud sinu täpsustatud andmebaasi ühendada. Palun kontrollige oma andmebaasi seadeid';
$string['dbcreationerror'] = 'Andmebaasi loomise viga. Ei suudetud luua andmebaasi antud nimega ';
$string['dbhost'] = 'Pea Server';
$string['dbpass'] = 'Parool';
$string['dbprefix'] = 'Tabeli eesliide';
$string['dbtype'] = 'Tüüp';
$string['directorysettings'] = '<p>Palun kinnita Moodle installerimise asukoht.</p>

<p><b>Veebi aadress:</b>
Täpsuta veebi aadress kus Moodlel on läbipääsu
Kui sinu veebilehel on läbipääs läbi mitme URL aadressi siis kasuta seda mis on sinu õpilaste jaoks kõige kergem meeles pidada</p>

<p><b>Moodle Kataloog:</b>
Täpsusta kataloogi kogu teekond kuni installeerimiseni.
Tee kindlaks ,et suured/väiketähed oleksid õiged</p>

<p><b>Andmete kataloog:</b>
Sul on vaja kohta kus Moodle saab salvestada ülesse laetud failid. See kataloog peaks olem loetav JA KIRJUTATAV veebi serveri kasutaja poolt. Kuid sellel ei tohiks olla läbipääsu otseselt läbi veebi
</p>

';
$string['dirroot'] = 'Moodle kataloog';
$string['dirrooterror'] = 'Moodle Kataloogi seaded paistavad olevat valed. Me ei suuda Moodle installerimist siit leida. Väärtus on nullitud';
$string['download'] = 'Lae alla';
$string['fail'] = 'Fail';
$string['fileuploads'] = 'Failide üleslaadimine';
$string['fileuploadserror'] = 'See peaks olema sisse lülitatud';
$string['fileuploadshelp'] = '<p>Faili ülesse laadimine tundub olevat sinu serveris välja lülitatud.</p>

<p>Moodlet võib ikka installeerida kui ilma selle võimaluseta. Te ei saa kursuse faile ülesse laadida</p>

<p>Ülesselaadimise sisselülitamiseks pead sa redigeerima main php.ini faili oma süsteemis ja vahetama seaded
<b>file_uploads</b> to \'1\'.</p>';
$string['gdversion'] = 'GD versioon';
$string['gdversionerror'] = 'GD teek ei tohiks olla esitatud piltide protsessimiseks ja loomiseks';
$string['gdversionhelp'] = '<p>Sinu serveril ei paist GD installeeritud olevat.</p>

<p>GD on andmeteek mis on vajalik PHP-le selleks et ta lubaks Moodlel protsessida pilte. (Selliseid nagu profiili ikoone) ja luua uusi pilte ( nagu graafika logi) Moodle tõõtab ikka ilma GD-ta aha need võimalused oleksid teil välja lülitatud.</p>

<p>GD lisamine PHP-le Unixi all, kompileeri PHP-d kasutates --with-gd parameetrit.</p>';
$string['installation'] = 'Installeerimine';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'See peaks olema välja lülitatud';
$string['magicquotesruntimehelp'] = '<p>Magic quotes runtime peaks olema välja lülitatud ,et Moodle saaks korralikult funktsioneerida.</p>

<p>Tavalielt on see vaikimisi välja lülitatud. Vaata seadistusi <b>magic_quotes_runtime</b>  sinu php.ini file </p>

<p>Kui sul ei ole ligipääsu oma php.ini failile siis sa peaksid lisama järgmise koodi faili mille nimi on .htaccess mis asub sinu Moodle kataloogis:
<blockquote>php_value magic_quotes_runtime Off</blockquote>
</p>';
$string['memorylimit'] = 'Mälu limiit';
$string['memorylimiterror'] = 'PHP mälu limiit on pandud päris madalale .... hiljem võib sellega tekkida probleeme';
$string['memorylimithelp'] = '<p>PHP mälu limiit sinu serveris on hetkel $a.</p>

<p>See võib hiljem tekitada Moodlel mälu probleeme
</p>

<p>Me soovitame ,et sa konfigureeriksid PHP-d kõrgema limiidi peale, näiteks 16M. On mitmeid viise selle tegemiseks:</p>
<ol>
<li>kui võimalik siis kompileeri PHP uuesti <i>--enable-memory-limit</i>.

See lubab Moodlel ise määrata mälu limiiti.</li>
<li>Kui sul on läbipäaas oma php.ini failile siis saa saad muuta  <b>mälu limiiti</b> sealt. Kui sul ei ole läbipääsu siis sa võid administraatorilt abi paluda
</li>
<li>Mõnedel PHP serveritel sa saad tekitada  .htaccess faili oma Moodle kataloogi mis sisaldaks seda koodi:<p><blockquote>php_value memory_limit 16M</blockquote></p>
<p>Kuigi mõnedel serveritel ei pruugi see töödata 
(Sa näed vigu kui vaatad lehti) Siis sa pead eemaldama selle .htaccess faili.</p></li>
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'PHP-d ei ole õieti MySQL-ga konfigureeritud. Palun kontrollige oma php.ini faili';
$string['pass'] = 'Korras';
$string['phpversion'] = 'PHP versioon';
$string['phpversionerror'] = 'PHP versioon peab olema vähemalt 4.1.0';
$string['phpversionhelp'] = '<p>Moodle vajab vähemalt 4.1.0 php versiooni</p>
<p>Sinu jooksev versioon on $a</p>
<p>Sa pead oma PHP-d uuendama!</p>';
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = 'Moodle\'l võib tekkida safe mode\'s komplikatsioone';
$string['safemodehelp'] = '<p>Moodle võib olla mitmesuguseid probleeme kui safe mood on sisse lülitatud. Ta ei pruugi lubada luua uusi faile.</p>

<p>Safe mood on tavaliselt sisse lülitatud paranoiliste veebi peremeeste poolt seega sa pead leidma endale uue veebi teenuse pakkuja </p>

<p>Sa võid proovida installeerimist jätkata kui soovid aga arvatavasti tekib sul hiljem probleeme</p>
';
$string['sessionautostart'] = 'Session Auto Start';
$string['sessionautostarterror'] = 'See peaks olema välja lülitatud';
$string['sessionautostarthelp'] = '<p>Moodle vahab sessiooni tuge ja ei tööta ilma selleta.</p>

<p>Sessioone saab sisse lülitada php.ini failist.</p>';
$string['wwwroot'] = 'Veebi aadress';
$string['wwwrooterror'] = 'Veebi aadress ei paista olevat õige. Moodle installatsioon ei paista olevat seal';

?>
