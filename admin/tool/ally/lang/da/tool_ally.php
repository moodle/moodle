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
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['adminurl'] = 'Start-URL';
$string['adminurldesc'] = 'LTI-start-URL, som bruges til at få adgang til tilgængelighedsrapporten.';
$string['allyclientconfig'] = 'Ally-konfiguration';
$string['ally:clientconfig'] = 'Få adgang til, og opdater, klientkonfiguration';
$string['ally:viewlogs'] = 'Visning af Ally-logfiler';
$string['clientid'] = 'Klient-ID';
$string['clientiddesc'] = 'Klient-ID for Ally';
$string['code'] = 'Kode';
$string['contentauthors'] = 'Indholdets forfattere';
$string['contentauthorsdesc'] = 'Administratorer og brugere, som er tildelt disse roller, vil få deres uploadede kursusfiler vurderet i forhold til deres tilgængelighed. Filerne vil få en tilgængelighedsbedømmelse. En lav bedømmelse vil betyde, at filen skal ændres og gøres mere tilgængelig.';
$string['contentupdatestask'] = 'Opdatering af indhold';
$string['curlerror'] = 'cURL-fejl: {$a}';
$string['curlinvalidhttpcode'] = 'Ugyldig HTTP-statuskode: {$a}';
$string['curlnohttpcode'] = 'Kan ikke bekræfte HTTP-statuskoden';
$string['error:invalidcomponentident'] = 'Ugyldigt komponent-ID {$a}';
$string['error:pluginfilequestiononly'] = 'Kun spørgsmålskomponenter er understøttet for denne url';
$string['error:componentcontentnotfound'] = 'Indhold ikke fundet for {$a}';
$string['error:wstokenmissing'] = 'Webtjenestetoken mangler. Måske skal en administratorbruger køre en automatisk konfiguration?';
$string['excludeunused'] = 'Udelad ubrugte filer';
$string['excludeunuseddesc'] = 'Udelad filer, der er vedhæftet HTML-indhold, men som er linket/referencer i HTML-filen.';
$string['filecoursenotfound'] = 'Den indsendte fil tilhører ikke noget kursus';
$string['fileupdatestask'] = 'Push filopdateringer til Ally';
$string['id'] = 'Id';
$string['key'] = 'Nøgle';
$string['keydesc'] = 'LTI-forbrugernøglen.';
$string['level'] = 'Niveau';
$string['message'] = 'Besked';
$string['pluginname'] = 'Ally';
$string['pushurl'] = 'URL for filopdateringer';
$string['pushurldesc'] = 'Push notifikationer om filopdateringer til denne URL.';
$string['queuesendmessagesfailure'] = 'Der opstod en fejl, da vi sendte beskeder til AWS SQS. Fejldata: $a';
$string['secret'] = 'Hemmelighed';
$string['secretdesc'] = 'LTI-hemmeligheden.';
$string['showdata'] = 'Vis data';
$string['hidedata'] = 'Skjul data';
$string['showexplanation'] = 'Vis forklaring';
$string['hideexplanation'] = 'Skjul forklaring';
$string['showexception'] = 'Vis undtagelse';
$string['hideexception'] = 'Skjul undtagelse';
$string['usercapabilitymissing'] = 'Den angivne bruger har ikke den nødvendige egenskab til at slette denne fil.';
$string['autoconfigure'] = 'Konfigurer Allys webservice automatisk';
$string['autoconfiguredesc'] = 'Opret webservicerolle og bruger for Ally automatisk.';
$string['autoconfigureconfirmation'] = 'Opret webtjenesterolle og bruger for Ally automatisk, og aktivér webtjeneste. Følgende handlinger udføres:<ul><li>opret en rolle med titlen &quot;ally_webservice&quot; og en bruger med brugernavnet &quot;ally_webuser&quot;</li><li>føj &quot;ally_webuser&quot;-brugeren til rollen &quot;ally_webservice&quot;</li><li>aktivér webtjenester</li><li>aktivér de resterende webtjenesteprotokoller</li><li>aktivér Ally-webtjeneste automatisk</li><li>opret en token for &quot;ally_webuser&quot;-kontoen</li></ul>';
$string['autoconfigsuccess'] = 'Fuldført – Ally-webtjenesten er blevet konfigureret.';
$string['autoconfigtoken'] = 'Token for webtjenesten er som følger:';
$string['autoconfigapicall'] = 'På følgende URL kan du teste, om webtjenesten fungerer:';
$string['privacy:metadata:files:action'] = 'Handlingen, som er foretaget på filen, f.eks: oprettet, opdateret eller slettet.';
$string['privacy:metadata:files:contenthash'] = 'Filens indholds-hash for at afgøre entydighed.';
$string['privacy:metadata:files:courseid'] = 'Kursus-ID&apos;et, som filen tilhører.';
$string['privacy:metadata:files:externalpurpose'] = 'For at kunne integreres med Ally, skal der udveksles filer med Ally.';
$string['privacy:metadata:files:filecontents'] = 'Den faktiske fils indhold sendes til Ally, så det kan blive evalueret for tilgængelighed.';
$string['privacy:metadata:files:mimetype'] = 'Filens MIME-type, f.eks tekst/almindelig, billede/jpeg, osv.';
$string['privacy:metadata:files:pathnamehash'] = 'Filens stinavns-hash for at den kan defineres entydigt.';
$string['privacy:metadata:files:timemodified'] = 'Tidspunktet, hvor feltet sidst blev ændret.';
$string['cachedef_annotationmaps'] = 'Gem noteringsdata for kurser';
$string['cachedef_fileinusecache'] = 'Tilknyt filer i brugscache';
$string['cachedef_pluginfilesinhtml'] = 'Tilknyt filer i HTML-cache';
$string['cachedef_request'] = 'Ally-filterets anmodningscache';
$string['pushfilessummary'] = 'Oversigt over Ally-filopdateringer.';
$string['pushfilessummary:explanation'] = 'Oversigt over filopdateringer sendt til Ally.';
$string['section'] = 'Sektion {$a}';
$string['lessonanswertitle'] = 'Svar for lektion &quot;{$a}&quot;';
$string['lessonresponsetitle'] = 'Svar for lektion &quot;{$a}&quot;';
$string['logs'] = 'Ally-logfiler';
$string['logrange'] = 'Loginterval';
$string['loglevel:none'] = 'Ingen';
$string['loglevel:light'] = 'Let';
$string['loglevel:medium'] = 'Mellem';
$string['loglevel:all'] = 'Alle';
$string['logcleanuptask'] = 'Oprydningsopgave for Ally-logfiler';
$string['loglifetimedays'] = 'Behold logfiler så mange dage';
$string['loglifetimedaysdesc'] = 'Behold Ally-logfiler i så mange dage. Angiv 0 for aldrig at slette logfiler. En planlagt opgave er (som standard) indstillet til at køre dagligt og fjerner logposter, der er mere end så mange dage gamle.';
$string['logger:filtersetupdebugger'] = 'Ally-filterets opsætningslog';
$string['logger:pushtoallysuccess'] = 'Push til Ally-slutpunkt lykkedes';
$string['logger:pushtoallyfail'] = 'Push til Ally-slutpunkt lykkedes ikke';
$string['logger:pushfilesuccess'] = 'Push af filen/filerne til Ally-slutpunkt blev gennemført.';
$string['logger:pushfileliveskip'] = 'Direkte push af filer mislykkedes';
$string['logger:pushfileliveskip_exp'] = 'Springer direkte push af filer over på grund af kommunikationsproblemer. Direkte push af filer vil blive genoprettet, når opdatering af filerne er fuldført. Tjek din konfiguration.';
$string['logger:pushfileserror'] = 'Push til Ally-slutpunkt lykkedes ikke';
$string['logger:pushfileserror_exp'] = 'Fejl knyttet til push af indholdsopdatering til Ally-tjenester.';
$string['logger:pushcontentsuccess'] = 'Push af indhold til Ally-slutpunkt blev gennemført';
$string['logger:pushcontentliveskip'] = 'Direkte push af indhold mislykkedes';
$string['logger:pushcontentliveskip_exp'] = 'Springer direkte push af indhold over på grund af kommunikationsproblemer. Direkte push af indhold vil blive genoprettet, når opdatering af indhold er fuldført. Tjek din konfiguration.';
$string['logger:pushcontentserror'] = 'Push til Ally-slutpunkt lykkedes ikke';
$string['logger:pushcontentserror_exp'] = 'Fejl knyttet til push af indholdsopdatering til Ally-tjenester.';
$string['logger:addingconenttoqueue'] = 'Tilføjer indhold til pushkøen';
$string['logger:annotationmoderror'] = 'Annotering af Ally-modulindhold mislykkedes.';
$string['logger:annotationmoderror_exp'] = 'Modulet blev ikke identificeret.';
$string['logger:failedtogetcoursesectionname'] = 'Kunne ikke hente kursussektionens navn.';
$string['logger:moduleidresolutionfailure'] = 'Kunne ikke løse modulets ID';
$string['logger:cmidresolutionfailure'] = 'Kunne ikke fortolke kursusmodulets ID';
$string['logger:cmvisibilityresolutionfailure'] = 'Kunne ikke fortolke kursusmodulets synlighed';
$string['courseupdatestask'] = 'Push kursusbegivenheder til Ally';
$string['logger:pushcoursesuccess'] = 'Push af kursusbegivenhed(er) til Ally-slutpunkt blev fuldført';
$string['logger:pushcourseliveskip'] = 'Direkte push af kursusbegivenhed mislykkedes';
$string['logger:pushcourseerror'] = 'Direkte push af kursusbegivenhed mislykkedes';
$string['logger:pushcourseliveskip_exp'] = 'Springer direkte push af kursusbegivenhed(er) over på grund af kommunikationsproblemer. Direkte push af kursusbegivenheder vil blive genoprettet, når opdateringen af kursusbegivenheder er fuldført. Tjek din konfiguration.';
$string['logger:pushcourseserror'] = 'Push til Ally-slutpunkt lykkedes ikke';
$string['logger:pushcourseserror_exp'] = 'Fejl knyttet til push af kursusopdatering til Ally-tjenester.';
$string['logger:addingcourseevttoqueue'] = 'Tilføjer kursusbegivenhed til pushkøen';
$string['logger:cmiderraticpremoddelete'] = 'Kususmodulets ID har problemer med at slette det på forhånd.';
$string['logger:cmiderraticpremoddelete_exp'] = 'Modulet blev ikke identificeret. Enten eksisterer det ikke på grund af sletning af en sektion, eller der er andre faktorer, der har udløst sletningen, så det ikke kan findes.';
$string['logger:servicefailure'] = 'Fejl under brug af servicen.';
$string['logger:servicefailure_exp'] = '<br>Klasse: {$a->class}<br>Parametre: {$a->params}';
$string['logger:autoconfigfailureteachercap'] = 'Fejl ved tildeling af en egenskab for arketypen lærer til rollen ally_webservice.';
$string['logger:autoconfigfailureteachercap_exp'] = '<br>Egenskab: {$a->cap}<br>Tilladelse: {$a->permission}';
$string['deferredcourseevents'] = 'Send udsatte kursusbegivenheder';
$string['deferredcourseeventsdesc'] = 'Gør det muligt at sende gemte kursusbegivenheder, der blev samlet ved det mislykkede forsøg på at kommunikere med Ally';
