<?PHP // $Id$ 
      // auth.php - created with Moodle 1.0.6.4 beta (2002112001)


$string['auth_dbdescription'] = "Denna metod använder en extern databastabell för att kontrollera hurvida ett givet användarnamn och lösenord är giltigt.  Om kontot är nytt, så kan information från andra fält också kopieras till Moodle.";
$string['auth_dbextrafields'] = "Detta fält är valfritt.  Du kan välja att fylla i på förhand några användarfält för Moodle med information från <B>externa databas fält</B> som du kan specificera här. <P>Om du lämnar dessa fält tomma, så kommer standardvärden att användas.<P>I vilket fall som helst, kommer användaren kunna redigera alla dessa fält efter det att de loggat in.";
$string['auth_dbfieldpass'] = "Namn hos detta fält som innehåller lösenord";
$string['auth_dbfielduser'] = "Namn hos detta fält som innehåller användarnamn";
$string['auth_dbhost'] = "Datorn (värd) som används för databas-servern.";
$string['auth_dbname'] = "Namn på databasen själv";
$string['auth_dbpass'] = "Lösenord som matchar ovanstående användarnamn";
$string['auth_dbtable'] = "Namn på tabellen i databasen";
$string['auth_dbtitle'] = "Använd en extern databas";
$string['auth_dbtype'] = "Databastyp (se <A HREF=../lib/adodb/readme.htm#drivers>ADOdb dokumentation</A> för detaljer)";
$string['auth_dbuser'] = "Använarnamn med läsbehörighet till databasen";
$string['auth_emaildescription'] = "Epostbekräftelse är standardvalet som autenticeringsmetod.  När användaren registrerar sig, väljer eget nytt användarnamn och lösenord, kommer en bekräftelse via epost sändas till användarens epostadress.  Detta epostbrev innehåller en säker länk till en sida där användaren kan bekräfta sitt konto. Framtida inlogging kontrollerar bara användarnamn och lösenord mot de lagrade värdena i Moodles databas.";
$string['auth_emailtitle'] = "Epostbaserad autenticering";
$string['auth_imapdescription'] = "Denna metod använder en IMAP-server för att kontrollera hurvida ett givet användarnamn och lösenord är giltigt.";
$string['auth_imaphost'] = "IMAP-serverns adress. Använd IP-nummer, inte DNS namn.";
$string['auth_imapport'] = "IMAP-serverns portnummer. Vanligtvis är detta 143 eller 993.";
$string['auth_imaptitle'] = "Använd en IMAP server";
$string['auth_imaptype'] = "IMAP servertyp.  IMAP servrar kan ha olika typer av autenticeringar och förhandlingar.";
$string['instructions'] = "Instruktioner";
$string['auth_ldap_bind_dn'] = "Om du vill bruka bind-användare för att söka användare, specificera det här. Något som 'cn=ldapuser,ou=public,o=org'";
$string['auth_ldap_bind_pw'] = "Lösenord för bind-användare.";
$string['auth_ldap_contexts'] = "Lista av kontexter där användaren är lokaliserade.  Separera olika kontexter med ';'.  Till exempel: 'ou=users,o=org; ou=others,o=org'";
$string['auth_ldap_host_url'] = "Specificera en LDAP-värd i URL-form som 'ldap://ldap.myorg.com/' eller 'ldaps://ldap.myorg.com/' ";
$string['auth_ldap_search_sub'] = "Sätt in ett värde &lt;&gt; 0 om du vill söka användare från subkontexter.";
$string['auth_ldap_update_userinfo'] = "Uppdatera användarinformation (förnamn, efternamn, adress..) från LDAP till Moodle.  Se /auth/ldap/attr_mappings.php för mappnings information";
$string['auth_ldap_user_attribute'] = "Attributet som används för namn/sökning av användare.  Vanligtvis 'cn'.";
$string['auth_ldapdescription'] = "Denna metod ger autenticering mot en extern LDAP-server.
                                   Om det givna användarnamnet och lösenordet är giltiga skapar
                                   Moodle en plats för en ny användare i databasen.
                                   Denna modul kan läsa användarattribut från LDAP och fylla i på förhand 
                                   önskade fält i Moodle.  För följande login är endast användarnamn och 
                                   lösenord kontrollerade.";
$string['auth_ldapextrafields'] = "Dessa fält är valfria.  Du kan välja att fylla i på förhand några användarfält för Moodle med information från <B>LDAP-fält</B> som du kan specificera här. <P>Om du lämnar dessa fält tomma, så kommer inget att föras över från LDAP och standardvärden för Moodle kommer att användas istället.<P>I vilket fall som helst, kommer användaren kunna redigera alla dessa fält efter det att de loggat in.";
$string['auth_ldaptitle'] = "Använd en LDAP-server";
$string['auth_nntpdescription'] = "Denna metod använder en NNTP-server för att kontrollera hurvida ett givet användarnamn och lösenord är giltiga.";
$string['auth_nntphost'] = "NNTP-serverns adress.  Använd IP-nummer, inte DNS namn.";
$string['auth_nntpport'] = "Serverport (119 är den vanligaste)";
$string['auth_nntptitle'] = "Använd en NNTP-server";
$string['auth_nonedescription'] = "Användare kan logga in och skapa giltiga konton omedelbart, utan autenticering mot extern server och heller ingen bekräftelse via epost.  Var försiktig med användning av detta val - tänk på säkerheten och administrativa problem detta kan orsaka.";
$string['auth_nonetitle'] = "Ingen autenticering";
$string['auth_pop3description'] = "Denna metod använder en POP3 server för att kontrollera hurvida ett givet användarnamn och lösenord är giltiga.";
$string['auth_pop3host'] = "POP3-serveradressen. Använd IP-nummer, inte DNS namn.";
$string['auth_pop3port'] = "Serverport (110 är den vanligaste)";
$string['auth_pop3title'] = "Använd en POP3-server";
$string['auth_pop3type'] = "Servertyp. Om din server använder certifikat som säkerhet, välj pop3cert.";
$string['authenticationoptions'] = "Autenticering tillval";
$string['authinstructions'] = "Här kan du ge instruktioner för dina användare, så att de vet vilket användarnamn och lösenord de bör använda.  Texten du skriver in här kommer att visas på loginsidan.  Om du lämnar detta tomt så kommer inga instruktioner att visas.";
$string['changepassword'] = "Ändra lösenord URL";
$string['changepasswordhelp'] = "Här kan du specificera en plats där dina användare kan återställa eller ändra sina användarnamn/lösenord om de har glömt.  Detta kommer att visas för användarna som en knapp på loginsidan och deras användarsidor.  Om du lämnar detta tomt kommer inte knappen att visas.";
$string['chooseauthmethod'] = "Välj en autentiseringsmetod: ";
$string['guestloginbutton'] = "Knapp för gästlogin";
$string['showguestlogin'] = "Du kan gömma eller visa knappen för gästlogin på loginsidan.";

?>
