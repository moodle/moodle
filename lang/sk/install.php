<?PHP // $Id$ 
      // install.php - created with Moodle 1.6 development (2005052400)


$string['admindirerror'] = 'Adresár pre správu (admin) nie je urèený správne';
$string['admindirname'] = 'Adresár pre správu (admin)';
$string['admindirsetting'] = 'Niektorí poskytovatelia web priestoru pou¾ívajú adresár /admin pre prístup ku kontrolnému panelu, prípadne ku podobným funkciám. To bohu¾iaµ nie je v súlade so ¹tandardným umiestnením adresáru pre správu v Moodle. Tento konflikt je mo¾né vyrie¹i» premenovaním adresáru pre správu vo Va¹ej in¹talácii. Vlo¾te sem nový názov, napr.<br /><br />
<b>moodleadmin</b><br /><br />
Tým se opravia odkazy na správu Moodle.';
$string['caution'] = 'Varovanie';
$string['chooselanguage'] = 'Vyberte jazyk';
$string['compatibilitysettings'] = 'Kontrola nastavení vá¹ho PHP...';
$string['configfilenotwritten'] = 'In¹talaèný skript nebol schopný automaticky vytvori»  config.php súbor, obsahujúci Va¹e zvolené nastavenia, pravdepodobne preto, ¾e adresár Moodle nie je zapisovateµný. Mô¾ete ruène skopírova» nasledovný kód do súboru s názvom config.php v rámci  koreòového adresára Moodle.';
$string['configfilewritten'] = 'súbor config.php bol úspe¹ne vytvorený';
$string['configurationcomplete'] = 'Konfigurácia ukonèená';
$string['database'] = 'Databáza';
$string['databasesettings'] = 'Teraz potrebujete nastavi» databázu, kde bude uchovávaná väè¹ina údajov Moodle. Táto databáza v¹ak musí by» predtým vytvorená a tie¾ musí by» vytvorené pou¾ívateµské meno a prístupové heslo.<br /><br /><br />
<b>Typ:</b> mysql alebo postgres7<br />
<b>Host:</b> napr. localhost alebo db.isp.com<br />
<b>Meno:</b> meno databázy, napr. moodle<br />
<b>Pou¾ívateµ:</b> pou¾ívateµské meno Va¹ej databázy<br />
<b>Heslo:</b> heslo Va¹ej databázy<br />
<b>Predpona tabuliek:</b> nepovinná predpona pre v¹etky mená tabuliek';
$string['dataroot'] = 'Adresár pre údaje';
$string['datarooterror'] = '\'Adresár pre údaje\', ktorý ste zadali, nemô¾e by» nájdený alebo vytvorený. Upravte buï cestu alebo vytvorte ten adresár ruène.';
$string['dbconnectionerror'] = 'Nemohli sme sa pripoji» k vami zadanej databáze. Prosím skontrolujte nastavenia Va¹ej databázy.';
$string['dbcreationerror'] = 'Chyba pri vytváraní databázy. Nebolo mo¾né vytvori» databázu so zadaným menom a jej nastaveniami';
$string['dbhost'] = 'Hos»ovský server';
$string['dbpass'] = 'Heslo';
$string['dbprefix'] = 'Predpona tabuliek';
$string['dbtype'] = 'Typ';
$string['directorysettings'] = '<p>Prosím, potvrïte umiestnenie in¹talácie Moodle.</p>

<p><b>Web adresa:</b> ©pecifikujte celú web adresu, kde bude Moodle umiestnený. Ak sa na túto adresu pristupuje z viacerých url adries, potom vyberte tú, ktorú budú pou¾íva» Va¹i ¹tudenti. Na konci nepou¾ívajte lomítko.</p>

<p><b>Adresár Moodle:</b> ©pecifikujte celú cestu k adresáru a tejto in¹talácii. Ubezpeète sa, ¾e ste korektne pou¾ili veµké a malé písmená.</p>

