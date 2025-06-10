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
$string['pluginname'] = 'Turnitin-Plugin zur Plagiarismuserkennung';
$string['turnitin'] = 'Turnitin';
$string['task_name'] = 'Turnitin-Plugin zur Plagiarismuserkennung – Aufgabe';
$string['connecttesterror'] = 'Bei der Verbindung mit Turnitin ist ein Fehler aufgetreten, siehe Fehlermeldung:<br />';

// Assignment Settings.
$string['turnitin:enable'] = 'Turnitin aktivieren';
$string['excludebiblio'] = 'Bibliografie ausschließen';
$string['excludequoted'] = 'Zitiertes Material ausschließen';
$string['excludevalue'] = 'Geringfügige Übereinstimmungen ausschließen';
$string['excludewords'] = 'Wörter';
$string['excludepercent'] = 'Prozent';
$string['norubric'] = 'Keine Rubrik';
$string['otherrubric'] = 'Rubrik einer anderen Lehrkraft verwenden';
$string['attachrubric'] = 'Dieser Aufgabe eine Rubrik anhängen';
$string['launchrubricmanager'] = 'Rubrikmanager starten';
$string['attachrubricnote'] = 'Hinweis: Studenten können angehängte Rubriken und deren Inhalt vor dem Übermitteln aufrufen.';
$string['anonblindmarkingnote'] = 'Hinweis: Die separate Turnitin-Einstellung für anonyme Benotung wurde entfernt. Turnitin legt die Einstellung für anonymes Benoten anhand der Moodle-Einstellung für Blindbewertung fest.';
$string['transmatch'] = 'Übersetzte Übereinstimmung';
$string["reportgen_immediate_add_immediate"] = "Erstellen Sie Berichte unverzüglich. Die Einreichungen werden sofort zum Repository hinzugefügt (sofern ein Repository festgelegt wurde).";
$string["reportgen_immediate_add_duedate"] = "Erstellen Sie Berichte unverzüglich. Die Einreichungen werden zum Fälligkeitsdatum zum Repository hinzugefügt (sofern ein Repository festgelegt wurde).";
$string["reportgen_duedate_add_duedate"] = "Erstellen Sie Berichte zum Fälligkeitsdatum. Die Einreichungen werden zum Fälligkeitsdatum zum Repository hinzugefügt (sofern ein Repository festgelegt wurde).";
$string['launchquickmarkmanager'] = 'QuickMark-Manager starten';
$string['launchpeermarkmanager'] = 'PeerMark-Manager starten';
$string['studentreports'] = 'Studenten den Echtheitsbericht anzeigen';
$string['studentreports_help'] = 'Ermöglicht Studenten das Anzeigen von Turnitin-Echtheitsberichten. Wenn Sie die Option "Ja" wählen, können Studenten den von Turnitin erstellten Echtheitsbericht ansehen.';
$string['submitondraft'] = 'Datei beim ersten Hochladen übermitteln';
$string['submitonfinal'] = 'Datei übermitteln, wenn der Student sie zum Markieren sendet.';
$string['draftsubmit'] = 'Wann muss die Datei an Turnitin übermittelt werden?';
$string['allownonor'] = 'Jeden Dateityp zur Übermittlung zulassen?';
$string['allownonor_help'] = 'Mit dieser Einstellung können alle Dateitypen übermitteln werden. Ist diese Option auf &#34;Ja&#34; gesetzt, werden Übermittlungen ggf. auf ihre Echtheit überprüft und zum Download bereitgestellt. Außerdem stehen wenn möglich GradeMark-Feedbacktools zur Verfügung.';
$string['norepository'] = 'Kein Repository';
$string['standardrepository'] = 'Standard-Repository ';
$string['submitpapersto'] = 'Studentenarbeiten ablegen';
$string['institutionalrepository'] = 'Institutions-Repository (wenn vorhanden)';
$string['checkagainstnote'] = 'Hinweis: Wenn Sie nicht für mindestens eine der folgenden Abgleichoptionen „Ja“ auswählen, wird KEIN Echtheitsbericht generiert.';
$string['spapercheck'] = 'Abgleich mit vorhandenen Studentenarbeiten';
$string['internetcheck'] = 'Abgleich mit dem Internet';
$string['journalcheck'] = 'Abgleich mit Zeitungen,<br />Periodika und anderen Publikationen';
$string['compareinstitution'] = 'Eingereichte Dateien mit den an dieser Institution übermittelten Arbeiten vergleichen';
$string['reportgenspeed'] = 'Geschwindigkeit beim Erstellen des Berichts';
$string['locked_message'] = 'Gesperrte Nachricht';
$string['locked_message_help'] = 'Wenn Einstellungen gesperrt sind, wird in dieser Nachricht der Grund dafür angegeben.';
$string['locked_message_default'] = 'Diese Einstellung ist auf Websiteebene gesperrt.';
$string['sharedrubric'] = 'Freigegebene Rubrik';
$string['turnitinrefreshsubmissions'] = 'Übermittlungen aktualisieren';
$string['turnitinrefreshingsubmissions'] = 'Übermittlungen werden aktualisiert...';
$string['turnitinppulapre'] = 'Um eine Datei an Turnitin zu übermitteln, müssen Sie zunächst unsere EULA akzeptieren. Wenn Sie unsere EULA nicht akzeptieren, wird Ihre Datei nur an Moodle übermittelt. Bitte klicken Sie hier, um die Vereinbarung zu lesen und zu akzeptieren.';
$string['noscriptula'] = '(Da Sie Javascript nicht aktiviert haben, müssen Sie diese Seite manuell aktualisieren, ehe Sie nach dem Akzeptieren der Nutzungsbedingungen von Turnitin eine Übermittlung vornehmen können)';
$string['filedoesnotexist'] = 'Datei wurde gelöscht';
$string['reportgenspeed_resubmission'] = 'Sie haben bereits eine Arbeit zu dieser Aufgabe übermittelt, und ein Ähnlichkeitsbericht wurde für Ihre übermittelte Arbeit erstellt. Wenn Sie Ihre Arbeit erneut übermitteln möchten, wird Ihre frühere Arbeit ersetzt und ein neuer Bericht wird erstellt. Nach {$a->num_resubmissions} erneuten Übermittlungen müssen Sie {$a->num_hours} Stunden warten, bis ein neuer Ähnlichkeitsbericht angezeigt wird.';

