<?PHP // $Id$ 
      // auth.php - created with Moodle 1.0.8 dev (2002122301)


$string['auth_dbdescription'] = "This method uses an external database table to check whether a given username and password is valid.  If the account is a new one, then information from other fields may also be copied across into Moodle.";
$string['auth_dbextrafields'] = "Diese Felder sind optional. Sie können auswählen, einige Moodle Nutzer-Felder mit Informationen des <b>externen Datenbank-Feldes</b> vorauszufüllen, das Sie hier angeben.
<p>Wenn Sie dieses leer lassen, dann werden Standardwerte benutzt.<P>Im anderen Fall wird der Nutzer befähigt, alle diese Felder nach der Anmeldung zu bearbeiten.";
$string['auth_dbfieldpass'] = "Name des Feldes, das das Kennwort enthält";
$string['auth_dbfielduser'] = "Name des Feldes, das den Nutzernamen enthält";
$string['auth_dbhost'] = "Der Computer, der die Datenbank bereitstellt";
$string['auth_dbname'] = "Name der Datenbank selbst";
$string['auth_dbpass'] = "Das Passwort, das zum Nutzernamen gehört";
$string['auth_dbpasstype'] = "Spezifizieren Sie das Format, das das Kennwortfeld benutzt. MD5-Verschlüsselung ist nützlich dafür, mit anderen üblichen Netzanwendungen Verbindung aufzunehmen wie z.B. PostNuke";
$string['auth_dbtable'] = "Name of the table in the database";
$string['auth_dbtitle'] = "Eine externe Datenbank benutzen";
$string['auth_dbtype'] = "Der Datenbank-Typ (Siehe <A HREF=../lib/adodb/readme.htm#drivers>ADOdb Anleitung</A> für Einzelheiten)";
$string['auth_dbuser'] = "Nutzername mit Schreibzugriff auf die Datenbank";
$string['auth_emaildescription'] = "Email confirmation is the default authentication method.  When the user signs up, choosing their own new username and password, a confirmation email is sent to the user's email address.  This email contains a secure link to a page where the user can confirm their account. Future logins just check the username and password against the stored values in the Moodle database.";
$string['auth_emailtitle'] = "Email-based authentication";
$string['auth_imapdescription'] = "This method uses an IMAP server to check whether a given username and password is valid.";
$string['auth_imaphost'] = "The IMAP server address. Use the IP number, not DNS name.";
$string['auth_imapport'] = "IMAP server port number. Usually this is 143 or 993.";
$string['auth_imaptitle'] = "Use an IMAP server";
$string['auth_imaptype'] = "The IMAP server type.  IMAP servers can have different types of authentication and negotiation.";
$string['auth_ldap_bind_dn'] = "If you want to use bind-user to search users, specify it here. Someting like 'cn=ldapuser,ou=public,o=org'";
$string['auth_ldap_bind_pw'] = "Password for bind-user.";
$string['auth_ldap_contexts'] = "List of contexts where users are located. Separate different contexts with ';'. For example: 'ou=users,o=org; ou=others,o=org'";
$string['auth_ldap_create_context'] = "Wenn Sie die Nutzer-Erstellung mit Email-Bestätigung aktivieren, geben Sie die Umgebung an, wo die Nutzer erstellt werden sollen. Diese Umgebung sollte sich von der anderere Nutzer unterscheiden, um Sicherheitsrisiken zu vermeiden. Sie brauchen diese Umgebung nicht zur ldap_context Variable hinzuzufügen, Moodle sucht in dieser Umgebung automatisch nach Nutzern ";
$string['auth_ldap_creators'] = "Einen Liste von Gruppen, denen es erlaubt ist, neue Kurse zu erstellen. Trennen Sie mehrere Gruppen durch ';'. Normalerweise etwas wie 'cn=teachers,ou=staff, o=myorg'";
$string['auth_ldap_host_url'] = "Geben Sie einen ldap Server in URL-form an wie 'ldap://ldap.myorg.de/' oder 'ldaps://ldap.myorg.de/' ";
$string['auth_ldap_memberattribute'] = "Geben Sie die Mitgliedsoptionen an, wenn Nutzer zu einer Gruppe gehören. Normalereise 'member'";
$string['auth_ldap_search_sub'] = "Put value &lt;&gt; 0 if  you like to search users from subcontexts.";
$string['auth_ldap_update_userinfo'] = "Update user information (firstname, lastname, address..) from LDAP to Moodle. Look at /auth/ldap/attr_mappings.php for mapping information";
$string['auth_ldap_user_attribute'] = "The attribute used to name/search users. Usually 'cn'.";
$string['auth_ldapdescription'] = "This method provides authentication against an external LDAP server.
                                  If the given username and password are valid, Moodle creates a new user 
                                  entry in its database. This module can read user attributes from LDAP and prefill 
                                  wanted fields in Moodle.  For following logins only the username and 
                                  password are checked.";
