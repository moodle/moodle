<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.6 development (2005111101)


$string['adminauthorizeccapture'] = 'Orderreview & Auto-Capture Einstellungen';
$string['adminauthorizeemail'] = 'E-Mailversand-Einstellungen';
$string['adminauthorizesettings'] = 'Authorize.net Einstellungen';
$string['adminauthorizewide'] = 'Seitenweite Einstellungen';
$string['adminavs'] = 'Klicken Sie die Funktion an wenn Sie das Adress Verification System (AVS) im Authorize Account aktiviert haben. Damit sind Eintragungen in Adressfeldern wie Strasse, Staat, Country und ZiP beim Ausfüllen des Zahlungsvorgangs erforderlich.';
$string['admincronsetup'] = 'Die cron.php Datei wurde in den letzten 24 Stunden nicht ausgelöst.<br />Der Cron-Prozess ist erforderlich, um das Autocapture Feature zu nutzen.<br /><a href=\"../doc/?frame=install.html&#8834;=cron\">Cron Setup</a> oder deaktivieren Sie an_review wieder.<br />Wenn Sie autocapture deaktivieren werden Transaktionen deaktiviert, die nicht innerhalb von 30 Tagen manuell bestätigt werden.<br />Prüfen Sie an_review und geben Sie \'0\' im Feld an_capture_day<br />wenn Sie innerhalb von 30 Tagen  Zahlungen manuell akzeptieren/zurückweisen wollen.';
$string['adminemailexpired'] = 'Versendet <b>$a</b> Tage vor Ende der Bearbeitungsfrist für offene Zahlungsvorgänge Warn-E-Mails an Admins. (0=E-Mail versenden deaktivieren, default=2, max=5)<br />Die Einstellung ist hilfreich wenn manuelles Capturing aktiviert ist (an_review=checked, an_capture_day=0).';
$string['adminhelpcapture'] = 'Sie müssen Zahlungen nicht manuell akzeptieren oder zurückweisen. Mit Hilfe von Autocapture können Sie verhindern, dass Zahlungsvorgänge widerrufen werden. Was ist zu tun?
- Cron setup vornehmen.
- an_review prüfen
- Eintrag eines Wertes zwischen 1 und 29 in das Feld an_capture_day Feld. Kartenzahlungen werden angenommen und die Teilnehmer/innen in die Kurse eingetragen bis Sie innerhalb der Frist den Vorgang bearbeitet haben.';
$string['adminhelpreview'] = 'Wie kann ich manuell Zahlungen annehmen/zurückweisen?
- an_review prüfen.
- \'0\' im Feld an_capture_day eintragen.

Wie werden Teilnehmer/innen sofort nach Eingabe der Kartendaten in den Kurs eingetragen?
- Deaktivieren Sie an_review.';
$string['adminneworder'] = 'An den Admin,

Eine neue Zhalungist registriert worden:

Order ID: $a->orderid
TransaKtion ID: $a->transid
Nutzer/innen: $a->user
Kurse: $a->course
Betrag: $a->amount

AUTO-CAPTURE AKTIVIERT?: $a->acstatus

Mit aktivem auto-capture wird die Kreditkarte angenommen unter $a->captureon und die Teilnehmer/in wird im Kurs eingetragen. Sonst wird die Karte unter $a->captureon eingetragen und kann am gleichen Tag nicht mehr akzeptiert werden.

