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
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC
 */

/*
 * To change this template, choose Tools | Templates.
 * and open the template in the editor.
 */

// General.
$string['pluginname'] = 'Turnitin plagiatplugin';
$string['turnitin'] = 'Turnitin';
$string['task_name'] = 'Pluginuppgiften plagiat i Turnitin';
$string['connecttesterror'] = 'Det uppstod ett fel vid försök att ansluta till Turnitin. Inkommande felmeddelande visas nedan:<br />';

// Assignment Settings.
$string['turnitin:enable'] = 'Aktivera Turnitin';
$string['excludebiblio'] = 'Exkludera källförteckning';
$string['excludequoted'] = 'Exkludera citerat material';
$string['excludevalue'] = 'Exkludera mindre matchningar';
$string['excludewords'] = 'Ord';
$string['excludepercent'] = 'Procent';
$string['norubric'] = 'Ingen bedömningsmatris';
$string['otherrubric'] = 'Använd en bedömnigsmatris som tillhör andra instruktörer';
$string['attachrubric'] = 'Bifoga en bedömningsmatris till denna uppgift';
$string['launchrubricmanager'] = 'Starta bedömningsmatrishanteraren';
$string['attachrubricnote'] = 'Observera: Studenterna kommer att kunna se bifogade bedömningsmatriser och deras innehåll innan de lämnar in.';
$string['anonblindmarkingnote'] = 'Obs: Den separata Turnitin-funktionen anonyma kommentarer har tagits bort. Turnitin kommer att använda Moodles blindkommentarer för att avgöra om funktionen anonyma kommentarer ska användas.';
$string['transmatch'] = 'Matchande översättning';
$string["reportgen_immediate_add_immediate"] = "Generera rapporter omedelbart. Inlämningar läggs till i arkivet omedelbart (om arkivet är konfigurerat).";
$string["reportgen_immediate_add_duedate"] = "Generera rapporter omedelbart. Inlämningar läggs till i arkivet på förfallodatumet (om arkivet är konfigurerat).";
$string["reportgen_duedate_add_duedate"] = "Generera rapporter på förfallodatumet. Inlämningar läggs till i arkivet på förfallodatumet (om arkivet är konfigurerat).";
$string['launchquickmarkmanager'] = 'Starta Quickmark-hanteraren';
$string['launchpeermarkmanager'] = 'Starta Peermark Hanteraren';
$string['studentreports'] = 'Visa originalitetsrapporter för studenter';
$string['studentreports_help'] = 'Låter dig visa Turnitin originalitetsrapporter till studentanvändare. Om inställd på ja kommer originalitetsrapporten som genereras av Turnitin att bli tillgänglig för studenten att se.';
$string['submitondraft'] = 'Lämna in filen när den har laddats upp';
$string['submitonfinal'] = 'Lämna in filen när den har laddats upp';
$string['draftsubmit'] = 'När ska filen lämnas in till Turnitin?';
$string['allownonor'] = 'Tillåt inlämning av alla filtyper?';
$string['allownonor_help'] = 'Denna inställning gör så att alla filtyper kan lämnas in. Med det här alternativet inställt på &#34;Ja&#34; kommer inlämningar att kontrolleras för originalitet när så är möjligt, inlämningar kommer att finnas tillgängliga för nedladdning, och verktyg för GradeMark-respons kommer att finnas tillgängliga när detta är möjligt.';
$string['norepository'] = 'Inget arkiv';
$string['standardrepository'] = 'Standardarkiv';
$string['submitpapersto'] = 'Lagra studentuppsatser';
$string['institutionalrepository'] = 'Institutionellt arkiv (Om tillämpligt)';
$string['checkagainstnote'] = 'Obs. Om du inte väljer "Ja" för minst en av alternativen under "Kontrollera mot..." kommer en originalitetsrapport inte att skapas.';
$string['spapercheck'] = 'Kontrollera mot lagrade studentuppsatser';
$string['internetcheck'] = 'Kontrollera mot internet';
$string['journalcheck'] = 'Kontrollera mot facktidskrifter,<br />tidningar och publikationer';
$string['compareinstitution'] = 'Jämför inlämnade filer med uppsatser som lämnats in inom denna institution';
$string['reportgenspeed'] = 'Rapportera generationshastighet';
$string['locked_message'] = 'Låst meddelande';
$string['locked_message_help'] = 'Om några inställningar låses kommer detta meddelande att visa varför.';
$string['locked_message_default'] = 'Den här inställningen låses på anläggningsnivå';
$string['sharedrubric'] = 'Delad rubrik';
$string['turnitinrefreshsubmissions'] = 'Uppdatera inlämningar';
$string['turnitinrefreshingsubmissions'] = 'Uppdaterar inlämningar';
$string['turnitinppulapre'] = 'Du måste först godkänna Turnitins EULA för att skicka in en fil. Om du väljer att inte acceptera vårt EULA skickas din fil endast till Moodle. Klicka här för att läsa och acceptera avtalet.';
$string['noscriptula'] = 'Eftersom du inte har JavaScript aktiverat måste du manuellt uppdatera den här sidan innan du kan göra en inlämning (efter att du accepterat Turnitins Användaravtal)';
$string['filedoesnotexist'] = 'Filen har raderats';
$string['reportgenspeed_resubmission'] = 'Du har redan lämnat in en uppsats i den här uppgiften och en Likhetsrapport skapades för din inlämning. Om du väljer att lämna in din uppsats igen kommer din tidigare inlämning att ersättas och en ny rapport kommer att skapas. Efter {$a->num_resubmissions} återinlämningar behöver du vänta {$a->num_hours} timmar efter en återinlämning för att kunna se en ny Likhetsrapport.';

