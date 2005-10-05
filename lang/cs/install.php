<?PHP // $Id$ 
      // install.php - created with Moodle 1.5.2 + (2005060223)


$string['admindirerror'] = 'Adresáø správy (admin) není urèen správnì';
$string['admindirname'] = 'Adresáø správy (admin)';
$string['admindirsetting'] = 'Velmi malé mno¾ství webových hostitelù pou¾ívá /admin jako speciální URL k pøístupu ke kontrolnímu panelu nebo k podobným funkcím. To bohu¾el zpùsobuje konflikty se standardním umístìním adresáøe správy v Moodle. Tento konflikt mù¾ete vyøe¹it pøejmenováním adresáøe správy va¹í instalace. Vlo¾te sem nový název, napø. <br/> <br /><b>moodleadmin</b><br /> <br />Tím se opraví odkazy na správu Moodle.';
$string['caution'] = 'Varování';
$string['chooselanguage'] = 'Vyberte jazyk';
$string['compatibilitysettings'] = 'Kontrola nastavení va¹eho PHP...';
$string['configfilenotwritten'] = 'Instalaèní skript nemohl automaticky vytvoøit soubor config.php s va¹í konfigurací - pravdìpodobnì z dùvodù nastavení práv k zápisu do adresáøe Moodle. Mù¾ete ruènì zkopírovat následující kód do souboru s názvem config.php v hlavním adresáøi va¹í instalace Moodle.';
$string['configfilewritten'] = 'config.php byl úspì¹nì vytvoøen';
$string['configurationcomplete'] = 'Konfigurace hotová';
$string['database'] = 'Databáze';
$string['databasecreationsettings'] = 'Nyní musíte nakonfigurovat spojení k databázi, kde si bude Moodle ukládat vìt¹inu svých dat. Tato databáze bude vytvoøena automaticky instalaèním programem Moodle4Windows s následujícím nastavením.<br/>
<br /> <br />
<b>Typ:</b>instalátor nastaví na \"mysql\"<br />
<b>Hostitel:</b>instalátor nastaví na \"localhost\"<br />
<b>Název:</b> název databáze, napø. moodle<br />
<b>U¾ivatel:</b>instalátor nastaví na \"root\"<br />
<b>Heslo:</b> heslo k tomuto úètu<br />
<b>Pøedpona tabulek:</b> volitelná pøedpona, která se vlo¾í pøed názvy v¹ech tabulek (umo¾òuje mít jednu databázi pro více instalací Moodle)';
$string['databasesettings'] = 'Nyní musíte nakonfigurovat spojení k databázi, kde si bude Moodle ukládat vìt¹inu svých dat. Tato databáze musí ji¾ existovat, stejnì jako musí být nastaveno u¾ivatelské jméno a heslo pro pøístup k ní.<br/>
<br /> <br />
<b>Typ:</b> mysql nebo postgres7<br />
<b>Hostitel:</b> napø. localhost nebo db.naseskola.cz<br />
<b>Název:</b> název databáze, napø. moodle<br />
<b>U¾ivatel:</b> u¾ivatelské jméno úètu pro pøístup k databázi<br />
<b>Heslo:</b> heslo k tomuto úètu<br />
<b>Pøedpona tabulek:</b> volitelná pøedpona, která se vlo¾í pøed názvy v¹ech tabulek (umo¾òuje mít jednu databázi pro více instalací Moodle)';
$string['dataroot'] = 'Datový adresáø';
$string['datarooterror'] = 'Vámi specifikovaný datový adresáø nebyl nalezen a nemohl být vytvoøen. Buï opravte vlo¾enou cestu, nebo vytvoøte adresáø ruènì.';
$string['dbconnectionerror'] = 'Nemù¾u se spojit s databází, kterou jste specifikovali. Prosím, zkontrolujte nastavení databáze.';
$string['dbcreationerror'] = 'Chyba pøi vytváøení databáze. Nelze vytvoøit databázi daného jména s poskytnutým nastavením';
$string['dbhost'] = 'Hostitelský server';
$string['dbpass'] = 'Heslo';
$string['dbprefix'] = 'Pøedpona tabulek';
$string['dbtype'] = 'Typ';
$string['directorysettings'] = '<p>Prosím, potvrïte umístìní této Moodle instalace.</p>

