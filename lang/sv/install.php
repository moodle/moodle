<?PHP // $Id$ 
      // install.php - created with Moodle 1.3.2 (2004052502)


$string['admindirerror'] = 'Den katalog för administration som är angiven är felaktig';
$string['admindirname'] = 'Katalog för administration';
$string['admindirsetting'] = 'Ett litet fåtal webbvärdar (t ex hotell) använder /admin som en speciell URL som Du får tillgång till för att kunna använda en kontrollpanel e d. Tyvärr så stämmer detta inte så bra överens med standardplaceringen av Moodles sidor för administration. Du kan ordna till det genom att döpa om admin katalogen i Din installation och skriva in detta nya namn här. Till exempel: <br/> <br /><b>moodleadmin</b><br /> <br /> Detta kommer att rätta till länkarna till admin i Moodle';
$string['caution'] = 'Varning';
$string['chooselanguage'] = 'Välj ett språk';
$string['compatibilitysettings'] = 'Kontrollerar Dina PHP-inställningar';
$string['configfilenotwritten'] = 'Skriptet för installationen kunde inte automatiskt skapa en config.php som innehåller de inställningar som Du har valt. Var snäll och kopiera den följande koden till en fil med namnet config.php i Moodles \"root\"-katalog.';
$string['configfilewritten'] = 'config.php har skapats framgångsrikt';
$string['configurationcomplete'] = 'Konfigurationen är  genomförd';
$string['database'] = 'Databas';
$string['databasesettings'] = 'Nu behöver Du konfigurera den databas där det mesta av Moodles data kommer att sparas. Den här databasen måste redan vara skapad och det måste ingå ett användarnamn och ett lösenord som Du kan använda.<br />
<br /> <br />
<b>Typ:</b> mysql eller postgres7<br />
<b>Värd:</b> t ex localhost eller db.isp.com<br />
<b>Namn:</b> namn på databasen, t ex moodle<br />
<b>Användare:</b> Ditt användarnamn för tillgång till databasen<br />
<b>Lösenord:</b> Ditt lösenord för tillgång till databasen<br />
<b>Prefix för tabeller:</b> ett valfritt prefix som kopplas till alla namn på tabeller
';
$string['dataroot'] = 'katalog för data';
$string['datarooterror'] = 'Den \"katalog för data\" som Du har angivit gick inte att hitta eller skapa. Du får antingen korrigera sökvägen eller skapa katalogen manuellt.';
$string['dbconnectionerror'] = 'Det gick inte att ansluta till den databas som Du har angivit. Var snäll och kontrollera inställningarna till Din databas.';
$string['dbcreationerror'] = 'Fel (error) när databasen skulle skapas. Det gick tyvärr inte att skapa det namn (och med de inställningar) på databasen som Du har angivit ';
$string['dbhost'] = 'Värdserver';
$string['dbpass'] = 'Lösenord';
$string['dbprefix'] = 'Prefix för tabeller';
$string['dbtype'] = 'Typ';
$string['directorysettings'] = '<p>Var snäll och bekräfta placeringarna av denna installation av Moodle</p>
<p><b>Webbadress</b>
Ange den fullständiga adressen till Moodle. Om Din webbplats går att nå via flerfaldiga (ett antal olika) URL:er så bör Du välja den som är mest naturlig för Dina användare (studenter etc).
Ta inte inte med något avslutande vänsterslutat snedstreck \"/\".</p>

