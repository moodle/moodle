<?PHP // $Id$ 
      // install.php - created with Moodle 1.6 development (2005060201)


$string['admindirerror'] = 'De adminmap die je opgeeft is niet juist';
$string['admindirname'] = 'Adminmap';
$string['admindirsetting'] = 'Enkele webhosts gebruiken /admin als speciale URL  om je toegang te geven tot een controlepaneel of iets dergelijks. Jammer genoeg geeft dit conflicten met de standaardmap voor de Moodle-beheerpagina\'s. Je kunt dit toch aan het werk krijgen door de adminmap van jouw installatie te hernoemen en deze nieuwe mapnaam hier te zetten. Bijvoorbeeld <br /> <br /><b>moodleadmin</b><br /> <br />. Dit zal alle beheerlinks in Moodle aanpassen.';
$string['caution'] = 'Opgelet';
$string['chooselanguage'] = 'Kies een taal';
$string['compatibilitysettings'] = 'Bezig je PHP-instellingen te controleren ...';
$string['configfilenotwritten'] = 'Het installatiescript kon het bestand config.php met jouw gekozen instellingen niet automatisch aanmaken.  Kopieer de volgende code in een bestand dat je config.php noemt en plaats dat in de rootmap van Moodle.';
$string['configfilewritten'] = 'Het maken van config.php is gelukt';
$string['configurationcomplete'] = 'De configuratie is volledig';
$string['database'] = 'Databank';
$string['databasecreationsettings'] = 'Nu moet je de databank configureren waar de meeste gegevens van Moodle bewaard zullen worden. Deze databank zal automatisch gecreëerd worden door de Moodle4Windows installatietechnologie met de onderstaande instellingen.<br />
<br /> <br />
<b>Type:</b> vastgezet op \"mysql\" door de installatie.<br />
<b>Host:</b> vastgezet op \"localhost\" door de installatie.<br />
<b>Naam:</b> naam voor de databank, bijvoorbeeld moodle<br />
<b>Gebruiker:</b> vastgezet op \"root\" door de installatie.<br />
<b>Wachtwoord:</b> jouw wachtwoord voor de databank.<br />
<b>Tabelvoorvoegsel:</b> optionneel voorvoegsel om de naam van alle tabellen mee te beginnen.';
$string['databasesettings'] = 'Nu moet je de databank voor de gegevens van Moodle configureren. Deze databank zou je al aangemaakt moeten hebben, samen met een gebruikersnaam en wachtwoord voor toegang tot die databank.<br />
<br /> <br />
<b>Type:</b> mysql of postgres7<br />
<b>Host Server:</b> bv localhost of db.isp.com<br />
<b>Naam:</b> databanknaam, bv moodle<br />
<b>Gebruiker: de gebruikersnaam voor je databank<br />
<b>Wachtwoord:</b> het wachtwoord voor je databank<br />
<b>Tabelvoorvoegsel:</b> een voorvoegsel dat je wil gebruiken voor alle tabelnamen';
$string['dataroot'] = 'Gegevens';
$string['datarooterror'] = 'De \'data-map\' die je opgaf kon niet gevonden of gemaakt worden. Verbeter ofwel het pad of maak die map manueel.';
$string['dbconnectionerror'] = 'We konden geen verbinding maken met de databank die je opgegeven hebt. Controleer je databankinstellingen';
$string['dbcreationerror'] = 'Probleem met het opbouwen van de databank. De databanknaam kon niet aangemaakt worden met de gegevens die je opgegeven hebt';
$string['dbhost'] = 'Hostserver';
$string['dbpass'] = 'Wachtwoord';
$string['dbprefix'] = 'Tabelvoorvoegsel';
$string['dbtype'] = 'Type';
$string['directorysettings'] = '<p>Bevestig de verschillende lokaties voor deze Moodle-installatie.</p>

<p><b>Webadres:</b>
Geef hier het volledige webadres op langswaar je toegang tot Moodle geeft. Als je website vanaf verschillende URL\'s toegankelijk is, kies dan diegene die je leerlingen zullen gebruiken. Voeg achteraan het adres geen schuine streep toe.</p>

<p><b>Moodle-map</b>
Geef het volledige fysieke pad van het besturingssysteem naar diezelfde lokatie. Let op dat je hoofdletters en kleine letters juist zet.</p>

