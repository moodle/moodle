<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Swedish strings for tincanlaunch
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package mod_tincanlaunch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['apCreationFailed'] = 'Misslyckades med att skapa Watershed Activity Provider.';
$string['badarchive'] = 'Du måste ha en giltigt zip-fil';
$string['badimsmanifestlocation'] = 'En tincan.xml-fil hittades, men den var inte placerad i roten på din zip-fil, du behöver paketera om din kurs';
$string['badmanifest'] = 'Vissa fel i manifest: se fellogg';
$string['behaviorheading'] = 'Modulens beteende';
$string['checkcompletion'] = 'Kontrollera genomförande';
$string['completionexpiry'] = 'Förfaller';
$string['completionexpirygroup'] = 'Genomförande förfaller efter (dagar)';
$string['completionexpirygroup_help'] = 'Om valt, så kommer Totara vid sökning efter genomförande-data endast söka på data som lagrats i LRS under de senaste X dagarna. Totara kommer också ta bort genomförande för deltagare som har ett tidigare, numera förfallet, genomförande.';
$string['completionverb'] = 'Verb';
$string['completionverbgroup'] = 'Spåra genomförande via verb';
$string['completionverbgroup_help'] = 'Totara kommer att söka efter uttryck (statements) där aktören är nuvarande användaren, objektet är specificerat av aktivitets-ID och verbet är det som angivits här. Om det hittar ett matchande uttryck, så kommer aktiviteten bli markerad som genomförd.';
$string['eventactivitycompleted'] = 'Aktivitet genomförd';
$string['eventactivitylaunched'] = 'Aktivitet startad';
$string['expirecredentials'] = 'Uppgifter om förfaller';
$string['idmissing'] = 'Du måste ange ett "course_module ID" eller ett "instance ID"';
$string['lrsdefaults'] = 'Förvalda LRS-inställningar';
$string['lrsheading'] = 'LRS-inställningar';
$string['lrssettingdescription'] = 'Som standard, så använder denna aktivitet de globala LRS-inställningarna som hittas under Administration av webbplats > Plugin-program > Aktivitetsmoduler > Tin Can Launch Link. För att ändra inställningarna för just denna aktivitet, välj Lås upp förvalda inställningar.';
$string['modulename'] = 'Tin Can Launch Link';
$string['modulename_help'] = 'En plugin-modul för Totara som gör det möjligt att starta TinCan (xAPI)-innehåll, som då kan spåras i ett separat LRS (Learning Record Store).';
$string['modulenameplural'] = 'Tin Can Launch Links';
$string['nomanifest'] = 'Felaktigt filpaket - saknar tincan.xml';
$string['overridedefaults'] = 'Lås upp förvalda inställningar';
$string['overridedefaults_help'] = 'Tillåter att aktiviteten har andra LRS-inställningar än de som gäller som förvalda inställningar på webbplatsnivå.';
$string['pluginadministration'] = 'Administration av Tin Can Launch Link';
$string['pluginname'] = 'Tin Can Launch Link';
$string['tincanactivityid'] = 'Aktivitets-ID';
$string['tincanactivityid_help'] = 'Den IRI som identifierar den primära aktivitet som startas.';
$string['tincanlaunch'] = 'Tin Can Launch Link';
$string['tincanlaunch_attempt'] = 'Starta ny registrering';
$string['tincanlaunch_completed'] = 'Övning genomförd!';
$string['tincanlaunch_notavailable'] = 'LRS (Learning Record Store) är inte tillgängligt. Kontakta din webbplatsadministratör. Om du är administratör, gå till Administration av webbplats / Utveckling / Felsökning (debugging) - och sätt Meddelanden om felsökning (debug) till UTVECKLARE. När du har fått tag på felmeddelanden, kom ihåg att återställa till INGA eller MINIMAL.';
$string['tincanlaunch_progress'] = 'Försök pågår.';
$string['tincanlaunch_regidempty'] = 'Registrerings-ID hittades inte. Stäng detta fönster.';
$string['tincanlaunch:addinstance'] = 'Lägg till en ny TinCan (xAPI)-aktivitet till en kurs';
$string['tincanlaunchcustomacchp'] = 'Anpassad hemsida för konto';
$string['tincanlaunchcustomacchp_help'] = 'Om angivet, så kommer Totara använda denna hemsida i samband med ID-numret i användarens profilfält för att identifiera deltagaren. Om ID-numret inte är angett för deltagaren, så kommer de istället bli identifierade via e-postadress eller Totaras ID-nummer. Notera: om en deltagares ID förändras, så kommer de förlora tillgång till sina registreringar som hänger ihop med gamla ID:n, och genomförande-data kommer att nollställas. Rapporter i ditt LRS kan också påverkas.';
$string['tincanlaunchlrsauthentication'] = 'LRS-integration';
$string['tincanlaunchlrsauthentication_help'] = 'Använd tilläggsfunktionalitet för integration för att skapa nya autentiseringsuppgifter vid varje start för de LRS som stödjer detta.';
$string['tincanlaunchlrsauthentication_option_0'] = 'Ingen';
$string['tincanlaunchlrsauthentication_option_1'] = 'Watershed';
$string['tincanlaunchlrsauthentication_option_2'] = 'Learning Locker';
$string['tincanlaunchlrsauthentication_watershedhelp'] = 'Notering: för integration med Watershed, så krävs inte att API-access är aktiverat.';
$string['tincanlaunchlrsauthentication_watershedhelp_label'] = 'Watershed-integration';
$string['tincanlaunchlrsduration'] = 'Tidslängd';
$string['tincanlaunchlrsduration_default'] = '9000';
$string['tincanlaunchlrsduration_help'] = 'Används med "LRS integrated basic authentication". Begär att LRS håller autentiseringsuppgifter giltiga under detta antal minuter.';
$string['tincanlaunchlrsendpoint'] = 'Endpoint';
$string['tincanlaunchlrsendpoint_help'] = 'Endpoint för LRS (t ex https://lrs.example.com/endpoint/). Måste inkludera snedstreck.';
$string['tincanlaunchlrsfieldset'] = 'Förvalda värden för aktivitetsinställningar i TinCan Launch Link';
$string['tincanlaunchlrsfieldset_help'] = 'Dessa är förvalda inställningar på webbplatsnivå som används när en ny aktivitet skapas. För varje unik aktivitet kan förvalda värden ignoreras och specifika alternativa värden anges.';
$string['tincanlaunchlrslogin'] = 'Basic Login';
$string['tincanlaunchlrslogin_help'] = 'Din login-nyckel för LRS';
$string['tincanlaunchlrspass'] = 'Basic Password';
$string['tincanlaunchlrspass_help'] = 'Ditt LRS-lösenord (hemlighet)';
$string['tincanlaunchname'] = 'Namn på Launch Link';
$string['tincanlaunchname_help'] = 'Namnet på Launch Link, såsom det kommer att visas för användaren.';
$string['tincanlaunchurl'] = 'Launch URL';
$string['tincanlaunchurl_help'] = 'Bas-URL för den TinCan-aktivitet du vill starta (t ex https://example.com/content/index.html).';
$string['tincanlaunchuseactoremail'] = 'Identifiera via e-post';
$string['tincanlaunchuseactoremail_help'] = 'Om valt, så kommer deltagare att bli identifierade på sin e-postadress, om de har en lagrad i Totara.';
$string['tincanlaunchviewfirstlaunched'] = 'Först startad';
$string['tincanlaunchviewlastlaunched'] = 'Senast startad';
$string['tincanlaunchviewlaunchlink'] = 'starta';
$string['tincanlaunchviewlaunchlinkheader'] = 'Startlänk';
$string['tincanmultipleregs'] = 'Tillåt multipla organisationer';
$string['tincanmultipleregs_help'] = 'Om valt, tillåt att deltagaren startar mer än en registrering på aktiviteten. Deltagare kan alltid gå tillbaka till en registrering de har startat, även om denna inställning inte är vald.';
$string['tincanpackage'] = 'Zip-paket';
$string['tincanpackage_help'] = 'Om du har kurs paketerad i TinCan (xAPI)-format så kan du ladda upp den här. Om du laddar upp en kurs, så kommer ovanstående fält för Launch URL och Aktivitets-ID automatiskt att fyllas i när du sparar. Detta genom att denna data då hämtas från filen tincan.xml som är en del av zip-paketet. Du kan redigera dessa inställningar när som helst, men du bör inte ändra Aktivitets-ID (vare sig direkt eller via filuppladdning) om du inte förstår konsekvenserna av detta.';
$string['tincanpackagetext'] = 'Du kan fylla i inställningar för Launch URL och Aktivitets-ID direkt, eller genom att ladda upp ett zip-paket som innehåller en tincan.xml-fil. Den Launch URL som definieras i tincan.xml kan peka på andra filer i zip-paketet, eller till en extern URL. Aktivitets-ID måste alltid vara en fullständig URL (eller annan IRI).';
$string['tincanpackagetitle'] = 'Start-inställningar';
