<?PHP // $Id$ 
      // auth.php - created with Moodle 1.3 development (2004040500)


$string['auth_dbdescription'] = 'Deze methode gebruikt een externe database om te controleren of een bepaalde gebruikersnaam en een bepaald wachtwoord geldig zijn. Als de account nieuw is dan kan informatie vanuit andere velden ook naar Moodle worden gekopieerd.';
$string['auth_dbextrafields'] = 'Deze velden zijn niet verplicht. Je kunt ervoor kiezen om sommige Moodle gebruikersvelden in te vullen met informatie uit de <B>externe database velden</B> die je hier aangeeft. <P>Als je deze niet invult zullen standaardwaarden worden gebruikt. In beide gevallen kan de gebruiker alle velden wijzigen zodra hij/zij is ingelogd.';
$string['auth_dbfieldpass'] = 'Naam van het veld dat de wachtwoorden bevat  ';
$string['auth_dbfielduser'] = 'Naam van het veld dat de gebruikersnamen bevat  ';
$string['auth_dbhost'] = 'De computer die de database server host';
$string['auth_dbname'] = 'Naam van de database zelf';
$string['auth_dbpass'] = 'Wachtwoord dat bij de bovengenoemde gebruikersnaam past';
$string['auth_dbpasstype'] = 'Geef hier aan welk format het wachtwoordveld gebruikt. MD5 encryptie is handig om een verbinding te maken naar andere veel voorkomende web applicaties zoals PostNuke';
$string['auth_dbtable'] = 'Naam  van  de  tabel in de database';
$string['auth_dbtitle'] = 'Gebruik een externe database';
$string['auth_dbtype'] = 'Het type database (Bekijk <A HREF=../lib/adodb/readme.htm#drivers>ADOdb documentatie</A> voor meer informatie)';
$string['auth_dbuser'] = 'Gebruikersnaam met read access tot de database';
$string['auth_emaildescription'] = 'E-mail bevestiging is standaard ingesteld als authenticatie methode. Op het moment dat de gebruiker zich aanmeldt en daarbij een nieuwe gebruikersnaam en wachtwoord kiest wordt er een bevestigings e-mail gestuurd naar het e-mail adres van de gebruiker. In deze e-mail staat een veilige link naar een pagina waar de gebruiker zijn account kan bevestigen. In alle latere logins worden de gebruikersnaam en het wachtwoord alleen maar vergeleken met de bewaarde waarden in de Moodle database.';
$string['auth_emailtitle'] = 'Op e-mail gebaseerde authenticatie';
$string['auth_imapdescription'] = 'Deze methode gebruikt een IMAP server om te controleren of een bepaalde gebruikersnaam en een bepaald wachtwoord geldig zijn.
';
$string['auth_imaphost'] = 'Het adres van de IMAP server. Gebruik een IP adres, geen DNS naam.  
';
$string['auth_imapport'] = 'Het nummer van de poort van de IMAP server. Meestal is dit 143 of 993.
';
$string['auth_imaptitle'] = 'Gebruik een IMAP  server ';
$string['auth_imaptype'] = 'Het type van de IMAP server. IMAP servers kunnen verschillende manieren van authenticatie en onderhandeling hebben.
';
$string['auth_ldap_bind_dn'] = 'Als je \'bind-user\' wilt gebruiken om gebruikers te zoeken, dan moet je dat hier aangeven. Bijvoorbeeld \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Wachtwoord voor de \'bind-user\'';
$string['auth_ldap_contexts'] = 'Lijst met contexten waar de gebruikers gelocaliseerd zijn. Scheid verschillende contexten met \';\'. Bijvoorbeeld: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Als je het aanmaken van gebruikers met e-mail bevestiging aanzet, moet je de context aangeven waarin gebruikers worden aangemaakt. Deze context moet verschillen van andere contexten om beveiligingsproblemen te vermijden. Deze context hoef je niet toe te voegen aan ldap_context_variable. Moodle zoekt automatisch de gebruikers uit deze context.';
$string['auth_ldap_creators'] = 'Lijst met groepen gebruikers. De leden van de groepen mogen nieuwe vakken aanmaken. Scheid meerdere groepen met \';\'. Meestal iets als \'cn=docenten,ou=medewerkers,o=mijnorganisatie\'';
$string['auth_ldap_host_url'] = 'Geef de LDAP host in de vorm van een URL zoals bijvoorbeeld: \'ldap://ldap.myorg.com/\' of \'ldaps://ldap.myorg.com/\'  Com/\'or \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_memberattribute'] = 'Geef gebruiker lid attribuut, voor als gebruikers tot een groep behoren. Meestal \'member\'';
$string['auth_ldap_search_sub'] = 'Zet waarde &lt;&gt; 0 als je gebruikers wilt kunnen zoeken in subcontexten.';
$string['auth_ldap_update_userinfo'] = 'Werk de gebruikersinformatie bij (voornaam, achternaam, adres, ..) van LDAP naar Moodle. Bekijk /auth/ldap/attr_mappings.php om informatie te vinden over de \'mapping\'.';
$string['auth_ldap_user_attribute'] = 'Het attribuut dat wordt gebruikt om gebruikers te benoemen of zoeken. Meestal \'cn\'.';
$string['auth_ldap_version'] = 'De versie van het LDAP protocol die jouw server gebruikt.';
$string['auth_ldapdescription'] = 'Deze methode levert authenticatie door middel van een externe LDAP server.  
Als de gebruikersnaam en wachtwoord geldig zijn maakt Moodle een nieuwe gebruiker aan in zijn database. Deze module kan gebruikerseigenschappen vanuit LDAP lezen en bepaalde velden in Moodle alvast invullen. Bij latere logins worden alleen de gebruikersnaam en het wachtwoord gecontroleerd.';
$string['auth_ldapextrafields'] = 'Deze velden zijn niet verplicht. Je kunt ervoor kiezen om sommige Moodle gebruikersvelden van te voren in te vullen met informatie uit de <B>LDAP velden</B> die je hier aan kunt geven. <P>Als je deze velden leeg laat zal er niets vanuit LDAP worden overgebracht en worden de standaardwaarden van Moodle gebruikt.<P> In beide gevallen kan de gebruiker al deze velden wijzigingen zodra hij/zij ingelogd is.';
$string['auth_ldaptitle'] = 'Gebruik een LDAP server';
$string['auth_manualdescription'] = 'Deze methode verwijdert alle mogelijkheden voor gebruikers om hun eigen accounts aan te maken. Alle accounts moeten handmatig worden aangemaakt door de beheerder.';
$string['auth_manualtitle'] = 'Alleen handmatige accounts';
$string['auth_multiplehosts'] = 'Je kunt meerdere hosts ingeven (bijv. host1.com;host2.com;host3.com)';
$string['auth_nntpdescription'] = 'Deze methode gebruikt een NTTP server om te controleren of een gebruikersnaam en wachtwoord geldig zijn.';
$string['auth_nntphost'] = 'Het adres van de NNTP server. Gebruik het IP adres, niet een DNS naam.';
$string['auth_nntpport'] = 'De poort van de server (meestal is dat 119)';
$string['auth_nntptitle'] = 'Gebruik een  NNTP server';
$string['auth_nonedescription'] = 'De gebruikers kunnen meteen inloggen en een geldige account aanmaken, zonder authenticatie door middel van een externe server en zonder bevestiging via e-mail. Wees voorzichtig met het gebruiken van deze mogelijkheid - denk aan de beveiligings- en beheerproblemen die hieruit zouden kunnen ontstaan.
';
$string['auth_nonetitle'] = 'Geen authenticatie';
$string['auth_pop3description'] = 'Deze methode gebruikt een POP3 server om te controleren of een gebruikersnaam en wachtwoord geldig zijn.
';
$string['auth_pop3host'] = 'Het adres van de POP3 server. Gebruik het IP adres, niet een DNS naam.';
$string['auth_pop3port'] = 'De poort van de server (meestal is dat 110)';
$string['auth_pop3title'] = 'Gebruik een  POP3 server';
$string['auth_pop3type'] = 'Het type van de server. Als jouw server gebruikt maakt van beveiliging door middel van een certificaat, kies pop3cert.';
$string['auth_user_create'] = 'Zet aanmaken gebruikers aan';
$string['auth_user_creation'] = 'Nieuwe (anonieme) gebruikers kunnen gebruikersaccounts aanmaken op de externe authenticatiebron en bevestigen via e-mail. Als je dit aanzet, vergeet dan niet ook de module specifieke opties voor het aanmaken van gebruikers te configureren.';
$string['auth_usernameexists'] = 'De gekozen gebruikersnaam bestaat al. Kies alsjeblieft een andere gebruikersnaam.';
$string['authenticationoptions'] = 'Opties voor authenticatie';
$string['authinstructions'] = 'Hier kun je instructies geven aan de gebruikers, zodat ze weten welke gebruikersnaam en welk wachtwoord ze moeten gebruiken. De tekst die je hier invult komt te staan op de login pagina. Als je dit leeg laat zullen er geen instructies worden weergegeven.';
$string['changepassword'] = 'URL voor het veranderen van het wachtwoord';
$string['changepasswordhelp'] = 'Hier kun je een locatie aangeven waar gebruikers hun gebruikersnaam/wachtwoord kunnen terugkrijgen als ze deze vergeten zijn. Dit zal aan de gebruikers worden gegeven als een knop op de login pagina en op hun gebruikerspagina. Als je dit leeg laat zal de knop niet verschijnen.
';
$string['chooseauthmethod'] = 'Kies een methode van authenticatie:';
$string['guestloginbutton'] = 'Knop voor login als gast';
$string['instructions'] = 'Instructies';
$string['md5'] = 'MD5 encryptie';
$string['plaintext'] = 'Platte tekst';
$string['showguestlogin'] = 'Je kunt de knop voor login als gast verbergen of laten zien op de login pagina.
';

?>
