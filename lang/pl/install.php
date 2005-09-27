<?PHP // $Id$ 
      // install.php - created with Moodle 1.5.2 + (2005060222)


$string['admindirerror'] = 'Podany katalod admin jest nieprawid³owy';
$string['admindirname'] = 'Katalog admin';
$string['caution'] = 'Ostrze¿enie';
$string['chooselanguage'] = 'Wybierz jêzyk';
$string['compatibilitysettings'] = 'Sprawdzanie Twoich ustawieñ PHP';
$string['configfilenotwritten'] = 'Instalator nie móg³ automatycznie utworzyæ plik config.php zawieraj±cy Twoje parametry instalacyjne, prawdopodobnie dlatego ¿e katalog Moodle nie ma prawa zapisu. Musisz rêcznie przekopiowaæ poni¿szy kod do pliku config.php, który powinien znajdowaæ siê w g³ównym katalogu Moodle.';
$string['configfilewritten'] = 'config.php zosta³ pomy¶lnie stworzony';
$string['configurationcomplete'] = 'Konfiguracja skoñczona';
$string['database'] = 'Baza danych';
$string['databasecreationsettings'] = 'Teraz skonfiguruj bazê danych gdzie Moodle mo¿e przechowywaæ dane. Ta baza danych bêdzie stworzona automatycznie przez instalator: Moodle4Windows z parametrami instalacyjnymi okre¶lanymi poni¿ej.<br />
<br /> <br />
<b>Typ:</b>Instalator ustali³  \"mysql\"<br/>
<b>Host:</b> Instalator ustali³ \"localhost\"<br />
<b>nazwa:</b>Nazwa Twojej bazy danych, np. Moodle<br/>
<b>U¿ytkownik:</b> u¿ytkownik Twojej bazy danych<br />
<b>Has³o:</b> Has³o dostêpu do bazy danych<br />
<b>Prefiksy tabel:</b> opcjonalny prefiks u¿ywany przed wszystkimi nazwami tabeli ';
$string['databasesettings'] = 'Teraz skonfiguruj bazê danych gdzie Moodle mo¿e przechowywaæ dane. Baza danych musi byæ utworzona, oraz u¿ytkownik i has³o który mo¿e siê odwo³ywaæ do bazy danych.<br/><br/><br/>
<b>Typ:</b> mysql lub postgres 7<br/>
<b>Host:</b> np: localhost lub db.isp.com<br />
<b>nazwa:</b>Nazwa Twojej bazy danych, np. Moodle<br/>
<b>U¿ytkownik:</b> u¿ytkownik Twojej bazy danych<br />
<b>Has³o:</b> Has³o dostêpu do bazy danych<br />
<b>Prefiksy tabel:</b> opcjonalny prefiks u¿ywany przed wszystkimi nazwami tabeli';
$string['dataroot'] = 'Katalog z danymi';
$string['datarooterror'] = 'Katalog z danymi który poda³e¶ nie mo¿e byæ znaleziony lub utworzony. Popraw ¶cie¿kê lub utwórz katalog rêcznie.';
$string['dbconnectionerror'] = 'Nie mo¿na po³±czyæ siê z podan± baz± danych. Sprawd¼ ustawienia Twojej bazy danych.';
$string['dbhost'] = 'Serwer baz danych';
$string['dbpass'] = 'Has³o';
$string['dbprefix'] = 'prefiksy tabel';
$string['dbtype'] = 'Typ';
$string['directorysettings'] = '<p> Potwierd¼ lokalizacjê dla tej instalacji Moodle.</p>

<p><b>Adres w sieci:</b>
Podaj pe³ny adres w sieci gdzie Moodle bêdzie dostêpny. 
Je¿eli adresów w sieci jest wiele wybierz jeden który bêd± u¿ywali studenci. Nie dodawaj slash</p>

<p><b> Katalog Moodle:</b>
Podaj pe³n± ¶cie¿kê dostêpu do tej intalacji upewnij siê ¿e wielko¶æ liter jest poprawna. </p>

<p><b> Katalog z danymi:</b>
Miejsce gdzie Moodle mo¿e przechowywaæ pliki, Ten katalog powinien mieæ prawo odczytu i ZAPISU dla serwera www(przewa¿nie \'nobody\' lub \'apache\'), ale nie ma byæ dostêpny bezpo¶rednio przez sieæ </p>';
$string['dirroot'] = 'Katalog Moodle';
$string['dirrooterror'] = '\"Katalog Moodle\" wydaje siê byæ nieprawid³owy - tam nie mo¿na znale¼æ instalacji Moodle. Warto¶ci poni¿ej zostan± usuniête.';
$string['download'] = 'Pobierz';
$string['fail'] = 'zawie¶æ';
$string['fileuploads'] = 'Plik pobrany';
$string['fileuploadserror'] = 'Powinno byæ w³±czone';
$string['gdversion'] = 'versja biblioteki GD';
$string['installation'] = 'Instalacja';
$string['magicquotesruntime'] = 'Magic Quotes Runtime';
$string['magicquotesruntimeerror'] = 'Powinno byæ wy³±czone';
$string['memorylimit'] = 'Ograniczenie pamiêci';
$string['phpversion'] = 'wersja PHP';
$string['phpversionerror'] = 'Wersja PHP musi byæ ca najmniej 4.1.0';
$string['phpversionhelp'] = '<p> Moodle wymaga wersji PHP co najmniej 4.1.0. </p> 
<p>Obecnie jest uruchomiona wersja $a</p>
<p> Musisz uaktualniæ wersje PHP lub przenie¶æ na host z nowsz± wersj± PHP!</p>';
$string['safemode'] = 'Bezpieczny tryb';
$string['safemodeerror'] = 'Moodle ma trudno¶ci z w³±czeniem bezpiecznego trybu';
$string['sessionautostarterror'] = 'To powinno byæ wy³±czone';
$string['wwwroot'] = 'Adres w sieci';
$string['wwwrooterror'] = 'Adres w sieci wydaje siê byæ niepoprawny - wydaje siê ¿e nie ma tam instalacji Moodle';

?>