// Plugin settings.
$string['config'] = 'Konfiguration';
$string['defaults'] = 'Standardeinstellungen';
$string['showusage'] = 'Datenspeicher anzeigen';
$string['saveusage'] = 'Datenanzeige sichern';
$string['errors'] = 'Fehler';
$string['turnitinconfig'] = 'Konfiguration für das Turnitin-Plug-in gegen Plagiarismus';
$string['tiiexplain'] = 'Turnitin ist ein kommerzielles Produkt, und Sie benötigen ein zahlungspflichtiges Abonnement, um diesen Dienst nutzen zu können. Weitere Informationen finden Sie unter <a href=http://docs.moodle.org/en/Turnitin_administration>http://docs.moodle.org/en/Turnitin_administration</a>.';
$string['useturnitin'] = 'Turnitin aktivieren';
$string['useturnitin_mod'] = 'Turnitin aktivieren für {$a}';
$string['turnitindefaults'] = 'Standardeinstellungen für das Turnitin-Plugin zur Plagiarismuserkennung';
$string['defaultsdesc'] = 'Die folgenden Einstellungen bilden den Standard, wenn Turnitin mit einem Aktivitätsmodul aktiviert ist.';
$string['turnitinpluginsettings'] = 'Einstellungen für das Turnitin-Plugin zur Plagiarismuserkennung';
$string['pperrorsdesc'] = 'Beim Versuch, die folgenden Dateien bei Turnitin hochzuladen, ist ein Problem aufgetreten. Wählen Sie für eine erneute Übermittlung die gewünschten Dateien aus, und klicken Sie auf die Schaltfläche „Erneut übermitteln“. Diese Dateien werden dann bei der nächsten Cron-Ausführung verarbeitet.';
$string['pperrorssuccess'] = 'Die ausgewählten Dateien wurden erneut übermittelt und werden von Cron verarbeitet.';
$string['pperrorsfail'] = 'Bei einigen der ausgewählten Dateien ist ein Problem aufgetreten. Für diese Dateien konnte kein neues Cron-Ereignis erstellt werden.';
$string['resubmitselected'] = 'Ausgewählte Dateien erneut übermitteln';
$string['deleteconfirm'] = 'Möchten Sie diese Übermittlung wirklich löschen? \n\nDieser Vorgang kann nicht rückgängig gemacht werden.';
$string['deletesubmission'] = 'Übermittlung löschen';
$string['semptytable'] = 'Keine Ergebnisse vorhanden.';
$string['configupdated'] = 'Konfiguration aktualisiert';
$string['defaultupdated'] = 'Turnitin-Standards aktualisiert';
$string['notavailableyet'] = 'Nicht verfügbar';
$string['resubmittoturnitin'] = 'Erneut an Turnitin übermitteln';
$string['resubmitting'] = 'Wird erneut übermittelt...';
$string['id'] = 'ID';
$string['student'] = 'Student';
$string['course'] = 'Kurs';
$string['module'] = 'Module';

