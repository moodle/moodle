<?PHP // $Id$ 
      // install.php - created with Moodle 1.4 alpha (2004081500)


$string['PHPversion'] = 'PHP-versie';
$string['PHPversionerror'] = 'PHP-versie moet minstens versie 4.1.0 zijn';
$string['admindirerror'] = 'De admin map die je opgeeft is niet juist';
$string['admindirname'] = 'Admin map';
$string['admindirsetting'] = '<p>Enkele webhosts gebruiken /admin als speciale URL  om je toegang te geven tot een controlepaneel of iets dergelijks. Jammer genoeg geeft dit conflicten met de standaardmap voor de Moodle beheerpagina\'s. Je kunt dit toch aan het werk krijgen door de adminmap van jouw installatie te hernoemen en deze nieuwe mapnaam hier te zetten. Bijvoorbeeld <blockquote>moodleadmin</blockquote>. Dit zal alle beheerslinks in Moodle aanpassen.</p>';
$string['chooselanguage'] = 'Kies een taal';
$string['compatibilitysettings'] = 'Bezig je PHP-instellingen te controleren ...';
$string['configfilenotwritten'] = 'Het installatiescript kon het bestand config.php met jouw gekozen instellingen niet automatisch aanmaken.  Kopieer de volgende code in een bestand dat je config.php noemt en plaats dat in de rootmap van Moodle.';
$string['configfilewritten'] = 'Het maken van config.php is gelukt';
$string['configurationcomplete'] = 'De configuratie is volledig';
$string['database'] = 'Databank';
$string['databasesettings'] = '<p>Nu moet je de databank voor de gegevens van Moodle configureren. Deze databank zou je al aangemaakt moeten hebben, samen met een gebruikersnaam en wachtwoord voor toegang tot die databank.</p>
<p>Type: mysql of postgres7<br/>
Host Server: bv localhost of db.isp.com<br/>
Naam: databanknaam, bv moodle<br/>
Gebruiker: de gebruikersnaam voor je databank<br/>
Wachtwoord: het wachtwoord voor je databank<br/>
Tabelvoorvoegsel: een voorvoegsel dat je wil gebruiken voor alle tabelnamen</p>';
$string['dataroot'] = 'Gegevens';
$string['datarooterror'] = 'De instelling  voor \'Gegevens\' is niet juist';
$string['dbconnectionerror'] = 'Probleem met de connectie naar de databank. Controleer je databankinstellingen';
$string['dbcreationerror'] = 'Probleem met het opbouwen van de databank. De databanknaam kon niet aangemaakt worden met de gegevens die je ingegeven hebt';
$string['dbhost'] = 'Host server';
$string['dbpass'] = 'Wachtwoord';
$string['dbprefix'] = 'Tabelvoorvoegsel';
$string['dbtype'] = 'Type';
$string['directorysettings'] = '<p><b>WWW:</b>
Je moet Moodle laten weten waar het te vinden is. Geef het volledige webadres op waar Moodle geïnstalleerd is. Als je website vanaf meerdere URL\'s toegankelijk is, kies dan degene die je leerlingen zullen gebruiken. Voeg achteraan het adres geen schuine streep toe.</p>
<p><b>Map</b>
Geef het volledige fysieke pad van het besturingssysteem naar diezelfde lokatie. Let op dat je hoofdletters en kleine letters juist zet.</p>
<p><b>Data:</b>
Je moet een plaats voorzien waar Moodle geüploade bestanden kan plaatsen. Deze map moet leesbaar EN BESCHRIJFBAAR zijn door de webserver (meestal gebruiker \'nobody\' of \'apache\'), maar ze mag niet rechtstreeks leesbaar zijn vanaf het internet.</p>';
$string['dirroot'] = 'Map';
$string['dirrooterror'] = 'De \'map\' instelling was niet juist. Probeer volgende instelling';
$string['fail'] = 'Mislukt';
$string['fileuploads'] = 'Bestanden uploaden';
$string['fileuploadserror'] = 'Dit moet ingeschakeld zijn';
$string['fileuploadshelp'] = 'Voor de werking van Moodle is het nodig dat het uploaden van bestanden ingeschakeld is';
$string['gdversion'] = 'GD-versie';
$string['gdversionerror'] = 'De GD-bibliotheek moet geïnstalleerd zijn om afbeeldingen te kunnen maken en verwerken';
$string['gdversionhelp'] = 'De GD-bibliotheek moet geïnstalleerd zijn om afbeeldingen te kunnen maken en verwerken';
$string['installation'] = 'Installatie';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'Dit moet uitgeschakeld zijn';
$string['magicquotesruntimehelp'] = 'Magic quotes moet uitgeschakeld zijn';
$string['memorylimit'] = 'Geheugenlimiet';
$string['memorylimiterror'] = 'De PHP geheugenlimiet instelling moet minstens 16 MB of wijzigbaar zijn';
$string['memorylimithelp'] = 'De PHP geheugenlimiet instelling moet minstens 16 MB of wijzigbaar zijn. Je huidige geheugenlimiet is ingesteld op $a.<p>Je kunt je geheugenlimiet wijzigen in je php.ini bestand, soms ook door een .htaccess-bestand te maken in de Moodlemap met de lijn <p><ul>php_value memory_limit 16M</ul>';
$string['pass'] = 'OK';
$string['phpversionhelp'] = 'De PHP-versie voor moodle moet minstens 4.1.0 zijn. Je huidige versie is $a';
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = 'Moodle kan bestanden niet juist behandelen met safe mode ingeschakeld';
$string['safemodehelp'] = 'Moodle kan bestanden niet juist behandelen met safe mode ingeschakeld';
$string['sessionautostart'] = 'Session Auto Start';
$string['sessionautostarterror'] = 'Dit moet uitgeschakeld zijn';
$string['sessionautostarthelp'] = 'Session auto start moet uitgeschakeld zijn';
$string['sessionsavepath'] = 'Session Save Path';
$string['sessionsavepatherror'] = 'Het lijkt er op dat je server geen sessions ondersteund';
$string['sessionsavepathhelp'] = 'Moodle heeft ondersteuning voor sessions nodig';
$string['wwwroot'] = 'www';
$string['wwwrooterror'] = 'De \'www\' instelling is niet juist';

?>
