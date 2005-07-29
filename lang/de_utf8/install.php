<?PHP // $Id$ 
      // install.php - created with Moodle 1.5.2 + (2005060221)


$string['admindirerror'] = 'Das angegebene Admin-Verzeichnis ist falsch.';
$string['admindirname'] = 'Name für das Admin-Verzeichnis';
$string['admindirsetting'] = 'Einige wenige Webhosting-Anbieter benutzen /admin als spezielles Verzeichnis für den Zugang zum Administrationstool oder andere Dinge. Leider kommt es dadurch zu Konflikten mit dem Standard für das Administrationsverzeichnis von moodle. Sie können dies ändern, indem Sie das admin-Verzeichnis in der moodle-Installation umbenennen. Den gewählten Namen dieses Verzeichnisses müssen Sie hier eingeben.
Zum Beispiel: <br/> <br /><b>moodleadmin</b><br /> 
Dies ändert die Links für das Admin-Verzeichnis in moodle.';
$string['caution'] = 'Warnung';
$string['chooselanguage'] = 'Eine Sprache wählen';
$string['compatibilitysettings'] = 'Prüfung Ihrer PHP-Einstellungen ...';
$string['configfilenotwritten'] = 'Ds Installationsscript kann die Datei config.php, welche die gewählten Einstellungen enthält, nicht automatisch erstellen, weil der web-user keine Schreibrechte für das moodle-Verzeichnis hat. Sie können den folgenden Code manuell in einer Datei config.php speichern und diese ins moodle- Hauptverzeichnis kopieren.';
$string['configfilewritten'] = 'Die Datei config.php wurde erfolgreich erstellt';
$string['configurationcomplete'] = 'Die Konfiguration ist abgeschlossen.';
$string['database'] = 'Datenbank';
$string['databasecreationsettings'] = 'Jetzt wird die Datenbank erstellt, in der die meisten moodle-Daten gespeichert werden. Diese Datenbank muss bereits angelegt sein und Sie müssen den Datenbankuser und das Passwort kennen.<br/>
 <br />
<b>Typ:</b> mysql oder postgres7<br />
<b>Host:</b> z.B. localhost oder db.isp.com<br />
<b>Name:</b> Datenbankname, z.B. moodle<br />
<b>Nutzer:</b> Ihr Benutzername für die Datenbank<br />
<b>Passwort:</b> Ihr Passwort für die Datenbank<br />
<b>Tabellen Prefix:</b> optionaler Prefix, der für aller Tabellen genutzt wird ';
$string['databasesettings'] = 'Jetzt wird die Datenbank erstellt, in der die meisten moodle-Daten gespeichert werden. Diese Datenbank muss bereits angelegt sein und Sie müssen den Datenbankuser und das Passwort kennen.<br/>
 <br />
<b>Typ:</b> mysql oder postgres7<br />
<b>Host:</b> z.B. localhost oder db.isp.com<br />
<b>Name:</b> Datenbankname, z.B. moodle<br />
<b>Nutzer:</b> Ihr Benutzername für die Datenbank<br />
<b>Passwort:</b> Ihr Passwort für die Datenbank<br />
<b>Tabellen Prefix:</b> optionaler Prefix, der für aller Tabellen genutzt wird ';
$string['dataroot'] = 'Datenverzeichnis';
$string['datarooterror'] = 'Das angegebene Datenverzeichnis ist nicht vorhanden und kann nicht angelegt werden. Korrigieren Sie diese Eingabe oder legen Sie das Verzeichnis manuell an.';
$string['dbconnectionerror'] = 'Eine Verbindung zur angegebenen Datenbank konnte nicht hergestellt werden. Bitte überprüfen Sie Ihre Eingaben.';
$string['dbcreationerror'] = 'Fehler beim Anlegen der Datenbank. Die Datenbank konnte mit diesen Einstellungen nicht erstellt werden.';
$string['dbhost'] = 'Name des Datenbankservers';
$string['dbpass'] = 'Passwort';
$string['dbprefix'] = 'Prefix für alle Tabellen';
$string['dbtype'] = 'Datenbankart';
$string['directorysettings'] = '<p>Bitte überprüfen Sie das Verzeichnis für diese Moodle Installation.</p>

