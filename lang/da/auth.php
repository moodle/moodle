<?PHP // $Id$ 
      // auth.php - created with Moodle 1.3 development (2004041800)


$string['auth_dbdescription'] = 'Denne metode bruger en ekstern database for at kontrollere om et givet username og password er gyldigt. Hvis konto\'en er ny, kan oplysninger fra andre felter også kopieres ind i Moodle.';
$string['auth_dbextrafields'] = 'Disse felter er optional. Du kan vælge at for-udfylde nogle Moodle bruger felter fra <B>den eksterne database</B>, som du har specificeret her.<P> Hvis du ikke skriver noget her, vil default værdierne blive brugt.<P> I alle tilfælde vil brugeren være i stand til at skrive i alle felterne, når de er logget ind.';
$string['auth_dbfieldpass'] = 'Navn på feltet der indeholder password';
$string['auth_dbfielduser'] = 'Navnet på feltet der indeholder usernames';
$string['auth_dbhost'] = 'Computeren der hoster database serveren.';
$string['auth_dbname'] = 'Navnet på databasen';
$string['auth_dbpass'] = 'Password der matcher ovennævnte username';
$string['auth_dbpasstype'] = 'Angiv hvilket format password feltet anvender. MD5 kryptering er anvendeligt når der connectes til almindelige web applikationer som PostNuke';
$string['auth_dbtable'] = 'Navnet på tabellen i databasen';
$string['auth_dbtitle'] = 'Brug en ekstern database';
$string['auth_dbtype'] = 'Database typen(Se <A HREF=../lib/adodb/readme.htm#drivers>ADOdb documentation</A> for detaljer)';
$string['auth_dbuser'] = 'Username med læserrettigheder til databasen';
$string['auth_emaildescription'] = 'Email bekræftigelse er default godkendelsesmetode. Når brugerne melder sig ind og vælger deres username og password, vil en bekræftigelses email blive sendt til brugerens emailaddresse. Denne email indeholder et sikkert link til en side, hvor brugeren kan bekræftige sine oplysninger. Fremtidige logins kontrolleres ved sammenligning af det username og password, som der er gemt i databasen.';
$string['auth_emailtitle'] = 'Email-baseret godkendelse';
$string['auth_imapdescription'] = 'Denne metode bruger en IMAP server for at kontrollere om usernam og password er gyldigt.';
$string['auth_imaphost'] = 'IMAP server addressen. Brug IP nummeret, ikke DNS navn.';
$string['auth_imapport'] = 'IMAP server port nummer. Sædvanligvis er det 143 eller 993.';
$string['auth_imaptitle'] = 'Brug en IMAP server';
$string['auth_imaptype'] = 'IMAP server typen. IMAP servere kan have forskellige typer godkendelser.';
$string['auth_ldap_bind_dn'] = 'Hvis du bruger bind-user til søgning, skal det angives her. Noget  med \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Password for bind-user.';
$string['auth_ldap_contexts'] = 'Liste med indhold over brugere. deldifferent indhold med \';\'. For example: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Hvis du tillader brugeroprettelse med email verifiering, så angiv i hvilken sammenhæng brugerne bliver oprettet. Denne sammenhæng skulle være anderledes end andre p.g.a. sikkerhedsmæssige årsager. Du behøves ikke at tilføje sammenhængen til \'ldap_context\' variablen, Moodle vil automatisk søge efter brugere i denne sammenhæng.';
$string['auth_ldap_creators'] = 'List af grupper hvis medlemmer kan oprette nye kurser. Adskil flere grupper med \';\'. F.eks. i stil med \'cn=teachers, ou=staff, o=myirg\'';
$string['auth_ldap_host_url'] = 'Angiv LDAP host i URL-form f.eks. \'ldap://ldap.myorg.com/\' eller \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_memberattribute'] = 'Angiv bruger attribut, når en bruger tilhører en gruppe. Normalt \'member\'';
$string['auth_ldap_search_sub'] = 'Sæt værdi &lt;&gt; 0 hvis du vil søge efter brugere ud fra underemner.';
$string['auth_ldap_update_userinfo'] = 'Updater bruger information (fornavn, efternavn, addresse..) fra LDAP til Moodle. Se /auth/ldap/attr_mappings.php for information';
$string['auth_ldap_user_attribute'] = 'Attributten til navngivning/søgning af brugere. Sædvanligvis \'cn\'.';
$string['auth_ldap_version'] = 'Versionen af LDQP protokollen din server bruger.';
$string['auth_ldapdescription'] = 'Denne metode kræver godkendelse op mod en ekstern LDAP server.
Hvis det givne username/password er gyldige opretter Moodle en ny bruger i databasen.     Dette modul kan læse bruger attributter fra en LDAP server og udfylde ønskede felter i Moodle 
For følgende logins bliver kun username og password kontrolleret.';
$string['auth_ldapextrafields'] = 'Disse felter er optional.  Du kan vælge at udfylde Moodle felter på forhånd fra <B>LDAP felterne</B> som du angiver her. <P>Hvis du ikke skriver noget her, vil intet overføres fra LDAP og Moodle defaults\'ene vil blive brugt i stedet.<P>I alle tilfælde vil brugeren være i stand ændre i felterne efter de har logget ind.';
$string['auth_ldaptitle'] = 'Brug en LDAP server';
$string['auth_manualdescription'] = 'Denne metode fjerner enhver måde for brugerne selv at oprette en brugerkonto. Alle brugerkonti skal laves manuelt af en admin bruger.';
$string['auth_manualtitle'] = 'Kun manuel brugeroprettelse.';
$string['auth_multiplehosts'] = 'Flere værter kan speciferes (f.eks. host1.com;host2.com;host3.com)';
$string['auth_nntpdescription'] = 'Denne metode bruger en NNTP server for at kontrollere om de givne username og password er gyldige.';
$string['auth_nntphost'] = 'NNTP server addressen. Brug IP nummeret, ikke DNS navn.';
$string['auth_nntpport'] = 'Server port (119 er den mest almindelige)';
$string['auth_nntptitle'] = 'Brug en NNTP server';
$string['auth_nonedescription'] = 'Brugerne kan oprette sig selv og lave gyldige konti med det samme, uden godkendelse op mod en ekstern server og uden bekræftigelse via e-mail. Vær forsigtig med at bruge denne mulighed - tænk administrationsproblemerne!';
$string['auth_nonetitle'] = 'No godkendelse';
$string['auth_pop3description'] = 'Denne metode bruger en POP3 server til at kontrollere om de givne username og password er gyldige';
$string['auth_pop3host'] = 'POP3 server addressen. Brug IP nummeret, ikke DNS navn.';
$string['auth_pop3port'] = 'Server port (110 er mest almindelig)';
$string['auth_pop3title'] = 'Brug en POP3 server';
$string['auth_pop3type'] = 'Server type. Hvis din server anvender certificate security, vælg pop3cert.';
$string['auth_user_create'] = 'Tillad bruger oprettelse';
$string['auth_user_creation'] = 'Nye (anonyme) brugere kan blive oprettet v.h.a. en extern authorisations kilde og bekræftiget via email. Hvis du tillader dette, så husk at konfigurer modul-afhængig mulighed for brugeroprettelse.';
$string['auth_usernameexists'] = 'Det valgte brugernavn eksistere allerede. Vælg venlist et andet.';
$string['authenticationoptions'] = 'Godkendelses options';
$string['authinstructions'] = 'Her kan du komme med anvisninger til dine brugere om, hvordan de skal oprette username og password. Teksten du skriver her, vil blive vist på login siden. Hvis du ikke skriver noget her, vil der ikke vises noget på login siden.';
$string['changepassword'] = 'Lav password URL om';
$string['changepasswordhelp'] = 'Her kan du angive et sted, hvor dine brugere kan finde eller ændre deres username/password, hvis de har glemt det. Brugerne vil få vist en knap på login siden. Hvis du ikke skriver noget her, vil knappen ikke blive vist.';
$string['chooseauthmethod'] = 'Vælg en godkendelses metode';
$string['guestloginbutton'] = 'Gæste login knap';
$string['instructions'] = 'Instruktioner';
$string['md5'] = 'MD5 kryptering';
$string['plaintext'] = 'Alm. text';
$string['showguestlogin'] = 'Du kan vise eller gemme gæste login knappen på login-siden.';

?>
