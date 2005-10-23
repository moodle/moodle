<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.6 development (2005101200)


$string['adminreview'] = 'Controleer de bestelling voor het aanvaarden van de kredietkaart';
$string['anlogin'] = 'Authorize.net: Login naam';
$string['anpassword'] = 'Authorize.net: Wachtwoord (niet vereistà';
$string['anreferer'] = 'Type hier de URL-verwijzing als je dit instelt met je authorize.net account. Dit zal een header \"Referer:URL\" in de webaanvraag zetten.';
$string['antestmode'] = 'Authorize.net: test transacties';
$string['antrankey'] = 'Authorize.net: transactiesleutel';
$string['ccexpire'] = 'Vervaldatum';
$string['ccexpired'] = 'De kredietkaart is vervallen';
$string['ccinvalid'] = 'Ongeldig kaartnummer';
$string['ccno'] = 'Kredietkaartnummer';
$string['cctype'] = 'Kredietkaarttype';
$string['ccvv'] = 'CV2';
$string['ccvvhelp'] = 'Kijk op de achterkant van de kaart (laatste 3 cijfers)';
$string['choosemethod'] = 'Als je de cursussleutel voor deze cursus kent, geef die dan in, indien je hem niet kent moet je betalen voor deze cursus';
$string['chooseone'] = 'Vul één of beide velden in';
$string['description'] = 'Met de Authorize.net module kun je betaalde cursussen inrichten via CC-providers. Als de prijs voor een cursus 0 is, dan krijgen leerlingen de vraag om te betalen niet. Er is een standaardprijs die je hier voor de hele site kunt instellen en er is een instelling om de prijs per cursus vast te leggen. De prijs per cursus gaat over de standaardprijs van de site.';
$string['enrolname'] = 'Authorize.net Kredietkaart toegang';
$string['httpsrequired'] = 'Jammer, maar je aanvraag kan nu niet verwerkt worden. De instellingen van deze site konden niet juist gezet worden<br /><br />
Geef het nummer van je kredietkaart niet in voor je een geel hangslot onderaan je browser ziet. Dat betekent dat alle informatie die over internet verstuurd wordt, versleuteld is. Op die manier is de informatie tijdens de transactie beschermd en kan je kredietkaartnummer niet onderschept worden op het internet.';
$string['logindesc'] = 'Deze optie moet AAN staan.<br /><br />
Je kunt de optie <a href=\"$a->url\">loginhttps</a> instellen in de sectie Variablen/Veiligheid.
<br /><br />
Door die instelling te gebruiken zal Moodle een veilige https-connectie maken voor de aanmelding- en betalingspagina\'s.';
$string['nameoncard'] = 'Naam op de kaart';
$string['reviewday'] = 'Vraag het kredietkaartnummer automatisch, tenzij een leraar of beheer de bestelling herziet binnen de <b>$a</b> dagen. CRON MOET INGESCHAKELD ZIJN.<br />
<0 dagen = schakel automatisch vragen uit = leraar of beheerder herzien de bestelling manueel. De transactie zal niet doorgaan als je automatisch kredietkaart vragen uitschakelt tenzij je ze goedkeurt binnen de 30 dagen.)';
$string['reviewnotify'] = 'Je betaling zal bekeken worden. Verwacht binnen enkele dagen een e-mail van je leraar.';
$string['sendpaymentbutton'] = 'Stuur betaling';
$string['zipcode'] = 'Postcode';

?>
