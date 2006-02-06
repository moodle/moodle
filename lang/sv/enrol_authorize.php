<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.6 development (2005101200)


$string['adminauthorizeccapture'] = 'Översyn av beställningar och inställningar för 
\'Auto-Capture\' (automatiskt notera/registrera?)';
$string['adminauthorizeemail'] = 'Inställningar för sändning av e-post';
$string['adminauthorizesettings'] = 'Inställningar för Authorize.net ';
$string['adminauthorizewide'] = 'Inställningar på global webbplatsnivå';
$string['adminneworder'] = 'Kare administratör!

Du har fått en ny avvaktande beställning:

Beställning ID: $a->orderid
Transaktion ID: $a->transid
Användare: $a->user
Kurs: $a->course
Summa: $a->amount

\'AUTO-CAPTURE\' AKTIVERAD?: $a->acstatus

Om \'auto-capture\' är aktiverat så kommer 
kreditkortet att \'noteras\' $a->captureon
och sedan kommer studenten/eleven/deltagaren/
den lärande att registreras på kursen annars
kommer det att utgå på $a->expireon och då går 
det inte att \'notera\' efter denna dag. 

Du kan även omedelbart acceptera/avslå betalningen
för att registrera studenten/eleven/deltagaren/den
lärande genom att följa denna länk: 
$a->url
';
$string['adminnewordersubject'] = '$a->course: Ny avvaktande beställning($a->orderid)';
$string['adminreview'] = 'Granska beställningen igen innan kreditkortet behandlas.';
$string['amount'] = 'Summa';
$string['anlogin'] = 'Authorize.net: Namn för inloggning';
$string['anpassword'] = 'Authorize.net: Lösenord (inte obligatoriskt)';
$string['anreferer'] = 'Definiera referer URL om det är nödvändigt. 
Detta skickar raden \"Referer: URL\" som en 
inbäddad del  webb-förfrågan. ';
$string['antestmode'] = 'Authorize.net:  Testa bara transaktionerna (inga pengar kommer att dras)';
$string['antrankey'] = 'Authorize.net: Transaktionskod';
$string['authorizedpendingcapture'] = 'Auktoriserad/Avvaktar notering';
$string['canbecredit'] = 'Återbetalning kan ske t.o.m.  $a->upto';
$string['cancelled'] = 'Avbruten';
$string['capture'] = 'Noterad';
$string['capturedpendingsettle'] = 'Noterad/Avvaktar överenskommelse';
$string['capturedsettled'] = 'Noterad/Överenskommen';
$string['capturetestwarn'] = 'Noteringen tycks fungera men ingen post i databasen
har uppdaterats i testläge';
$string['captureyes'] = 'Kreditkortet kommer att bli noterat och studenten/eleven/
deltagaren/den lärande kommer att bli registrerad på kursen. Är Du säker?';
$string['ccexpire'] = 'Datum för utgång';
$string['ccexpired'] = 'Kreditkortet är utgånget';
$string['ccinvalid'] = 'Ogiltigt kortnummer';
$string['ccno'] = 'Nummer på kreditkort';
$string['cctype'] = 'Typ av kreditkort';
$string['ccvv'] = 'Verifiering av kort';
$string['ccvvhelp'] = 'Se på kortets baksida (de tre sista siffrorna)';
$string['choosemethod'] = 'Om Du har kursnyckeln för att registrera Dig på
kursen - skriv då in den; annars måste Du betala 
för den här kursen.';
$string['chooseone'] = 'Fyll i det ena eller båda av de följande fälten';
$string['credittestwarn'] = 'Kredit tycks vara accepterad men ingen post i databasen har lagts in i testläge';
$string['cutofftime'] = 'Avbrott av transaktion. När den senaste transaktion hämtas för överenskommelse?';
$string['description'] = 'Modulen Authorize.net gör det möjligt för Dig att
arrangera betalkurser. Om kostnaden för kursen är 
NOLL så blir inte
studenterna/eleverna/deltagarna/de lärande 
avkrävda någon betalning. Det finns en inställning
för kostnad som avser hela webbplatsen som Du kan
ställa in som standard och en inställning som
avser kurser som Du kan ställa in för varje 
enskild kurs. Kostnaden för en kurs gäller före
den för webbplatsen.<br /><br /><b>OBS!</b>Om Du anger en nyckel för att registrera sig på kursen
så kan studenter/elever/deltagare/lärande även 
registrera sig på det sättet. Detta kan Du använda
för att administrera en blandning av betalande och
inte-betalande deltagare.
';
$string['enrolname'] = 'Authorize.net: Credit Card Gateway';
$string['expired'] = 'Utgått';
$string['howmuch'] = 'Hur mycket?';
$string['httpsrequired'] = 'Tyvärr kan vi inte behandla Din förfrågan just nu.
Konfigurationen av den här webbplatsen fungerade
inte korrekt. <br /><br />
Var snäll och mata inte in numret på Ditt kreditkort
om Du inte ser ett gult lås längst ner på webbläsaren. 
Det betyder att alla data som sänds mellan klienten
och servern krypteras. Så informationen är skyddad 
under förflyttningen mellan två datorer och ingen 
kan fånga upp Ditt kortnummer under den proceduren.
';
$string['logindesc'] = 'Det här alternativet måste vara PÅ.
<br /><br />
Vi rekommenderar starkt att Du ställer in  alternativet
<a href=\"$a->url\">loginhttps ON</a> i Admin>> Variabler>> Säkerhet.
<br /><br /> 
Om Du aktiverar detta så kommer Moodle att använda en säker https anslutning enbart för sidorna för inloggning och betalning.';
$string['nameoncard'] = 'Namn på kort';
$string['noreturns'] = 'Inga \'returns\' ersättningar';
$string['notsettled'] = 'Inte överenskommen';
$string['orderid'] = 'ID för beställning';
$string['paymentmanagement'] = 'Administration av betalningar';
$string['paymentpending'] = 'Din betalning för den här kursen är avvaktande enligt det här beställningsnumret $a->orderid.';
$string['refund'] = 'Återbetalning';
$string['refunded'] = 'Återbetalad';
$string['returns'] = ' \'returns\' ersättningar';
$string['reviewday'] = 'Registrera automatiskt kreditkortet om inte en 
lärare eller administratör granskar beställningen
igen inom <b>$a</b> dagar. CRON MÅSTE VARA AKTIVERAT.<br />
(O dagar innebär att automatisk registrering
avaktiveras, det innebär också att  lärare, admin
granskar beställningen manuellt. Transaktionen 
kommer att avbrytas om Du avaktiverar autoregistrering 
eller om Du inte granskar den inom 30 dagar).';
$string['reviewnotify'] = 'Din betalning kommer att granskas. Du kan förvänta
Dig ett e-postmeddelande från Din lärare inom ett
par dagar.';
$string['sendpaymentbutton'] = 'Skicka betalning';
$string['settled'] = 'Överenskommen';
$string['settlementdate'] = 'Datum för överenskommelse';
$string['subvoidyes'] = 'Transaktion för återbetalning $a->transid kommer att
avbrytas och det kommer att kreditera Ditt konto med
$a->amount   Är Du säker?';
$string['tested'] = 'Testad';
$string['testmode'] = '[TESTLÄGE]';
$string['transid'] = 'ID för transaktion';
$string['unenrolstudent'] = 'Avregistrera lärande?';
$string['void'] = 'Void';
$string['voidtestwarn'] = '\'Void\' tycks fungera men ingen post i databasen har  uppdaterats i testläge';
$string['voidyes'] = 'Transaktionen kommer att avbrytas. Är Du säker?';
$string['zipcode'] = 'Postkod, t.ex . postnummer';

?>