<p><b>URL Adresse:</b>
Geben Sie hier die vollständige URL für Ihre Moodle Installation an. Sollte Ihre Seite über mehrere Adressen erreichbar sein, geben Sie die Adresse an, die am häufigsten genutzt wird. Bitte geben Sie am Ende kein Backslash ein.</p>

<p><b>Moodle Verzeichnis:</b>
Geben Sie den absoluten Pfad für Ihre Moodle Installation an. Bitte beachten Sie ob Gross- und Kleinschreibung korrekt ist.</p>

<p><b>Datenverzeichnis:</b>
Moodle benötigt ein Verzeichnis, indem hochgeladene Dateien abgelegt werden. Dieses Verzeichnis muss Lese- und Schreibrechte für den 
Webuser des Servers haben. (üblicherweise \'nobody\' or \'apache\'), aber es sollte nicht direkt über das Internet erreichbar sein.</p>';
$string['dirroot'] = 'Moodle Verzeichnis';
$string['dirrooterror'] = 'Die Einstellungen für das Moodle-Verzeichnis sind nicht korrekt.  Es wurde keine Moodle Installation gefunden. Die anderen Werte wurden gelöscht.';
$string['download'] = 'Herunterladen';
$string['fail'] = 'Fehlgeschlagen';
$string['fileuploads'] = 'Dateien hochladen';
$string['fileuploadserror'] = 'Dies sollte auf \'on\' stehen';
$string['fileuploadshelp'] = '<p>Dateien hochladen ist auf diesem Server abgestellt.</p>

<p>Moodle kann installiert werden. Es ist aber nicht möglich, Dateien für Kurse oder Bilder in den Profilen hochzuladen.
<p>Um das Hochladen von Dateien zu ermöglichen, müssen Sie oder der Adminstrator des Servers die Datei php.ini anpassen und die Einstellungen für<b>file_uploads</b> ändern auf \'1\'.</p>';
$string['gdversion'] = 'GD Version';
$string['gdversionerror'] = 'Die GD Bibliothek sollte verfügbar sein, um Bilder zu erzeugen und anzuzeigen.';
$string['gdversionhelp'] = '<p>Auf Ihrem Server ist vermutlich GD nicht installiert. </p>
<p>GD ist eine Bibliothek, die von PHP benötigt wird, damit Bilder, z.B. Nutzer-Bilder oder grafische Darstellungen der LOG-Daten, von moodle angezeigt werden können. moodle arbeitet auch ohne GD. Die o.g. Funktionen stehen Ihnen dann jedoch nicht zur Verfügung.</p>
<p> Wenn Sie GD unter UNIX zu PHP hinzufügen wollen, kompilieren Sie PHP unter Verwendung des Parameters   with-gd </p>
<p>Unter Windows können Sie die Datei php.ini bearbeiten und die Zeile libgd.dll auskommentieren.</p>';
$string['installation'] = 'Installation';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'Dies sollte ausgeschaltet sein (\'off\')';
$string['magicquotesruntimehelp'] = '<p>Magic Quotes Runtime sollte abgeschaltet \'off\' sein, damit moodle richtig läuft.  </p>
<p>Normalerweise ist dies der Fall. Prüfen Sie die Einstellung <b>magic_quotes_runtime</b> in der Datei php.ini. </p>
<p>Wenn Sie keinen Zugriff zur Datei php.ini haben sollten Sie die folgende Zeile in eine Datei .htacess in Ihrem moodle-Verzeichnis einfügen: <blockquote>php_value magic_quotes_runtime Off</blockquote></p>';
$string['memorylimit'] = 'Memory Limit';
$string['memorylimiterror'] = 'Die Speichereinstellung PHP memory limit ist zu niedrig. Es wird bei der künftigen Nutzung vermutlich zu Problemen kommen.';
$string['memorylimithelp'] = '<p>Die Einstellung der PHP  memory limit für Ihren Server ist zur Zeit auf $a eingestellt. </p>
<p>Dies wird vermutlich zu Problemen führen wenn Sie moodle mit vielen Aktivitäten oder vielen Nutzer/innen verwenden. </p>
<p>Wir empfehlen die Einstellung zu erhöhen. Empfohlen werden 16M oder mehr. Dies können Sie auf verschiedene Arten machen:</p>
<ol>
<li>Wenn Sie PHP neu kompilieren können, nehmen Sie die Einstellung <i>--enable-memory-limit</i>. Dann kann moodle die Einstellung selber vornehmen.
<li>Wenn Sie Zugriff auf die Datei php.ini haben, können Sie die Einstellung <b>memory_limit</b> selber auf z.B. 16M anpassen. Wenn Sie selber keinen Zugriff haben, fragen Sie den/die Administrator/in, dies für Sie zu tun.
<li>Auf einigen PHP-Servern können Sie eine .htaccess-Datei im moodle-Verzeichnis einrichten. Tragen Sie darin die folgende Zeile ein: <p><blockquote>php_value memory_limit 16M</blockquote></p>
<p>Achtung: auf einigen Servern hindert diese Einstellung <b>alle</b> PHP-Seiten und Sie erhalten Fehlermeldungen. Entfernen Sie dann den Eintrag in der .htaccess-Datei wieder.</p>
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'PHP wurde noch nicht richtig für diese MySQL Erweiterung konfiguriert. Daher kann es nicht mit MySQL kommunizieren. Prüfen Sie bitte die php.ini-Einstellungen oder kompilieren Sie PHP neu.';
$string['pass'] = 'Durchgang';
$string['phpversion'] = 'PHP Version';
$string['phpversionerror'] = 'PHP muss mindestens in der Version 4.1.0 installiert sein.';
$string['phpversionhelp'] = '<p>moodle erwartet PHP mit der Version 4.1.0 oderhöher.</p>
<p>Sie nutzen zur Zeit die Version $a.</p>
<p>Sie müssen Ihre PHP-Verson aktualisieren oder auf einen Rechner wechseln, der eine neuere Version von PHP nutzt.</p>';
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = 'Die Nutzung von moodle im safe mode kann zu Schwierigkeiten führen.';
$string['safemodehelp'] = '<p>moodle kann beim Betrieb im safe mode verschiedene Probleme haben, nicht zuletzt kann es unmöglich sein, neue Dateien zu erzeugen. </p>
<p>Safe Mode ist zumeist nur auf einigen öffentlichen Webservern eingestellt. Suchen Sie sich einen Anbieter, der auf diese Einstellung verzichtet oder bitten Sie Ihren Dienstleister, dass Sie auf einen Server \'umziehen\' können, der diese Einstellung nicht verwendet.</p>
<p>Sie können versuchen, die Installation fortzusetzen. Sie müssen aber später mit Problemen rechnen. </p>';
$string['sessionautostart'] = 'Session Auto Start';
$string['sessionautostarterror'] = 'Diese Option sollte abgestellt sein.';
$string['sessionautostarthelp'] = '<p>Moodle braucht session support und kann nicht funktionieren ohne diese Einstellung.</p>
<p>Sessions sind möglich durch Einstellungen in der Datei php.ini. Bitte suchen Sie nach der Einstellung für session.auto_start </p>';
$string['wwwroot'] = 'URL-Adresse';
$string['wwwrooterror'] = 'Diese URL scheint nicht gültig zu sein. Moodle ist nicht unter dieser Adresse installiert.';

?>