// Plugin settings.
$string['config'] = 'Konfigurering';
$string['defaults'] = 'Standardinställningar';
$string['showusage'] = 'Visa datadump';
$string['saveusage'] = 'Spara datadump';
$string['errors'] = 'Fel';
$string['turnitinconfig'] = 'Konfiguration av plagiatplugin i Turnitin';
$string['tiiexplain'] = 'Turnitin är en kommersiell produkt och du måste ha ett betalt abonnemang för att använda den här tjänsten. För mer information se <a href=http://docs.moodle.org/en/Turnitin_administration>http://docs.moodle.org/en/Turnitin_administration</a>';
$string['useturnitin'] = 'Aktivera Turnitin';
$string['useturnitin_mod'] = 'Aktivera Turnitin för {$a}';
$string['turnitindefaults'] = 'Turnitin plagiatplugin, standardinställningar';
$string['defaultsdesc'] = 'Följande inställningar är standardvärden som fastställs vid aktivering av Turnitin inom en Aktivitetsmodul';
$string['turnitinpluginsettings'] = 'Turnitin plagiatplugin, inställningar';
$string['pperrorsdesc'] = 'Det finns ett problem med att ladda upp nedanstående filer till Turnitin. För att lämna in igen markerar du filerna du vill lämna in och trycker på inlämningsknappen. De kommer sedan att bearbetas nästa gång Cron körs.';
$string['pperrorssuccess'] = 'De valda filerna har lämnats in och kommer att bearbetas av Cron.';
$string['pperrorsfail'] = 'Ett problem uppståd med några av de valda filerna. En ny Cron-händelse kunde inte skapas för dem.';
$string['resubmitselected'] = 'Lämna in valda filer på nytt';
$string['deleteconfirm'] = 'Är du säker på att du vill radera denna inlämning?\n\nDenna åtgärd kan inte ångras.';
$string['deletesubmission'] = 'Radera inlämning';
$string['semptytable'] = 'Inga sökresultat hittades.';
$string['configupdated'] = 'Konfiguration uppdaterad';
$string['defaultupdated'] = 'Turnitin standardinställningar uppdaterade';
$string['notavailableyet'] = 'Ej tillgänglig';
$string['resubmittoturnitin'] = 'Lämna in igen till Turnitin';
$string['resubmitting'] = 'Ominlämning';
$string['id'] = 'ID';
$string['student'] = 'Student';
$string['course'] = 'Kurs';
$string['module'] = 'Modul';

// Grade book/View assignment page.
$string['turnitin:viewfullreport'] = 'Visa Originalitetsrapport';
$string['launchrubricview'] = 'Visa bedömningsmatrisen som används för kommentering';
$string['turnitinppulapost'] = 'Din fil har inte lämnats in till Turnitin. Klicka här för att godkänna våra användaravtal.';
$string['ppsubmissionerrorseelogs'] = 'Den här filen har inte skickas till Turnitin. Be din systemadministratör om hjälp.';
$string['ppsubmissionerrorstudent'] = 'Den här filen har inte skickas till Turnitin. Be din lärare om hjälp.';