Zahlungen können angenommen/zurückgewiesen werden wenn Sie diesem Link folgen: 
$a->url';
$string['adminnewordersubject'] = '$a->course: Neue offene Zahlungen ($a->orderid)';
$string['adminpendingorders'] = 'Sie haben das auto-capture Feature deaktiviert. <br />Insgesamt $a->count Transaktionen mit dem Status AN_STATUS_AUTH werden zurückgewiesen wenn Sie diese nicht prüfen.<br />Gehen Sie zum <a href=\'§a->url\'>Zahlungsmanagement</a>, um diese zu bearbeiten.';
$string['adminreview'] = 'Zahlung überprüfen bevor Kreditkarte akzeptiert wird';
$string['amount'] = 'Betrag';
$string['anlogin'] = 'Authorize.net: Loginname';
$string['anpassword'] = 'Authorize.net: Passwort (nicht erforderlich)';
$string['anreferer'] = 'Tragen Sie hier die URL ein wenn Sie dies in Ihrem authorize.net account eintragen. Damit wird eine \"Referer:URL\" in der Webanfrage erstellt.';
$string['antestmode'] = 'Authorize.net: Test Transaktionen';
$string['antrankey'] = 'Authorize.net: Transaktionskey';
$string['authorizedpendingcapture'] = 'Bestätigte/Wartende Zahlungen';
$string['canbecredit'] = 'Kann erstattet werden an $a->upto';
$string['cancelled'] = 'Aufgehoben';
$string['capture'] = 'Zahlungen';
$string['capturedpendingsettle'] = 'Bestätigte/offene Zahlungen';
$string['capturedsettled'] = 'Bestätigt/ gezahlt';
$string['capturetestwarn'] = 'Die Zahlungen scheinen zu funktionieren. Im Testmodus wurde aber kein Datensatz aktualisiert.';
$string['captureyes'] = 'Die Kreditkarte wird angenommen und der/die Teilnehmer/in in den Kurs eingetragen. Sind Sie sicher?';
$string['ccexpire'] = 'Verfallsdatum';
$string['ccexpired'] = 'Die Kreditkarte ist abgelaufen';
$string['ccinvalid'] = 'Ungültige Kartennummer';
$string['ccno'] = 'Kreditkartennummer';
$string['cctype'] = 'Kreditkartentyp';
$string['ccvv'] = 'Kreditkarten Überprüfung';
$string['ccvvhelp'] = 'Schauen Sie auf der Kartenrückseite nach (letzte drei Zeichen)';
$string['choosemethod'] = 'Wenn Sie den Zugangsschlüssel kennen, tragen Sie ihn hier ein. Im anderen Fall müssen Sie erst die Kursgebühren entrichten.';
$string['chooseone'] = 'Füllen Sie eines oder beide Felder aus';
$string['credittestwarn'] = 'Die Kreditkartenabwicklung scheint zu funktionieren. Im Testmodus wurde aber kein Datensatz zur Datenbank hinzugefügt.';
$string['cutofftime'] = 'Transaktionsende. Wannn soll die letze Zahlung zur Abwicklung aufgenommen werden?';
$string['delete'] = 'Löschen';
$string['description'] = 'Das Authorize.net Modul erlaubt Kursgebühren über Kreditkarten abzurechnen. Wenn der Betrag für einen Kurs auf \'0\' gesetzt wird, wird die Gebührenabfrage nicht gestartet. Sie können hier einen seitenweit gültigen Betrag einsetzen, der als Grundbetrag für jeden Kurs voreingestellt ist. Diese Einstellung kann in den Kurseinstellungen überschrieben werden.';
$string['enrolname'] = 'Authorize.net Kreditkartenabrechnung';
$string['expired'] = 'Ablauffrist';
$string['howmuch'] = 'Wie viel?';
$string['httpsrequired'] = 'Ihre Anfrage kann leider zur Zeit nicht bearbeitet werden. Die Konfiguration der Seite weist einen Fehler auf. <br /><br />
Geben Sie Ihre Kreditkartennummer solange nicht ein bis Sie ein gelbes Schloß am Fuß des Browsers sehen können. Es signalisiert eine einfache Verschlüsselung für die Übermittlung aller Daten zwischen Ihrem Rechner und dem Server. Damit wird die Datenübertragung geschützt und Ihre Kreditkartendaten können nichtin falsche Hände geraten.';
$string['logindesc'] = 'Sie können in den Optionen (Variables/Security) eine sichere <a href=\"$a->url\">Https Verbindung</a> auswählen.
<br /><br />
Ist diese Variable gesetzt, wird Moodle für die Login- und Zahlungsseite eine sichere https Verbindung aufbauen.';
$string['nameoncard'] = 'Name auf den die Karte ausgestellt ist';
$string['noreturns'] = 'Kein Zurück!';
$string['notsettled'] = 'Nicht bearbeitet';
$string['orderid'] = 'Order ID';
$string['paymentmanagement'] = 'Zahlungsmanagement';
$string['paymentpending'] = 'Ihre Zahlung für diesen Kurs wird unter der Nummer  $a->orderid  bearbeitet.';
$string['pendingordersemail'] = 'Hallo Admin,

$a->pending Transaktionen müssen innerhalb von zwei Tagen von Ihnen bearbeitet werden.

Dies ist ein Warnhinweis, weil Sie autocapture nicht eingerichtet haben. Die Zahlungen müssen daher manuell von Ihnen bestätigt oder zurückgewiesen werden.

Die offenen Zahlungen können unter $a->url bearbeitet werden.

Unter $a->enrolurl kann autcapture eingerichtet werden, damit Sie künftig diese E-Mial nicht mehr erhalten.';
$string['refund'] = 'Rückzahlung';
$string['refunded'] = 'Zurückgezahlt';
$string['returns'] = 'Rückläufe';
$string['reviewday'] = 'Bewahren der Kreditkarte automatisch für <b>$a </b> Tage bis ein/e Trainer/in oder ein/e Administrator/in die Zahlung geprüft hat. CronJobs müssen hierfür aktiv sein.<br/>Wert 0 Tage = Funktion deaktiviert<br/>autocapture = Trainer/in, Admin prüft manuell.<br/>Transaktion wird gelöscht wenn autocapture deaktiviert wird oder innerhalb von 30 Tagen keine Prüfung erfolgt ist.';
$string['reviewnotify'] = 'Ihre Zahlung wird geprüft. Sie erhalten eine E-Mailnachricht von Ihrer/m Trainer/in in einigen Tagen.';
$string['sendpaymentbutton'] = 'Zahlung übertragen';
$string['settled'] = 'Erledigt';
$string['settlementdate'] = 'Erledigungstermin';
$string['subvoidyes'] = 'Zurückgezahlte Transaktionen $a->transid werden aufgehoben und Ihr Account wird mit $a->amount belastet.';
$string['tested'] = 'Geprüft';
$string['testmode'] = '[TEST MODUS]';
$string['transid'] = 'Transaktons-ID';
$string['unenrolstudent'] = 'Teilnehmer/in aus Kurs austragen?';
$string['void'] = 'Gültig';
$string['voidtestwarn'] = 'Die Gültigkeitsprüfung scheint zu arbeiten. Im Testmodus wurde jedoch kein Datensatz aktualisiert. ';
$string['voidyes'] = 'Ihre Transaktion wird abgebrochen. Sind Sie sicher?';
$string['zipcode'] = 'Zip Code/Postleitzahl';

?>