<p><b>Katalogen för Moodle</b>
Ange den fullständiga sökvägen till den här installationen. Kontrollera att det stämmer med sådant som är skiftlägeskänsligt (stor/liten bokstav).
</p>
<p><b>Katalogen för data</b>
Du behöver ett utrymme där Moodle kan spara uppladdade filer. Till denna katalog bör det finnas läs- OCH SKRIV-rättigheter för användaren av webbservern (vanligtvis \'nobody\' eller  \'apache\') men katalogen bör inte vara tillgänglig direkt via webben. 
';
$string['dirroot'] = 'Katalogen för Moodle';
$string['dirrooterror'] = 'Inställningarna för \"Katalogen för Moodle\" tycks vara felaktiga - det går inte att hitta någon installation av Moodle där. Värdet här nedan har återställts.';
$string['download'] = 'Ladda ner';
$string['fail'] = 'Misslyckas';
$string['fileuploads'] = 'Uppladdningar av filer';
$string['fileuploadserror'] = 'Detta bör vara aktiverat (on)';
$string['fileuploadshelp'] = '<p>Uppladdning av filer verkar vara avaktiverat på Din server.</p>
<p>Det kan fortfarande vara så att Moodle är installerat, men utan denna funktionalitet så kommer Du inte att kunna ladda upp kursfiler eller nya bilder till användarprofilerna. </p>
<p>För att aktivera uppladdning av filer så måste Du (eller Din systemadministratör) redigera den övergripande php.ini-filen på Ert system och ändra inställningen för <b>uppladdning av filer (file uploads)</b> till \'1\'.</p>';
$string['gdversion'] = 'GD version';
$string['gdversionerror'] = 'GD biblioteket bör vara tillgängligt för att Du ska kunna bearbeta och skapa bilder. ';
$string['gdversionhelp'] = '<p>Det verkar som om GD inte är installerat på Din server inte. </p>
<p>GD är ett bibliotek som är nödvändigt i PHP om Moodle ska kunna bearbeta bilder (som t ex bilderna i användarprofilerna) eller skapa nya (som t ex graferna till loggarna). Moodle kommer fortfarande att fungera utan GD men dessa funktioner kommer alltså att saknas. </p>
<p>Om Du vill lägga till GD under UNIX, så får Du kompilera PHP genom att använda parametern --with-gd.</p>
<p>Under Windows kan Du vanligtvis redigera php.ini och ta bort kommentarmarkeringen för den rad som refererar till libgd.dll</p>';
$string['installation'] = 'Installation';
$string['magicquotesruntime'] = 'körtid för \'Magiska citat\'';
$string['magicquotesruntimeerror'] = 'Det här bör vara \'off\'';
$string['magicquotesruntimehelp'] = '<p>Körtid för \'Magiska citat\' (Magic quotes runtime) bör vara inställt till \'off\' för att Moodle ska fungera korrekt</p>
<p>Som standard är det normalt sett inställt till \'off\'... Kontrollera inställningen för \'Magic quotes runtime\' i Din php.ini-fil.</p>
<p>Om Du inte har tillgång till Din php.ini-fil så kanske Du kan lägga in följande rad i en fil som heter .htaccess i Din katalog för Moodle:<blockquote>php_value magic_quotes_runtime Off</blockquote>
</p>';
$string['memorylimit'] = 'Minnesbegränsning';
$string['memorylimiterror'] = 'Minnesbegränsningen för PHP på Din server är f n inställt till ett ganska lågt värde... Detta kan leda till problem senare.';
$string['memorylimithelp'] = '<p>Minnesbegränsningen för PHP på Din server är f n inställt till $a.</p>
<p>Detta kan förorsaka att Moodle får minnesproblem senare, särskilt om Du har aktiverat många moduler och/eller har många användare.</p>
<p>Vi rekommenderar att Du konfigurerar PHP med en högre begränsning, som t ex 16M. Det finns flera sätt att göra detta som Du kan pröva med:</p> <ol>
<li>Om Du har möjlighet till det så kan Du kompilera om PHP med<i>--enable-memory-limit </i>Detta gör det möjligt för Moodle att ställa in minnesbegränsningen själv. </li>
<li>Om Du har tillgång till Din php.ini-fil så kan Du ändra inställningen för <b>memory limit</b> till något i stil med 16M. Om Du inte har tillgång själv så kan Du kanske be Din systemadministratör att göra detta åt Dig.</li>
<li>På en del PHP-servrar kan Du skapa en .htaccess-fil i Moodle-katalogen som innehåller den här raden: <blockquote>php_value memory_limit 16M</blockquote>.<br />Detta kan dock på en del servrar leda till att <b>inga</b> PHP-sidor fungerar. (Du får Error-sidor istället för de riktiga) så då får Du ta bort .htaccess-filen.</ol>
</li><li></li>
</ol>
';
$string['pass'] = 'Pass';
$string['phpversion'] = 'PHP-version';
$string['phpversionerror'] = 'PHP-versionen måste vara minst 4.1.0';
$string['phpversionhelp'] = '<p>Moodle kräver minst PHP 4.1.0</p>
<p>Du använder f n verion $a</p>
<p>Du måste uppgradera PHP eller flytta till en värd som har en nyare version av PHP!</p>';
$string['safemode'] = 'Säkert läge';
$string['safemodeerror'] = 'Moodle kan få problem om \'säkert läge\' (safe mode) är aktiverat';
$string['safemodehelp'] = '<p>Moodle kan få ett antal problem om \'säkert 
läge\' är aktiverat. Systemet kommer t ex troligtvis inte att kunna skapa nya filer.</p>
<p>Säkert läge är normalt sett bara aktiverat hos mycket försiktiga webbvärdar(t ex webbhotell) så Du kanske helt enkelt måste hitta ett annat webbhotell för Din webbplats med Moodle.</p>
<p>Du kan försöka att fortsätta installationen om Du vill, men bli inte förvånad om det dyker upp ett och annat problem längre fram.</p>';
$string['sessionautostart'] = 'Automatisk start av session';
$string['sessionautostarterror'] = 'De här bör vara inställt till \'off\'.';
$string['sessionautostarthelp'] = '<p>Moodle kräver stöd för sessioner och kommer inte att fungera utan det.</p>
<p>Sessioner kan vara aktiverade i php.ini-filen... kontrollera parametern för session.auto_start. </p>';
$string['wwwroot'] = 'Webbadress';
$string['wwwrooterror'] = 'Webbadressen verkar inte vara giltig - den här installationen av Moodle verkar inte att finnas där.';

?>
