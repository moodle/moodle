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
$string['adminurldesc'] = 'Die LTI-Start-URL, über die der Bericht zur Barrierefreiheit aufgerufen wird.';
$string['allyclientconfig'] = 'Ally-Konfiguration';
$string['ally:clientconfig'] = 'Client-Konfiguration aufrufen und aktualisieren';
$string['ally:viewlogs'] = 'Ally-Protokollanzeigeprogramm';
$string['clientid'] = 'Client-ID';
$string['clientiddesc'] = 'Die Ally-Client-ID';
$string['code'] = 'Code';
$string['contentauthors'] = 'Autor(inn)en von Inhalten';
$string['contentauthorsdesc'] = 'Administrator(inn)en und Nutzer/innen, die diesen ausgewählten Rollen zugewiesen sind, lassen ihre hochgeladenen Kursdateien auf Barrierefreiheit prüfen. Dabei erhalten die Dateien eine entsprechende Bewertung. Eine niedrige Bewertung bedeutet, dass für eine größere Barrierefreiheit Änderungen an der Datei vorgenommen werden müssen.';
$string['contentupdatestask'] = 'Aufgabe zur Aktualisierung von Inhalten';
$string['curlerror'] = 'cURL-Fehler: {$a}';
$string['curlinvalidhttpcode'] = 'Ungültiger HTTP-Statuscode: {$a}';
$string['curlnohttpcode'] = 'HTTP-Statuscode kann nicht überprüft werden';
$string['error:invalidcomponentident'] = 'Ungültige Komponenten-ID {$a}';
$string['error:pluginfilequestiononly'] = 'Für diese URL werden nur Fragenkomponenten unterstützt';
$string['error:componentcontentnotfound'] = 'Inhalt für {$a} nicht gefunden';
$string['error:wstokenmissing'] = 'Webservice-Token fehlt. Eventuell muss ein(e) Admin-Nutzer/in die automatische Konfiguration ausführen.';
$string['excludeunused'] = 'Nicht verwendete Dateien ausschließen';
$string['excludeunuseddesc'] = 'Lassen Sie an HTML-Inhalte angehängte Dateien aus, die in der HTML verlinkt/querverwiesen sind.';
$string['filecoursenotfound'] = 'Die eingereichte Datei gehört zu keinem Kurs';
$string['fileupdatestask'] = 'Datei-Updates per Push in Ally übertragen';
$string['id'] = 'ID';
$string['key'] = 'Schlüssel';
$string['keydesc'] = 'Der LTI-Verbraucherschlüssel.';
$string['level'] = 'Stufe';
$string['message'] = 'Nachricht';
$string['pluginname'] = 'Ally';
$string['pushurl'] = 'URL für Datei-Updates';
$string['pushurldesc'] = 'Push-Benachrichtigungen über Datei-Updates an diese URL senden.';
$string['queuesendmessagesfailure'] = 'Beim Versenden von Mitteilungen an den AWS SQS ist ein Fehler aufgetreten. Fehlerdaten: $a';
$string['secret'] = 'Geheimer Schlüssel';
$string['secretdesc'] = 'Das LTI-Secret.';
$string['showdata'] = 'Daten einblenden';
$string['hidedata'] = 'Daten ausblenden';
$string['showexplanation'] = 'Erläuterung einblenden';
$string['hideexplanation'] = 'Erläuterung ausblenden';
$string['showexception'] = 'Ausnahme einblenden';
$string['hideexception'] = 'Ausnahme ausblenden';
$string['usercapabilitymissing'] = 'Die/der bereitgestellte Nutzer/in kann diese Datei nicht löschen.';
$string['autoconfigure'] = 'Ally-Webservice automatisch konfigurieren';
$string['autoconfiguredesc'] = 'Webservice-Rolle und - Nutzer/in für Ally automatisch erstellen.';
$string['autoconfigureconfirmation'] = 'Erstellen Sie automatisch eine Webservicerolle und Nutzer/innen für Ally und aktivieren Sie den Webservice. Folgende Aktionen werden durchgeführt:<ul><li>Erstellen einer Rolle mit dem Titel &quot;ally_webservice&quot; und eines Nutzers/einer Nutzerin mit dem Nutzer/innen-Namen &quot;ally_webuser&quot;</li><li>Hinzufügen des Nutzers/der Nutzerin &quot;ally_webuser&quot; zur Rolle &quot;ally_webservice&quot;</li><li>Aktivieren der Webservices</li><li>Aktivieren des Rest-Webdienstprotokolls</li><li>Aktivieren des Ally-Webservices</li><li>Erstellen eines Tokens für das Konto &quot;ally_webuser&quot;</li></ul>';
$string['autoconfigsuccess'] = 'Erfolg: Der Ally-Webservice wurde automatisch konfiguriert.';
$string['autoconfigtoken'] = 'Das Webservice-Token lautet wie folgt:';
$string['autoconfigapicall'] = 'Sie können über die folgende URL testen, ob der Webservice funktioniert:';
$string['privacy:metadata:files:action'] = 'Die an der Datei vorgenommene Aktion, z. B. erstellt, aktualisiert oder gelöscht.';
$string['privacy:metadata:files:contenthash'] = 'Der Inhalts-Hash der Datei, anhand dessen die Eindeutigkeit bestimmt wird.';
$string['privacy:metadata:files:courseid'] = 'Die Kurs-ID, zu der die Datei gehört.';
$string['privacy:metadata:files:externalpurpose'] = 'Zur Integration in Ally müssen die Dateien mit Ally ausgetauscht werden.';
$string['privacy:metadata:files:filecontents'] = 'Der Inhalt der Datei wird an Ally gesendet und auf Barrierefreiheit überprüft.';
$string['privacy:metadata:files:mimetype'] = 'Der MIME-Typ der Datei, z. B. text/plain, image/jpeg usw.';
$string['privacy:metadata:files:pathnamehash'] = 'Dier Pfadnamen-Hash zur eindeutigen Identifizierung der Datei.';
$string['privacy:metadata:files:timemodified'] = 'Die Uhrzeit, zu der das Feld zuletzt geändert wurde.';
$string['cachedef_annotationmaps'] = 'Kommentardaten für Kurse speichern';
$string['cachedef_fileinusecache'] = 'Zwischenspeicher für verwendete Ally-Dateien';
$string['cachedef_pluginfilesinhtml'] = 'Zwischenspeicher für Ally-Dateien in HTML';
$string['cachedef_request'] = 'Zwischenspeicher für Ally-Filteranfragen';
$string['pushfilessummary'] = 'Zusammenfassung der Ally-Datei-Updates.';
$string['pushfilessummary:explanation'] = 'Zusammenfassung der an Ally gesendeten Datei-Updates.';
$string['section'] = 'Abschnitt {$a}';
$string['lessonanswertitle'] = 'Antwort für Lektion &quot;{$a}&quot;';
$string['lessonresponsetitle'] = 'Beantwortung für Lektion &quot;{$a}&quot;';
$string['logs'] = 'Ally-Protokolle';
$string['logrange'] = 'Protokollbereich';
$string['loglevel:none'] = 'Keine';
$string['loglevel:light'] = 'Leicht';
$string['loglevel:medium'] = 'Mittel';
$string['loglevel:all'] = 'Alle';
$string['logcleanuptask'] = 'Bereinigungsaufgabe für Ally-Protokolle';
$string['loglifetimedays'] = 'Protokolle aufbewahren für einen Zeitraum von';
$string['loglifetimedaysdesc'] = 'Bewahren Sie Ally-Protokolle für den angegebenen Zeitraum auf. Setzen Sie den Wert auf „0“, damit Protokolle niemals gelöscht werden. Eine geplante Aufgabe ist (standardmäßig) so eingestellt, dass sie täglich ausgeführt wird, und entfernt Protokolleinträge, die außerhalb dieses Zeitraums liegen.';
$string['logger:filtersetupdebugger'] = 'Ally-Filter-Setup-Protokoll';
$string['logger:pushtoallysuccess'] = 'Erfolgreicher Push zu Ally-Endpunkt';
$string['logger:pushtoallyfail'] = 'Erfolgloser Push zu Ally-Endpunkt';
$string['logger:pushfilesuccess'] = 'Erfolgreicher Push von Datei(en) zu Ally-Endpunkt';
$string['logger:pushfileliveskip'] = 'Fehler bei Live-Datei-Push';
$string['logger:pushfileliveskip_exp'] = 'Der Push von Live-Dateien wird aufgrund von Kommunikationsproblemen übersprungen. Der Live-Datei-Push wird nach erfolgreichen Datei-Updates wiederhergestellt. Bitte prüfen Sie Ihre Konfiguration.';
$string['logger:pushfileserror'] = 'Erfolgloser Push zu Ally-Endpunkt';
$string['logger:pushfileserror_exp'] = 'Fehler im Zusammenhang mit dem Push von Inhalts-Updates an Ally-Services.';
$string['logger:pushcontentsuccess'] = 'Erfolgreicher Push von Inhalt an Ally-Endpunkt';
$string['logger:pushcontentliveskip'] = 'Fehler beim Push von Live-Inhalt';
$string['logger:pushcontentliveskip_exp'] = 'Der Push von Live-Inhalt wird aufgrund von Kommunikationsproblemen übersprungen. Der Push von Live-Inhalten wird nach erfolgreichen Inhalts-Updates wiederhergestellt. Bitte prüfen Sie Ihre Konfiguration.';
$string['logger:pushcontentserror'] = 'Erfolgloser Push zu Ally-Endpunkt';
$string['logger:pushcontentserror_exp'] = 'Fehler im Zusammenhang mit dem Push von Inhalts-Updates an Ally-Services.';
$string['logger:addingconenttoqueue'] = 'Hinzufügen von Inhalt zur Push-Warteschlange';
$string['logger:annotationmoderror'] = 'Die Kommentierung des Inhalts von Ally-Modulen ist fehlgeschlagen.';
$string['logger:annotationmoderror_exp'] = 'Das Modul wurde nicht korrekt identifiziert.';
$string['logger:failedtogetcoursesectionname'] = 'Der Kursabschnittsname konnte nicht abgerufen werden';
$string['logger:moduleidresolutionfailure'] = 'Modul-ID konnte nicht aufgelöst werden';
$string['logger:cmidresolutionfailure'] = 'Die Kurs-Modul-ID konnte nicht aufgelöst werden';
$string['logger:cmvisibilityresolutionfailure'] = 'Die Kursmodulsichtbarkeit konnte nicht aufgelöst werden.';
$string['courseupdatestask'] = 'Kursereignisse per Push an Ally senden';
$string['logger:pushcoursesuccess'] = 'Erfolgreicher Push von Kursereignissen an Ally-Endpunkt';
$string['logger:pushcourseliveskip'] = 'Fehler beim Push von Live-Kursereignissen';
$string['logger:pushcourseerror'] = 'Fehler beim Push von Live-Kursereignissen';
$string['logger:pushcourseliveskip_exp'] = 'Der Push von Live-Kursereignissen wird aufgrund von Kommunikationsproblemen übersprungen. Der Push von Live-Kursereignissen wird nach erfolgreichen Kursereignis-Updates wiederhergestellt. Bitte prüfen Sie Ihre Konfiguration.';
$string['logger:pushcourseserror'] = 'Erfolgloser Push zu Ally-Endpunkt';
$string['logger:pushcourseserror_exp'] = 'Fehler im Zusammenhang mit dem Push von Kurs-Updates an Ally-Services.';
$string['logger:addingcourseevttoqueue'] = 'Hinzufügen von Kursereignissen zur Push-Warteschlange';
$string['logger:cmiderraticpremoddelete'] = 'Kursmodul-ID hat Probleme mit der zuvor erfolgten Löschung.';
$string['logger:cmiderraticpremoddelete_exp'] = 'Modul wurde nicht korrekt identifiziert. Entweder ist es aufgrund der Löschung eines Abschnitts nicht vorhanden oder es liegen andere Faktoren vor, die den Löschhaken ausgelöst haben, wodurch es nicht gefunden wird.';
$string['logger:servicefailure'] = 'Beim Verarbeiten des Service fehlgeschlagen';
$string['logger:servicefailure_exp'] = '<br>Klasse: {$a->class}<br>Parameter: {$a->params}';
$string['logger:autoconfigfailureteachercap'] = 'Beim Zuweisen einer Trainer-Basisfähigkeit zur Rolle ally_webservice fehlgeschlagen';
$string['logger:autoconfigfailureteachercap_exp'] = '<br>Fähigkeit: {$a->cap}<br>Berechtigung: {$a->permission}';
$string['deferredcourseevents'] = 'Zurückgestellte Kursereignisse senden';
$string['deferredcourseeventsdesc'] = 'Senden von gespeicherten Kursereignissen zulassen, die während des Kommunikationsausfalls mit Ally angesammelt wurden';
