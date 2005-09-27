<?PHP // $Id$ 
      // install.php - created with Moodle 1.6 development (2005060201)


$string['admindirerror'] = 'Malî ang ibinigay na direktoryong pang-admin';
$string['admindirname'] = 'Pang-Admin na Direktoryo';
$string['admindirsetting'] = '    Mayroong ilang webhost na ginagamit ang /admin bilang isang espesyal na URL para mapasok mo ang
    kontrol panel o iba pa.  Nakakalungkot isipin pero sumasalungat ito sa 
    istandard na lokasyon ng mga pang-admin na pahina ng Moodle.  Maaayos ninyo ito sa pamamagitan ng
    pagpapalit ng pangalan ng pang-admin na direktoryo sa iyong instalasyon, alalaong baga\'y ilagay ninyo
    ang bagong pangalan na iyon dito.  Halimbawa: <br /> <br /><b>moodleadmin</b><br /> <br />
    Maaayos nito ang mga pang-admin na link sa Moodle.';
$string['caution'] = 'Mag-ingat';
$string['chooselanguage'] = 'Pumilì ng wika';
$string['compatibilitysettings'] = 'Sinusuri ang iyong kaayusan ng PHP...';
$string['configfilenotwritten'] = 'Hindi awtomatikong nakalikha ang installer script ng config.php file na siyang naglalaman ng mga pinilì mong kaayusan.  Marahil ay dahil sa hindi masulatan ang direktoryo ng Moodle.  Maaari mong kopyahin ng mano-mano ang sumusunod na code sa isang file na nagngangalang config.php sa loob ng punong direktoryo ng Moodle.';
$string['configfilewritten'] = 'matagumpay na nalikha ang config.php';
$string['configurationcomplete'] = 'Nakumpleto na ang pagsasaayos';
$string['database'] = 'Database';
$string['databasecreationsettings'] = 'Ngayon ay kailangan mong isaayos ang kaayusan ng database kung saan iiimbakin ang karamihan sa datos ng Moodle.  Awtomatikong lilikhain ang database na ito ng pang-instol na Moodle4Windows na may mga kaayusang itinatakda sa ibaba.<br />
<br /> <br />
<b>Uri:</b> ipinirmi ng pang-instol sa \"mysql\"<br />
<b>Host:</b> ipinirmi ng pang-instol sa \"localhost\"<br />
<b>Pangalan:</b> pangalan ng database, hal. moodle<br />
<b>User:</b> ipinirmi ng pang-instol sa \"root\"<br />
<b>Password:</b> ang password ng database mo<br />
<b>Unlapi ng Teybol:</b> opsiyonal na unlapi na gagamitin sa lahat ng pangalan ng teybol';
$string['databasesettings'] = '    Ngayon ay kailangan mong isaayos ang database kung saan iimbakin
    ang karamihan sa datos ng Moodle.  Dapat ay nalikha na ang database na ito
    at may username at password na upang mapasok ito.<br />
    <br /> <br />
       <b>Uri:</b> mysql o postgres7<br />
       <b>Host:</b> eg localhost o db.isp.com<br />
       <b>Pangalan:</b> pangalan ng database, eg moodle<br />
       <b>User:</b> ang iyong database username<br />
       <b>Password:</b> ang iyong database password<br />
       <b>Unlapi ng mga Teybol:</b> opsiyonal na prefix na gagamitin sa lahat ng pangalan ng teybol';
$string['dataroot'] = 'Direktoryo ng Datos';
$string['datarooterror'] = 'Hindi matagpuan o malikha ang \'Direktoryo ng Datos\' na ibinigay mo.  Alin sa dalawa, iwasto mo ang landas o lumikha ng direktoryo nang mano-mano.';
$string['dbconnectionerror'] = 'Hindi kami makakonekta sa ibinigay mong database.  Pakitsek ang kaayusan mo ng database.';
$string['dbcreationerror'] = 'Nagka-Error sa paglikha ng database.  Hindi malikha ang ibinigay na pangalan ng database nang may mga ibinigay na  kaayusan';
$string['dbhost'] = 'Host Server';
$string['dbpass'] = 'Password';
$string['dbprefix'] = 'Prefix ng mga teybol';
$string['dbtype'] = 'Uri';
$string['directorysettings'] = '<p>Pakikumpirma ang mga lokasyon ng instalasyong ito ng Moodle.</p>

