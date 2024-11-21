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
 * Strings for component 'format_remuiformat'
 *
 * @package    format_remuiformat
 * @copyright  2019 Wisdmlabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Plugin Name.
$string['pluginname'] = 'Edwiser Kursformate';
$string['plugin_description'] = 'Kurse werden als einklappbare Listen ODER als Karten von Abschnitten mit einem responsiven Design für bessere Navigation präsentiert.';
// Settings.
$string['defaultcoursedisplay'] = 'Standardmäßige Kursanzeige';
$string['defaultcoursedisplay_desc'] = 'Entweder alle Abschnitte auf einer Seite anzeigen oder Abschnitt Null und den ausgewählten Abschnitt auf der Seite anzeigen.';

$string['defaultbuttoncolour'] = 'Standardfarbe des Schaltfläche "Thema anzeigen"';
$string['defaultbuttoncolour_desc'] = 'Die Farbe der Schaltfläche "Thema anzeigen".';

$string['defaultoverlaycolour'] = 'Standardfarbe der Überlagerung beim Hover über Aktivitäten';
$string['defaultoverlaycolour_desc'] = 'Die Farbe der Überlagerung, wenn der Benutzer über Aktivitäten schwebt.';

$string['enablepagination'] = 'Paginierung aktivieren';
$string['enablepagination_desc'] = 'Dies aktiviert die Ansicht mehrerer Seiten, wenn die Anzahl der Abschnitte/Aktivitäten sehr groß ist.';

$string['defaultnumberoftopics'] = 'Standardanzahl der Themen pro Seite';
$string['defaultnumberoftopics_desc'] = 'Die Anzahl der Themen, die auf einer Seite angezeigt werden sollen.';

$string['defaultnumberofactivities'] = 'Standardanzahl der Aktivitäten pro Seite';
$string['defaultnumberofactivities_desc'] = 'Die Anzahl der Aktivitäten, die auf einer Seite angezeigt werden sollen.';

$string['off'] = 'Aus';
$string['on'] = 'An';

$string['defaultshowsectiontitlesummary'] = 'Zusammenfassung des Abschnittstitels beim Hover anzeigen';
$string['defaultshowsectiontitlesummary_desc'] = 'Zusammenfassung des Abschnittstitels beim Überfahren des Rasters anzeigen.';
$string['sectiontitlesummarymaxlength'] = 'Maximale Länge der Zusammenfassung des Abschnitts/Aktivitäten festlegen.';
$string['sectiontitlesummarymaxlength_help'] = 'Maximale Länge der Zusammenfassung des Abschnitts/Aktivitäten, die auf der Karte angezeigt wird, festlegen.';
$string['defaultsectionsummarymaxlength'] = 'Maximale Länge der Zusammenfassung des Abschnitts/Aktivitäten festlegen.';
$string['defaultsectionsummarymaxlength_desc'] = 'Maximale Länge der Zusammenfassung des Abschnitts/Aktivitäten, die auf der Karte angezeigt wird, festlegen.';
$string['hidegeneralsectionwhenempty'] = 'Allgemeinen Abschnitt ausblenden, wenn leer';
$string['hidegeneralsectionwhenempty_help'] = 'Wenn der allgemeine Abschnitt keine Aktivität und Zusammenfassung enthält, können Sie ihn ausblenden.';

// Section.
$string['sectionname'] = 'Abschnitt';
$string['sectionnamecaps'] = 'ABSCHNITT';
$string['section0name'] = 'Einführung';
$string['hidefromothers'] = 'Abschnitt ausblenden';
$string['showfromothers'] = 'Abschnitt anzeigen';
$string['viewtopic'] = 'Ansehen';
$string['editsection'] = 'Abschnitt bearbeiten';
$string['editsectionname'] = 'Abschnittsname bearbeiten';
$string['newsectionname'] = 'Neuer Name für Abschnitt {$a}';
$string['currentsection'] = 'Dieser Abschnitt';
$string['addnewsection'] = 'Abschnitt hinzufügen';
$string['moveresource'] = 'Ressource verschieben';

// Activity.
$string['viewactivity'] = 'Aktivität ansehen';
$string['markcomplete'] = 'Als abgeschlossen markieren';
$string['grade'] = 'Bewertung';
$string['notattempted'] = 'Nicht versucht';
$string['subscribed'] = 'Abonniert';
$string['notsubscribed'] = 'Nicht abonniert';
$string['completed'] = 'Abgeschlossen';
$string['notcompleted'] = 'Nicht abgeschlossen';
$string['progress'] = 'Fortschritt';
$string['showinrow'] = 'In Zeile anzeigen';
$string['showincard'] = 'In Karte anzeigen';
$string['moveto'] = 'Verschieben nach';
$string['changelayoutnotify'] = 'Seite aktualisieren, um Änderungen zu sehen.';
$string['generalactivities'] = 'Aktivitäten';
$string['coursecompletionprogress'] = 'Kursfortschritt';
$string['resumetoactivity'] = 'Fortsetzen';

// For list format.
$string['remuicourseformat'] = 'Layout wählen';
$string['remuicourseformat_card'] = 'Kartenlayout';
$string['remuicourseformat_list'] = 'Listenlayout';
$string['remuicourseformat_help'] = 'Kurslayout wählen';
$string['remuicourseimage_filemanager'] = 'Kursheaderbild';
$string['remuicourseimage_filemanager_help'] = 'Dieses Bild wird im allgemeinen Abschnitt der Karte im Kartenlayout und als Hintergrund des allgemeinen Abschnitts im Listenlayout angezeigt. <strong>Empfohlene Bildgröße 1272x288.<strong>';
$string['addsections'] = 'Abschnitte hinzufügen';
$string['teacher'] = 'Lehrer';
$string['teachers'] = 'Lehrer';
$string['remuiteacherdisplay'] = 'Lehrerbild anzeigen';
$string['remuiteacherdisplay_help'] = 'Lehrerbild im Kursheader anzeigen.';
$string['defaultremuiteacherdisplay'] = 'Lehrerbild anzeigen';
$string['defaultremuiteacherdisplay_desc'] = 'Lehrerbild im Kursheader anzeigen.';