// Receipts.
$string['messageprovider:submission'] = 'Meddelanden för digitala kvitton för plagiatplugin från Turnitin';
$string['digitalreceipt'] = 'Digitalt kvitto';
$string['digital_receipt_subject'] = 'Det här är ditt digitala kvitto från Turnitin';
$string['pp_digital_receipt_message'] = 'Hej {$a->firstname} {$a->lastname}!<br /><br />Du har lämnat in filen <strong>{$a->submission_title}</strong> för uppgift <strong>{$a->assignment_name}{$a->assignment_part}</strong> för kursen <strong>{$a->course_fullname}</strong> den <strong>{$a->submission_date}</strong>. Ditt inlämnings-ID är <strong>{$a->submission_id}</strong>. Ditt fullständiga digitala kvitto kan visas och skrivas ut med knappen skriv ut/ladda ner i dokumentvisaren.<br /><br />Tack för att du använder Turnitin,<br /><br />Turnitin-teamet';

// Paper statuses.
$string['turnitinid'] = 'Turnitin-ID';
$string['turnitinstatus'] = 'Turnitin-status';
$string['pending'] = 'Väntar på bekräftelse';
$string['similarity'] = 'Likhetsindex';
$string['notorcapable'] = 'Det går inte att skapa en originalitetsrapport för den här filen.';
$string['grademark'] = 'GradeMark';
$string['student_read'] = 'Studenten visade uppsatsen den:';
$string['student_notread'] = 'Studenten har inte visat denna uppsats.';
$string['launchpeermarkreviews'] = 'Starta Peermark-recensioner';

// Cron.
$string['ppqueuesize'] = 'Antal händelser i händelsekön för plagiatplugin';
$string['ppcronsubmissionlimitreached'] = 'Inga fler inlämningar kommer att skickas till Turnitin av denna Cron-aktivitet, eftersom endast {$a} bearbetas per körning';
$string['cronsubmittedsuccessfully'] = 'Inlämning: {$a->title} (TII ID: {$a->submissionid}) för uppgift {$a->assignmentname} på kursen {$a->coursename} har lämnats in till Turnitin.';
$string['pp_submission_error'] = 'Ett fel har uppstått hos Turnitin i samband med inlämningen:';
$string['turnitindeletionerror'] = 'Radering av inlämning för Turnitin misslyckades. Den lokala Moodle-kopian har tagits bort men inlämningen i Turnitin kunde inte raderas.';
$string['ppeventsfailedconnection'] = 'Inga händelser kommer att bearbetas av plagiatplugin i Turnitin för den här Cron-aktiviteten eftersom det inte gick att ansluta till Turnitin.';

