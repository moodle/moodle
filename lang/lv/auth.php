<?PHP // $Id$ 
      // auth.php - created with Moodle 1.1.1 (2003091111)


$string['auth_dbdescription'] = "©ï metode izmanto àrºju datu bàzi lietotàja un paroles pàrbaudei. Veidojot jaunu ierakstu, informàcija var tikt kopºta sistºmà.";
$string['auth_dbextrafields'] = "©ie ir papildus lauki. Jþs varat aizpildït tos ar informàciju no <B>àrºjàs datu bàzes</b>, kas ir noràdïta ¹eit. <P>Ja atstàsiet laukus neaizpildïtus, tad tiks ievietoti noklusºtie dati.</p> Jebkurà gadïjumà lietotàjs ielogojoties varºs labot ¹os laukus.";
$string['auth_dbfieldpass'] = "Paroles lauka nosaukums";
$string['auth_dbfielduser'] = "Lietotàjvàrda lauka nosaukums";
$string['auth_dbhost'] = "Datu bàzes serveris.";
$string['auth_dbname'] = "Datu bàzes nosaukums";
$string['auth_dbpass'] = "Atbilsto¹à lietotàja parole";
$string['auth_dbpasstype'] = "Noràdiet paroles lauka tipu.  MD5 kodº¹ana ir vislabàk izmantojamà lai savienotos ar citàm web-pielikumiem, piemºram, PostNuke";
$string['auth_dbtable'] = "Tabulas nosaukums datu bàzº";
$string['auth_dbtitle'] = "Izmantot àrºju datu bàzi";
$string['auth_dbtype'] = "Datu bàzes tips (Skatït <A HREF=../lib/adodb/readme.htm#drivers>ADOdb dokumentàciju</A>)";
$string['auth_dbuser'] = "Lietotàjs tikai ar datu bàzes lasï¹anas tiesïbàm";
$string['auth_emaildescription'] = "E-pasta apstiprinàjums ir noklusºtà autentifikàcijas metode. Kad lietotàjs re»istrºjas, uz lietotàja e-pastu tiek nosþtïta apstiprinàjuma vºstule. Vºstule satur nejau¹i izveidotu saiti, kur lietotàjs var apstiprinàt savu re»istràciju. Nàko¹ajàs reizºs, kad lietotàja sistºmas ieie¹anas reizºs, lietotàja vàrds un parole tiek salïdzinàta ar datu bàzi.";
$string['auth_emailtitle'] = "E-pasta autentifikàcija";
$string['auth_imapdescription'] = "©ï metode izmanto IMAP serveri, lai pàrbaudïtu lietotàja vàrdu un paroli.";
$string['auth_imaphost'] = "IMAP servera adrese. Lietojat IP adresi nevis DNS vàrdu.";
$string['auth_imapport'] = "IMAP servera porta numurs. Parasti 143 vai 993.";
$string['auth_imaptitle'] = "Lietot IMAP serveri";
$string['auth_imaptype'] = "IMAP servera tips. IMAP serveriem var bþt da¾àdas autentifikàcijas un saziñas metodes.";
$string['auth_ldap_bind_dn'] = "Ja Jþs gribat sasietu lietotàju, lai vàrºtu meklºt lietotàjus, tad noràdiet ¹eit.
Piemºram, 'cn=ldapuser,ou=public,o=org'";
$string['auth_ldap_bind_pw'] = "Parole sasietajam lietotàjam.";
$string['auth_ldap_contexts'] = "Kontekstu saraksts, kur lietotàji ir novietoti.
Atdaliet kontekstus ar ';'. Piemºram: 'ou=users,o=org; ou=others,o=org'";
$string['auth_ldap_create_context'] = "Ja Jþs veidojat lietotàjus ar e-pasta apstiprinàjumu, tad noràdiet kontekstu, kur tiek veidoti lietotàji. ©im kontekstam jàat¹óiras no citiem, lai novºrstu dro¹ïbas problºmas. Nav nepiecie¹ams pievienot ¹o kontekstu pie ldap_context-mainïgiem, sistºma meklºs lietotàjus no ¹ï konteksta automàtiski.";
$string['auth_ldap_creators'] = "Grupu saraksts, kuras varºs veidot jaunus kursus. Atdaliet grupas ar ';'. Piemºram, 'cn=teachers,ou=staff,o=myorg'";
$string['auth_ldap_host_url'] = "Noràdiet LDAP serveri URL-veidà piemºram 'ldap://ldap.myorg.com/' vai 'ldaps://ldap.myorg.com/' ";
$string['auth_ldap_memberattribute'] = "Noràdiet lietotàja atribþtu, kas nosaka lietotàja piederïbu grupai. Parasti 'member'";
$string['auth_ldap_search_sub'] = "Noràdiet &lt;&gt; 0 nozïmi, ja jums patïk meklºt lietotàjus pa zemkontekstiem.";
$string['auth_ldap_update_userinfo'] = "Izmainiet lietotàja informàciju (vàrds, uzvàrds, adrese..) no LDAP uz Moodle. Skatiet /auth/ldap/attr_mappings.php , informàcijas attºlo¹anai";
$string['auth_ldap_user_attribute'] = "Atribþts, ko izmanto vàrdam/meklº¹anai. Parasti 'cn'.";
$string['auth_ldapdescription'] = "©ï metode piedàvà autentificºties ar LDAP servera palïdzïbu. Ja dotais lietotàjs un parole ir pareizi, sistºma izveido jaunu lietotàju tàs datu bàzº. ©is modulis var nolasït lietotàja atribþtus no LDAP un aizpildït vajadzïgos laukus sistºmà. Pºc tam tiek pàrbaudïts tikai lietotàjvàrds un parole.";
$string['auth_ldapextrafields'] = "©is ir papildus lauks. Jþs varat aizpildït sistºmas lietotàja laukus ar informàciju no <B>LDAP laukiem</B> ko Jþs noràdàt ¹eit. <P>
Ja atstàsiet ¹os laukus tuk¹us, tad nekas netiks pàrvietots no LDAP uz sistºmu, tiks lietoti noklusºtie uzstàdïjumi. Abos gadïjumos lietotàjs ¹os datus varºs izmainït vºlàk.";
$string['auth_ldaptitle'] = "Lietot LDAP serveri";
$string['auth_manualdescription'] = "©ï metode aizliedz lietotàjiem veidot pa¹iem veikt re»istràciju. Visas re»istràcijas ir jàveic administrºtam lietotàjam.";
$string['auth_manualtitle'] = "Tikai manuàla re»istràcija";
$string['auth_nntpdescription'] = "©ï metode izmanto NNTP serveri, lai pàrbaudïtu lietotàjvàrdu un paroli.";
$string['auth_nntphost'] = "NNTP servera adrese. Jàlieto IP adrese, nevis DNS vàrds.";
$string['auth_nntpport'] = "Servera ports (parasti 119)";
$string['auth_nntptitle'] = "Lietot NNTP serveri";
$string['auth_nonedescription'] = "Lietotàji var ielogoties un veidot re»istrºt lietotàjus, bez àrºja servera palïdzïbas vai e-pasta apstiprinàjuma. Esat piesardzïgs lietojot ¹o iespºju, jo tà var radït dro¹ïbas un administràcijas problºmas.";
$string['auth_nonetitle'] = "Nekàda autentifikàcija";
$string['auth_pop3description'] = "©ï metode izmanto pop3 serveri, lai pàrbaudïtu lietotàjvàrdu un paroli.";
$string['auth_pop3host'] = "POP3 servera adrese. Jàlieto IP adrese, nevis DNS vàrds.";
$string['auth_pop3port'] = "Servera ports (parasti 110)";
$string['auth_pop3title'] = "Lietot POP3 serveri";
$string['auth_pop3type'] = "Servera tips. Ja Jþsu serveris izmanto uz sertifikàtiem balstïtu aizsardzïbu, izvºlaties pop3cert.";
$string['auth_user_create'] = "At¶aut lietotàju veido¹anu";
$string['auth_user_creation'] = "Jauni (anonïmi) lietotàji var veidot jaunus lietotàjus uz àrºja autentifikàcijas avotu un apstiprinàt caur e-pastu.
ts on the external authentication source and confirmed via email. Ja Jþs ¹o at¶aujat, neaizmirstiet nokonfigurºt module-specific opcijas lietotàju pievieno¹anai.";
$string['auth_usernameexists'] = "Izvºlºtais lietotàjvàrds pastàv. Izvºlaties citu.";
$string['authenticationoptions'] = "Authentication options";
$string['authinstructions'] = "©eit Jþs varat ievadït informàciju lietotàjiem, kàdu lietotàjvàrdu un paroli lietot. ©eit ievadïtais teksts paràdïsies lietotàja identifikàcijas lapà. Ja atstàsiet lauku tuk¹u, nekàdas instrukcijas netiks dotas.";
$string['changepassword'] = "Nomainït URL ar paroli";
$string['changepasswordhelp'] = "©eit Jþs varat noràdït veidu, kà lietotàjs var atgþt savu lietotàjvàrdu/paroli, ja viñi to ir aizmirsu¹i. Lietotàji ieraudzïs pogu lietotàju identifikàcijas lapà un lietotàja datu lapà. Ja Jþs atstàsiet ¹o lauku tuk¹u, poga netiks izvadïta.";
$string['chooseauthmethod'] = "Izvºlaties autentifikàcijas metodi:";
$string['guestloginbutton'] = "Viesu identifikàcijas poga";
$string['instructions'] = "Instrukcijas";
$string['md5'] = "MD5 kodº¹ana";
$string['plaintext'] = "Teksts";
$string['showguestlogin'] = "Jþs varat paràdït vai slºpt viesu identifikàcijas pogu identifikàcijas lapà.";

?>
