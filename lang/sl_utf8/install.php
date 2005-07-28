<?PHP // $Id:install.php from install.xml
      // Comments: tomaz at zid dot si

$string['admindirerror'] = 'Določen skrbniški imenik ni pravilen';
$string['admindirname'] = 'Skrbniški imenik';
$string['admindirsetting'] = '    Le redka spletna mesta uporabljajo /admin kot poseben URL za dostop
    do nadzorne plošče ali česa drugega.  Žal je to v sporu s
    standardno lokacijo Moodle skrbniških stran.  To lahko popravite s
    preimenovanjem skrbniškega imenika v vaši namestitvi in vstavljanjem tega
    novega imena tu.  Na primer: <br /> <br /><b>moodleadmin</b><br /> <br />
    To bo popravilo skrbniške povezave za Moodle.';
$string['caution'] = 'Pozor';
$string['chooselanguage'] = 'Izberite jezik';
$string['compatibilitysettings'] = 'Preverjanje vaših PHP nastavitev ...';
$string['configfilenotwritten'] = 'Skripta nameščanja ni mogla samodejno ustvariti datoteke config.php, ki bi vsebovala vaše izbrane nastavitve. Verjetno v Moodle imenik ni možno zapisovanje. Ročno lahko prekopirate sledečo kodo v datoteko z imenom config.php v korenskem imeniku Moodle.';
$string['configfilewritten'] = 'datoteka config.php je bila uspešno ustvarjena';
$string['configurationcomplete'] = 'Konfiguracija je dokončana';
$string['database'] = 'Podatkovna zbirka';
$string['databasesettings'] = '    Zdaj morate konfigurirati podatkovno zbirko, kjer bo večina Moodle podatkov
    shranjenih.  Ta podatkovna zbitka mora biti že ustvarjena
    in tudi uporabniško ime in geslo za dostop do nje.<br />
    <br /> <br />
       <b>Vrsta:</b> mysql ali postgres7<br />
       <b>Gostitelj:</b> npr. localhost ali db.isp.com<br />
       <b>Ime:</b> ime podatkovne zbirke, npr. moodle<br />
       <b>Uporabnik:</b> vaše uporabniško ime podatkovne zbirke<br />
       <b>Geslo:</b> vaše geslo podatkovne zbirke<br />
       <b>Predpona tabel:</b> dodatna predpona za vsa imena tabel';
$string['databasecreationsettings'] = '    Zdaj morate konfigurirati nastavitve podatkovne zbirke, kjer bo večina Moodle podatkov
    shranjenih.  Ta podatkovno zbirko bo samodejno ustvaril namestitveni program Moodle4Windows
    s spodaj določenimi nastavitvami.<br />
    <br /> <br />
       <b>Vrsta:</b> določeno "mysql" s strani namestitvenega programa<br />
       <b>Gostitelj:</b> določeno "localhost" s strani namestitvenega programa<br />
       <b>Ime:</b> ime podatkovne zbirke, npr. moodle<br />
       <b>Uporabnik:</b> določeno "root" s strani namestitvenega programa<br />
       <b>Geslo:</b> vaše geslo podatkovne zbirke<br />
       <b>Predpona tabel:</b> dodatna predpona za vsa imena tabel';
$string['dataroot'] = 'Imenik za podatke';
$string['datarooterror'] = '\'Imenika za podatke\', ki ste ga navedli ni možno najti ali ustvariti.  Bodisi popravite pot ali ustvarite imenik ročno.';
$string['dbconnectionerror'] = 'Povezave ni mogoče vzpostaviti s podatkovno zbirko, ki ste jo navedli. Prosimo, preverite vaše nastavitve podatkovne zbirke.';
$string['dbcreationerror'] = 'Napaka ustvarjanja podatkovne zbirke. S podanimi nastavitvami ni možno ustvariti podatkovne zbirke z navedenim imenom';
$string['dbhost'] = 'Gostiteljski strežnik';
$string['dbpass'] = 'Geslo';
$string['dbprefix'] = 'Predpona tabel';
$string['dbtype'] = 'Vrsta';
$string['directorysettings'] = '<p>Prosimo, potrdite lokacije te namestitve Moodle.</p>

<p><b>Spletni naslov:</b>
Navedite polni spletni naslov za dostop do Moodle.  
Če je vaše spletno mesto dostopno prek večih URL naslovov izberite
najbolj pogostega, ki ga bodo uporabljali udeleženci.  Ne vključite 
zaključne poševnice.</p>

<p><b>Imenik Moodle:</b>
Navedite polno pot imenika do te namestitve
Pazite, da bodo pravilne velike in male črke.</p>

