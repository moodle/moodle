<?PHP // $Id$ 
      // enrol_authorize.php - created with Moodle 1.6 development (2005090100)


$string['adminreview'] = 'Zahlung überprüfen bevor Kreditkarte akzeptiert wird';
$string['anlogin'] = 'Authorize.net: Loginname';
$string['anpassword'] = 'Authorize.net: Passwort (nicht erforderlich)';
$string['anreferer'] = 'Tragen Sie hier die URL ein wenn Sie dies in Ihrem authorize.net account eintragen. Damit wird eine \"Referer:URL\" in der Webanfrage erstellt.';
$string['antestmode'] = 'Authorize.net: Test Transaktionen';
$string['antrankey'] = 'Authorize.net: Transaktionskey';
$string['ccexpire'] = 'Verfallsdatum';
$string['ccexpired'] = 'Die Kreditkarte ist abgelaufen';
$string['ccinvalid'] = 'Ungültige Kartennummer';
$string['ccno'] = 'Kreditkartennummer';
$string['cctype'] = 'Kreditkartentyp';
$string['ccvv'] = 'Kreditkarten Überprüfung';
$string['ccvvhelp'] = 'Schauen Sie auf der Kartenrückseite nach (letzte drei Zeichen)';
$string['choosemethod'] = 'Wenn Sie den Zugangsschlüssel kennen, tragen Sie ihn hier ein. Im anderen Fall müssen Sie erst die Kursgebühren entrichten.';
$string['chooseone'] = 'Füllen Sie eines oder beide Felder aus';
$string['description'] = 'Das Authorize.net Modul erlaubt Kursgebühren über Kreditkarten abzurechnen. Wenn der Betrag für einen Kurs auf \'0\' gesetzt wird, wird die Gebührenabfrage nicht gestartet. Sie können hier einen seitenweit gültigen Betrag einsetzen, der als Grundbetrag für jeden Kurs voreingestellt ist. Diese Einstellung kann in den Kurseinstellungen überschrieben werden.';
$string['enrolname'] = 'Authorize.net Kreditkartenabrechnung';
$string['httpsrequired'] = 'Ihre Anfrage kann leider zur Zeit nicht bearbeitet werden. Die Konfiguration der Seite weist einen Fehler auf. <br /><br />
Geben Sie Ihre Kreditkartennummer solange nicht ein bis Sie ein gelbes Schloß am Fuß des Browsers sehen können. Es signalisiert eine einfache Verschlüsselung für die Übermittlung aller Daten zwischen Ihrem Rechner und dem Server. Damit wird die Datenübertragung geschützt und Ihre Kreditkartendaten können nichtin falsche Hände geraten.';
$string['logindesc'] = 'Sie können in den Optionen (Variables/Security) eine sichere <a href=\"$a->url\">Https Verbindung</a> auswählen.
<br /><br />
Ist diese Variable gesetzt, wird Moodle für die Login- und Zahlungsseite eine sichere https Verbindung aufbauen.';
$string['nameoncard'] = 'Name auf den die Karte ausgestellt ist';
$string['reviewday'] = 'Bewahren der Kreditkarte automatisch für <b>$a </b> Tage bis ein/e Trainer/in oder ein/e Administrator/in die Zahlung geprüft hat. CronJobs müssen hierfür aktiv sein.<br/>Wert 0 Tage = Funktion deaktiviert<br/>autocapture = Trainer/in, Admin prüft manuell.<br/>Transaktion wird gelöscht wenn autocapture deaktiviert wird oder innerhalb von 30 Tagen keine Prüfung erfolgt ist.';
$string['reviewnotify'] = 'Ihre Zahlung wird geprüft. Sie erhalten eine E-Mailnachricht von Ihrer/m Trainer/in in einigen Tagen.';
$string['sendpaymentbutton'] = 'Zahlung übertragen';
$string['zipcode'] = 'ZIp Code';

?>