<p><b>Data-map:</b>
Je moet een plaats voorzien waar Moodle geüploade bestanden kan plaatsen. Deze map moet leesbaar EN BESCHRIJFBAAR zijn door de webserver (meestal gebruiker \'nobody\' of \'apache\'), maar ze mag niet rechtstreeks leesbaar zijn vanaf het internet.</p>';
$string['dirroot'] = 'Moodle-map';
$string['dirrooterror'] = 'De instelling voor \'Moodle-map\' was niet juist - we kunnen daar geen Moodle-installatie vinden. Onderstaande waarde is gereset.';
$string['download'] = 'Download';
$string['fail'] = 'Niet OK';
$string['fileuploads'] = 'Bestanden uploaden';
$string['fileuploadserror'] = 'Dit moet ingeschakeld zijn';
$string['fileuploadshelp'] = '<p>Het lijkt er op dat het uploaden van bestanden uitgeschakeld is op jouw server.</p>
<p>Moodle kan verder geïnstalleerd worden, maar zonder deze mogelijkheid zul je geen cursusmateriaal of afbeeldingen voor de profielen van je gebruikers kunnen uploaden.</p>
<p>Om het uploaden van bestanden in te schakelen moet je (of je systeembeheerder) php.ini op je systeem bewerken en volgende instelling wijzigen:
<b>file_uploads</b> op \'1\' zetten.</p>';
$string['gdversion'] = 'GD-versie';
$string['gdversionerror'] = 'De GD-bibliotheek moet geïnstalleerd zijn om afbeeldingen te kunnen maken en verwerken';
$string['gdversionhelp'] = '<p>Blijkbaar is GD niet geïnstalleerd op je server.</p>
<p>PHP heeft de GD-bibliotheek nodig om afbeeldingen te kunnen maken (zoals de grafieken van de logbestanden) en te verwerken (zoals de profielbestanden van de gebruikers). Moodle zal werken zonder GD - alleen deze mogelijkheden zullen het niet doen.</p>
<p>Om GD toe te voegen aan PHP op een Unixmachine moet je PHP compileren met de --with-gd parameter.</p>
<p>Onder Windows kun je gewoonlijk php.ini bewerken en de commentaartekens voor de lijn met libgd.dll verwijderen.</p>';
$string['installation'] = 'Installatie';
$string['magicquotesruntime'] = 'Magic Quotes runtime';
$string['magicquotesruntimeerror'] = 'Dit moet uitgeschakeld zijn';
$string['magicquotesruntimehelp'] = '<p>Magic Quotes runtime moet uitgeschakeld zijn om Moodle goed te laten functioneren.</p>
<p>Normaal staat het af als standaardinstelling ... zie de instelling <b>magic_quotes_runtime</b> in je php.ini-bestand.</p>
<p>Als je geen toegang hebt tot php.ini, dan kun je proberen om onderstaande lijn in een bestand te zetten dat je .htaccess noemt en dat dan in je Moodle-map plaatsen: <blockquote>php_value magic_quotes_runtime Off</blockquote></p>';
$string['memorylimit'] = 'Geheugenlimiet';
$string['memorylimiterror'] = 'De PHP-geheugenlimiet staat eerder laag ingesteld ...  je zou hierdoor later problemen kunnen krijgen.';
$string['memorylimithelp'] = '<p>De PHP-geheugenlimiet van je server is ingesteld op $a.</p>
<p>Hierdoor kan Moodle geheugenproblemen krijgen, vooral als je veel modules installeert en/of veel gebruikers hebt.</p>

<p>We raden je aan PHP met een hogere geheugenlimiet te configureren indien mogelijk, bijvoorbeeld 16Mb. Er zijn verschillende mogelijkheden om dat te doen. Je kunt proberen:
<ol>
<li>Indien je kunt PHP hercompileren met <i>--enable-memory-limit</i>.
Hierdoor kan Moodle zelf zijn geheugenlimiet instellen.
<li>Als je toegang hebt tot het php.ini-bestand, kun je de <b>memory_limit</b>-instelling veranderen naar bv 16Mb. Als je geen toegang hebt kun je je systeembeheerder vragen dit voor je te wijzigen.</li>
<li>Op sommige PHP-servers kun je een .htaccess-bestand maken in de Moodle-map met volgende lijn: <p><blockquote>php_value memory_limit 16M</blockquote></p>
<p>Opgelet: op sommige servers zal dit verhinderen dat <b>alle</b> PHP-bestanden uitgevoerd worden. (je zult foutmeldingen zien wanneer je naar php-pagina\'s kijkt) Je zult dan het .htaccess-bestand moeten verwijderen.</li>
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'PHP is niet goed geconfigureerd met de MySQL-extentie om met MySQL te communiceren. Controleer je php.ini-bestand of hercompileer PHP.';
$string['pass'] = 'OK';
$string['phpversion'] = 'PHP-versie';
$string['phpversionerror'] = 'PHP-versie moet minstens 4.1.0 zijn';
$string['phpversionhelp'] = '<p>Moodle heeft minstens PHP-versie 4.1.0 nodig.</p> <p>De huidige versie op je server is $a</p>
<p>Je moet PHP upgraden of verhuizen naar een host met een nieuwere versie van PHP!</p>';
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = 'Moodle kan bestanden niet juist behandelen met safe mode ingeschakeld';
$string['safemodehelp'] = '<p>Moodle zal heel wat problemen vertonen met safe mode ingeschakeld, waaronder bijvoorbeeld het niet kunnen aanmaken van nieuwe bestanden.</p>
<p>Safe mode is gewoonlijk alleen maar ingeschakeld bij paranoïde webhosts, je zult dus best op zoek gaan naar een nieuwe webhost voor je Moodlesite.</p>
<p>Je kunt proberen verder te gaan met de installatie als je dat wil, maar verwacht je wat verder door aan heel wat problemen.</p>';
$string['sessionautostart'] = 'Session Auto Start';
$string['sessionautostarterror'] = 'Dit moet uitgeschakeld zijn';
$string['sessionautostarthelp'] = '<p>Moodle heeft session support nodig en zal zonder niet werken.</p>
<p>Sessies kunnen ingeschakeld worden in het php.ini-bestand ... zoek naar de session.auto_start parameter.</p>';
$string['wwwroot'] = 'Web adres';
$string['wwwrooterror'] = 'Het webadres lijkt niet geldig te zijn - deze Moodle-installatie is blijkbaar niet op die plaats.';

?>
