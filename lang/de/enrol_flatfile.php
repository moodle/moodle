<?PHP // $Id$ 
      // enrol_flatfile.php - created with Moodle 1.4 aiming-for-beta-soon (2004082200)


$string['description'] = 'Diese Methode benutzt mehrfach eine speziell formatierte Textdatei, die in dem angegebenen Verzeichnis abgelegt ist. Die Datei kann folgenden Aufbau haben:
<pre>
add, Teilnehmer, 5, CF101
add, Moderator, 6, CF101
add, teacheredit, 7, CF101
del, Teilnehmer, 8, CF101
del, Teilnehmer, 17, CF101
add, Teilnehmer, 21, CF101, 1091115000, 1091215000
</pre>';
$string['enrolname'] = 'Dateiname';
$string['filelockedmail'] = 'Die Textdatei ($a), die für die Registrierung genutzt wurde, kann durch den Cron-Job nicht gelöscht werden. Dies ist meist der Fall, wenn die Berechtigungen nicht richtig gesetzt sind. Bitte ändern Sie die Berechtigungen, so dass Moodle die Datei löschen kann. Ansonsten wird die Datei mit jedem Cron-Job wieder ausgeführt. ';
$string['filelockedmailsubject'] = 'Wichtiger Fehler:  Datei für die Registrierung';
$string['location'] = 'Angabe des  Verzeichnis, in dem die Datei abgelegt ist';
$string['mailadmin'] = 'Administrator per E-Mail benachrichtigen';
$string['mailusers'] = 'Benutzer per E-Mail benachrichtigen';

?>