<p><b>Web Address:</b>
Ibigay ang buong web address kung saan papasukin ang Moodle.
Kung ang web site mo ay mapapasok sa pamamagitan ng maraming URL piliin ang
pinakaangkop para sa mga mag-aaral mo.  Huwag lalagyan ng 
slash sa dulo.</p>

<p><b>Direktoryo ng Moodle:</b>
Ibigay ang buong landas ng direktoryo sa instalasyong ito
Tiyakin na ang malaki/maliit na titik ay wasto.</p>

<p><b>Direktoryo ng Datos:</b>
Kailangan mo ng pook kung saan puwedeng magsave ng inaplowd na file ang Moodle.  Ang
direktoryong ito ay dapat nababasa AT NASUSULATAN ng web server user
(kadalasan ay \'nobody\' o \'apache\'), pero hindi ito dapat mapapasok nang
direkta sa pamamagitan ng web.</p>';
$string['dirroot'] = 'Direktoryo ng Moodle';
$string['dirrooterror'] = 'Mukhang mali ang kaayusan ng \'Direktoryo ng Moodle\' - wala kaming matagpuang instalasyon ng Moodle doon.  Inireset ang halaga sa ibaba.';
$string['download'] = 'Idownload';
$string['fail'] = 'Bigô';
$string['fileuploads'] = 'Mga Inaplowd na File';
$string['fileuploadserror'] = 'Dapat ay buhay ito';
$string['fileuploadshelp'] = '<p>Mukhang patay ang pag-aaplowd ng file sa server mo.</p>

<p>Maaari pa ring iinstol ang Moodle, nguni\'t wala ang abilidad na ito, hindi
   ka makakapag-aplowd ng mga file ng kurso o ng mga bagong larawan para sa pagkakakilanlan ng user.</p>

<p>Para mabuhay ang pag-aaplowd ng file (ikaw o ang iyong administrador ng sistema) ay kailangang
   iedit ang pangunahing php.ini na file sa iyong sistema at gawing \'1\' ang halaga 
   ng kaayusang <b>file_uploads</b>.</p>';
$string['gdversion'] = 'Bersiyon ng GD';
$string['gdversionerror'] = 'Dapat ay may GD library para maproseso at makalikha ng mga larawan';
$string['gdversionhelp'] = '<p>Mukhang hindi nakainstol ang GD sa server mo.</p>

<p>Ang GD ay isang library na kailangan ng PHP upang mapahintulutan ang Moodle na magproseso ng mga larawan
   (tulad ng mga icon ng pagkakakilanlan ng user) at upang lumikha ng mga bagong larawan (tulad ng
   mga graph ng log).  Gagana pa rin ang Moodle kahit walang GD - hindi mo lamang magagamit
   ang mga katangiang ito.</p>

<p>Para maidagdag ang GD sa PHP sa loob ng Unix, icompile ang PHP gamit ang  --with-gd na parameter.</p>

<p>Sa loob ng Windows kadalasan ay maeedit mo ang php.ini at tanggalin ang comment sa linya na tumutukoy sa  libgd.dll.</p>';
$string['installation'] = 'Instalasyon';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'Dapat ay patay ito';
$string['magicquotesruntimehelp'] = '<p>Ang magic quotes runtime ay dapat patayin para gumana ng maayos ang Moodle.</p>

<p>Karaniwan ay off ito bilang default ... tingnan ang kaayusan na <b>magic_quotes_runtime</b> sa inyong php.ini file.</p>

<p>Kung wala kang karapatang pasukin ang php.ini, baka maaari mong ilagay lang sumusunod na linya sa isang file
   na tinatawag na .htaccess sa loob ng iyong direktoryo ng Moodle:
   <blockquote>php_value magic_quotes_runtime Off</blockquote>
</p>   
   ';
$string['memorylimit'] = 'Memory Limit';
$string['memorylimiterror'] = 'Labis na mababa ang memory limit ng PHP ... maaaring magkaproblema ka mamaya.';
$string['memorylimithelp'] = '<p>Ang memory limit ng PHP para sa server mo ay kasalukuyang nakatakda sa $a.</p>

<p>Maaaring magdulot ito ng mga problemang pangmemorya sa Moodle sa mga susunod na panahon, lalo na
   kung marami kang binuhay na modyul at/o marami kang user.</p>

<p>Iminumungkahi namin na isaayos mo ang PHP na may mas mataas na limit kung maaari, tulad ng 16M.
    May iba\'t-ibang paraan na magagawa kayo upang ito ay maiisakatuparan:</p>
<ol>
<li>Kunga maaari mong gawin, muling icompile ang PHP na may <i>--enable-memory-limit</i>.  
     Pahihintulutan nito ang Moodle na itakda ang memory limit sa sarili nito.</li>
<li>Kung mapapasok mo ang iyong php.ini file, mababago mo ang <b>memory_limit</b> 
    na kaayusan doon at gawin itong mga 16M.  Kung wala kang karapatang pasukin ito
    baka puwede mong hilingin sa administrador na gawin ito para sa iyo.</li>
<li>Sa ilang PHP serve maaari kang lumikha ng isang .htaccess file sa direktoryo ng Moodle
    na naglalaman ng linyang ito:
    <p><blockquote>php_value memory_limit 16M</blockquote></p>
    <p>Subali\'t sa ilang server ay pipigilin nito ang paggana ng <b>lahat</b> ng pahinang PHP 
    (makakakita ka ng mga error kapag tumingin ka sa mga pahina) kaya\'t kakailanganin mong tanggalin ang .htaccess file.</p></li>
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'Hindi isinaayos ang PHP na may MySQL extension para magawa nitong makipag-usap sa MySQL.  Pakitsek ang iyong php.ini file o muling icompile ang PHP.';
$string['pass'] = 'Pasado';
$string['phpversion'] = 'Bersiyon ng PHP';
$string['phpversionerror'] = 'Ang pinakamababang bersiyon ng PHP na puwedeng gamitin ay 4.1.0';
$string['phpversionhelp'] = '<p>Kinakailangan ng Moodle ang isang bersiyon ng PHP na kahit man lamang 4.1.0.</p>
<p>Sa kasalukuyan ay pinatatakbo mo ang bersiyon $a</p>
<p>Kailangan mong gawing bago ang PHP o lumipat sa isang host na may mas bagong bersiyon ng PHP!</p>';
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = 'Maaaring magkaproblema ang moodle kung naka-ON ang safe mode';
$string['safemodehelp'] = '<p>Maraming klase ng problema ang Moodle kapag naka-ON ang safe mode, isa rito
   ay maaaring hindi ito mapahintulutang lumikha ng mga bagong file.</p>
   
<p>Ang safe mode ay kadalasang binubuhay lamang ng mga paranoid na pampublikong web host, kaya kakailanganin
   mong humanap ng bagong web hosting na kumpanya para sa iyong site ng Moodle.</p>
   
<p>Maaari mong ipagpatuloy ang pag-instol kung nais mo, pero asahan mo na na magkakaproblema ka maya-maya.</p>';
$string['sessionautostart'] = 'Session Auto Start';
$string['sessionautostarterror'] = 'Dapat ay patay ito';
$string['sessionautostarthelp'] = '<p>Kailangan ng Moodle ng session support at hindi ito gagana kung wala ito.</p>

<p>Ang session ay mabubuhay sa php.ini file ... hanapin ang session.auto_start na parameter.</p>';
$string['wwwroot'] = 'Web address';
$string['wwwrooterror'] = 'Mukhang hindi tanggap ang web address - mukhang wala roon ang instalasyong ito ng Moodle.';

?>
