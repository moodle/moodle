<?PHP // $Id$ 
      // install.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2004101900)


$string['admindirerror'] = 'Yll‰pito hakemisto on m‰‰ritetty v‰‰rin';
$string['admindirname'] = 'Yll‰pito hakemisto';
$string['admindirsetting'] = 'Jotkut webpalvelut k‰ytt‰v‰t /admin hakemistoa yll‰pitotarkoituksiin tms. Valitettavasti t‰m‰ on ristiriidassa Moodlen yll‰pitosivujen normaalin paikan kanssa. Voit korjata t‰m‰n nime‰m‰ll‰ asennuksesi yll‰pitohakemiston uudelleen, ja laittamalla uuden nimen t‰h‰n. Esimerkiksi: 
<br/> <br /><b>moodleadmin</b><br /> <br />
T‰m‰ korjaa yll‰pito linkit Moodlessa.';
$string['caution'] = 'Varoitus';
$string['chooselanguage'] = 'Valitse kieli';
$string['compatibilitysettings'] = 'Tarkistetaan PHP:n asetukset';
$string['configfilenotwritten'] = 'Asennus ei pystynyt luomaan automaattisesti config.php tiedostoa, joka olisi sis‰lt‰nyt valitsemasi asetukset, todenn‰kˆisesti koska Moodlen hakemisto on kirjoitussuojattu. Voit manuaalisesti kopioida seuraavan koodin tiedostoon nimelt‰ config.php Moodlen p‰‰hakemiston sis‰ll‰.';
$string['configfilewritten'] = 'config.php on luotu.';
$string['configurationcomplete'] = 'Asetukset suoritettu';
$string['database'] = 'Tietokanta';
$string['databasesettings'] = 'Nyt sinun t‰ytyy valita tietokanta, miss‰ suurin osa Moodlen tiedoista s‰ilytet‰‰n. T‰m‰n tietokannan t‰ytyy jo valmiiksi olla luotu, kuten myˆs k‰ytt‰j‰nimen ja salasanan, joilla siihen p‰‰st‰‰n. .<br/>
<br /> <br />
<b>Tyyppi:</b> mysql or postgres7<br />
<b>Is‰nt‰:</b> localhost or db.isp.com<br />
<b>Nimi:</b> tietokannan nimi, eg moodle<br />
<b>K‰ytt‰j‰:</b> tietokantasi k‰ytt‰j‰nimi<br />
<b>Salasana:</b> tietokantasi salasana<br />
<b>Taulukon etuliite:</b> omavalintainen etuliite jota k‰ytet‰‰n kaikissa taulukoissa ';
$string['dataroot'] = 'Datahakemisto';
$string['datarooterror'] = '\"Datahakemistoa\", jonka m‰‰rittelit, ei voitu lˆyt‰‰, eik‰ luoda. Joko korjaa polku, tai luo hakemisto manuaalisesti.';
$string['dbconnectionerror'] = 'Emme pystyneet kytkeytym‰‰n tiedokantaan, jonka m‰‰rittelit. Tarkista tietokanta asetuksesi.';
$string['dbcreationerror'] = 'Tietokannan luomisvirhe. Ei pystytty luomaan annettua tietokannan nime‰ tarjotuilla asetuksilla.';
$string['dbhost'] = 'Is‰nt‰ palvelin';
$string['dbpass'] = 'salasana';
$string['dbprefix'] = 'taulukon etumerkki';
$string['dbtype'] = 'Tyyppi';
$string['directorysettings'] = '<p>Varmista t‰m‰n Moodle asennuksen paikka.</p>

<p><b>Web-osoite:</b>
T‰smenn‰ koko Web osoite, johon Moodlella on p‰‰sy.
Jos websivustoosi p‰‰st‰‰n monen URL:n kautta, valitse kaikkein luonnollisin vaihtoehto, se jota oppilaasikin k‰ytt‰isiv‰t. ƒl‰ sis‰llyt‰ kenoviivaa.</p>

<p><b>Moodle hakemisto:</b>
M‰‰rit‰ koko hakemistopolku t‰h‰n asennukseen. Varmista, ett‰ isot/pienet kirjaimet ovat oikein.</p>

