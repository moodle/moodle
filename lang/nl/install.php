<?PHP // $Id$ 
      // install.php - created with Moodle 1.4 development (2004081200)


$string['admindirerror'] = 'De admin map die je opgeeft is niet juist';
$string['admindirname'] = 'Admin map';
$string['admindirsetting'] = '<p>Enkele webhosts gebruiken /admin als speciale URL  om je toegang te geven tot een controlepaneel of iets dergelijks. Jammer genoeg geeft dit conflicten met de standaardmap voor de Moodle beheerpagina\'s. Je kunt dit toch aan het werk krijgen door de adminmap van jouw installatie te hernoemen en deze nieuwe mapnaam hier te zetten. Bijvoorbeeld <blockquote>moodleadmin</blockquote>. Dit zal alle beheerslinks in Moodle aanpassen.</p>';
$string['chooselanguage'] = 'Kies een taal';
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
$string['wwwroot'] = 'www';
$string['wwwrooterror'] = 'De \'www\' instelling is niet juist';

?>