<p><b>Webová adresa:</b>
Urèete úplnou webovou adresu, na ni¾ bude vá¹ Moodle dostupný. Jsou-li va¹e stránky dostupné pøes více URL, vyberte z nich tu, kterou budou pou¾ívat va¹i studenti. Na konci adresy nevkládejte lomítko.</p>

<p><b>Moodle adresáø:</b>
Urètete úplnou cestu k adresáøi s touto instalací. Ujistìte se, ¾e vám odpovídají malá/VELKÁ písmena.</p>

<p><b>Datový adresáø:</b>
Je tøeba mít diskový prostor, kam mù¾e Moodle ukládat nahrané (uploadované) soubory. K tomuto adresáøi musí mít proces webového serveru právo ke ètení I ZÁPISU (webový server bývá spou¹tìn pod u¾ivatelem \'nobody\' nebo \'apache\' nebo nìco podobného). Tento adresáø by nemìl být dostupný pøímo pøes webové rozhraní (mù¾e obsahovat neveøejná data).</p>';
$string['dirroot'] = 'Moodle adresáø';
$string['dirrooterror'] = 'Hodnota \'Moodle adresáø\' nevypadá nastavená správnì - nemù¾u tam najít Moodle instalaci. Následující hodnota byla resetována.';
$string['download'] = 'Stáhnout';
$string['fail'] = 'Selhalo';
$string['fileuploads'] = 'Nahrané soubory (uploads)';
$string['fileuploadserror'] = 'Mìlo by být zapnuto';
$string['fileuploadshelp'] = '<p>Vypadá to, ¾e na va¹em serveru není umo¾nìno nahrávat soubory.</p>

<p>Moodle mù¾e být i pøesto nainstalován, ale bez této funkce nebudete moci nahrávat ¾ádné soubory (napø. studijní materiály nebo fotografie u¾ivatelù).</p>

<p>Chcete-li povolit nahrávání souborù, budete muset vy (nebo vá¹ správce) upravit hlavní soubor php.ini na va¹em serveru a zmìnit nastavení
<b>file_uploads</b> na \'1\'.</p>';
$string['gdversion'] = 'Verze GD';
$string['gdversionerror'] = 'Knihovna GD je potøebná ke zpracovávání a tvorbì obrázkù (napø. fotografie, grafy apod.)';
$string['gdversionhelp'] = '<p>Vypadá to, ¾e na va¹em serveru není nainstalována knihovna GD.</p>

<p>GD je knihovna, kterou vy¾aduje PHP, aby umo¾nilo Moodlu zpracovávat obrázky (jako jsou ikony u¾ivatelù) a vytváøet nové obrázky (jako jsou napø. grafy pøístupù na va¹e stránky). Moodle bude fungovat i bez GD, ale tyto funkce nebudou dostupné.</p>

<p>Chcete-li pøidat GD do PHP pod Unixem, zkompilujte PHP s parametrem --with-gd .</p>

<p>Pod systémem Windows staèí vìt¹inou upravit php.ini a odkomentovat øádek odkazující na libgd.dll.</p>';
$string['installation'] = 'Instalace';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'Mìlo by být vypnuto';
$string['magicquotesruntimehelp'] = '<p>Funkce \'Magic quotes runtime\' by mìla být vypnuta pro správné fungování Moodlu.</p>

<p>Normálnì bývá tato funkce implicitnì vypnutá ... podívejte se na nastavení <b>magic_quotes_runtime</b> ve va¹em php.ini .</p>

<p>Pokud nemáte pøístup k va¹emu php.ini, mù¾ete zkusit umístit následující øádek do souboru  .htaccess ve va¹em Moodle adresáøi:
<blockquote>php_value magic_quotes_runtime Off</blockquote>
</p>';
$string['memorylimit'] = 'Limit pamìti';
$string['memorylimiterror'] = 'Limit pamìti pro PHP skripty je nastaven relativnì nízko ... pozdìji vás to mù¾e stát problémy.';
$string['memorylimithelp'] = '<p>Limit pamìti pro PHP skripty je na va¹em serveru momentálnì nastaven na hodnotu $a.</p>

<p>Toto mù¾e pozdìji zpùsobovat Moodlu problémy, zvlá¹tì pøi vìt¹ím mno¾ství modulù a/nebo u¾ivatelù.</p>

<p>Je-li to mo¾né, doporuèujeme vám nakonfigurovat PHP s vy¹¹ím limitem - napø. 16M. Je nìkolik zpùsobù, které mù¾ete zkusit:
<ol>
<li>Mù¾ete-li, pøekompilujte PHP s volbou <i>--enable-memory-limit</i>.
Toto umo¾ní Moodlu nastavit si pro sebe po¾adovaný limit.</li>
<li>Máte-li pøístup k va¹emu souboru php.ini, zmìòte nastavení <b>memory_limit</b>
na hodnotu blízkou 16M. Nemáte-li taková práva, po¾ádejte va¹eho správce webového serveru, aby to pro vás udìlal.</li>
<li>Na nìkterých PHP serverech mù¾ete v Moodle adresáøi vytvoøit soubor .htaccess s následujícím øádkem:
<p><blockquote>php_value memory_limit 16M</blockquote></p>
<p>Bohu¾el, na nìkterých serverech tímto vyøadíte z provozu <b>v¹echny</b> PHP stránky (pøi jejich prohlí¾ení uvidíte chybové zprávy), tak¾e budete muset soubor .htaccess odstranit.</li>
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'PHP nebylo korektnì nakonfigurováno pro komunikaci v MySQL. Zkontrolujte vá¹ php.ini nebo pøekompilujte PHP.';
$string['pass'] = 'Pro¹lo';
$string['phpversion'] = 'Verze PHP';
$string['phpversionerror'] = 'Verze PHP musí být alespoò 4.1.0 nebo vy¹¹í';
$string['phpversionhelp'] = '<p>Moodle vy¾aduje verzi PHP alespoò 4.1.0.</p>
<p>Va¹e stávající PHP má verzi $a</p>
<p>Musíte upgradovat va¹e PHP nebo Moodle nainstalovat na hostitele s vy¹¹í verzí!</p>';
$string['safemode'] = 'Bezpeèný re¾im (safe mode)';
$string['safemodeerror'] = 'Moodle mù¾e mít problémy pøi zapnutém bezpeèném re¾imu (safe mode)';
$string['safemodehelp'] = '<p>Moodle mù¾e mít mno¾ství problémù pøi zapnutém bezpeèném re¾imu PHP (tzv. safe mode). Jedním z nich je, ¾e pravdìpodobnì nebude moci vytváøet nové soubory.</p>

<p>Bezpeèný re¾im bývá zapnutý u paranoidních veøejných webových hostitelù, tak¾e mo¾ná bude staèit najít si jiného hostitele pro vá¹ Moodle.</p>

<p>Mù¾ete zkusit pokraèovat v instalaci, ale oèekávejte mo¾né problémy.</p>';
$string['sessionautostart'] = 'Session Auto Start';
$string['sessionautostarterror'] = 'Mìlo by být vypnuto';
$string['sessionautostarthelp'] = '<p>Moodle po¾aduje podporu session a nebude bez ní fungovat.</p>

<p>Podporu session mù¾ete povolit v souboru php.ini  ... podívejte se na parametr session.auto_start .</p>';
$string['wwwroot'] = 'Webová adresa';
$string['wwwrooterror'] = 'Toto nevypadá jako platná webová adresa této instalace Moodle.';

?>