<p><b>Data hakemisto:</b>
Tarvitset paikan, jonne Moodle voi tallentaa ladatut tiedostot. T‰m‰n hakemiston pit‰isi olla luettavissa ja kirjoitettavissa web palvelin k‰ytt‰j‰n taholta (usein \"nobody\" tai \"apache\"), mutta sen ei pit‰isi olla k‰ytett‰viss‰ suoraan web:in kautta.</p>';
$string['dirroot'] = 'Moodle hakemisto';
$string['dirrooterror'] = '\"Moodle hakemisto\" asetus n‰ytt‰isi olevan v‰‰r‰-emme voi lˆyt‰‰ Moodle asennusta sielt‰. Arvo alapuolella on nollattu.';
$string['download'] = 'Lataus';
$string['fail'] = 'Virhe';
$string['fileuploads'] = 'tiedostojen palvelimeen kopioinnit';
$string['fileuploadserror'] = 'T‰m‰n pit‰isi olla p‰‰ll‰';
$string['fileuploadshelp'] = '<p>Tiedostojen lataus n‰ytt‰‰ olevan tehty toimimattomaksi palvelimellasi.</p>

<p>Moodle voidaan silti asentaa, mutta ilman t‰t‰ kyky‰, et pysty lataamaan kurssitiedostoja tai uuden k‰ytt‰j‰n profiili kuvia.

<p>Mahdollistaaksesi tiedostojen latauksen sinun (tai systeemisi yll‰pit‰j‰n) t‰ytyy muokata varusohjelmien php.ini tiedosto systeemiisi ja muuttaa asetus <b>file_uploads</b> to \'1\'.</p>';
$string['gdversion'] = 'GD versio';
$string['gdversionerror'] = 'GD kirjaston pit‰isi olla p‰‰ll‰, ett‰ voidaan k‰sitell‰ ja luoda kuvia.';
$string['gdversionhelp'] = '<p>Palvelimellasi ei n‰ytt‰isi olevan GD:t‰ asennettuna.</p>

<p>GD on kirjasto jonka PHP vaatii voidakseen antaa Moodlen k‰sitell‰ kuvia (esimerkiksi k‰ytt‰j‰profiili kuvakkeita) ja luoda uusia kuvia (esimerkiksi kirjauskuvioita) Moodle toimii ilman GD:t‰kin, mutta silloin n‰m‰ toiminnot eiv‰t ole saatavilla.</p>

<p>Lis‰t‰ksesi GD:n PHP:hen Unix:in alaisena, k‰‰nn‰ PHP k‰ytt‰en --with-gd parametria.</p>

<p>Windowsin alaisena voit yleens‰ muokata php.ini:‰ ja olla kommentoimatta rivivertailua libgd.dll.</p>';
$string['installation'] = 'asennus';
$string['magicquotesruntime'] = 'Magic quotes ajoaika';
$string['magicquotesruntimeerror'] = 'T‰m‰n pit‰isi olla kiinni';
$string['magicquotesruntimehelp'] = '<p>Magic quotes ajoajan pit‰isi olla pois p‰‰lt‰, jotta Moodle voi toimia kunnolla.</p>

<p>Normaalisti se on pois p‰‰lt‰ oletuksena... Katso asetukset
<b>magic_quotes_runtime</b> in your php.ini file.</p>

<p>Jos sinulla ei ole p‰‰sy‰ php.ini:isi, saatat pysty‰ asettamaan seuraavan rivin tiedostoon nimelt‰ .htaccess Moodlen hakemiston sis‰ll‰:
<blockquote>php_value magic_quotes_runtime Off</blockquote>
</p> ';
$string['memorylimit'] = 'Muistiraja';
$string['memorylimiterror'] = 'PHP muistiraja on asetettu aika alas... Se saattaa aiheuttaa ongelmia myˆhemmin.';
$string['memorylimithelp'] = '<p>PHP muistiraja palvelimellesi on t‰ll‰ hetkell‰ asetettu $a:han.</p>

<p>T‰m‰ saattaa aiheuttaa Moodlelle muistiongelmia myˆhemmin, varsinkin jos sinulla on paljon mahdollisia moduuleita ja/tai paljon k‰ytt‰ji‰.

<p>Suosittelemme, ett‰ valitset asetuksiksi PHP:n korkeimmalla mahdollisella raja-arvolla, esimerkiksi 16M.
On olemassa monia tapoja joilla voit yritt‰‰ tehd‰ t‰m‰n:
<ol>
<li>Jos pystyt, uudelleenk‰‰nn‰ PHP <i>--enable-memory-limit</i>. :ll‰.
T‰m‰ sallii Moodlen asettaa muistirajan itse.
<li>Jos sinulla on p‰‰sy php.ini tiedostoosi, voit muuttaa <b>memory_limit</b> setuksen siell‰ johonkin kuten 16M. Jos sinulla ei ole p‰‰syoikeutta, voit kenties pyyt‰‰ yll‰pit‰j‰‰ tekem‰‰n t‰m‰n puolestasi.
<li>Joillain PHP palvelimilla voit luoda a .htaccess tiedoston Moodle hakemistossa, sis‰lt‰en t‰m‰n rivin:
<p><blockquote>php_value memory_limit 16M</blockquote></p>
<p>Kuitenkin, joillain palvelimilla t‰m‰ est‰‰  <b>kaikkia</b> PHP sivuja toimimasta (n‰et virheet, kun katsot sivuja), joten sinun t‰ytyy poistaa .htaccess tiedosto.
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'PHP:t‰ ei ole kunnolla valittu asetukseksi MySQL laajennuksen kanssa, jotta se voisi kommunikoida MySQL:n kanssa. Tarkista php.ini tiedostosi tai k‰‰nn‰ PHP uudelleen.';
$string['pass'] = 'Tarkastettu';
$string['phpversion'] = 'PHP versio';
$string['phpversionerror'] = 'PHP version t‰ytyy olla v‰hint‰‰n 4.1.0';
$string['phpversionhelp'] = '<p>Moodle vaatii v‰hint‰‰n PHP version 4.1.0.</p>
<p>K‰yt‰t parhaillaan versiota $a<p>
<p>Sinun t‰ytyy p‰ivitt‰‰ PHP tai siirt‰‰ is‰nt‰ uudemman PHP version kanssa!<p>';
$string['safemode'] = 'turvatila';
$string['safemodeerror'] = 'Moodlella saattaa olla ongelmia turvatilan ollessa p‰‰ll‰';
$string['safemodehelp'] = '<p>Moodlella saattaa olla lukuisia ongelmia turvatilan ollessa p‰‰ll‰, joista v‰h‰iosin ei ole se, ettei se todenn‰kˆisesti pysty luomaan uusia tiedostoja.</p> 
<p>Turvatila on yleens‰ tehnyt toimivaksi paranoidinen julkinen web-is‰nt‰, joten sinun ehk‰ t‰ytyy vaihtaa web-is‰nnˆinti yhtiˆt‰ Moodleasi varten.</p>

<p>Voit yritt‰‰ jatkaa asennusta, mutta varaudu ongelmiin myˆhemmin.</p>';
$string['sessionautostart'] = 'istunnon automaattinen aloitus';
$string['sessionautostarterror'] = 'T‰m‰n pit‰isi olla pois p‰‰lt‰';
$string['sessionautostarthelp'] = '<p>Moodle vaatii istuntotukea, eik‰ toimi ilman sit‰.</p>

<p>istunto voidaan mahdollistaa php.ini tiedostossa... Etsi istuntoa varten.auto_start parameter.</p>';
$string['thischarset'] = 'iso-8859-1';
$string['thisdirection'] = 'ltr';
$string['thislanguage'] = 'Suomi';
$string['wwwroot'] = 'Web-osoite';
$string['wwwrooterror'] = 'Web-osoite ei n‰ytt‰isi olevan voimassa- t‰m‰ Moodle asennus ei n‰ytt‰isi olevan siell‰.';

?>