// Grade book/View assignment page.
$string['turnitin:viewfullreport'] = 'Echtheitsbericht anzeigen';
$string['launchrubricview'] = 'Die für das Markieren verwendete Rubik anzeigen';
$string['turnitinppulapost'] = 'Ihre Datei wurde nicht an Turnitin übermittelt. Klicken Sie hier, um unsere EULA zu akzeptieren.';
$string['ppsubmissionerrorseelogs'] = 'Diese Datei wurde nicht an Turnitin übermittelt; wenden Sie sich an Ihren Systemadministrator.';
$string['ppsubmissionerrorstudent'] = 'Diese Datei wurde nicht an Turnitin übermittelt, für zusätzliche Details kontaktieren Sie bitte Ihren Tutor';

// Receipts.
$string['messageprovider:submission'] = 'Turnitin-Plugin zur Plagiarismuserkennung – Benachrichtigungen zum digitalen Beleg';
$string['digitalreceipt'] = 'Digitaler Beleg';
$string['digital_receipt_subject'] = 'Dies ist Ihr digitaler Beleg von Turnitin.';
$string['pp_digital_receipt_message'] = 'Sehr geehrte/r {$a->firstname} {$a->lastname},<br /><br />Sie haben die Datei <strong>{$a->submission_title}</strong> für die Aufgabe <strong>{$a->assignment_name}{$a->assignment_part}</strong> in Kurs <strong>{$a->course_fullname}</strong> am <strong>{$a->submission_date}</strong> erfolgreich hochgeladen. Ihre Übermittlungs-ID lautet <strong>{$a->submission_id}</strong>. Ihren vollständigen digitalen Beleg können Sie über die Schaltfläche „Drucken/Download“ in der Dokumentenansicht anzeigen und drucken.<br /><br />Vielen Dank, dass Sie Turnitin verwenden,<br /><br />das Turnitin-Team';

// Paper statuses.
$string['turnitinid'] = 'Turnitin-ID';
$string['turnitinstatus'] = 'Turnitin-Status';
$string['pending'] = 'Ausstehend';
$string['similarity'] = 'Ähnlichkeit';
$string['notorcapable'] = 'Für diese Datei lässt sich kein Echtheitsbericht erstellen.';
$string['grademark'] = 'GradeMark';
$string['student_read'] = 'Der Student hat die Arbeit aufgerufen über:';
$string['student_notread'] = 'Der Student hat die Arbeit nicht aufgerufen.';
$string['launchpeermarkreviews'] = 'PeerMark-Reviews starten';

// Cron.
$string['ppqueuesize'] = 'Anzahl der Ereignisse in der Ereigniswarteschlange des Plugin zur Plagiarismuserkennung';
$string['ppcronsubmissionlimitreached'] = 'Von diesem Cron-Ausdruck werden keine weiteren Übermittlungen mehr an Turnitin gesendet, da nur {$a} pro Ausführung verarbeitet werden.';
$string['cronsubmittedsuccessfully'] = 'Übermittlung: {$a->title} (TII-ID: {$a->submissionid}) für die Aufgabe {$a->assignmentname} in Kurs {$a->coursename} wurde erfolgreich an Turnitin übermittelt.';
$string['pp_submission_error'] = 'Turnitin hat einen Fehler für Ihre Übermittlung zurückgegeben:';
$string['turnitindeletionerror'] = 'Die Löschung der Turnitin-Übermittlung ist fehlgeschlagen. Die lokale Moodle-Kopie wurde entfernt, die Übermittlung bei Turnitin konnte jedoch nicht gelöscht werden.';
$string['ppeventsfailedconnection'] = 'In dieser Cron-Ausführung werden vom Turnitin-Plugin zur Plagiarismuserkennung keine Ereignisse verarbeitet, da keine Verbindung mit Turnitin hergestellt werden kann.';

