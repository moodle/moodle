<?PHP // $Id$ 
      // auth.php - created with Moodle 1.3 Beta (2004051100)


$string['auth_dbdescription'] = 'Denna metod använder en extern databastabell för att kontrollera huruvida ett givet användarnamn och lösenord är giltigt.  Om kontot är nytt, så kan information från andra fält också kopieras till Moodle.';
$string['auth_dbextrafields'] = 'Detta fält är valfritt.  Du kan välja att på förhand fylla i några användarfält för Moodle med information från <b>externa databasfält</b> som Du kan specificera här. <p>Om Du lämnar dessa fält tomma, så kommer standardvärden att användas.</p><p>I vilket fall som helst, kommer användaren kunna redigera alla dessa fält efter det att de loggat in.</p>';
$string['auth_dbfieldpass'] = 'Namn på det fält som innehåller lösenord';
$string['auth_dbfielduser'] = 'Namn på det fält som innehåller användarnamn';
$string['auth_dbhost'] = 'Den dator (värd) som används för databasservern.';
$string['auth_dbname'] = 'Namnet på själva databasen ';
$string['auth_dbpass'] = 'Lösenord som matchar ovanstående användarnamn';
$string['auth_dbpasstype'] = 'Specificera formatet på det fält som lösenordet ska ligga í. MD-kryptering går att använda om Du vill koppla upp Dig mot andra vanliga webbapplikationer som PostNuke.';
$string['auth_dbtable'] = 'Namn på tabellen i databasen';
$string['auth_dbtitle'] = 'Använd en extern databas';
$string['auth_dbtype'] = 'Databastyp (se <A HREF=../lib/adodb/readme.htm#drivers>ADOdb dokumentation</A> för detaljer)';
$string['auth_dbuser'] = 'Användarnamn med läsbehörighet till databasen';
$string['auth_emaildescription'] = 'E-postbekräftelse är standardvalet som autenticeringsmetod.  När användaren registrerar sig, väljer eget nytt användarnamn och lösenord, kommer en bekräftelse via e-post sändas till användarens e-postadress.  Detta e-postbrev innehåller en säker länk till en sida där användaren kan bekräfta sitt konto. Framtida inlogging kontrollerar bara användarnamn och lösenord mot de lagrade värdena i Moodles databas.';
$string['auth_emailtitle'] = 'E-postbaserad autenticering';
$string['auth_imapdescription'] = 'Denna metod använder en IMAP-server för att kontrollera huruvida ett givet användarnamn och lösenord är giltigt.';
$string['auth_imaphost'] = 'IMAP-serverns adress. Använd IP-nummer, inte DNS- namn.';
$string['auth_imapport'] = 'IMAP-serverns portnummer. Vanligtvis är detta 143 eller 993.';
$string['auth_imaptitle'] = 'Använd en IMAP-server';
$string['auth_imaptype'] = 'IMAP servertyp.  IMAP-servrar kan ha olika typer av autenticeringar och förhandlingar.';
$string['auth_ldap_bind_dn'] = 'Om Du vill bruka \'bind\'-användare för att söka användare, så ska Du specificera det här. Något som \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Lösenord för \'bind\'-användare.';
$string['auth_ldap_contexts'] = 'Lista av kontexter där användarna finns med.  Separera olika kontexter med \';\'.  Till exempel: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Om Du aktiverar \'Skapa användare\' med e-postbekräftelse så ska Du specifiera den kontext där användare skapas. Denna kontext bör vara en annan än den vanliga för att undvika säkerhetsrisker. Du behöver inte lägga till denna kontext till variabeln \'ldap_context\'. Moodle letar automatiskt efter användare från den här kontexten.';
$string['auth_ldap_creators'] = 'Lista av grupper som har behörighet att skapa nya kurser. Skilj på grupperna med \';\'. Vanligtvis något liknande \'ch=utbildare, ou=personal, o=minOrg\'';
$string['auth_ldap_host_url'] = 'Specificera en LDAP-värd i URL-form som \'ldap://ldap.myorg.com/\' eller \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_memberattribute'] = 'Specificera en medlems egenskaper när användare tillhör en grupp. Vanligtvis \'medlem\'';
$string['auth_ldap_search_sub'] = 'Sätt in ett värde <> 0 om Du vill söka användare från subkontexter.';
$string['auth_ldap_update_userinfo'] = 'Uppdatera användarinformation (förnamn, efternamn, adress..) från LDAP till Moodle.  Se /auth/ldap/attr_mappings.php för mappnings- information';
$string['auth_ldap_user_attribute'] = 'Attributet som används för namn/sökning av användare.  Vanligtvis \'cn\'.';
$string['auth_ldap_version'] = 'Detta är den version av LDAP-protokollet som Din server använder.';
$string['auth_ldapdescription'] = 'Denna metod ger autenticering mot en extern LDAP-server. Om det givna användarnamnet och lösenordet är giltiga skapar Moodle en plats för en ny användare i databasen. Denna modul kan läsa användarattribut från LDAP och fylla i på förhand                                 önskade fält i Moodle. För följande login är endast användarnamn och lösenord kontrollerade.';
$string['auth_ldapextrafields'] = 'Dessa fält är valfria.  Du kan välja att på förhand fylla i  några användarfält för Moodle med information från <B>LDAP-fält</B> som Du kan specificera här. <P>Om Du lämnar dessa fält tomma, så kommer inget att föras över från LDAP och standardvärden för Moodle kommer att användas istället.<P>I vilket fall som helst, kommer användaren kunna redigera alla dessa fält efter det att de loggat in.';
$string['auth_ldaptitle'] = 'Använd en LDAP-server';
$string['auth_manualdescription'] = 'Den här metoden gör det omöjligt för användare att skapa sina egna konton. Alla konton måste skapas manuellt av administratören.';
$string['auth_manualtitle'] = 'Endast manuellt skapade konton';
$string['auth_multiplehosts'] = 'Du kan ange flera värdar(t ex host1.com;host2.com;host3.com)  ';
$string['auth_nntpdescription'] = 'Denna metod använder en NNTP-server för att kontrollera huruvida ett givet användarnamn och lösenord är giltiga.';
$string['auth_nntphost'] = 'NNTP-serverns adress.  Använd IP-nummer, inte DNS-namn.';
$string['auth_nntpport'] = 'Serverport (119 är den vanligaste)';
$string['auth_nntptitle'] = 'Använd en NNTP-server';
$string['auth_nonedescription'] = 'Användare kan logga in och skapa giltiga konton omedelbart, utan autenticering mot extern server och heller ingen bekräftelse via e-post.  Var försiktig med användning av detta val - tänk på säkerheten och de administrativa problem som detta kan orsaka.';
$string['auth_nonetitle'] = 'Ingen autenticering';
$string['auth_pop3description'] = 'Denna metod använder en POP3 server för att kontrollera huruvida ett givet användarnamn och lösenord är giltiga.';
$string['auth_pop3host'] = 'POP3-serveradressen. Använd IP-nummer, inte DNS-namn.';
$string['auth_pop3port'] = 'Serverport (110 är den vanligaste)';
$string['auth_pop3title'] = 'Använd en POP3-server';
$string['auth_pop3type'] = 'Servertyp. Om Din server använder certifikat som säkerhet, välj pop3cert.';
$string['auth_user_create'] = 'Aktivera ';
$string['auth_user_creation'] = 'Nya (anonyma) användare kan utnyttja en extern källa för autenticering och skapa användarkonton som bekräftas med e-post. Om Du aktiverar detta får Du inte glömma att också konfigurera de modulspecifika valmöjligheterna som användare ska kunna skapa.';
$string['auth_usernameexists'] = 'Det valda användarnamnet finns redan. Du måste välja ett annat.';
$string['authenticationoptions'] = 'Autenticering tillval';
$string['authinstructions'] = 'Här kan Du ge instruktioner för Dina användare, så att de vet vilket användarnamn och lösenord de bör använda.  Texten Du skriver in här kommer att visas på loginsidan.  Om Du lämnar detta tomt så kommer inga instruktioner att visas.';
$string['changepassword'] = 'Ändra lösenord URL';
$string['changepasswordhelp'] = 'Här kan Du specificera en plats där Dina användare kan återställa eller ändra sina användarnamn/lösenord om de har glömt. Detta kommer att visas för användarna som en knapp på loginsidan och på deras användarsidor. Om Du lämnar detta tomt kommer inte knappen att visas.';
$string['chooseauthmethod'] = 'Välj en autentiseringsmetod: ';
$string['guestloginbutton'] = 'Knapp för gästlogin';
$string['instructions'] = 'Instruktioner';
$string['md5'] = 'MD5-kryptering';
$string['plaintext'] = 'Ren text';
$string['showguestlogin'] = 'Du kan gömma eller visa knappen för gästlogin på loginsidan.';

?>
