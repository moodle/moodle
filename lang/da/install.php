<?PHP // $Id$ 
      // install.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2005033100)


$string['admindirerror'] = 'Det angivende admin biblioteket er forkert';
$string['admindirname'] = 'Adminbibliotek';
$string['admindirsetting'] = 'Nogle få web-hoteller bruger /admin som en speciel URL til at administrere web-hotellet. Det er et problem da moodle også bruger /admin som standard til administrationssiderne. Hvis det er så kan du omdøbe adminbiblioteket og så angive den nye sti til admin biblioteket her. For eksempel: <br/> <br /><b>moodleadmin</b><br /> <br />
Dette vil rette admin linkene i moodle.';
$string['caution'] = 'Pas på';
$string['chooselanguage'] = 'Vælg et sprog';
$string['compatibilitysettings'] = 'Kontrollere dine PHP indstillinger';
$string['configfilenotwritten'] = 'Installationsscriptet var ikke i stand til at oprette config.php filen der indeholder de valgte indstillinger, sandsynligvis fordi den bruger hvis apache(PHP) kører med (apache, nobody ell) ikke har rettigheder til at skrive til moodlebiblioteket. Du kan manuelt kopiere den følgende kode ind i en fil med navnet \"config.php\" i roden af moodle-biblioteket.';
$string['configfilewritten'] = 'config.php er blevet oprettet';
$string['configurationcomplete'] = 'Konfigurationen er færdig.';
$string['database'] = 'Database';
$string['databasesettings'] = 'Du skal nu konfigurere databasen hvor det meste af moodle\'s data vil blive gemt. Databaseserveren skal allerede være oprettet og du skal bruge brugernavn og password til en brugerkonto der har rettigheder til at oprette og hente data.<br/>
<br /> <br />
<b>Type:</b> mysql eller postgres7<br />
<b>Vært:</b> f.eks. localhost eller db.isp.com<br />
<b>Database:</b> database navn, f.eks. moodle<br />
<b>Bruger:</b> Brugernavnet til databasen<br />
<b>Password:</b> Password til databasebrugeren<br />
<b>Tabel Præfix:</b> Valgfrit fornavn der bliver sat foran alle tabelnavne hvis der er flere systemer der skal bruge samme database.';
$string['dataroot'] = 'DataBibliotek';
$string['datarooterror'] = 'DataBiblioteket du specificerede kan ikke findes eller oprettes. Ret stien til biblioteket eller opret det manuelt.';
$string['dbconnectionerror'] = 'Den angive database kunne ikke kontaktes. Kontroller eller ret venligst databaseinformationerne.';
$string['dbcreationerror'] = 'Fejl ved oprettelse af databasen. Kan ikke oprette den givne database med de angivne indstillinger.';
$string['dbhost'] = 'Værts Server';
$string['dbpass'] = 'Password';
$string['dbprefix'] = 'præfix for tabeller';
$string['dbtype'] = 'Type';
$string['directorysettings'] = '<p>Kontroller venligst installationsplaceringen af moodle.</p>

<p><b>Web adresse:<b>
Angiv den fulde web-adresse (URL) hvor moodle kan findes. Hvis sitet kan tilgås fra flere URL\'er så vælg den mest naturlige, den som de studerende oftest vil bruge. Der skal ikke være en skråstreg til sidst.</p>

<p><b>Moodle bibliotek:</b>
Angiv den fulde bibliotekssti til moodle-installationen. Stien er casesensitiv.</p>