// Error codes.
$string['tii_submission_failure'] = 'Weitere Informationen erhalten Sie von Ihrem Tutor oder dem Systemadministrator.';
$string['faultcode'] = 'Fehlercode';
$string['line'] = 'Linie';
$string['message'] = 'Nachricht';
$string['code'] = 'Code';
$string['tiisubmissionsgeterror'] = 'Beim Versuch, von Turnitin Übermittlungen zu dieser Aufgabe abzurufen, ist ein Fehler aufgetreten.';
$string['errorcode0'] = 'Diese Datei wurde nicht an Turnitin übermittelt; wenden Sie sich an Ihren Systemadministrator.';
$string['errorcode1'] = 'Diese Datei wurde nicht an Turnitin gesendet, da sie nicht genügend Inhalt zum Erstellen eines Echtheitsberichts enthält.';
$string['errorcode2'] = 'Diese Datei wird nicht an Turnitin übermittelt, da sie die maximal zulässige Größe von {$a->maxfilesize} überschreitet.';
$string['errorcode3'] = 'Diese Datei wurde nicht an Turnitin übermittelt, da der Benutzer die Endbenutzer-Lizenzvereinbarung nicht akzeptiert hat.';
$string['errorcode4'] = 'Sie müssen einen unterstützten Dateityp für diese Aufgabe hochladen. Folgende Dateitypen werden akzeptiert: DOC, DOCX, PPT, PPTX, PPS, PPSX, PDF, TXT, HTM, HTML, HWP, ODT, WPD, PS und RTF.';
$string['errorcode5'] = 'Diese Datei wurde nicht an Turnitin übermittelt, da beim Erstellen des Moduls in Turnitin ein Problem aufgetreten ist, das Übermittlungen verhindert. Weitere Informationen finden Sie in Ihren API-Protokollen.';
$string['errorcode6'] = 'Diese Datei wurde nicht an Turnitin übermittelt, da beim Bearbeiten der Moduleinstellungen in Turnitin ein Problem aufgetreten ist, das Übermittlungen verhindert. Weitere Informationen finden Sie in Ihren API-Protokollen.';
$string['errorcode7'] = 'Diese Datei wurde nicht an Turnitin übermittelt, da beim Erstellen des Benutzers in Turnitin ein Problem aufgetreten ist, das Übermittlungen verhindert. Weitere Informationen finden Sie in Ihren API-Protokollen.';
$string['errorcode8'] = 'Diese Datei wurde nicht an Turnitin übermittelt, da beim Erstellen der temporären Datei ein Problem aufgetreten ist. Die wahrscheinlichste Ursache ist ein ungültiger Dateiname. Benennen Sie die Datei um, und laden Sie sie mit der Option zum Bearbeiten von Übermittlungen erneut hoch.';
$string['errorcode9'] = 'Die Datei kann nicht übermittelt werden, da im Dateipool kein zugänglicher Inhalt für eine Übermittlung vorhanden ist.';
$string['coursegeterror'] = 'Kursdaten konnten nicht abgerufen werden.';
$string['configureerror'] = 'Sie müssen dieses Modul vollständig als Administrator konfigurieren, um es in einem Kurs benutzen zu können. Wenden Sie sich an Ihren Moodle-Administrator.';
$string['turnitintoolofflineerror'] = 'Es ist ein vorübergehendes Problem aufgetreten. Bitte versuchen Sie es später erneut.';
$string['defaultinserterror'] = 'Beim Einfügen einer Standardwerteinstellung in die Datenbank ist ein Fehler eingetreten.';
$string['defaultupdateerror'] = 'Beim Aktualisieren einer Standardwerteinstellung in der Datenbank ist ein Fehler eingetreten.';
$string['tiiassignmentgeterror'] = 'Beim Versuch, eine Aufgabe von Turnitin abzurufen, ist ein Fehler aufgetreten.';
$string['assigngeterror'] = 'Daten für Turnitin konnten nicht aufgerufen werden.';
$string['classupdateerror'] = 'Daten des Turnitin-Kurses konnten nicht aktualisiert werden.';
$string['pp_createsubmissionerror'] = 'Beim Versuch, eine Übermittlung zu Turnitin einzurichten, ist ein Fehler aufgetreten.';
$string['pp_updatesubmissionerror'] = 'Beim Versuch, Ihre Übermittlung zu Turnitin erneut vorzunehmen, ist ein Fehler aufgetreten.';
$string['tiisubmissiongeterror'] = 'Beim Versuch, eine Übermittlung vom Turnitin zu erhalten, ist ein Fehler aufgetreten.';

