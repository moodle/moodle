<?PHP // $Id$ 
      // auth.php - created with Moodle 1.2.1 (2004032500)


$string['auth_dbdescription'] = 'Kjo metodë përdor një tabelë të një databaze të jashtme për të kontrolluar nëse emri i një përdoruesi dhe fjalëkalimi i tij janë të vlefshëm. Nëse është një llogari e re, atëherë informacioni nga fusha të tjera mund të kopjohet në Moodle.';
$string['auth_dbextrafields'] = 'Këto fusha janë fakultative. Mund të zgjidhni të mbushni më përpara disa fusha të përdoruesit të  Moodle me informacion nga <B>fushat e databazës së jashtme</B> që specifikoni këtu.  <P>Nëse i lini këto bosh atëherë do të përdoren default-et .<P>Në të dyja rastet përdoruesi mund t\'i ndryshojë të gjitha këto fusha pasi logohet.';
$string['auth_dbfieldpass'] = 'Emri i fushës që përmban fjalëkalimet';
$string['auth_dbfielduser'] = 'Emri i fushës që përmban emrat e përdoruesve';
$string['auth_dbhost'] = 'Kompjuteri ku gjendet serveri i databazave';
$string['auth_dbname'] = 'Emri i databazës';
$string['auth_dbpass'] = 'Fjalëkalimi që i korrespondon këtij emri përdoruesi';
$string['auth_dbpasstype'] = 'Specifiko formatin që përdor fusha e fjalëkalimit. Enkriptimi MD5 është i dobishëm dhe për t\'u lidhur me aplikime të tjera web si PostNuke';
$string['auth_dbtable'] = 'Emri i tabelës së databazës';
$string['auth_dbtitle'] = 'Përdor një databazë të jashtme';
$string['auth_dbtype'] = 'Tipi i databazës(Shiko <A HREF=../lib/adodb/readme.htm#drivers>dokumentacioni ADOdb</A> për detaje)';
$string['auth_dbuser'] = 'Emri i përdoruesit me akses për të lexuar në databazë';
$string['auth_emaildescription'] = 'Konfirmimi me email është mënyra standarte e verifikimit. Kur përdoruesi rregjistrohet, duke zgjedhur emrin e vet të përdoruesit dhe fjalëkalimin, një email konfirmimi dërgohet në adresën email të përdoruesit. Ky email përmban një link të sigurtë të një faqeje ku përdoruesi mund të konfirmojë llogarinë e vet. Login të ardhshëm vetëm kontrollojnë emrin e përdoruesit dhe fjalëkalimin me ato që ruhen në databazën e Moodle.';
$string['auth_emailtitle'] = 'Verifikimi nëpërmjet email-it';
$string['auth_imapdescription'] = 'Kjo metodë përdor një server IMAP për të kontrolluar nëse një emër i dhënë përdoruesi dhe një fjalëkalim janë të saktë. ';
$string['auth_imaphost'] = 'Adresa e serverit IMAP. Përdor numrin IP, jo emrin DNS.';
$string['auth_imapport'] = 'Porti i serverit IMAP. Normalisht është 143 ose 993.';
$string['auth_imaptitle'] = 'Përdor një server IMAP';
$string['auth_imaptype'] = 'Tipi i serverit IMAP. Serverat IMAP mund të kenë mënyra të ndryshme verifikimi dhe negocimi.';
$string['auth_ldap_bind_dn'] = 'Nëse do të përdorësh bind-user për të kërkuar përdoruesit, specifikoje këtu. Diçka si \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Fjalëkalimi për përdorues të verbër.';
$string['auth_ldap_contexts'] = 'Lista e specifikimeve (konteksti) ku futen përdoruesit. Ndaji specifikimet e ndryshme me \';\'. Psh : \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Nëse e pajisni krijimin e përdoruesit me konfirmimin nëpërmjet email-it, specifiko kontekstin ku krijohen përdoruesit. Ky kontekst duhet të jetë i ndryshëm nga përdoruesit e tjerë për arsye sigurie. Nuk është e nevojshme ta shtosh këtë kontekst tek variabli ldap_context, Moodle do të kërkojë automatikisht përdoruesit në këtë kontekst. ';
$string['auth_ldap_creators'] = 'Lista e grupeve në të cilat antarët mund të krijojnë kurse të reja. Ngaji grupet e shumfishta multipli me \';\'. Zakonisht diçka si \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_host_url'] = 'Specifiko server-in LDAP me një URL të tipit \'ldap://ldap.myorg.com/\' o \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_memberattribute'] = 'Specifiko atributin e antarit përdorues, kur  përdoruesit i përkasin një grupi. Zakonisht \'member\'';
$string['auth_ldap_search_sub'] = 'Vendos vlerat e &lt;&gt; 0 nëse preferon të kërkosh përdoruesit sipas nënkonteksteve.';
$string['auth_ldap_update_userinfo'] = 'Azhorno informacionin e përdoruesit (emri, mbiemri, adresa...) nga LDAP te Moodle. Shiko te /auth/ldap/attr_mappings.php për informacione mbi rrugëzimin';
$string['auth_ldap_user_attribute'] = 'Atributi i përdorur për të kërkuar emrat e përdoruesve. Zakonisht \'cn\'.';
$string['auth_ldapdescription'] = 'Kjo metodë jep autentifikimin nëpërmjet një server-i LDAP të jashtëm.
Mëse emri i përdoruesit dhe password-i që janë dhënë janë të vlefshme, Moodle krijon një përdorues të ri tek baza e të dhënave. Ky modul mund të lexojë atributet nga LDAP dhe të mbushi fushat e kërkuara në Moodle. Në logimet suksesive, vetëm emri i përdoruesit dhe password-i do të kontrollohen.';
$string['auth_ldapextrafields'] = 'Këto fusha janë opsionale. Mund të zgjedhësh që të paraplotësohen disa fusha të përdoruesit të  Moodle me informacione nga <b>fushat LDAP</b> që ju i specifikoni këtu. <p>Nëse i lini këto fusha bosh, atëhere asgjë nuk do të trasferohet nga LDAP dhe do të përdoren të dhënat default të Moodle.</p><p>Në rast të kundërt, përdoruesit  mund ti modifikojnë të gjitha këto fusah pasi të jenë loguar.';
$string['auth_ldaptitle'] = 'Përdor një server LDAP';
$string['auth_manualdescription'] = 'Kjo metodë u heq çdo mundësi përdoruesve që tä krijojnë llogaritë e tyre. Të gjitha llogaritë duhet të krijohen manualisht nga një administrator.';
$string['auth_manualtitle'] = 'Vetëmo regjistrime manualisht';
$string['auth_multiplehosts'] = 'Mund të listohen host-e (kupmjutera të largët) të shumëfishtë (psh. host1.com;host2.com;host3.com)';
$string['auth_nntpdescription'] = 'Kjo metodë përdor një server NNTP për të kontrolluar nëse emri i përdoruesit dhe password-i është i vlefshëm.';
$string['auth_nntphost'] = 'Adresa e server-it NNTP. Përdor numerin IP, jo emrin DNS.';
$string['auth_nntpport'] = 'Porta e server-it  (zakonisht 119 )';
$string['auth_nntptitle'] = 'Përdor një server NNTP';
$string['auth_nonedescription'] = 'Përdoruesit mund të regjistrohen dhe të krijojnë llogari të vlefshme menjëherë, pa autentifikimin nga një server i jashtëm dhe pa konfirmim nëpërmjet email-it. Kujdes në përdorimin e këtij opsioni - mendo për sigurinë dhe problemet e  administrimit që mund të shkaktojë ky opsion. ';
$string['auth_nonetitle'] = 'Pa autentfikim';
$string['auth_pop3description'] = 'Kjo metodë përdor një server POP3 për të kontrolluar nëse emri i përdoruesit dhe password-i është i vlefshëm.';
$string['auth_pop3host'] = 'Adresa e server-it POP3. Përdor numerin IP, dhe jo emrin DNS.';
$string['auth_pop3port'] = 'Porta e server-it (zakonisht 110 )';
$string['auth_pop3title'] = 'Përdor server POP3';
$string['auth_pop3type'] = 'Tipi i server-it. Nëse server-i juaj përdor çertifikime sigurie, zgjidh pop3cert.';
$string['auth_user_create'] = 'Mundëso krijimin e përdoruesvete';
$string['auth_user_creation'] = 'Përdoruesit e rinj (anonimë) mund të krijojnë llogari përdoruesish te autentifikuesi i jashëm dhe të konfirmohen nëpërmjet email-it. Nëse e mundëson këtë, mbaje mend që të konfigurosh edhe  opsionet specifike të modulit për krijimin e përdoruesve.';
$string['auth_usernameexists'] = 'Emri i zgjedhur për përdorues është tashmë i përdorur. Zgjidhni një tjetër. ';
$string['authenticationoptions'] = 'Opsionet e autentifikimit';
$string['authinstructions'] = 'Këtu mund të japësh instruksione për përdoruesit e tu, kështu ata mund të mësojnë se cilin emër përdoruesi dhe password-i duhet të përdorin. Teksti që ju futni këtu do të shfaqet në faqen e login-it. Nëse e lë bosh nuk do të jepen instruksione.';
$string['changepassword'] = 'Ndrysho password-in URL';
$string['changepasswordhelp'] = 'Këtu mund të specifikosh se ku mund të rikuperojnë ose ndryshojnë përdoruesit username/password nëse i kanë harruar. Kjo do tu jepet përdoruesve si një buton në faqen e logimit dhe në faqen e përdoruesit. Nëse e lë bosh butoni nuk do të shfaqet.';
$string['chooseauthmethod'] = 'Zgjidhi një metodë  autentifikimi:';
$string['guestloginbutton'] = 'Butoni i logimit si guest ';
$string['instructions'] = 'Instruksione';
$string['md5'] = 'Kodimi MD5';
$string['plaintext'] = 'Tekst i thjeshtë';
$string['showguestlogin'] = 'Mund ta fshehësh ose ta shfaqësh butonin e logimit si vizitor(mysafir) në faqen e logimit.';

?>
