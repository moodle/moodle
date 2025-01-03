<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     tiny_ai
 * @category    string
 * @copyright   2024, ISB Bayern
 * @author      Dr. Peter Mayer
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['additional_prompt'] = 'Zusätzlicher Prompt';
$string['ai:view'] = 'Die AI-Schaltfläche anzeigen';
$string['aigenerating'] = 'KI generiert...';
$string['aisuggestion'] = 'KI-Vorschlag';
$string['audiogen_headline'] = 'Audio aus Text generieren';
$string['audiogen_placeholder'] = 'Text eingeben oder einfügen, der in Audio umgewandelt werden soll';
$string['back'] = 'Zurück';
$string['backbutton_tooltip'] = 'Zurück zur vorherigen Seite';
$string['cancel'] = 'Abbrechen';
$string['deletebutton_tooltip'] = 'Aktuelles Ergebnis verwerfen und zurück zur Einstellungsseite';
$string['describe_baseprompt'] = 'Beschreibe den nachfolgenden Text';
$string['describe_headline'] = 'Ausführliche Beschreibung des markierten Texts';
$string['describeimg_baseprompt'] = 'Beschreibe, was auf dem Bild zu sehen ist';
$string['describeimg_headline'] = 'Bildbeschreibung';
$string['dismiss'] = 'Verwerfen';
$string['dismisssuggestion'] = 'Möchten Sie den KI-Vorschlag verwerfen?';
$string['error_nofile'] = 'Keine Datei eingefügt. Bitte Datei hinzufügen.';
$string['error_nofileinclipboard_text'] = 'Die Zwischenablage enthält keine Datei. Bitte eine Datei/einen Screenshot in die Zwischenablage einfügen.';
$string['error_nofileinclipboard_title'] = 'Keine Datei';
$string['error_nopromptgiven'] = 'Kein Prompt angegeben. Bitte einen Prompt eintippen oder einfügen.';
$string['error_tiny_ai_notavailable'] = 'Die KI-Funktionen stehen Ihnen nicht zur Verfügung.';
$string['error_unsupportedfiletype_text'] = 'Der Dateityp wird nicht unterstützt. unterstützte Typen sind: {$a}';
$string['error_unsupportedwrongfiletype_title'] = 'Nicht unterstützter Dateityp';
$string['errorwithcode'] = 'Ein Fehler ist aufgetreten, Fehlercode: {$a}';
$string['freeprompt_placeholder'] = 'Geben Sie der KI eine beliebige Anweisung zur Textgenerierung...';
$string['freepromptbutton_tooltip'] = 'Generiere KI-Antwort';
$string['gender'] = 'Geschlecht';
$string['generalerror'] = 'Ein Fehler ist aufgetreten';
$string['generate'] = 'Jetzt generieren';
$string['generatebutton_tooltip'] = 'KI eine Antwort generieren lassen';
$string['generating'] = 'Die KI-Antwort wird generiert...';
$string['hideprompt'] = 'Prompt ausblenden';
$string['imagefromeditor'] = 'Bild aus Editor';
$string['imagetotext_baseprompt'] = 'Gib den Text auf dem Bild zurück';
$string['imagetotext_headline'] = 'Texterkennung';
$string['imagetotext_insertimage'] = 'Ziehen Sie eine Datei per Drag&Drop in diesen Bereich oder fügen Sie sie aus der Zwischenablage ein';
$string['imggen_headline'] = 'Bildgenerierung';
$string['imggen_placeholder'] = 'Beschreibung des Bilds hier eingeben oder einfügen, z. B. "Generiere ein fotorealistisches Bild eines Affen mit einem Bleistift in der Hand und einem Hut auf dem Kopf"';
$string['insertatcaret'] = 'An aktueller Position einfügen';
$string['insertatcaret_tooltip'] = 'Aktuelles Ergebnis an der aktuellen Position des Cursors einfügen';
$string['insertbelow'] = 'Unten einfügen';
$string['insertbelow_tooltip'] = 'Aktuelles Ergebnis an den Editor-Inhalt anhängen';
$string['keeplanguagetype'] = 'Sprache unverändert lassen';
$string['languagetype'] = 'Art der Sprache';
$string['languagetype_prompt'] = 'Der Text muss {$a} nutzen';
$string['mainselection_heading'] = 'Wobei soll Ihnen die KI helfen?';
$string['maxwordcount'] = 'Maximale Wortanzahl';
$string['maxwordcount_prompt'] = 'Der Text darf nicht mehr als {$a} Wörter beinhalten';
$string['more_options'] = 'Mehr Optionen';
$string['nomaxwordcount'] = 'Keine Beschränkung';
$string['nopurposesconfigured'] = 'Es wurden keine KI-Tools konfiguriert. Wenden Sie sich an Ihren ByCS-Admin.';
$string['pluginname'] = 'KI-Tools';
$string['privacy:metadata'] = 'Dieses Plugin speichert keine personenbezogenen Daten.';
$string['prompt'] = 'Prompt';
$string['regeneratebutton_tooltip'] = 'Prompt verbessern und erneut generieren';
$string['replaceselection'] = 'Auswahl ersetzen';
$string['replaceselection_tooltip'] = 'Auswahl mit dem aktuellen Ergebnis ersetzen';
$string['results_heading'] = 'Ergebnis';
$string['results_please_wait'] = 'Bitte warten! Dies kann ein paar Sekunden dauern.';
$string['reworkprompt'] = 'Prompt überarbeiten';
$string['selectionbarbuttontitle'] = 'KI-Funktionen auf markierten Text anwenden';
$string['showprompt'] = 'Prompt anzeigen';
$string['showpromptbutton_tooltip'] = 'Prompt anzeigen/ausblenden';
$string['simplelanguage'] = 'Einfache Sprache';
$string['size'] = 'Größe';
$string['summarize_baseprompt'] = 'Fasse den folgenden Text zusammen';
$string['summarize_headline'] = 'Zusammenfassen des markierten Texts';
$string['targetlanguage'] = 'Ausgabesprache';
$string['technicallanguage'] = 'Fachsprache';
$string['texttouse'] = 'Der Text lautet';
$string['toolbarbuttontitle'] = 'KI-Funktionen';
$string['toolname_audiogen'] = 'Audiogenerierung';
$string['toolname_describe'] = 'Ausführliche Beschreibung';
$string['toolname_describe_extension'] = 'des markierten Textes';
$string['toolname_describeimg'] = 'Bildbeschreibung';
$string['toolname_imagetotext'] = 'Texterkennung';
$string['toolname_imggen'] = 'Bildgenerierung';
$string['toolname_summarize'] = 'Zusammenfassen';
$string['toolname_summarize_extension'] = 'des markierten Textes';
$string['toolname_translate'] = 'Übersetzen';
$string['toolname_translate_extension'] = 'des markierten Textes';
$string['toolname_tts'] = 'Audio erstellen';
$string['toolname_tts_extension'] = 'aus dem markierten Text';
$string['translate_baseprompt'] = 'Übersetze den folgenden Text in die Sprache {$a} und gib dabei ausschließlich den übersetzten Text aus';
$string['translate_headline'] = 'Übersetzen des markierten Texts';
$string['tts_headline'] = 'Audio aus markiertem Text generieren';
$string['voice'] = 'Stimme';
