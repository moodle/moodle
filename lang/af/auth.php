<?PHP // $Id$ 
      // auth.php - created with Moodle 1.0.9 development (2003032400)



$string['auth_dbdescription'] = "Die metode gebruik 'n eksterne databasis tabel om 'n gegewe gebruikersnaam en wagwoord te valideer.  As die rekening 'n nuwe rekening is, sal inligting in die ander velde moonlik oor gekopieer word na Moodle.";
$string['auth_dbextrafields'] = "Die velde is optioneel.  Dit is moontlik om van die Moodle gebruiker velde te <i>pre-fill</i> met die <B>eksterne databasis velde</B> wat jy gespesifiseer het. <P>As jy die velde oop los sal Moodle verstek waardes gebruik word.<P>Die gebruiker sal instaat wees om die velde te redigeer sodra die gebruiker aanteken.";
$string['auth_dbfieldpass'] = "Naam van die veld wat wagwoorde bevat";
$string['auth_dbfielduser'] = "Naam van die veld wat gebruikersname bevat";
$string['auth_dbhost'] = "Die rekenaar wat optree as gasheer vir die databasis bediener.";
$string['auth_dbname'] = "Naam van die databasis";
$string['auth_dbpass'] = "Wagwoord vir die bogenoemde gebruikersnaam";
$string['auth_dbpasstype'] = "Spesifiseer die formaat wat die wagwoord veld gebruik.  MD5 enkripsie is handig om mee konneksies te maak na ander web toepassings soos PostNuke";
$string['auth_dbtable'] = "Naam van die tabel in die databasis";
$string['auth_dbtitle'] = "Gebruik 'n eksterne databasis";
$string['auth_dbtype'] = "Die databasis tipe (Sien <A HREF=../lib/adodb/readme.htm#drivers>ADOdb documentation</A> vir details)";
$string['auth_dbuser'] = "Gebruiker met lees toegang tot databasis";
$string['auth_emaildescription'] = "Email bevestiging is die verstek geldigheidsvasstellings metode.  Wanneer die gebruiker aanteken en sy eie gebruikersnaam en wagwoord kies, sal 'n email as bevestiging aan sy email adres gestuur word. Die email bevat 'n veilige skakel na 'n bladsy waar die gebruiker sy rekening kan bevestig. As die gebruiker weer aanteken, word sy wagwoord en gebruikersnaam getoets teen die waardes wat in die Moodle databasis gestoor is.";
$string['auth_emailtitle'] = "Email gebaseerde geldigheidsvasstellings";
$string['auth_imapdescription'] = "Die metode gebruik 'n IMAP bediener om vastestel of 'n gebruikersnaam en wagwoord geldig is.";
$string['auth_imaphost'] = "Die IMAP bediener adres. Gebruik die IP nommer, nie die DNS naam nie.";
$string['auth_imapport'] = "IMAP bediener poort nommer. Dit is gewoonlik 143 of 993.";
$string['auth_imaptitle'] = "Gebruik 'n IMAP bediener";
$string['auth_imaptype'] = "Die IMAP bediener tipe.  IMAP bedieners kan verskillende tipes geldigheidsvasstellings en onderhandelings hê.";
$string['auth_ldap_bind_dn'] = "Spesifiseer hier, as jy wil <i>bind-user</i> gebruik om vir gebruikers te soek. Iets soos 'cn=ldapuser,ou=public,o=org'";
$string['auth_ldap_bind_pw'] = "Wagwoord vir <i>bind-user</i>.";
$string['auth_ldap_contexts'] = "Lys van kontekste waar gebruikers gevestig is. Verdeel verskillende kontekste met ';'. Byvoorbeeld: 'ou=users,o=org; ou=others,o=org'";
$string['auth_ldap_create_context'] = "As jy gebruikers in werking stel met email geldigheidsvasstellings, spesifiseer die konteks waar gebruikers geskep word. Die konteks moet anders wees as ander gebruikers, weens sekuriteits redes. Jy hoef nie die konteks by te voeg by die ldap_context-variable, Moodle sal outomaties soek vir gebruikers in die konteks.";
$string['auth_ldap_creators'] = "Lys van groepe waarvan die lede kursusse mag skep. Verdeel meervoudige groepe met ';'. Gewoonlik iets soos 'cn=teachers,ou=staff,o=myorg'";
$string['auth_ldap_host_url'] = "Spesifiseer LDAP gasheer in URL formaat. Byvoorbeeld 'ldap://ldap.myorg.com/' of 'ldaps://ldap.myorg.com/' ";
$string['auth_ldap_memberattribute'] = "As gebruiker lid van 'n groep is, spesifiseer hulle einskap. Iets soos 'lid' of 'member'";
$string['auth_ldap_search_sub'] = "Gebruik waarde &lt;&gt; 0 as jy 'n soektog doen op gebruikers van 'n subkonteks.";
$string['auth_ldap_update_userinfo'] = "Dateer gebruiker inligting op (naam, van, adres...) vanaf LDAP na Moodle. Kyk na /auth/ldap/attr_mappings.php vir binding inligting";
$string['auth_ldap_user_attribute'] = "Die einskap wat gebruik word om gebruikers te benoem of te soek. Gewoonlik 'cn'.";
$string['auth_ldapdescription'] = "Die metode gebruik 'n eksterne LDAP bediener om geldigheidvastelling te voorsien.
                                  Indien die gegewe gebruikersnaam en wagwoord geldig is, sal Moodle 'n nuwe 
                                  gebruiker in sy databasis skep. Die module kan gebruiker eienskappe inlees vanaf LDAP  
                                  en die verlangde velde in Moodle in vul.  As die gebruiker aanteken sal net die                 
                                  gebruikersnaam en wagwoord gebruik word.";