<p><b>Moodle Databibliotek</b>
Det bibliotek hvor moodle kan gemme uploadede filer. Dette bibliotek skal være læsbar OG SKRIVBAR af den bruger apache kører under, ¨(typisk \'nobody\' eller \'apache\') men der bør ikke være direkte adgang til det fra webserveren.</p>';
$string['dirroot'] = 'Moodle bibliotek';
$string['dirrooterror'] = 'Det angivne moodle-bibliotek lader ikke til at være rigtigt - der kan ikke findes en Moodle-installation. Den nedenstående værdi er blevet fjernet.';
$string['download'] = 'Download';
$string['fail'] = 'Mislykkedes';
$string['fileuploads'] = 'Fil uploads';
$string['fileuploadserror'] = 'Denne skulle være aktiveret';
$string['fileuploadshelp'] = '<p>Fil-upload lader til at være slået fra på din server.</p>

<p>Moodle kan stadig installeres, men uden uploade kursusfiler og profilbilleder.</p>

<p>For at tillade filupload skal du (eller systemadministratoren) rette i php.ini for at ændre indstillingen for <b>fil_uploads</b> til \'1\'.</p>';
$string['gdversion'] = 'GD version';
$string['gdversionerror'] = 'GD library skal være tilgængelig for PHP for at der kan billeder kan manipuleres og oprettes.';
$string['gdversionhelp'] = '<p>Det lader til at din server ikke har GD installeret.</p>

<p>GD er et library som PHP bruger til at behandle billeder (såsom brugerprofil-billeder) og til at oprette nye billeder såsom loggrafer. Moodle kan stadig fungere uden GD - men disse funktioner vil så ikke være til rådighed.</p>

<p>For at tilføje GD på unix skal PHP kompileres med \'--with-gd\" parameteret.</p>

<p>Under windows er det normalt nok at udkommentere den linje i php.ini der referere til libgd.dll </p>';
$string['installation'] = 'Installation';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'Denne skulle være deaktiveret';
$string['magicquotesruntimehelp'] = '<p>\'Magic quotes runtime\' bør slås fra for at moodle kan fungere ordentligt.</p>

<p>Normalt er denne indstilling slået fra som standard. Den slås til og fra vha. indstillingen <b>\'magic_quotes_runtime\'</b> i din php.ini fil.</p>

<p>Hvis du ikke har adgang til webserverens php.ini fil kan du evt. lave en tekstfil, kalde den .htaccess og gemme den i moodlebiblioteket. Den skal indholde linjen. <blockquote>php_value magic_quotes_runtime Off</blockquote>
</p> ';
$string['memorylimit'] = 'Hukommelses begrænsning';
$string['memorylimiterror'] = 'Den tilgængelige hukommelse til PHP er ret lav... Det kan være at der opstår problemer senere.';
$string['memorylimithelp'] = '<p>Mængden af hukommelse som PHP kan bruge er sat til $a.</p>

<p>Dette kan forårsage at der opstår problemer senere, især hvis du har mange moduler installeret eller mange brugere.</p>

<p>Vi anbefaler at du konfigurere PHP til at kunne bruge mere hukommelse, f.eks. 16Mb. 
Der er flere måder hvorpå du kan rette det.</p>
<ol>
<li>Hvis du har mulighed for det kan du rekompilere PHP med <i>--enable-memory-limit</i>. 
Dette vil tillade at Moodle selv kan definere hvor meget hukommelse der er brug for.</li>
<li>Hvis du har adgang til php.ini filen kan du ændre <b>memory_limit</b> 
indstillingen så der er minimum 16Mb til rådighed. Hvis du ikke har direkte adgang til den kan du spørge systemadministratoren om han han vil gøre det for dig.</li>
<li>På nogle servere kan du oprette en \'.htaccess\' fil, gemme den i moodle biblioteket med linjen <p><blockquote>php_value memory_limit 16M</blockquote></p>
<p>Det kan dog på nogle servere forårsage en fejl på <b>alle</b> PHPsiderne. i så fald kan du blive nødt til at fjerne \'.htaccess\' filen igen.</p></li>
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'PHP er ikke blevet ordentligt konfigureret med MySQL  udvidelsen så den kan kommunikere med MySQL. Det kan skyldes at MySQL extension/dll ikke er loadet. Kontroller venlist phpinfo() og php.ini filen eller rekompiler PHP.';
$string['pass'] = 'OK';
$string['phpversion'] = 'PHP version';
$string['phpversionerror'] = 'PHP versionen skal være nyere end 4.1.0';
$string['phpversionhelp'] = '<p>Moodle kræver en PHP version der er nyere end 4.1.0.</p>
<p>Webserveren bruger i øjeblikket version $a</p>
<p>Du bliver nødt til at opdatere PHP eller flytte systemet over på en anden webserver der har en nyere version af PHP!</p>';
$string['safemode'] = 'Safe mode';
$string['safemodeerror'] = 'Moodle kan have problemer med \"Safe mode : on\"';
$string['safemodehelp'] = '<p>Moodle kan have flere problemer når \'safe mode\' er slået til, ikke mindst så kan systemet sansynligvis ikke oprette nye filer.</p>

<p>\'Safe Mode\' er oftest slået til hos paranoide offentlige webhoteller, så det kan være at du bliver nødt til at finde et andet webhotel til moodle.</p>

<p>Du kan godt fortsætte installationen, men der vil sansynligvis opstå probler senere.</p>';
$string['sessionautostart'] = 'Session autostart';
$string['sessionautostarterror'] = 'Denne skulle være deaktiveret';
$string['sessionautostarthelp'] = '<p>Moodle kræver at PHP understøtter sessions.</p>

<p>Sessions kan aktiveres i php.ini filen ... kik efter parameteret session.auto_start</p>';
$string['wwwroot'] = 'Web adresse';
$string['wwwrooterror'] = 'Webadressen lader ikke til at være korrekt - Moodleinstallationen kunne ikke findes der.';

?>