$string['remuidefaultsectionview'] = 'Standardansicht für Abschnitte wählen';
$string['remuidefaultsectionview_help'] = 'Standardansicht für die Abschnitte des Kurses wählen.';
$string['expanded'] = 'Alle erweitern';
$string['collapsed'] = 'Alle einklappen';

$string['remuienablecardbackgroundimg'] = 'Hintergrundbild des Abschnitts';
$string['remuienablecardbackgroundimg_help'] = 'Hintergrundbild des Abschnitts aktivieren. Standardmäßig ist es deaktiviert. Es holt das Bild aus der Abschnittszusammenfassung.';
$string['enablecardbackgroundimg'] = 'Hintergrundbild im Abschnitt der Karte anzeigen.';
$string['disablecardbackgroundimg'] = 'Hintergrundbild im Abschnitt der Karte ausblenden.';
$string['next'] = 'Weiter';
$string['previous'] = 'Zurück';

$string['remuidefaultsectiontheme'] = 'Standardthema für Abschnitte wählen';
$string['remuidefaultsectiontheme_help'] = 'Standardthema für die Abschnitte des Kurses wählen.';

$string['dark'] = 'Dunkel';
$string['light'] = 'Hell';

$string['defaultcardbackgroundcolor'] = 'Hintergrundfarbe des Abschnitts im Kartenformat festlegen.';
$string['cardbackgroundcolor_help'] = 'Hintergrundfarbe der Karte Hilfe.';
$string['cardbackgroundcolor'] = 'Hintergrundfarbe des Abschnitts im Kartenformat festlegen.';
$string['defaultcardbackgroundcolordesc'] = 'Beschreibung der Hintergrundfarbe der Karte.';

// GDPR.
$string['privacy:metadata'] = 'Das Edwiser Kursformate-Plugin speichert keine persönlichen Daten.';

// Validation.
$string['coursedisplay_error'] = 'Bitte wählen Sie die richtige Kombination aus Layout.';

// Activities completed text.
$string['activitystart'] = 'Lass uns anfangen';
$string['outof'] = 'von';
$string['activitiescompleted'] = 'Aktivitäten abgeschlossen';
$string['activitycompleted'] = 'Aktivität abgeschlossen';
$string['activitiesremaining'] = 'Verbleibende Aktivitäten';
$string['activityremaining'] = 'Verbleibende Aktivität';
$string['allactivitiescompleted'] = 'Alle Aktivitäten abgeschlossen';

// Used in format.js on change course layout.
$string['showallsectionperpage'] = 'Alle Abschnitte pro Seite anzeigen';

// Card format general section.
$string['showfullsummary'] = '+ Volle Zusammenfassung anzeigen';
$string['showless'] = 'Weniger anzeigen';
$string['showmore'] = 'Mehr anzeigen';
$string['Complete'] = 'vollständig';

// Usage tracking.
$string['enableusagetracking'] = 'Nutzungsverfolgung aktivieren';
$string['enableusagetrackingdesc'] = '<strong>HINWEIS ZUR NUTZUNGSVERFOLGUNG</strong>

<hr class="text-muted" />

<p>Edwiser wird ab sofort anonyme Daten sammeln, um Nutzungsstatistiken zu erstellen.</p>

<p>Diese Informationen helfen uns, die Entwicklung in die richtige Richtung zu lenken und die Edwiser-Community zu fördern.</p>

<p>Wir erfassen dabei keine persönlichen Daten von Ihnen oder Ihren Schülern. Sie können dieses Feature jederzeit im Plugin deaktivieren.</p>

<p>Eine Übersicht der gesammelten Daten finden Sie <strong><a href="https://forums.edwiser.org/topic/67/anonymously-tracking-the-usage-of-edwiser-products" target="_blank">hier</a></strong>.</p>';


$string['edw_format_hd_bgpos'] = 'Position des Hintergrundbildes des Kursheaders';
$string['bottom'] = 'unten';
$string['center'] = 'zentriert';
$string['top'] = 'oben';
$string['left'] = 'links';
$string['right'] = 'rechts';
$string['edw_format_hd_bgpos_help'] = 'Position des Hintergrundbildes wählen';

$string['edw_format_hd_bgsize'] = 'Größe des Hintergrundbildes des Kursheaders';
$string['cover'] = 'abdecken';
$string['contain'] = 'enthalten';
$string['auto'] = 'automatisch';
$string['edw_format_hd_bgsize_help'] = 'Größe des Hintergrundbildes des Kursheaders wählen';
$string['courseinformation'] = 'Kursinformationen';
$string['defaultheader'] = 'Standard';
$string['remuiheader'] = 'Header';
$string['headereditingbutton'] = 'Position der Bearbeitungsschaltfläche wählen';
$string['headereditingbutton_help'] = 'Position der Bearbeitungsschaltfläche wählen. Diese Einstellung funktioniert nicht in remui, überprüfen Sie die Kurseinstellung';

$string['headeroverlayopacity'] = 'Deckkraft der Header-Überlagerung ändern';
$string['headeroverlayopacity_help'] = 'Der Standardwert ist bereits auf "100" gesetzt. Um die Deckkraft anzupassen, geben Sie einen Wert zwischen 0 und 100 ein.';
$string['viewalltext'] = 'Alles ansehen';