$string['auth_ldapextrafields'] = "These fields are optional.  You can choose to pre-fill some Moodle user fields with information from the <B>LDAP fields</B> that you specify here. <P>If you leave these fields blank, then nothing will be transferred from LDAP and Moodle defaults will be used instead.<P>In either case, the user will be able to edit all of these fields after they log in.";
$string['auth_ldaptitle'] = "Use an LDAP server";
$string['auth_manualdescription'] = "Diese Methode verhindert, dass Nutzer Ihre eigenen Zugänge anlegen können. Jeder Zugang muss manuell vom Administrator selbst eingerichtet werden.";
$string['auth_manualtitle'] = "Nur manuelle Zugänge";
$string['auth_nntpdescription'] = "This method uses an NNTP server to check whether a given username and password is valid.";
$string['auth_nntphost'] = "The NNTP server address. Use the IP number, not DNS name.";
$string['auth_nntpport'] = "Server port (119 is the most common)";
$string['auth_nntptitle'] = "Use an NNTP server";
$string['auth_nonedescription'] = "Users can sign in and create valid accounts immediately, with no authentication against an external server and no confirmation via email.  Be careful using this option - think of the security and administration problems this could cause.";
$string['auth_nonetitle'] = "No authentication";
$string['auth_pop3description'] = "This method uses a POP3 server to check whether a given username and password is valid.";
$string['auth_pop3host'] = "The POP3 server address. Use the IP number, not DNS name.";
$string['auth_pop3port'] = "Server port (110 is the most common)";
$string['auth_pop3title'] = "Use a POP3 server";
$string['auth_pop3type'] = "Server type. If your server uses certificate security, choose pop3cert.";
$string['auth_user_create'] = "Nutzer-Erstellung aktivieren";
$string['auth_user_creation'] = "Neue (anonyme) Nutzer können Nutzer-Accounts erstellen außerhalb der Autentisierungsquelle und per Email bestätigen. Sofern Sie dies aktivieren, achten Sie darauf, ebenso modulspezifische optionen für die Modulerstellung zu konfigurieren.";
$string['auth_usernameexists'] = "Ausgewählter Nutzername existiert bereits. Bitte wählen Sie einen neuen.";
$string['authenticationoptions'] = "Authentication options";
$string['authinstructions'] = "Here you can provide instructions for your users, so they know which username and password they should be using.  The text you enter here will appear on the login page.  If you leave this blank then no instructions will be printed.";
$string['changepassword'] = "Kennwort-URL ändern";
$string['changepasswordhelp'] = "Hier können Sie einen Ort angeben, an dem Ihre Benutzer ihren Nutzernamen/Kennwort ändern können, sofern Sie es vergessen haben. Diese Option wird den Nutzern als Schaltfläche auf Anmeldungsseite angeboten.Wenn Sie dieses Feld leer lassen, wird er nicht angezeigt.";
$string['chooseauthmethod'] = "Choose an authentication method: ";
$string['guestloginbutton'] = "Guest login button";
$string['instructions'] = "Instructions";
$string['md5'] = "MD5-Verschlüsselung";
$string['plaintext'] = "Reiner Text";
$string['showguestlogin'] = "You can hide or show the guest login button on the login page.";

?>
