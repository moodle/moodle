<?PHP // $Id$ 
      // auth.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2005020101)


$string['auth_dbextrafields'] = 'Diese Felder sind optional. Sie können auswählen, einige Moodle Nutzer-Felder mit Informationen des <b>externen Datenbank-Feldes</b> vorauszufüllen, das Sie hier angeben.
<p>Wenn Sie dieses leer lassen, dann werden Standardwerte benutzt.</p><p>Im anderen Fall wird der Nutzer befähigt, alle diese Felder nach der Anmeldung zu bearbeiten.</p>';
$string['auth_dbfieldpass'] = 'Name des Feldes, das das Kennwort enthält';
$string['auth_dbfielduser'] = 'Name des Feldes, das den Nutzernamen enthält';
$string['auth_dbhost'] = 'Der Computer, der die Datenbank bereitstellt';
$string['auth_dbname'] = 'Name der Datenbank selbst';
$string['auth_dbpass'] = 'Das Passwort, das zum Nutzernamen gehört';
$string['auth_dbpasstype'] = 'Spezifizieren Sie das Format, das das Kennwortfeld benutzt. MD5-Verschlüsselung ist nützlich dafür, mit anderen üblichen Netzanwendungen Verbindung aufzunehmen wie z.B. PostNuke';
$string['auth_dbtable'] = 'Name of the table in the database';
$string['auth_dbtitle'] = 'Eine externe Datenbank benutzen';
$string['auth_dbtype'] = 'Der Datenbank-Typ (Siehe <A HREF=../lib/adodb/readme.htm#drivers>ADOdb Anleitung</A> für Einzelheiten)';
$string['auth_dbuser'] = 'Nutzername mit Schreibzugriff auf die Datenbank';
$string['auth_emaildescription'] = 'Email confirmation is the default authentication method.  When the user signs up, choosing their own new username and password, a confirmation email is sent to the user\'s email address.  This email contains a secure link to a page where the user can confirm their account. Future logins just check the username and password against the stored values in the Moodle database.';
$string['auth_emailtitle'] = 'Email-based authentication';
$string['auth_ldap_create_context'] = 'Wenn Sie die Nutzer-Erstellung mit Email-Bestätigung aktivieren, geben Sie die Umgebung an, wo die Nutzer erstellt werden sollen. Diese Umgebung sollte sich von der anderere Nutzer unterscheiden, um Sicherheitsrisiken zu vermeiden. Sie brauchen diese Umgebung nicht zur ldap_context Variable hinzuzufügen, Moodle sucht in dieser Umgebung automatisch nach Nutzern ';
$string['auth_ldap_creators'] = 'Einen Liste von Gruppen, denen es erlaubt ist, neue Kurse zu erstellen. Trennen Sie mehrere Gruppen durch \';\'. Normalerweise etwas wie \'cn=teachers,ou=staff, o=myorg\'';
$string['auth_ldap_host_url'] = 'Geben Sie einen ldap Server in URL-form an wie \'ldap://ldap.myorg.de/\' oder \'ldaps://ldap.myorg.de/\' ';
$string['auth_ldap_memberattribute'] = 'Geben Sie die Mitgliedsoptionen an, wenn Nutzer zu einer Gruppe gehören. Normalereise \'member\'';
$string['auth_ldap_update_userinfo'] = 'on';
$string['auth_manualdescription'] = 'Diese Methode verhindert, dass Nutzer Ihre eigenen Zugänge anlegen können. Jeder Zugang muss manuell vom Administrator selbst eingerichtet werden.';
$string['auth_manualtitle'] = 'Nur manuelle Zugänge';
$string['auth_user_create'] = 'Nutzer-Erstellung aktivieren';
$string['auth_user_creation'] = 'Neue (anonyme) Nutzer können Nutzer-Accounts erstellen außerhalb der Autentisierungsquelle und per Email bestätigen. Sofern Sie dies aktivieren, achten Sie darauf, ebenso modulspezifische optionen für die Modulerstellung zu konfigurieren.';
$string['auth_usernameexists'] = 'Ausgewählter Nutzername existiert bereits. Bitte wählen Sie einen neuen.';
$string['changepassword'] = 'Kennwort-URL ändern';
$string['changepasswordhelp'] = 'Hier können Sie einen Ort angeben, an dem Ihre Benutzer ihren Nutzernamen/Kennwort ändern können, sofern Sie es vergessen haben. Diese Option wird den Nutzern als Schaltfläche auf Anmeldungsseite angeboten.Wenn Sie dieses Feld leer lassen, wird er nicht angezeigt.';
$string['chooseauthmethod'] = 'Choose an authentication method: ';
$string['md5'] = 'MD5-Verschlüsselung';
$string['plaintext'] = 'Reiner Text';

?>
