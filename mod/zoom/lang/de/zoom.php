<?php
// This file is part of the Zoom plugin for Moodle - http://moodle.org/
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
 * English strings for zoom.
 *
 * @package    mod_zoom
 * @copyright  2020 JKU Linz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'Aktionen';
$string['addtocalendar'] = 'Zum Kalender hinzufügen';
$string['alternative_hosts'] = 'Alternative Hosts';
$string['alternative_hosts_help'] = 'Mit der Option "Alternativer Gastgeber" können Sie Meetings planen und einen anderen Pro-Benutzer auf demselben Konto zum Starten des Meetings oder Webinars bestimmen, falls Sie nicht in der Lage sind. Dieser Benutzer erhält eine E-Mail, die ihn darüber informiert, dass er als alternativer Gastgeber hinzugefügt wurde, mit einem Link zum Starten des Meetings. Trennen Sie mehrere E-Mails durch Komma (ohne Leerzeichen).';
$string['allmeetings'] = 'Alle Meetings';
$string['apikey'] = 'Zoom API key';
$string['apikey_desc'] = '';
$string['apisecret'] = 'Zoom API secret';
$string['apisecret_desc'] = '';
$string['apiurl'] = 'Zoom API url';
$string['apiurl_desc'] = '';
$string['audio_both'] = 'Computeraudio und Telefon';
$string['audio_telephony'] = 'Nur Telefon';
$string['audio_voip'] = 'Nur Computeraudio';
$string['cachedef_zoomid'] = 'Die Zoom NutzerInnen-ID dieser Person';
$string['cachedef_sessions'] = 'Information der Zoom "Get User Report" Anfrage';
$string['calendardescriptionURL'] = 'URL zum Meeting-Beitritt: {$a}.';
$string['calendardescriptionintro'] = "\nBeschreibung:\n{\$a}";
$string['calendariconalt'] = 'Kalendarsymbol';
$string['clickjoin'] = 'Hat den "Meeting Beitreten" Knopf gedrückt';
$string['connectionok'] = 'Verbindung funktioniert.';
$string['connectionfailed'] = 'Verbindung fehlgeschlagen: ';
$string['connectionstatus'] = 'Verbindungsstatus';
$string['defaultsettings'] = 'Zoom Standardeinstellungen';
$string['defaultsettings_help'] = 'Diese Einstellungen legen den Standard für alle neuen Zoom-Meetings und Webinare fest.';
$string['downloadical'] = 'iCal herunterladen';
$string['duration'] = 'Dauer (Minuten)';
$string['endtime'] = 'Endzeit';
$string['err_duration_nonpositive'] = 'Die Dauer muss positiv sein.';
$string['err_duration_too_long'] = 'Die Dauer kann 150 Stunden nicht überschreiten.';
$string['err_long_timeframe'] = 'Der angeforderte Zeitrahmen ist zu lang und zeigt die Ergebnisse des letzten Monats im Bereich.';
$string['err_password'] = 'Das Passwort darf nur die folgenden Zeichen enthalten: [a-z A-Z 0-9 @ - _ *]. Maximal 10 Zeichen.';
$string['err_start_time_past'] = 'Das Startdatum kann nicht in der Vergangenheit liegen.';
$string['errorwebservice'] = 'Zoom Webservice Fehler: {$a}.';
$string['export'] = 'Exportieren';
$string['firstjoin'] = 'Frühester Beitrittszeitpunkt';
$string['firstjoin_desc'] = 'Der früheste Zeitpunkt, wann ein/eine TeilnehmerIn einem geplanten Meeting beitreten kann (Minuten vor Start).';
$string['getmeetingreports'] = 'Meeting-Bericht von Zoom erhalten';
$string['invalid_status'] = 'Status ungültig, bitte die Datenbank untersuchen.';
$string['join'] = 'Beitreten';
$string['joinbeforehost'] = 'Beitritt vor Moderator aktivieren';
$string['join_link'] = 'Beitrittslink';
$string['join_meeting'] = 'Meeting beitreten';
$string['jointime'] = 'Zeitpunkt des Beitritts';
$string['leavetime'] = 'Zeitpunkt des Verlassens';
$string['licensesnumber'] = 'Anzahl der Lizenzen';
$string['redefinelicenses'] = 'Lizenzen neu definieren';
$string['lowlicenses'] = 'Wenn die Anzahl Ihrer Lizenzen die erforderliche Anzahl überschreitet, wird bei der Erstellung jeder neuen Aktivität durch den Benutzer eine PRO-Lizenz zugewiesen, indem der Status eines anderen Benutzers herabgesetzt wird. Diese Option ist wirksam, wenn die Anzahl der aktiven PRO-Lizenzen mehr als 5 beträgt.';
$string['maskparticipantdata'] = 'Teilnehmerdaten maskieren';
$string['maskparticipantdata_help'] = 'Verhindert, dass Teilnehmerdaten in Berichten angezeigt werden (nützlich für Webseiten die Teilnehmerdaten maskieren, z. B. für GDPR).';
$string['meeting_nonexistent_on_zoom'] = 'Existiert nicht auf Zoom';
$string['meeting_finished'] = 'Fertig';
$string['meeting_not_started'] = 'Nicht gestartet';
$string['meetingoptions'] = 'Meeting Optionen';
$string['meetingoptions_help'] = '*Vor dem Host beitreten* ermöglicht es den TeilnehmernInnen, dem Meeting beizutreten, bevor der Gastgeber beitritt oder wenn der Gastgeber nicht an dem Meeting teilnehmen kann.';
$string['meeting_started'] = 'Laufend';
$string['meeting_time'] = 'Startzeit';
$string['modulename'] = 'Zoom meeting';
$string['modulenameplural'] = 'Zoom Meetings';
$string['modulename_help'] = 'Zoom ist eine Video- und Webkonferenz-Lösung die authorisierten NutzerInnen die Möglichkeit bietet, Online-Meetings abzuhalten.';
$string['newmeetings'] = 'Neue Meetings';
$string['nomeetinginstances'] = 'Keine Sitzungen für dieses Meeting gefunden.';
$string['noparticipants'] = 'Keine TeilnehmerInnen gefunden für diese Sitzung zur angegebenen Zeit.';
$string['nosessions'] = 'Keine Sitzungen im angegebenen Bereich gefunden.';
$string['nozooms'] = 'Keine Meetings';
$string['off'] = 'Aus';
$string['oldmeetings'] = 'Abgeschlossene Meetings';
$string['on'] = 'Ein';
$string['option_audio'] = 'Audioeinstellungen';
$string['option_host_video'] = 'Video vom Host';
$string['option_jbh'] = 'Beitritt vor Host möglich';
$string['option_participants_video'] = 'Video von TeilnehmerInnen';
$string['option_proxyhost'] = 'Proxyserver benutzen';
$string['option_proxyhost_desc'] = 'Der Proxyserver, hier als \'<code><hostname>:<port></code>\' eingetragen, wird nur für die Kommunikation mit Zoom benutzt. Bitte lassen Sie dieses Feld leer, um die Moodle Standardeinstellungen für Proxyserver zu benutzen. Sie müssen diese Einstellung nur nutzen, falls Sie keinen globalen Proxyserver in Moodle eintragen wollen.';
$string['participantdatanotavailable'] = 'Details nicht verfügbar';
$string['participantdatanotavailable_help'] = 'Teilnehmerdaten sind für diese Zoom-Sitzung nicht verfügbar (z. B. Aufgrund von GDPR-Auflagen).';
$string['participants'] = 'TeilnehmerInnen';
$string['password'] = 'Passwort';
$string['passwordprotected'] = 'Passwortgeschützt';
$string['pluginadministration'] = 'Zoom-Meeting verwalten';
$string['pluginname'] = 'Zoom meeting';
$string['privacy:metadata:zoom_meeting_details'] = 'Die Datenbanktabelle, in der Informationen über die Meeting-Instanzen gespeichert werden.';
$string['privacy:metadata:zoom_meeting_details:topic'] = 'Der Name des Meetings, das ein/eine TeilnehmerIn besucht hat.';
$string['privacy:metadata:zoom_meeting_participants'] = 'Die Datenbanktabelle, in der Informationen über Meeting-TeilnehmerInnen gespeichert werden.';
$string['privacy:metadata:zoom_meeting_participants:duration'] = 'Wie lange der/die TeilnehmerIn im Meeting war';
$string['privacy:metadata:zoom_meeting_participants:join_time'] = 'Der Zeitpunkt, wann der/die TeilnehmerIn das Meeting betreten hat';
$string['privacy:metadata:zoom_meeting_participants:leave_time'] = 'Der Zeitpunkt, wann der/die TeilnehmerIn das Meeting verlassen hat';
$string['privacy:metadata:zoom_meeting_participants:name'] = 'Der Name des/der TeilnehmerIn';
$string['privacy:metadata:zoom_meeting_participants:user_email'] = 'Die E-Mail des/der TeilnehmerIn';
$string['recurringmeeting'] = 'Dauerhaft';
$string['recurringmeeting_help'] = 'Hat kein Ende';
$string['recurringmeetinglong'] = 'Dauerhaftes Meeting (Meeting ohne Endzeit)';
$string['report'] = 'Berichte';
$string['reportapicalls'] = 'Bericht über Verbrauch von allen API Calls';
$string['requirepasscode'] = 'Meeting-Passwort notwendig';
$string['resetapicalls'] = 'Die Anzahl der verfügbaren API Calls zurücksetzen';
$string['search:activity'] = 'Zoom - Informationen über die Aktivität';
$string['sessions'] = 'Sitzungen';
$string['start'] = 'Start';
$string['starthostjoins'] = 'Video starten wenn der Host beitritt';
$string['start_meeting'] = 'Meeting starten';
$string['startpartjoins'] = 'Video starten wenn TeilnehmerIn beitritt';
$string['start_time'] = 'Wann';
$string['starttime'] = 'Startzeit';
$string['status'] = 'Status';
$string['title'] = 'Titel';
$string['topic'] = 'Thema';
$string['unavailable'] = 'Beitritt zu diesem Zeitpunkt nicht möglich';
$string['updatemeetings'] = 'Meeting-Einstellungen aus Zoom ändern';
$string['usepersonalmeeting'] = 'Persönliche Meeting-ID benutzen {$a}';
$string['webinar'] = 'Webinar';
$string['webinar_help'] = 'Diese Option ist nur für vorauthorisierte Zoom-Zugänge verfügbar.';
$string['webinar_already_true'] = '<p><b>Diese Aktivität wurde bereits als Webinar und nicht als Meeting angelegt. Sie können diese Einstellung nicht mehr ändern, nachdem das Webinar angelegt wurde.</b></p>';
$string['webinar_already_false'] = '<p><b>Diese Aktivität wurde bereits als Meeting und nicht als Webinar angelegt. Sie können diese Einstellung nicht mehr ändern, nachdem das Meeting angelegt wurde.</b></p>';
$string['zoom:addinstance'] = 'Ein neues Zoom Meeting planen';
$string['zoomerr'] = 'Es trat ein allgemeiner Fehler auf.'; // Generic error.
$string['zoomerr_apikey_missing'] = 'Zoom API Key nicht gefunden';
$string['zoomerr_apisecret_missing'] = 'Zoom API secret nicht gefunden';
$string['zoomerr_id_missing'] = 'Sie müssen eine course_module ID oder eine instance ID angeben';
$string['zoomerr_licensescount_missing'] = 'Zoom "utmost" Einstellung gefunden, aber "licensescount" Einstellung nicht gefunden.';
$string['zoomerr_meetingnotfound'] = 'Dieses Meeting wurde auf Zoom nicht gefunden. Sie können es <a href="{$a->recreate}">hier neu anlegen</a> oder <a href="{$a->delete}">hier vollständig löschen</a>.';
$string['zoomerr_meetingnotfound_info'] = 'Dieses Meeting wurde auf Zoom nicht gefunden. Bitte kontaktieren Sie den Meeting-Host wenn Sie Fragen haben.';
$string['zoomerr_usernotfound'] = 'Ihr Konto konnte bei Zoom nicht gefunden werden. Wenn Sie Zoom zum ersten Mal verwenden, müssen Sie Ihr Konto zoomen, indem Sie sich bei Zoom <a href="{$a}" target="_blank">{$a}</a> anmelden. Sobald Sie Ihr Zoom-Konto aktiviert haben, laden Sie diese Seite erneut und fahren Sie mit der Einrichtung Ihres Meetings fort. Andernfalls stellen Sie sicher, dass Ihre E-Mail auf Zoom mit Ihrer E-Mail auf diesem System übereinstimmt.';
$string['zoomurl'] = 'Zoom Webseite URL';
$string['zoomurl_desc'] = '';
$string['zoom:view'] = 'Zoom Meetings ansehen';
