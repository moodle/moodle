<?PHP // $Id$ 
      // auth.php - created with Moodle 1.4.1 (2004083101)


$string['auth_dbdescription'] = 'Metodo honek kanpo datubase bateko taula bat erabiltzen du erabiltzaile izen eta pasahitz zehatz bat balidatzeko. Erabiltzaile kontua berria bada, beste eremuetako informazioa ere kopia daiteke Moodle-n.';
$string['auth_dbextrafields'] = 'Eremu hauek aukerakoak dira. Zuk Moodle-erabiltzailearen eremu batzuk, hemen zehazten duzun <strong>kanpo databaseko eremuetatik</strong> hartutako informazioaz, aldez aurretik betetzea aukeratu ahal duzu. <p>Ez baduzu hau betetzen, lehenetsitako baloreak hartuko dira</p>.<p>Kasu bietan, erabiltzaileak eremu horiek guztiak editatu ahal izango ditu sartu ostean</p>.';
$string['auth_dbfieldpass'] = 'Pasahitzak dituen eremuaren izena';
$string['auth_dbfielduser'] = 'Erabiltzaile izenak dituen eremuaren izena';
$string['auth_dbhost'] = 'Datubase zerbitzaria dagoen ordenadorea.';
$string['auth_dbname'] = 'Datubasearen izena';
$string['auth_dbpass'] = 'Aurreko erabiltzaile izenari dagokion pasahitza';
$string['auth_dbpasstype'] = 'Zehaztu pasahitza eremurak erabiltzen duen formatoa. MD5 enkriptazioa oso erabilgarria da PostNuke bezalako beste Web aplikazio batzuekin lotura egiteko.';
$string['auth_dbtable'] = 'Taularen izena datubasean';
$string['auth_dbtitle'] = 'Kanpo datubase bat erabili';
$string['auth_dbtype'] = 'Datubase mota (Zehaztasun gehiagorako <a href=../lib/adodb/readme.htm#drivers>ADOdb-ren dokumentazioa</a> ikusi)';
$string['auth_dbuser'] = 'Datubasean irakurtzeko baimena daukan erabiltzailea';
$string['auth_emaildescription'] = 'Posta elektroniko bidezko egiaztapena lehenetsitako autentikazio metodoa da. Erabiltzaileak izena ematean, bere erabiltzaile izen propioa eta pasahitza aukeratuz, egiaztapenerako e-mail bat bidaltzen da bere posta helbidera. E-mail honek erabiltzaileak bere kontua egiaztatzeko orrialde baterako esteka seguru bat du. Ondorengo sarreretan erabiltzaile izena eta pasahitza egiaztatzen da Moodle-ren datubasean gordetako baloreekin.';
$string['auth_emailtitle'] = 'E-mail-en oinarritutako autentikazioa';
$string['auth_imapdescription'] = 'Metodo honek IMAP zerbitzari bat erabiltzen du erabiltzaile izena eta pasahitza baliodunak diren ala ez egiaztetzeko.';
$string['auth_imaphost'] = 'IMAP zerbitzariaren helbidea. IP zenbakia erabili, ez DNS izena.';
$string['auth_imapport'] = 'IMAP zerbitzariaren portu zenbakia. Ohikoena 143 edo 993 izaten da.';
$string['auth_imaptitle'] = 'IMAP zerbitzari bat erabili';
$string['auth_imaptype'] = 'IMAP zerbitzari mota. IMAP zerbitzariek autentikazio eta negoziaketa mota ezberdinak izan ditzakete.';
$string['auth_ldap_bind_dn'] = 'Erabiltzaileak bilatzeko \'bind-user\' erabili nahi baduzu, esan emen. \'cn=ldapuser,ou=public,o=org\' bezalako zerbait';
$string['auth_ldap_bind_pw'] = 'bind-user-erako pasahitza.';
$string['auth_ldap_contexts'] = 'Erabiltzaileak kokatuta dauden testuinguruen zerrenda. Testuinguru ezberdinak banatzeko erabili \';\'. Adibidez: \'ou=usuarios,o=org; ou=otros,o=org\'';
$string['auth_ldap_create_context'] = 'Erabiltzaileen sorrera, posta elektroniko bidezko egiaztapenarekin gaitzen baduzu, zehaztu zein testuingurutan sortzen diren erabiltzaileak. Testuinguru hau desberdina izan behar du erabiltzaile bakoitzean segurtasun arazoak ekiditzeko. Ez da beharrezkoa testuinguru hau gehitzea Idap_context-variable-an, Moodlek automatikoki bilatuko ditu testuinguru honetarako erabiltzaileak.';
$string['auth_ldap_creators'] = 'Kurtso berriak sortzeko baimena duten erabiltzaile taldeen zerrenda. Talde batzuk banatu ahal dira hau erabiliz: \';\'. Normalean horrela: \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_host_url'] = 'LDAP host-a URL moduan zehaztu, adibidez: \'ldap://ldap.myorg.com/\' edo \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_memberattribute'] = 'Erabiltzaile izenerako ezaugarria zehaztu, erabiltzaileak talde batean sartzen direnean. Normalean \'partaidea\'';
$string['auth_ldap_search_sub'] = '<> 0 balorea jarri bigarren mailako testuinguruetatik erabiltzaileak bilatu nahi badituzu.';
$string['auth_ldap_update_userinfo'] = 'Erabiltzaile informazioa eguneratu (izena, abizena, helbidea..) LDAP-etik Moodle-ra. /auth/ldap/attr_mappings.php-n begiratu mapatze informaziorako';
$string['auth_ldap_user_attribute'] = 'Erabiltzaileak izendatu/bilatzeko erabiltzen den ikurra. Normalean \'cn\'.';
$string['auth_ldapdescription'] = 'Metodo honek kanpo LDAP zerbitzari baten kontrako autentikazioa ematen du.
Emandako erabiltzaile izena eta pasahitza baliodunak ez badira, Moodle-k, erabiltzaile horrentzat, sarrera berri bat sortzen du bere datubasean. Modulu honek erabiltzaile ezaugarriak irakurri ahal ditu LDAPetik eta Moodle-n beharrezkoak diren eremuak aldez aurretik bete. Ondorengo sarreretarako erabiltzaile izena eta pasahitza baino ez da egiaztatzen.';
$string['auth_ldapextrafields'] = 'Eremu hauek aukerakoak dira. Zuk aukeratu ahal duzu zenbati erabiltzaile eremu betetzea hemen zehazten dituzun <strong>LDAP eremu</strong>etako informazioaz. <p>Eremu hauek ez badituzu betetzen, ez da ezer bidaliko LDAPetik eta Moodle-n lehenetsitako sistema erabiliko da.</p><p>Kasu bietan, erabiltzaileek eremu hauek guztiak editatu ahal izango dituzte sartutakoan.</p>';
$string['auth_ldaptitle'] = 'LDAP zerbitzari bat erabili';
$string['auth_manualdescription'] = 'Metodo honek erabiltzaile sorrera automatikoa ekiditen du. Erabiltzaileak administrariak sortu behar ditu.';
$string['auth_manualtitle'] = 'Kontuak eskuz baino ez sortu';
$string['auth_nntpdescription'] = 'Metodo honek NNTP zerbitzari bat erabiltzen du erabiltzaile izena eta pasahitza egiaztatzeko.';
$string['auth_nntphost'] = 'NNTP zerbitzariaren helbidea. IP zenbakia erabili, ez DNS izena.';
$string['auth_nntpport'] = 'Zerbitzariaren portua (119 izaten da ohikoena)';
$string['auth_nntptitle'] = 'NNTP zerbitzari bat erabili';
$string['auth_nonedescription'] = 'Erabiltzaileak momentuan erregistratu ahal dira eta baliodun kontuak sortu ahal dituzte, kanpo zerbitzari baten kontrako autentikaziorik gabe eta posta bidezko egiaztapenik gabe. Kontuz aukera hau erabiltzean - kontuan hartu sor dezakeen seguridade eta administrazio .';
$string['auth_nonetitle'] = 'Autentikaziorik gabe';
$string['auth_pop3description'] = 'Metodo honek POP3 zerbitzari bat erabiltzen du erabiltzaile izena eta pasahitza egiaztatzeko.';
$string['auth_pop3host'] = 'POP3 zerbitzariaren helbidea. IP zenbakia erabili, ez DNS izena.';
$string['auth_pop3port'] = 'Zerbitzariaren portua (110 izaten da ohikoena)';
$string['auth_pop3title'] = 'POP3 zerbitzari bat erabili';
$string['auth_pop3type'] = 'Zerbitzari mota. Zure zerbitzariak segurtasun ziurtagiri bat erabiltzen badu, pop3cert aukeratu.';
$string['auth_user_create'] = 'Erabiltzailei sorrera ahalbidetu';
$string['auth_user_creation'] = 'Erabiltzaile berriek (anonimoek) autentikazio kanpo kodearen gainean sortu ahal dituzte kontuak, eta posta bidez egiaztatu. Hau gaitzen baduzu, gogoratu ere erabiltzaileak sortzeko modulu zehatzaren aukerak konfiguratzeaz.';
$string['auth_usernameexists'] = 'Hautatutako erabiltzaile izena badago lehendik. Mesedez, hautatu besteren bat.';
$string['authenticationoptions'] = 'Autentikazio aukerak';
$string['authinstructions'] = 'Hemen argibideak eman ahal dizkiezu erabiltzaileei, zein erabiltzaile izen eta pasahitz erabili behar duten jakin dezaten. Hemen sartzen duzun testua sarrera orrian agertuko da. Ez baduzu ezer idazten ez da argibiderik agertuko.';
$string['changepassword'] = 'URL pasahitza aldatu';
$string['changepasswordhelp'] = 'Hemen zehaztu ahal duzu erabiltzaileek non berreskuratu edo aldatu ahal duten euren erabiltzaile izena/pasahitza ahazt baldin badute. Horretarako, sarrera orrian botoi bat agertuko da. Ez baduzu hau betetzen, botoi hau ez da agertuko.';
$string['chooseauthmethod'] = 'Autentikazio metodo bat aukeratu: ';
$string['guestloginbutton'] = 'Gonbidatuentzako sarrera botoia';
$string['instructions'] = 'Argibideak';
$string['md5'] = 'MD5 enkriptazioa';
$string['parentlanguage'] = 'es';
$string['plaintext'] = 'Testu laua';
$string['showguestlogin'] = 'Sarrera orrialdeko gonbidatuentzako sarrera botoia erakutsi edo ezkutatu ahal duzu.';
$string['thischarset'] = 'iso-8859-1';
$string['thisdirection'] = 'ltr';
$string['thislanguage'] = 'Euskara';

?>
