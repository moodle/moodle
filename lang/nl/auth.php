<?PHP // $Id$ 
      // auth.php - created with Moodle 1.0.7 (2002121000)


$string['auth_dbdescription'] = "Deze methode gebruikt een externe database om te controleren of een bepaalde gebruikersnaam en een bepaald wachtwoord geldig zijn. Als de account nieuw is dan kan informatie vanuit andere velden ook naar Moodle worden gekopieerd.";
$string['auth_dbextrafields'] = "Deze velden zijn niet verplicht. Je kunt ervoor kiezen om sommige Moodle gebruikersvelden in te vullen met informatie uit de <B>externe database velden</B> die je hier aangeeft. <P>Als je deze niet invult zullen standaardwaarden worden gebruikt. In beide gevallen kan de gebruiker alle velden wijzigen zodra hij/zij is ingelogd.";
$string['auth_dbfieldpass'] = "Naam van het veld dat de wachtwoorden bevat  ";
$string['auth_dbfielduser'] = "Naam van het veld dat de gebruikersnamen bevat  ";
$string['auth_dbhost'] = "De computer die de database server host";
$string['auth_dbname'] = "Naam van de database zelf";
$string['auth_dbpass'] = "Wachtwoord dat bij de bovengenoemde gebruikersnaam past";
$string['auth_dbtable'] = "Naam  van  de  tabel in de database";
$string['auth_dbtitle'] = "Gebruik een externe database";
$string['auth_dbtype'] = "Het type database (Bekijk <A HREF=../lib/adodb/readme.htm#drivers>ADOdb documentatie</A> voor meer informatie)";
$string['auth_dbuser'] = "Gebruikersnaam met read access tot de database";
$string['auth_emaildescription'] = "E-mail bevestiging is standaard ingesteld als authenticatie methode. Op het moment dat de gebruiker zich aanmeldt en daarbij een nieuwe gebruikersnaam en wachtwoord kiest wordt er een bevestigings e-mail gestuurd naar het e-mail adres van de gebruiker. In deze e-mail staat een veilige link naar een pagina waar de gebruiker zijn account kan bevestigen. In alle latere logins worden de gebruikersnaam en het wachtwoord alleen maar vergeleken met de bewaarde waarden in de Moodle database.";
$string['auth_emailtitle'] = "Op e-mail gebaseerde authenticatie";
$string['auth_imapdescription'] = "Deze methode gebruikt een IMAP server om te controleren of een bepaalde gebruikersnaam en een bepaald wachtwoord geldig zijn.
";
$string['auth_imaphost'] = "Het adres van de IMAP server. Gebruik een IP adres, geen DNS naam.  
";
$string['auth_imapport'] = "Het nummer van de poort van de IMAP server. Meestal is dit 143 of 993.
";
$string['auth_imaptitle'] = "Gebruik een IMAP  server ";
$string['auth_imaptype'] = "Het type van de IMAP server. IMAP servers kunnen verschillende manieren van authenticatie en onderhandeling hebben.
";
$string['auth_ldap_bind_dn'] = "Als je 'bind-user' wilt gebruiken om gebruikers te zoeken, dan moet je dat hier aangeven. Bijvoorbeeld 'cn=ldapuser,ou=public,o=org'";
$string['auth_ldap_bind_pw'] = "Wachtwoord voor de 'bind-user'";
$string['auth_ldap_contexts'] = "Lijst met contexten waar de gebruikers gelocaliseerd zijn. Scheid verschillende contexten met ';'. Bijvoorbeeld: 'ou=users,o=org; ou=others,o=org'";
$string['auth_ldap_host_url'] = "Geef de LDAP host in de vorm van een URL zoals bijvoorbeeld: 'ldap://ldap.myorg.com/' of 'ldaps://ldap.myorg.com/'  Com/'or 'ldaps://ldap.myorg.com/' ";
$string['auth_ldap_search_sub'] = "Zet waarde &lt;&gt; 0 als je gebruikers wilt kunnen zoeken in subcontexten.";
$string['auth_ldap_update_userinfo'] = "Werk de gebruikersinformatie bij (voornaam, achternaam, adres, ..) van LDAP naar Moodle. Bekijk /auth/ldap/attr_mappings.php om informatie te vinden over de 'mapping'.";
$string['auth_ldap_user_attribute'] = "Het attribuut dat wordt gebruikt om gebruikers te benoemen of zoeken. Meestal 'cn'.";
$string['auth_ldapdescription'] = "Deze methode levert authenticatie door middel van een externe LDAP server.  
Als de gebruikersnaam en wachtwoord geldig zijn maakt Moodle een nieuwe gebruiker aan in zijn database. Deze module kan gebruikerseigenschappen vanuit LDAP lezen en bepaalde velden in Moodle alvast invullen. Bij latere logins worden alleen de gebruikersnaam en het wachtwoord gecontroleerd.";
$string['auth_ldapextrafields'] = "Deze velden zijn niet verplicht. Je kunt ervoor kiezen om sommige Moodle gebruikersvelden van te voren in te vullen met informatie uit de <B>LDAP velden</B> die je hier aan kunt geven. <P>Als je deze velden leeg laat zal er niets vanuit LDAP worden overgebracht en worden de standaardwaarden van Moodle gebruikt.<P> In beide gevallen kan de gebruiker al deze velden wijzigingen zodra hij/zij ingelogd is.";
$string['auth_ldaptitle'] = "Gebruik een LDAP server";
$string['auth_nntpdescription'] = "Deze methode gebruikt een NTTP server om te controleren of een gebruikersnaam en wachtwoord geldig zijn.";
$string['auth_nntphost'] = "Het adres van de NNTP server. Gebruik het IP adres, niet een DNS naam.";
$string['auth_nntpport'] = "De poort van de server (meestal is dat 119)";
$string['auth_nntptitle'] = "Gebruik een  NNTP server";
$string['auth_nonedescription'] = "De gebruikers kunnen meteen inloggen en een geldige account aanmaken, zonder authenticatie door middel van een externe server en zonder bevestiging via e-mail. Wees voorzichtig met het gebruiken van deze mogelijkheid - denk aan de beveiligings- en beheerproblemen die hieruit zouden kunnen ontstaan.
";
$string['auth_nonetitle'] = "Geen authenticatie";
$string['auth_pop3description'] = "Deze methode gebruikt een POP3 server om te controleren of een gebruikersnaam en wachtwoord geldig zijn.
";
$string['auth_pop3host'] = "Het adres van de POP3 server. Gebruik het IP adres, niet een DNS naam.";
$string['auth_pop3port'] = "De poort van de server (meestal is dat 110)";
$string['auth_pop3title'] = "Gebruik een  POP3 server";
$string['auth_pop3type'] = "Het type van de server. Als jouw server gebruikt maakt van beveiliging door middel van een certificaat, kies pop3cert.";
$string['authenticationoptions'] = "Opties voor authenticatie";
$string['authinstructions'] = "Hier kun je instructies geven aan de gebruikers, zodat ze weten welke gebruikersnaam en welk wachtwoord ze moeten gebruiken. De tekst die je hier invult komt te staan op de login pagina. Als je dit leeg laat zullen er geen instructies worden weergegeven.";
$string['changepassword'] = "URL voor het veranderen van het wachtwoord";
$string['changepasswordhelp'] = "Hier kun je een locatie aangeven waar gebruikers hun gebruikersnaam/wachtwoord kunnen terugkrijgen als ze deze vergeten zijn. Dit zal aan de gebruikers worden gegeven als een knop op de login pagina en op hun gebruikerspagina. Als je dit leeg laat zal de knop niet verschijnen.
";
$string['chooseauthmethod'] = "Kies een methode van authenticatie:";
$string['guestloginbutton'] = "Knop voor login als gast";
$string['instructions'] = "Instructies";
$string['showguestlogin'] = "Je kunt de knop voor login als gast verbergen of laten zien op de login pagina.
";

?>