// Javascript.
$string['closebutton'] = 'Schließen';
$string['loadingdv'] = 'Turnitin-Dokumentenansicht wird geladen...';
$string['changerubricwarning'] = 'Durch das Ändern oder Entfernen einer Rubrik werden alle vorhandenen Rubrikbewertungen der Arbeiten zu dieser Aufgabe entfernt, einschließlich ausgefüllter Bewertungskarten. Gesamtnoten für zuvor bewertete Arbeiten bleiben erhalten.';
$string['messageprovider:submission'] = 'Turnitin-Plugin zur Plagiarismuserkennung – Benachrichtigungen zum digitalen Beleg';

// Turnitin Submission Status.
$string['turnitinstatus'] = 'Turnitin-Status';
$string['deleted'] = 'Gelöscht';
$string['pending'] = 'Ausstehend';
$string['because'] = 'Ursache: Ein Administrator hat die ausstehende Aufgabe aus der Verarbeitungswarteschlange gelöscht und die Übermittlung an Turnitin abgebrochen.<br /><strong>Die Datei ist weiterhin in Moodle vorhanden; wenden Sie sich an die zuständige Lehrkraft.</strong><br />Fehlercodes siehe unten:';
$string['submitpapersto_help'] = '<strong>Kein Repository: </strong><br />In Turnitin ist festgelegt, dass übermittelte Dokumente nicht in einem Repository gespeichert werden. Die Arbeiten werden lediglich verarbeitet, um die eigentliche Ähnlichkeitsprüfung durchzuführen.<br /><br /><strong>Standard-Repository : </strong><br />Turnitin speichert eine Kopie des übermittelten Dokuments nur im Standard-Repository. Bei Auswahl dieser Option verwendet Turnitin nur gespeicherte Dokumente, um Ähnlichkeitsprüfungen mit zukünftig übermittelten Dokumenten durchzuführen.<br /><br /><strong>Institutions-Repository (wenn vorhanden): </strong><br />Mit dieser Option wird festgelegt, dass Turnitin übermittelte Dokumente nur zum privaten Repository Ihres Instituts hinzufügt. Ähnlichkeitsprüfungen übermittelter Dokumente werden von anderen Lehrkräften Ihrer Institution durchgeführt.';
$string['errorcode12'] = 'Diese Datei wurde nicht an Turnitin übertragen, da sie zu einer Aufgabe gehört, deren zugehöriger Kurs gelöscht wurde. Zeilen-ID: ({$a->id}) | Kursmodul-ID: ({$a->cm}) | Benutzer-ID: ({$a->userid})';
$string['errorcode15'] = "Diese Datei wurde nicht an Turnitin übermittelt, da das zugehörige Aktivitätsmodul nicht gefunden werden konnte.";
$string['tiiaccountconfig'] = 'Turnitin-Accountkonfiguration';
$string['turnitinaccountid'] = 'Turnitin-Account-ID';
$string['turnitinsecretkey'] = 'Shared Key von Turnitin';
$string['turnitinapiurl'] = 'Turnitin API URL';
$string['tiidebugginglogs'] = 'Debuggen und Protokollieren';
$string['turnitindiagnostic'] = 'Diagnosemodus aktivieren';
$string['turnitindiagnostic_desc'] = '<b>[Vorsicht]</b><br />Aktivieren Sie den Diagnosemodus nur, um die Ursache von Problemen mit der Turnitin-API zu ermitteln.';
$string['tiiaccountsettings_desc'] = 'Stellen Sie sicher, dass diese Einstellungen der Konfiguration in Ihrem Turnitin-Account entsprechen, da andernfalls Probleme mit der Aufgabenerstellung und/oder von Studenten übermittelten Arbeiten auftreten können.';
$string['tiiaccountsettings'] = 'Turnitin-Accounteinstellungen';
$string['turnitinusegrademark'] = 'GradeMark verwenden';
$string['turnitinusegrademark_desc'] = 'Legen Sie fest, ob GradeMark zum Benoten von Übermittlungen verwendet wird.<br /><i>(Diese Option ist nur für Benutzer verfügbar, die GradeMark für ihren Account konfiguriert haben.)</i>';
$string['turnitinenablepeermark'] = 'PeerMark-Anleitungen aktivieren';
$string['turnitinenablepeermark_desc'] = 'Legen Sie fest, ob das Erstellen von PeerMark-Aufgaben zulässig ist.<br/><i>(Diese Option ist nur für Benutzer verfügbar, die PeerMark für ihren Account konfiguriert haben.)</i>';
$string['transmatch_desc'] = 'Legt fest, ob die Einstellung „Übersetzte Übereinstimmung“ auf dem Einrichtungsbildschirm einer Aufgabe verfügbar ist.<br /><i>(Aktivieren Sie diese Option nur, wenn übersetzte Übereinstimmungen in Ihrem Turnitin-Account aktiviert sind.)</i>';
$string['repositoryoptions_0'] = 'Standardmäßige Repository-Optionen für Lehrkräfte aktivieren';
$string['repositoryoptions_1'] = 'Erweiterte Ablageoptionen für Lehrkräfte aktivieren';
$string['repositoryoptions_2'] = 'Alle Arbeiten an die Standardablage übermitteln';
$string['repositoryoptions_3'] = 'Übermitteln Sie keine Arbeiten an eine Ablage';
$string['turnitinrepositoryoptions'] = 'Ablage für Arbeiten zu Aufgaben';
$string['turnitinrepositoryoptions_desc'] = 'Wählen Sie die Repository-Optionen für Turnitin-Aufgaben aus.<br /><i>(Institutions-Repositories stehen nur Benutzern zur Verfügung, die diese Option für ihren Account aktiviert haben.)</i>';
$string['tiimiscsettings'] = 'Verschiedene Plug-in-Einstellungen';
$string['pp_agreement_default'] = 'Durch das Anklicken des Kontrollkästchens bestätige ich, dass diese Übermittlung meine eigene Arbeit ist. Ich übernehme die Verantwortung für jede Copyright-Verletzung, die aufgrund meiner Übermittlung entstehen könnte.';
$string['pp_agreement_desc'] = '<b>[Optional]</b><br />Geben Sie eine Bestätigung der Nutzungsbedingungen für Übermittlungen an.<br />(<b>Hinweis:</b> Wenn keine Angabe gemacht wird, müssen Studenten bei der Übermittlung keine Bestätigung angeben.)';
$string['pp_agreement'] = 'Haftungsausschluss / Zustimmung';
$string['studentdataprivacy'] = 'Einstellungen für den Datenschutz von Studenten';
$string['studentdataprivacy_desc'] = 'Die folgenden Einstellungen können so konfiguriert werden, dass persönliche Daten von Studenten nicht über die API an Turnitin übermittelt werden.';
$string['enablepseudo'] = 'Datenschutz aktivieren (Student)';
$string['enablepseudo_desc'] = 'Ist diese Option ausgewählt, werden die E-Mail-Adressen von Studenten für Turnitin-API-Aufrufe in ein entsprechendes Pseudo-Element umgewandelt.<br /><i>(<b>Hinweis:</b> Diese Option kann nicht geändert werden, wenn Moodle-Benutzerdaten bereits mit Turnitin synchronisiert wurden.)</i>';
$string['pseudofirstname'] = 'Pseudo-Vorname (Student)';
$string['pseudofirstname_desc'] = '<b>[Optional]</b><br />Vorname des Studenten für die Anzeige in der Turnitin-Dokumentenansicht';
$string['pseudolastname'] = 'Pseudo-Nachname (Student)';
$string['pseudolastname_desc'] = 'Den Nachnamen des Studenten in der Dokumentenansicht von Turnitin anzeigen';
$string['pseudolastnamegen'] = 'Nachnamen automatisch erstellen';
$string['pseudolastnamegen_desc'] = 'Wenn diese Option aktiviert und der Pseudo-Nachname auf ein Benutzerprofilfeld festgelegt ist, wird das Feld automatisch mit einem eindeutigen Bezeichner ausgefüllt.';
$string['pseudoemailsalt'] = 'Pseudo-Verschlüsselungssalt';
$string['pseudoemailsalt_desc'] = '<b>[Optional]</b><br />Ein optionaler Salt, der die Komplexität der für Studenten generierten Pseudo-E-Mail-Adressen erhöht.<br />(<b>Hinweis:</b> Der Salt sollte nicht verändert werden, damit die Pseudo-E-Mail-Adressen konsistent bleiben.)';
$string['pseudoemaildomain'] = 'Pseudo-E-Mail-Domäne';
$string['pseudoemaildomain_desc'] = '<b>[Optional]</b><br />Eine optionale Domäne für Pseudo-E-Mail-Adressen. (Ohne Angabe wird standardmäßig @tiimoodle.com verwendet.)';
$string['pseudoemailaddress'] = 'Pseudo-E-Mail-Adresse';
$string['connecttest'] = 'Verbindung mit Turnitin testen';
$string['connecttestsuccess'] = 'Moodle wurde erfolgreich mit Turnitin verbunden.';
$string['diagnosticoptions_0'] = 'Aus';
$string['diagnosticoptions_1'] = 'Standard';
$string['diagnosticoptions_2'] = 'Debuggen';
$string['repositoryoptions_4'] = 'Alle Arbeiten an das Instituts-Repository übermitteln';
$string['turnitinrepositoryoptions_help'] = '<strong>Standardmäßige Repository-Optionen für Lehrkräfte aktivieren: </strong><br />Lehrkräfte können in Turnitin festlegen, ob Dokumente zum Standard-Repository, zum privaten Repository der Institution oder zu keinem Repository hinzugefügt werden sollen.<br /><br /><strong>Erweiterte Ablageoptionen für Lehrkräfte aktivieren: </strong><br />Mit dieser Option können Lehrkräfte die Aufgabeneinstellungen anzeigen, damit Studenten in Turnitin festlegen können, wo ihre Dokumente gespeichert werden. Studenten können wählen, ob ihre Dokumente zum Standard-Studenten-Repository oder zum privaten Repository der Institution hinzugefügt werden.<br /><br /><strong>Alle Arbeiten an die Standardablage übermitteln: </strong><br />Alle Dokumente werden standardmäßig zum Standard-Studenten-Repository hinzugefügt.<br /><br /><strong>Übermitteln Sie keine Arbeiten an eine Ablage: </strong><br />Dokumente werden ausschließlich für die eigentliche Prüfung durch Turnitin und zum Anzeigen für die Lehrkraft zur Benotung verwendet.<br /><br /><strong>Alle Arbeiten an das Instituts-Repository übermitteln: </strong><br />In Turnitin ist festgelegt, dass alle übermittelten Arbeiten in der Institutionsablage für Arbeiten gespeichert werden. Ähnlichkeitsprüfungen der übermittelten Dokumente werden ausschließlich von anderen Lehrkräften in Ihrer Institution durchgeführt.';
$string['turnitinuseanon'] = 'Anonyme Benotung verwenden';
$string['createassignmenterror'] = 'Beim Versuch, eine Aufgabe in Turnitin zu erzeugen, ist ein Fehler aufgetreten.';
$string['editassignmenterror'] = 'Beim Versuch, die Aufgabe in Turnitin zu bearbeiten, ist ein Fehler aufgetreten.';
$string['ppassignmentediterror'] = 'Modul {$a->title} (TII-ID: {$a->assignmentid}) konnte in Turnitin nicht bearbeitet werden; weitere Informationen finden Sie in Ihren API-Protokollen.';
$string['pp_classcreationerror'] = 'Dieser Kurs konnte auf Turnitin nicht erstellt werden, für zusätzliche Informationen prüfen Sie bitte Ihre API-Protokolle';
$string['unlinkusers'] = 'Benutzer trennen';
$string['relinkusers'] = 'Benutzer erneut verlinken';
$string['unlinkrelinkusers'] = 'Benutzer erneut verlinken / Verlinkung aufheben';
$string['nointegration'] = 'Keine Integration';
$string['sprevious'] = 'Zurück';
$string['snext'] = 'Weiter';
$string['slengthmenu'] = 'Anzeigen_MENU_ Einträge';
$string['ssearch'] = 'Suchen:';
$string['sprocessing'] = 'Daten werden von Turnitin geladen...';
$string['szerorecords'] = 'Keine Daten vorhanden';
$string['sinfo'] = 'Anzeigen _START_ bis _END_ von _TOTAL_ Einträgen.';
$string['userupdateerror'] = 'Benutzerdaten konnten nicht aktualisiert werden.';
$string['connecttestcommerror'] = 'Keine Verbindung mit Turnitin möglich. Überprüfen Sie Ihre API-URL-Einstellung.';
$string['userfinderror'] = 'Beim Suchen des Benutzers in Turnitin ist ein Fehler aufgetreten.';
$string['tiiusergeterror'] = 'Beim Versuch, Benutzerinformation von Turnitin abzurufen, ist ein Fehler aufgetreten.';
$string['usercreationerror'] = 'Das Erstellen eines Turnitin-Benutzers ist fehlgeschlagen.';
$string['ppassignmentcreateerror'] = 'Dieses Modul konnte auf Turnitin nicht erstellt werden, für zusätzliche Informationen prüfen Sie bitte Ihre API-Protokolle';
$string['excludebiblio_help'] = 'Mit dieser Einstellung kann die Lehrkraft festlegen, ob beim Erstellen von Echtheitsberichten Text in der Bibliografie, aus zitierten Werken oder in Referenzabschnitten von Studentenarbeiten von der Prüfung auf Übereinstimmungen ausgenommen wird. Diese Einstellung kann für einzelne Echtheitsberichte aufgehoben werden.';
$string['excludequoted_help'] = 'Mit dieser Einstellung kann die Lehrkraft festlegen, ob beim Erstellen von Echtheitsberichten der Text aus Zitaten von der Prüfung auf Übereinstimmungen ausgenommen wird. Diese Einstellung kann für einzelne Echtheitsberichte aufgehoben werden.';
$string['excludevalue_help'] = 'Mit dieser Einstellung kann die Lehrkraft festlegen, ob Übereinstimmungen von nicht ausreichender Länge (von der Lehrkraft festzulegen) beim Erstellen von Echtheitsberichten nicht berücksichtigt werden. Diese Einstellung kann für einzelne Echtheitsberichte aufgehoben werden.';
$string['spapercheck_help'] = 'Mit der Turnitin-Ablage für Studentenarbeiten beim Erstellen des Echtheitsberichts für Arbeiten abgleichen. Der Prozentsatz beim Ähnlichkeitsindex kann abnehmen, wenn dies abgewählt ist.';
$string['internetcheck_help'] = 'Mit dem Internet-Repository von Turnitin beim Erstellen des Echtheitsberichts für Arbeiten abgleichen. Der Prozentsatz beim Ähnlichkeitsindex kann abnehmen, wenn dies abgewählt ist.';
$string['journalcheck_help'] = 'Mit dem Repository von Turnitin beim Erstellen des Echtheitsberichts für Arbeiten abgleichen. Der Prozentsatz beim Ähnlichkeitsindex kann abnehmen, wenn dies abgewählt ist.';
$string['reportgenspeed_help'] = 'Für diese Aufgabeneinstellung sind drei Optionen verfügbar: &#39;Erstellen Sie Berichte unverzüglich. Die Einreichungen werden zum Fälligkeitsdatum zum Repository hinzugefügt (sofern ein Repository festgelegt wurde).&#39;, &#39;Erstellen Sie Berichte unverzüglich. Die Einreichungen werden sofort zum Repository hinzugefügt (sofern ein Repository festgelegt wurde).&#39; und &#39;Erstellen Sie Berichte zum Fälligkeitsdatum. Die Einreichungen werden zum Fälligkeitsdatum zum Repository hinzugefügt (sofern ein Repository festgelegt wurde).&#39;<br /><br />Mit der Option &#39;Erstellen Sie Berichte unverzüglich. Die Einreichungen werden zum Fälligkeitsdatum zum Repository hinzugefügt (sofern ein Repository festgelegt wurde).&#39; wird der Echtheitsbericht sofort erstellt, wenn ein Student eine Übermittlung vornimmt. Ist diese Option festgelegt, können Ihre Studenten keine erneuten Übermittlungen für die Aufgabe vornehmen.<br /><br />Wenn Sie erneute Übermittlungen zulassen möchten, wählen Sie die Option &#39;Erstellen Sie Berichte unverzüglich. Die Einreichungen werden sofort zum Repository hinzugefügt (sofern ein Repository festgelegt wurde).&#39;. Bei dieser Option können Studenten bis zum Fälligkeitsdatum wiederholt Arbeiten für die Aufgabe übermitteln. Die Verarbeitung von Echtheitsberichten für erneute Übermittlungen kann bis zu 24 Stunden dauern.<br /><br />Mit der Option &#39;Erstellen Sie Berichte zum Fälligkeitsdatum. Die Einreichungen werden zum Fälligkeitsdatum zum Repository hinzugefügt (sofern ein Repository festgelegt wurde).&#39; wird der Echtheitsbericht erst am Fälligkeitsdatum für Aufgabe erstellt. Mit dieser Einstellung werden bei der Erstellung der Echtheitsberichte alle für die Aufgabe übermittelten Arbeiten miteinander abgeglichen.';
$string['turnitinuseanon_desc'] = 'Legen Sie fest, ob beim Bewerten die anonyme Benotung zulässig ist.<br /><i>(Diese Option ist nur für Benutzer verfügbar, die für ihren Account die anonyme Benotung konfiguriert haben.)</i>';
