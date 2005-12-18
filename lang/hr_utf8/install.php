<?PHP // $Id$ 
      // install.php - created with Moodle 1.5.2 + (2005060220)


$string['admindirsetting'] = '  Manji broj webhosting tvrtki koristi /admin kao posebni URL za Vaš pristup upravljanju vašim hosting paketom. Nažalost, time se događa konflikt sa standardnom lokacijom za Moodle administratorsku stranicu. Navedenu lokaciju unutar Moodle sustava možete preimenovati. Na primjer: <br /> <br /><b>moodleadmin</b><br /> <br />
Ovo će promijeniti administratorski link na Moodle sustavu u novu vrijednost.';
$string['caution'] = 'Oprez';
$string['chooselanguage'] = 'Odaberite jezik';
$string['compatibilitysettings'] = 'Provjeravanje vaših PHP postavki ...';
$string['configfilenotwritten'] = 'Instalacijska skripta nije bila u mogućnosti automatski kreirati datoteku naziva config.php koja bi sadržavala vaše odabrane postavke, vjerojatno zbog toga što nema prava na pisanje (mijenjanje sadržaja) u vašoj Moodle mapi. Ako zo želite, možete ručno kopirati kod u datoteku config.php u osnovnoj mapi vaše Moodle instalacije.';
$string['configfilewritten'] = 'config.php je uspješno kreiran';
$string['configurationcomplete'] = 'Konfiguracija završena';
$string['database'] = 'Baza podataka';
$string['databasecreationsettings'] = 'Sada biste trebali unijeti postavke baze podataka u koju će Moodle ubuduću pohranjivati većinu podataka. <br />
<br /> <br />
<b>Type:</b> fixed to \"mysql\" by the installer<br />
<b>Host:</b> fixed to \"localhost\" by the installer<br />
<b>Name:</b> database name, eg moodle<br />
<b>User:</b> fixed to \"root\" by the installer<br />
<b>Password:</b> your database password<br />
<b>Tables Prefix:</b> optional prefix to use for all table names';
$string['databasesettings'] = 'Sada biste trebali unijeti postavke baze podataka u koju će Moodle ubuduću pohranjivati većinu podataka. Ova baza podataka, kao i korisničko ime i lozinka za pristup istoj moraju biti prethodno kreirani.<br/>
    <br /> <br />
       <b>Tip:</b> mysql ili postgres7<br />
       <b>Poslužitelj:</b> npr. localhost ili ime.posluzitelja.hr<br />
       <b>Naziv:</b> ima baze podataka, npr. moodle<br />
       <b>Korisnik:</b> korisničko ime za pristup bazi podataka<br />
       <b>Lozinka:</b> lozinka za pristup bazi podataka<br />
       <b>Prefiks tablice:</b> opcionalni prefiks za imenovanje svih tablica povezanih s Moodle sustavom';
$string['dataroot'] = 'Mapa s podacima';
$string['datarooterror'] = 'The \'Data Directory\' you specified could not be found or created.  Either correct the path or create that directory manually.';
$string['dbconnectionerror'] = 'Nemoguće je uspostaviti vezu sa bazom podataka koju ste naveli. Molimo provjerite podatke koje ste unijeli.';
$string['dbcreationerror'] = 'Pogreška pri kreiranju baze podataka. Nije bilo moguće kreirati bazu navedenog imena uz zadane postavke';
$string['dbhost'] = 'Poslužitelj';
$string['dbpass'] = 'Lozinka';
$string['dbprefix'] = 'Prefiks tablice';
$string['dbtype'] = 'Tip';
$string['fileuploadserror'] = 'Ova opcija bi trebala biti uključena';
$string['gdversion'] = 'GD inačica';
$string['installation'] = 'Instalacija';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'Ova opcija bi trebala biti isključena';
$string['phpversion'] = 'PHP inačica';
$string['phpversionerror'] = 'PHP inačica mora biti bar 4.1.0';
$string['sessionautostarterror'] = 'Ova opcija bi trebala biti isključena';

?>