$string['auth_ldapextrafields'] = "Die velde is opsioneel.  Dit is moontlik om van die Moodle gebruiker velde te <i>pre-fill</i> met inligting van die <B>LDAP velde</B> wat jy gespesifiseer het. <P>As jy die velde oop los sal Moodle verstek waardes gebruik word. Geen data oordrag vanaf LDAP sal gemaak word nie.<P>Die gebruiker sal instaat wees om die velde te redigeer sodra die gebruiker aanteken.";
$string['auth_ldaptitle'] = "Gebruik 'n LDAP bediener";
$string['auth_manualdescription'] = "Met die metode kan gebruikers nie self hulle rekeninge skep nie.  Gebruiker rekeninge moet deur die admin. gebruiker geskep word.";
$string['auth_manualtitle'] = "Admin. gebruiker skep rekeninge";
$string['auth_nntpdescription'] = "Die metode gebruik 'n NNTP bediener om vas te stel of 'n gebruikersnaam en wagwoord geldig is.";
$string['auth_nntphost'] = "Die NNTP bediener adres. Gebruik die IP nommer, nie die DNS naam nie.";
$string['auth_nntpport'] = "Bediener poort (119 is die mees algemeen)";
$string['auth_nntptitle'] = "Gebruik 'n NNTP bediener";
$string['auth_nonedescription'] = "Gebruikers teken in, en kan dan self dadelik 'n geldige rekening skep, geen geldigheidvastelling teen 'n eksterne databasis nie, en geen konformasie via email nie.  Waarskuwing: die opsie kan moontlik baie sekuriteit en admin probleme veroorsaak.";
$string['auth_nonetitle'] = "Geen geldigheidsvastelling";
$string['auth_pop3description'] = "Die metode gebruik 'n POP3 bediener om vas te stel of 'n gebruikersnaam en wagwoord geldig is.";
$string['auth_pop3host'] = "Die POP3 bediener adres. Gebruik die IP nommer, nie die DNS naam nie.";
$string['auth_pop3port'] = "Bediener adres (110 is die mees algemeen)";
$string['auth_pop3title'] = "Gebruik 'n POP3 bediener";
$string['auth_pop3type'] = "Bediener tipe. Indien jou bediener sertifikaat sekuriteit benut, kies pop3cert.";
$string['auth_user_create'] = "Stel gebruiker skepping in werking";
$string['auth_user_creation'] = "Nuwe (anonieme) gebruikers kan gebruikers rekeninge op 'n eksterne geldigheidvastellings bron skep, en bevestig dan deur email. As jy dit gebruik, onthou om module-spesifieke opsies vir gebruiker skepping op te stel.";
$string['auth_usernameexists'] = "Gebruikersnaam bestaan reeds, kies asseblief 'n ander naam.";
$string['authenticationoptions'] = "Geldigheidvastellings opsies";
$string['authinstructions'] = "Voorsien hier instruksies vir jou gebruikers, sodat hulle kan weet watter gebruikersname en wagwoorde hulle kan gebruik.  Die teks wat jy hier in sit sal op die aanteken bladsy verskyn.  As jy dit oop los sal geen instuksies vertoon word nie.";
$string['changepassword'] = "Verander wagwoord URL";
$string['changepasswordhelp'] = "Spesifiseer hier 'n plek waar jou gebruikers hulle gebruikersname en wagwoorde kan verander of dit herwin indien hulle dit vergeet het.  Dit sal as 'n knoppie op die aanteken bladsy aan die gebruikers voorsien word. As jy dit oop los sal daar geen knoppie op die bladsy verskyn nie";
$string['chooseauthmethod'] = "Kies 'n geldigheidvastellings metode: ";
$string['guestloginbutton'] = "Tekenaan as 'n gas";
$string['instructions'] = "Instruksies";
$string['md5'] = "MD5 enkripsie";
$string['plaintext'] = "Gewone teks";
$string['showguestlogin'] = "Jy kan die gas aanteken knoppie wys of wegsteek op die aanteken bladsy.";

?>