<p><b>Imenik za podatke:</b>
Potrebujete prostor kamor lahko Moodle shranjuje naložene datoteke.  Ta
imenik mora omogočati branje IN PISANJE za uporabniško ime spletnega strežnika
(običajno \'nobody\' ali \'apache\'), a ne sme biti dostopen
neposredno prek spleta.</p>';
$string['dirroot'] = 'Imenik Moodle';
$string['dirrooterror'] = 'Nastavitev \'Imenik Moodle\' je kot kaže napačna - tam ni najti namestitve Moodle.  Spodnja vrednost je bila ponovno nastavljena.';
$string['download'] = 'Prenos';
$string['fail'] = 'Neuspeh';
$string['fileuploads'] = 'Nalaganje datotek';
$string['fileuploadserror'] = 'To bi moralo biti vključeno';
$string['fileuploadshelp'] = '<p>Nalaganje datotek je kot kaže onemogočeno na vašem strežniku.</p>

<p>Moodle je še vedno možno namestiti, vendar brez te možnosti, ne boste mogli 
   nalagati datotek predmetov ali novih slik uporabniških profilov.</p>

<š>Za omogočanje nalaganja datotek boste (ali vaš skrbnik sistema) morali 
   urediti glavno datoteko php.ini na vašem sistemu in spremeniti nastavitev za 
   <b>file_uploads</b> na \'1\'.</p>';
$string['gdversion'] = 'Različica GD';
$string['gdversionerror'] = 'Knjižnica GD mora biti prisotno za obdelavo in ustvarjanje slik';
$string['gdversionhelp'] = '<p>Na vašem strežniku kot kaže ni nameščen GD.</p>

<p>GD je knjižnica, ki jo potrebuje PHP, da lahko Moodle obdeluje slike 
   (kot so ikone uporabniškega profila) in ustvarja nove slike (kot so 
   grafi dnevnikov).  Moodle bo deloval tudi brez GD - te možnosti 
   vam preprosto ne bodo na voljo.</p>

<p>Za dodajanje GD v PHP v sistemu Unix, prevedite PHP s parametrom --with-gd.</p>

<p>V okolju Windows lahko običajno uredite php.ini in odkomentirate vrstico, ki se sklicuje na libgd.dll.</p>';
$string['installation'] = 'Namestitev';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'To bi moralo biti izključeno';
$string['magicquotesruntimehelp'] = '<p>Možnost Magic quotes runtime bi morala biti izključena za pravilno delovanje Moodle.</p>

<p>Po navadi je privzeta vrednost izključena ... poglejte nastavitev <b>magic_quotes_runtime</b> v vaši datoteki php.ini.</p>

<p>Če nimate dostopa do datoteke php.ini, boste morda lahko vstavili sledečo vrstico v datoteko 
   imenovano .htaccess v vašem imenuko Moodle:
   <blockquote>php_value magic_quotes_runtime Off</blockquote>
</p>   
   ';
$string['memorylimit'] = 'Omejitev pomnilnika';
$string['memorylimiterror'] = 'Omejitev pomnilnika PHP je nastavljena precej nizko ... pozneje lahko pride do težav.';
$string['memorylimithelp'] = '<p>Omejitev pomnilnika PHP je trenutno na vašem strežniku nastavljena na $a.</p>

<p>To lahko povzroči, da bo imel Moodle pozneje težave s pomnilnikom. Še posebej,
   če imate vključenih veliko modulov oziroma veliko uporabnikov.</p>

<p>Priporočamo, da konfigurirate PHP z višjo omejitvijo, če je možno npr. 16M.  
   To lahko poskusite storiti na več načinov:</p>
<ol>
<li>Če lahko, ponovno prevedite PHP z <i>--enable-memory-limit</i>.  
    To bo omogočilo, da bo Moodle sam nastavil omejitev pomnilnik zase.</li>
<li>Če imate dostop do vaše datoteke php.ini, lahko spremenite vrednost <b>memory_limit</b> 
    v tej datoteki na, recimo, 16M.  Če nimate dostopa, boste morda 
    lahko prosili vašega skrbnika, da to naredi za vas.</li>
<li>Na nekaterih strežnikih PHP lahko ustvarite datoteko .htaccess v imeniku Moodle, 
    ki naj vsebuje to vrstico:
    <p><blockquote>php_value memory_limit 16M</blockquote></p>
    <p>Vendar lahko to prepreči delovanje <b>vseh</b> PHP strani 
    (ob prikazu strani boste videli napake) in boste morali odstraniti datoteko .htaccess.</p></li>
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'PHP ni bil pravilno konfiguriran z razširitvijo MySQL in zato ne more komunicirati z MySQL.  Prosimo, preverite vašo datoteko php.ini ali ponovno prevedite PHP.';
$string['pass'] = 'Uspešno';
$string['phpversion'] = 'Različica PHP';
$string['phpversionerror'] = 'Različica PHP mora biti vsaj 4.1.0';
$string['phpversionhelp'] = '<p>Moodle potrebuje različico PHP vsaj 4.1.0.</p>
<p>Vaša trenutna različica je $a</p>
<p>Posodobiti in nadgraditi morate PHP ali premakniti program na strežnik s novejšo različico PHP!</p>';
$string['safemode'] = 'Varni način';
$string['safemodeerror'] = 'Moodle lahko ima težave z vključenim varnim načinom';
$string['safemodehelp'] = '<p>Moodle ima lahko razne težave z vključenim varnim načinom. Ne samo, da
   verjetno ne bo smel ustvarjati novih datotek.</p>
   
<p>Varni način je običajno vključen pri paranoidnih javnih spletnih gostiteljih in boste morda morali
   poiskati navo podjetje za gostovanje vašega Moodle spletnega mesta.</p>
   
<p>Če želite lahko poskusite nadaljevati z namestitvijo, a pričakujte nekaj težav pozneje.</p>';
$string['sessionautostart'] = 'Samodejni začetek seje';
$string['sessionautostarterror'] = 'To bi moralo biti izključeno';
$string['sessionautostarthelp'] = '<p>Moodle zahteva podporo za seje in ne bo deloval brez tega.</p>

<p>Seje lahko omogočite v datoteki php.ini ... poiščite parameter session.auto_start.</p>';
$string['wwwroot'] = 'Spletni naslov';
$string['wwwrooterror'] = 'Spletni naslov kot kaže ni veljaven - te namestitve Moodle, kot kaže, ni tam.';


?>