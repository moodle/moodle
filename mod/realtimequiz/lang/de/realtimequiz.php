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
 * German language strings
 *
 * @copyright Joachim Vogelgesang
 * @package mod_realtimequiz
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

// Translation: Joachim Vogelgesang.

$string['modulename'] = 'Echtzeit Test';
$string['pluginname'] = 'Echtzeit Test';
$string['modulenameplural'] = 'Echtzeit Tests';
$string['editquestions'] = 'Fragen bearbeiten';
$string['seeresponses'] = 'Antworten sehen';

// Used by backuplib.php.
$string['questions'] = 'Fragen';
$string['answers'] = 'Antworten';
$string['sessions'] = 'Sitzungen';
$string['submissions'] = 'Einreichungen';

// Capabilities.
$string['realtimequiz:control'] = 'Start / Test Kontrolle';
$string['realtimequiz:attempt'] = 'Test versuchen';
$string['realtimequiz:seeresponses'] = 'Testantworten sehen';
$string['realtimequiz:editquestions'] = 'Fragen für einen Test bearbeiten';

// Editing the realtime quiz settings.
$string['questiontime'] = 'Standardzeit um jede Frage anzuzeigen (Sekunden): ';
$string['questionimage'] = '(Optional) Bild: ';
$string['removeimage'] = 'Bild entfernen: ';

// Editing the realtime quiz questions.
$string['addquestion'] = 'Frage hinzufügen';
$string['backquiz'] = 'Zurück zum Echtzeittest';
$string['questiontext'] = 'Frage Text:';
$string['editquestiontime'] = 'Fragezeit (0 für Standard)';
$string['answertext'] = 'Antwort Text:';
$string['correct'] = 'Korrekte Antwort?';
$string['updatequestion'] = 'Frage aktualisieren';
$string['saveadd'] = 'Frage speichern und weitere hinzufügen';
$string['addanswers'] = '3 Antworten hinzufügen';
$string['errorquestiontext'] = 'Fehler: Sie haben keine Frage eingegeben';
$string['onecorrect'] = 'Fehler: Es muss exakt eine korrekte Anwort existieren';
$string['deletequestion'] = 'Frage löschen';
$string['checkdelete'] = 'Möchten Sie wirklich diese Frage löschen?';
$string['questionslist'] = 'Fragen in diesem Echtzeitest: ';
$string['yes'] = 'Ja';
$string['no'] = 'Nein';
$string['addingquestion'] = 'Fragen hinzufügen ';
$string['edittingquestion'] = 'Frage bearbeiten ';
$string['answer'] = 'Antwort ';
$string['view'] = 'Test sehen';
$string['responses'] = 'Antworten sehen';
$string['edit'] = 'Test bearbeiten';

// Viewing the responses from different students.
$string['nosessions'] = 'Dieser Echtzeitest wurde noch nicht versucht';
$string['choosesession'] = 'Wählen Sie ein Frage zum Anzeigen: ';
$string['showsession'] = 'Anzeigen';
$string['allsessions'] = 'Alle Sitzungen';
$string['backresponses'] = 'Zurück zu den kompletten Antworten';
$string['prevquestion'] = 'Vorherige Frage';
$string['nextquestion'] = 'Nächste Frage';
$string['allquestions'] = 'Zurück zu allen Ergebnissen';
$string['noanswers'] = 'Keine Beantwortungen';


// Used by quizdata.php.
$string['notallowedattempt'] = 'Sie dürfen diesen Echtzeitest nicht versuchen';
$string['badsesskey'] = 'Falscher Sitzungsschlüssel';
$string['badquizid'] = 'Falsche quizid: '; // Do not translate 'quizid'.
$string['badcurrentquestion'] = 'Falsche currentquestion: '; // Do not translate 'currentquestion'.
$string['alreadyanswered'] = 'Sie haben diese Frage schon beantwortet';
$string['notauthorised'] = 'Sie sind nicht autorisiert diesen Echtzeittest zu kontrollieren';
$string['unknownrequest'] = 'Unbekannte Anfrage: \'';
$string['incorrectstatus'] = 'Test hat den falschen Status: \'';

// Used by view_student.js
// Important - do not use any double-quotes (") in this text as it will cause problems when passing
// the text into javascript (edit 'view.php' if this is a major problem).
$string['joinquiz'] = 'Test Teilnahme';
$string['joininstruct'] = 'Warten Sie mit dem Klicken solange bis Ihr Tutor Ihnen Bescheid gibt!';
$string['waitstudent'] = 'Warten auf Teilnehmerverbindung...';
$string['clicknext'] = 'Klicken Sie \'Weiter\' wenn alle fertig sind!';
$string['waitfirst'] = 'Warten Sie bitte bis die erste Frage übermittelt wird...';
$string['question'] = 'Frage ';
$string['invalidanswer'] = 'Ungültige Antwortnummer ';
$string['finalresults'] = 'Endresultate';
$string['classresult'] = 'Klassen Ergebnis: ';
$string['classresultcorrect'] = ' korrekt';
$string['questionfinished'] = 'Frage beendet, auf Ergebnisse warten';
$string['httprequestfail'] = 'Aufgeben :( Es kann keine XMLHTTP- Seite aufgebaut werden';
$string['noquestion'] = 'Falsche Antwort - keine Fragedaten: ';
$string['tryagain'] = 'Möchten Sie es wieder versuchen?';
$string['resultthisquestion'] = 'Diese Frage: ';
$string['resultoverall'] = ' Insgesamt korrekt: ';
$string['resultcorrect'] = ' korrekt.';
$string['answersent'] = 'Antwort wurde verschickt - zum Beenden auf Frage warten';
$string['quiznotrunning'] = 'Test läuft im Augenblick nicht - warten Sie bis Ihr Tutor ihn startet';
$string['servererror'] = 'Server meldet Fehler: ';
$string['badresponse'] = 'Unerwartete Antwort vom Server - ';
$string['httperror'] = 'Es gab ein Problem bei der Anfrage - Status: ';
$string['yourresult'] = 'Ihr Ergebnis: ';
$string['displaynext'] = 'Zeit bis zur nächsten Anzeige:';
$string['sendinganswer'] = 'Antwort senden';
$string['timeleft'] = 'Verbleibende Zeit für die Antwort:';
$string['tick'] = 'Richtige Antwort';
$string['cross'] = 'Falsche Antwort';

// Used by view_teacher.js
// Important - do not use any double-quotes (") in this text as it will cause problems when passing
// the text into javascript (edit 'view.php' if this is a major problem).
$string['next'] = 'Weiter >>';
$string['startquiz'] = 'Test starten';
$string['studentconnected'] = 'schüler verbunden';
$string['studentsconnected'] = 'studenten verbunden';
$string['teacherstartinstruct'] = 'Hier können Sie einen Test für die Teilnehmer beginnen.<br />Benutzen Sie das Eingabefeld um einen Namen für diese Sitzung festzulegen (das kann helfen um zu einem späteren Zeitpunkt die Ergebnisse besser zu finden).';
$string['teacherjoinquizinstruct'] = 'Benutzen Sie dies um selbst einen Test auszuprobieren<br />(Sie müssen ebenfalls den Test in einem separaten Fenster starten)';