// Error codes.
$string['tii_submission_failure'] = 'Vänd dig till din handledare eller administratör för mer information.';
$string['faultcode'] = 'Felkod';
$string['line'] = 'Rad';
$string['message'] = 'Meddelande';
$string['code'] = 'Kod';
$string['tiisubmissionsgeterror'] = 'Det uppstod ett fel vid försök att hämta inlämningar för denna Turnitin uppgift';
$string['errorcode0'] = 'Den här filen har inte skickas till Turnitin. Be din systemadministratör om hjälp.';
$string['errorcode1'] = 'Filen har inte skickats till Turnitin eftersom den inte har tillräckligt mycket innehåll för att skapa en originalitetsrapport.';
$string['errorcode2'] = 'Den här filen kommer inte att skickas till Turnitin eftersom den överstiger högsta tillåtna storlek på {$a->maxfilesize}.';
$string['errorcode3'] = 'Den här filen kunde inte skickas till Turnitin eftersom användaren inte har godkänt Turnitins licensavtal för slutanvändare.';
$string['errorcode4'] = 'Du måste ladda upp en filtyp som stöds för den här uppgiften. Godkända filtyper är: .doc, .docx, .ppt, .pptx, .pps, .ppsx, .pdf, .txt, .htm, .html, .hwp, .odt, .wpd, .ps och .rtf';
$string['errorcode5'] = 'Filen har inte lämnats in till Turnitin eftersom ett fel uppstod när modulen skapades i Turnitin, vilket förhindrar inlämningar. Mer information finns i dina API-loggar';
$string['errorcode6'] = 'Filen har inte lämnats in till Turnitin eftersom ett fel uppstod när modulinställningarna redigerades i Turnitin, vilket förhindrar inlämningar. Mer information finns i dina API-loggar.';
$string['errorcode7'] = 'Filen har inte lämnats in till Turnitin eftersom ett fel uppstod när användaren skapades i Turnitin, vilket förhindrar inlämningar. Mer information finns i dina API-loggar.';
$string['errorcode8'] = 'Filen har inte lämnats in till Turnitin eftersom ett fel uppstod när den tillfälliga filen skulle skapas. Detta beror sannolikt på ett felaktigt filnamn. Ge filen ett nytt namn och ladda upp den igen med Redigera inlämning.';
$string['errorcode9'] = 'Filen kan inte lämnas in eftersom det inte finns något tillgängligt innehåll i filpoolen att lämna in.';
$string['coursegeterror'] = 'Kunde inte hämta kursinformation';
$string['configureerror'] = 'Du måste konfigurera denna modul fullt ut som Administratör innan du använder den i en kurs. Var god kontakta din Moodle-administratör.';
$string['turnitintoolofflineerror'] = 'Vi har ett tillfälligt problem. Försök igen om en liten stund.';
$string['defaultinserterror'] = 'Det uppstod ett fel vid försök att infoga en standardvärdeinställning i databasen';
$string['defaultupdateerror'] = 'Det uppstod ett fel vid försök att uppdatera en standardvärdeinställning i databasen';
$string['tiiassignmentgeterror'] = 'Det uppstod ett fel vid försök att hämta en uppgift från Turnitin';
$string['assigngeterror'] = 'Gick inte att hämta Turnitin data';
$string['classupdateerror'] = 'Kunde inte uppdatera Turnitin-klassinformation';
$string['pp_createsubmissionerror'] = 'Det uppstod ett fel vid försök att skapa en inlämning i Turnitin';
$string['pp_updatesubmissionerror'] = 'Det uppstod ett fel vid försök att återinlämna din uppgift till Turnitin';
$string['tiisubmissiongeterror'] = 'Det uppstod ett fel vid försök att hämta ett inlämnande från Turnitin';

// Javascript.
$string['closebutton'] = 'Stäng';
$string['loadingdv'] = 'Laddar dokumentvisaren i Turnitin...';
$string['changerubricwarning'] = 'Om du ändrar eller tar bort en bedömningsmatris kommer detta att avlägsna all existerande bedömningsmatris-poäng från samtliga uppsatser i denna uppgift, inklusive poängkort som har markerats tidigare. Sammanlagda betyg för tidigare betygsatta uppsatser kommer att finnas kvar.';
$string['messageprovider:submission'] = 'Meddelanden för digitala kvitton för plagiatplugin från Turnitin';