<p><b>Adresár pre údaje:</b> Potrebujete miesto, kde Moodle bude uklada» prená¹ané súbory. Tento adresár by mal by» pou¾ívateµovi webového servera prístupný aj na èítanie, aj na ZAPISOVANIE (zvyèajne \'nobody\' alebo \'apache\'), ale nemalo by sa da» k nemu pristupova» priamo z webu.</p>';
$string['dirroot'] = 'Adresár Moodle';
$string['dirrooterror'] = 'Nastavenia v \'Adresári Moodle\' sú nesprávne - nemô¾eme tu nájs» in¹taláciu Moodle. Hodnota dole bola vynulovaná.';
$string['download'] = 'Stiahnu»';
$string['fail'] = 'Neúspe¹né';
$string['fileuploads'] = 'prenesené súbory';
$string['fileuploadserror'] = 'Toto by malo by» zapnuté';
$string['fileuploadshelp'] = '<p>Zdá sa, ¾e na Va¹om serveri nie je aktivovaný prenos súborov.</p>

<p>Moodle mô¾e by» aj napriek tomu nain¹talovaný, ale bez tejto mo¾nosti, nebudete schopní prenies» súbory kurzu, alebo obrázky v nových pou¾ívateµských profiloch.</p>

<p>Na aktivovanie prenosu súborov, Vy (alebo Vá¹ systémový administrátor) budete musie» upravi» main php.ini súbor v systéme a zmeni» nastavenie pre <b>file_uploads</b> na \'1\'.</p>';
$string['gdversion'] = 'Verzia kni¾nice GD';
$string['gdversionerror'] = 'Kni¾nica GD by mala existova» na spracovávanie a vytváranie obrázkov';
$string['gdversionhelp'] = '<p>Na Va¹om serveri zrejme nie je nain¹talovaná GD kni¾nica.</p>

<p>GD je kni¾nica, ktorú si vy¾aduje PHP, aby mohlo Moodle povoli» spracováva» obrázky (napr. ikony v pou¾ívateµských profiloch) a vytvára» nové obrázky (napr. grafy z prihlásení). Moodle bude stále pracova» bez GD - tieto mo¾nosti budú dostupné len Vám.</p>

<p>Keï chcete prida» GD do PHP pod Unixom, vytvorte PHP pou¾itím --with-gd parameter.</p>

<p>Pod Windows mô¾ete upravi» php.ini a odkomentova» riadok obsahujúci libgd.dll.</p>';
$string['installation'] = 'In¹talácia';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'Toto by malo by» vypnuté';
$string['magicquotesruntimehelp'] = '<p>Magic quotes runtime by malo by» vypnuté, aby Moodle fungoval tak, ako má.</p>

<p>Zvyèajne je voµba ¹tandardne vypnutá ... pozri nastavenia <b>magic_quotes_runtime</b> vo Va¹om php.ini súbore.</p>

<p>Ak nemáte prístup k súboru php.ini, mali by ste nasledovný riadok do súboru s názvom .htaccess v rámci adresára Moodle: 
<blockquote>php_value magic_quotes_runtime Off</blockquote>
</p> ';
$string['memorylimit'] = 'Limit pamäte';
$string['memorylimiterror'] = 'PHP limit pamäte je nastavený na minimum...S týmto mô¾ete ma» neskôr problémy.';
$string['memorylimithelp'] = '<p>PHP limit pamäte pre Vá¹ server je momentálne nastavený na $a.</p>

<p>Toto mô¾e neskôr spôsobi» problémy v Moodle, najmä ak máte veµa modulov a/alebo veµa pou¾ívateµov.</p>

<p>Odporúèame Vám, aby ste nastavili PHP s vy¹¹ím limitom pamäte, ak je to mo¾né, napr. 16M. Na to existuje veµa spôsobov, ktoré mô¾ete vyskú¹a»:</p>
<ol>
<li>Ak je to mo¾né, znovu vytvorte PHP s <i>--enable-memory-limit</i>. Toto umo¾ní Moodle samonastavenie limitu pamäte.</li>
<li>Ak máte prístup k Vá¹mu php.ini súboru, mô¾ete zmeni» <b>memory_limit</b> nastavenie, napr. na 16M. Ak nemáte prístup k súboru, mô¾ete sa na to spýta» Vá¹ho administrátora.</li>
Na niektorých PHP serveroch, si mô¾ete vytvori» súbor .htaccess v Adresári Moodle, ktorý bude obsahova» tento riadok: <p><blockquote>php_value memory_limit 16M</blockquote></p>
<p>Av¹ak, na niektorých serveroch bude toto bráni» <b>v¹etkým</b> PHP stránkam v práci (budete vidie» chyby, keï sa pozriete na stránky), tak¾e budete musie» odstráni» súbor .htaccess.</p></li>
</ol> 	';
$string['mysqlextensionisnotpresentinphp'] = 'PHP nebolo správne nakonfigurované s MySQL roz¹írením a tak nemô¾e komunikova» s MySQL. Prosím, skontrolujte si Vá¹ php.ini súbor alebo znovu vytvorte PHP.';
$string['pass'] = 'Prejs»';
$string['phpversion'] = 'Verzia PHP';
$string['phpversionerror'] = 'Verzia PHP musí by» aspoò  4.1.0';
$string['phpversionhelp'] = '<p>Moodle si vy¾aduje verziu PHP aspoò  4.1.0.</p>
<p>Vy máte momentálne nain¹talovanú túto verziu $a</p>
<p>Musíte obnovi» PHP alebo presunú» na hostiteµský poèítaè s novou verziou PHP!</p>';
$string['safemode'] = 'Bezpeèný mód';
$string['safemodeerror'] = 'Moodle mô¾e ma» problémy, ak je zapnutý bezpeèný mód';
$string['safemodehelp'] = '<p>Moodle mô¾e ma» viacero problémov, ak je zapnutý bezpeèný mód, pravdepodobne nedovolí vytvára» nové súbory.</p>

<p>Bezpeèný mód je zvyèajne povolený verejnými poskytovateµmi webového priestoru, tak¾e by ste si mali nájs» nového poskytovateµa webového priestoru pre stránku Moodle.</p>';
$string['sessionautostart'] = 'Auto¹tart sekcie';
$string['sessionautostarterror'] = 'Toto by malo by» vypnuté';
$string['sessionautostarthelp'] = '<p>Moodle vy¾aduje podporu sekcie a nebude bez nej fungova».</p>';
$string['wwwroot'] = 'Web adresa';
$string['wwwrooterror'] = 'Táto web adresa pravdepodobne nie je platná - táto in¹talácia Moodle tu pravdepodobne nie je.';

?>