// Turnitin Submission Status.
$string['turnitinstatus'] = 'Turnitin-status';
$string['deleted'] = 'Raderad';
$string['pending'] = 'Väntar på bekräftelse';
$string['because'] = 'Detta beror på att administratören raderade uppgiften som väntade på bekräftelse från uppgiftskön och avbröt inlämningen till Turnitin.<br /><strong>Filen finns fortfarande i Moodle. Kontakta din lärare.</strong><br />Se nedanstående felkoder:';
$string['submitpapersto_help'] = '<strong>Inget arkiv: </strong><br />Enligt anvisning sparar Turnitin inte dokument i något arkiv. Uppsatser behandlas endast för den första likhetsgranskningen.<br /><br /><strong>Standardarkiv: </strong><br />Turnitin sparar en kopia av det inlämnade dokumentet endast i standardarkivet. Om alternativet väljs, använder Turnitin endast sparade dokument i framtida likhetsgranskningar av andra dokument.<br /><br /><strong>Institutionellt arkiv (Om tillämpligt): </strong><br />Om alternativet väljs, sparar Turnitin inlämnade dokument endast i institutionens privata arkiv. Likhetsgranskningar av inlämnade dokument görs endast av andra instruktörer vid institutionen.';
$string['errorcode12'] = 'Den här filen har inte skickats till Turnitin eftersom den tillhör en uppgift där kursen har tagits bort. Rad-ID: ({$a->id}) | Kursmodul-ID: ({$a->cm}) | Användar-ID: ({$a->userid})';
$string['errorcode12'] = 'Den här filen har inte skickats in till Turnitin då aktivitetsmodulen den tillhör inte kunde hittas';
$string['tiiaccountconfig'] = 'Kontokonfigurering för Turnitin';
$string['turnitinaccountid'] = 'Turnitin Konto-ID';
$string['turnitinsecretkey'] = 'Turnitin Delad Nyckel';
$string['turnitinapiurl'] = 'Turnitin API-URL';
$string['tiidebugginglogs'] = 'Felsökning och loggning';
$string['turnitindiagnostic'] = 'Aktivera Diagnostikläge';
$string['turnitindiagnostic_desc'] = '<b>[Varning]</b><br />Aktivera Diagnostikläge endast för att spåra problem med Turnitin API.';
$string['tiiaccountsettings_desc'] = 'Kontrollera att dessa inställningar matchar de som du har konfiguerat i ditt Turnitin-konto. Annars kan du få problem med att skapa uppgifter och/eller studentinlämningar.';
$string['tiiaccountsettings'] = 'Kontoinställningar för Turnitin';
$string['turnitinusegrademark'] = 'Använd GradeMark';
$string['turnitinusegrademark_desc'] = 'Välj om du vill använda GradeMark eller Moodle för att betygsätta inlämningar.<br /><i>(Detta är endast tillgängligt för dem som har GradeMark konfigurerat för sitt konto)</i>';
$string['turnitinenablepeermark'] = 'Aktivera Peermark Uppgifter';
$string['turnitinenablepeermark_desc'] = 'Välj om du vill tillåta skapandet av PeerMark Uppgifter<br/><i>(Detta är endast tillgängligt för de som har PeerMark konfigurerat för sitt konto)</i>';
$string['transmatch_desc'] = 'Avgör om Matchande översättning ska vara en aktiv inställning på inställningsskärmen för uppgiften.<br /><i>(Aktivera endast detta alternativ om Matchande översättning är aktiverat för ditt Turnitin-konto)</i>';
$string['repositoryoptions_0'] = 'Aktivera instruktörens alternativ för standardarkiv';
$string['repositoryoptions_1'] = 'Aktivera utvidgade arkiveringsalternativ för lärare';
$string['repositoryoptions_2'] = 'Skicka alla uppsatser till standardarkivet';
$string['repositoryoptions_3'] = 'Skicka inte in några uppsatser till ett arkiv';
$string['turnitinrepositoryoptions'] = 'Arkiv för uppgifter';
$string['turnitinrepositoryoptions_desc'] = 'Välj arkivalternativen för Turnitin-uppgifter. <br /><i>(Institutionellt arkiv är endast tillgängligt för de som har det aktiverat på sitt konto)</i>';
$string['tiimiscsettings'] = 'Övriga plugin-inställningar';
$string['pp_agreement_default'] = 'Jag bekräftar att detta inlämnande är mitt eget arbete och jag accepterar allt ansvar för eventuella intrång i upphovsrätten som kan uppstå som en följd av detta inlämnande.';
$string['pp_agreement_desc'] = '<b>[Valfritt]</b><br />Ange en avtalsbekräftelse för inlämningar. <br />(<b>Obs:</b> Om avtalsdelen lämnas helt tom kommer ingen avtalsbekräftelse att krävas av studenterna under deras inlämnande.';
$string['pp_agreement'] = 'Friskrivningsklausul/avtal';
$string['studentdataprivacy'] = 'Sekretessinställningar för Studentdata';
$string['studentdataprivacy_desc'] = 'Följande inställningar kan konfigureras så att studentens personuppgifter inte överförs till Turnitin via API.';
$string['enablepseudo'] = 'Aktivera studentsekretess';
$string['enablepseudo_desc'] = 'Om detta alternativ väljs kommer studenternas e-postadresser att omvandlas till en pseudo motsvarighet för Turnitin API-samtal.<br /><i>(<b>Obs:</b> Detta alternativ går inte att ändra om Moodle användardata redan har synkroniserats med Turnitin vid ett tidigare tillfälle)</i>';
$string['pseudofirstname'] = 'Pseudonym, förnamn (Student)';
$string['pseudofirstname_desc'] = '<b>[Tillval]</b><br />Studentens förnamn som ska visas i Turnitins dokumentvisare';
$string['pseudolastname'] = 'Pseudonym, efternamn (Student)';
$string['pseudolastname_desc'] = 'Studentens efternamn som ska visas i Turnitins dokumentvisare';
$string['pseudolastnamegen'] = 'Generera Efternamn Automatiskt';
$string['pseudolastnamegen_desc'] = 'Om inställt på ja och pseudo efternamnet är inställt på ett användarprofil-fält, så kommer fältet automatiskt att fyllas i med en unik identifierare.';
$string['pseudoemailsalt'] = 'Pseudokryptering, salt';
$string['pseudoemailsalt_desc'] = '<b>[Valfritt]</b><br />Ett valfritt salt för att öka komplexiteten i den genererade Pseudo Student-epostadressen.<br />(<b>Obs:</b> Saltet bör förbli oförändrat för att bibehålla konsekventa pseudo e-postadresser)';
$string['pseudoemaildomain'] = 'Pseudo-epostdomän';
$string['pseudoemaildomain_desc'] = '<b>[Valfritt]</b><br />En valfri domän för pseudo-epostadresser. (@tiimoddle.com är standard om fältet lämnas tomt)';
$string['pseudoemailaddress'] = 'Pseudonym, e-postadress';
$string['connecttest'] = 'Testa anslutningen till Turnitin';
$string['connecttestsuccess'] = 'Moodle har nu anslutits till Turnitin.';
$string['diagnosticoptions_0'] = 'Av';
$string['diagnosticoptions_1'] = 'Standardklass';
$string['diagnosticoptions_2'] = 'Felsökning';
$string['repositoryoptions_4'] = 'Skicka alla uppsatser till institutionens datalager';
$string['turnitinrepositoryoptions_help'] = '<strong>Aktivera instruktörens alternativ för standardarkiv: </strong><br />Instruktörer kan instruera Turnitin att antingen lägga till dokument i standardarkivet eller i institutionens privata arkiv eller inte lägga till dokument i något arkiv.<br /><br /><strong>Aktivera utvidgade arkiveringsalternativ för lärare: </strong><br />Med detta alternativ kan instruktörer visa en uppgiftsinställning som tillåter studenter att bestämma vart Turnitin sparar deras dokument. Studenter kan välja att spara sina dokument i standardarkivet eller i institutionens privata arkiv.<br /><br /><strong>Skicka alla uppsatser till standardarkivet: </strong><br />Alla dokument läggs till i standardarkivet som standard.<br /><br /><strong>Skicka inte in några uppsatser till ett arkiv: </strong><br />Dokument används endast för den första kontrollen med Turnitin och för visning för instruktören för betygsättning.<br /><br /><strong>Skicka alla uppsatser till institutionens datalager: </strong><br />Enligt anvisning sparar Turnitin alla uppsatser i institutionens arkiv. Likhetsgranskningar av inlämnade uppsatser görs endast av instruktörer vid institutionen.';
$string['turnitinuseanon'] = 'Använd Anonyma Kommentarer';
$string['createassignmenterror'] = 'Det uppstod ett fel vid försök att skapa uppgiften i Turnitin';
$string['editassignmenterror'] = 'Det uppstod ett fel vid försök att redigera uppgiften i Turnitin';
$string['ppassignmentediterror'] = 'Modulen {$a->title} (TII ID: {$a->assignmentid}) kunde inte redigeras i Turnitin. Mer information finns i dina API-loggar.';
$string['pp_classcreationerror'] = 'Den här klassen kunde inte skapas i Turnitin. Hänvisa till dina API-loggar för mer information.';
$string['unlinkusers'] = 'Ta bort länk från användare';
$string['relinkusers'] = 'Återkoppla Användare';
$string['unlinkrelinkusers'] = 'Lösgör / Återkoppla Turnitin Användare';
$string['nointegration'] = 'Ingen Integration';
$string['sprevious'] = 'Föregående';
$string['snext'] = 'Nästa';
$string['slengthmenu'] = 'Visa_MENU_Inlägg';
$string['ssearch'] = 'Sök:';
$string['sprocessing'] = 'Hämtar information från Turnitin...';
$string['szerorecords'] = 'Inga uppgifter att visa.';
$string['sinfo'] = 'Visar _START_till_END_av_TOTAL_inlägg.';
$string['userupdateerror'] = 'Kunde inte uppdatera användardata';
$string['connecttestcommerror'] = 'Kunde inte ansluta till Turnitin. Dubbelkolla din inställningen för API-webbadressen.';
$string['userfinderror'] = 'Det uppstod ett fel vid försök att hitta användaren i Turnitin';
$string['tiiusergeterror'] = 'Det uppstod ett fel vid försök att hämta användaruppgifterna från Turnitin';
$string['usercreationerror'] = 'Turnitin användare gick inte att skapa';
$string['ppassignmentcreateerror'] = 'Den här modulen kunde inte skapas i Turnitin. Hänvisa till dina API-loggar för mer information.';
$string['excludebiblio_help'] = 'Denna inställning gör det möjligt för instruktören att välja exkludering av text som återges i studentuppsatsen (källförteckningen, citerade verk, eller referens-sektioner) från att kontrolleras för matchning (vid skapande av Originalitetsrapporter). Denna inställning kan upphävas för enskilda Originalitetsrapporter.';
$string['excludequoted_help'] = 'Denna inställning gör det möjligt för instruktören att välja exkludering av text som återges i citat (från att kontrolleras för matchningar vid skapande av Originalitetsrapporter). Denna inställning kan upphävas för enskilda Originalitetsrapporter.';
$string['excludevalue_help'] = 'Denna inställning gör det möjligt för instruktören att utesluta matchningar som inte är av tillräcklig längd (detta bestäms av instruktören). Dessa blir då uteslutna vid skapande av Originalitetsrapporter. Denna inställning kan upphävas vid enskilda Originalitetsrapporter.';
$string['spapercheck_help'] = 'Kontrollera mot Turnitins arkiv för studentuppsatser vid behandling av Originalitetsrapporter för uppsatser. Procentandelen för likhetsindexet kan minska om ett detta är avmarkerat.';
$string['internetcheck_help'] = 'Kontrollera mot Turnitins internetarkiv vid behandling av Originalitetsrapporter för uppsatser. Procentandelen för likhetsindexet kan minska om ett detta är avmarkerat.';
$string['journalcheck_help'] = 'Kontrollera mot facktidskrifter, tidningar och publikationer i Turnitins arkiv vid behandling av Originalitetsrapporter för uppsatser. Procentandelen för likhetsindexet kan minska om ett detta är avmarkerat.';
$string['reportgenspeed_help'] = 'Det finns tre alternativ för den här uppgiftens inställningar: &#39;Generera rapporter omedelbart. Inlämningar läggs till i arkivet på förfallodatumet (om arkivet är konfigurerat).&#39;, &#39;Generera rapporter omedelbart. Inlämningar läggs till i arkivet omedelbart (om arkivet är konfigurerat).&#39; och &#39;Generera rapporter på förfallodatumet. Inlämningar läggs till i arkivet på förfallodatumet (om arkivet är konfigurerat)."&#39;<br /><br />Alternativet &#39;Generera rapporter omedelbart. Inlämningar läggs till i arkivet på förfallodatumet (om arkivet är konfigurerat).&#39; skapar en originalitetsrapport när studenten lämnar in. Med detta alternativ kommer dina elever inte att kunna göra flera inlämningar av uppgiften.<br /><br />För att tillåta ominlämningar väljer du alternativet &#39;Generera rapporter omedelbart. Inlämningar läggs till i arkivet omedelbart (om arkivet är konfigurerat).. Detta låter eleverna lämna in flera inlämningar fram till inlämningsdatumet. Det kan ta upp till 24 timmar skapa originalitetsrapporter för ominlämningar.<br /><br />Alternativet &#39;Generera rapporter på förfallodatumet. Inlämningar läggs till i arkivet på förfallodatumet (om arkivet är konfigurerat)." Den här inställningen gör att alla uppsatser som lämnas in kommer att jämföras med varandra när originalitetsrapporterna skapas.';
$string['turnitinuseanon_desc'] = 'Välj om du vill möjliggöra Anonyma kommentarer vid betygsättning av inlämningar.<br /><i>(Detta är endast tillgängligt för dem som har Anonyma Kommentarer konfigurerat för sitt konto)</i>';
