<?PHP // $Id$ 
      // auth.php - created with Moodle 1.4.1 (2004083101)


$string['auth_dbdescription'] = 'Metodo honek kanpoko datu-base taula bat erabiltzen du emandako erabiltzaile izen eta pasahitz bat baliozkoa den egiaztatzeko.  kontua berria bada, beste eremuetako informazioa ere zeharka kopiatu daiteke Moddlen.';
$string['auth_dbextrafields'] = 'Eremu hauek aukerazkoak dira.  Moddle erabiltzaileen eremu batzuk hemen zehaztutako <B>kanpoko datu-base eremuetatik</B> aurrez betetzea erabaki dezakezu. <P>Hutsik uzten badituzu, lehenetsitako balioak erabiliko dira.<P>Edozein kasutan, erabiltzaileek eremu guztiak editazeko aukera izango dute behin saioa hasita.';
$string['auth_dbfieldpass'] = 'Pasahitzak dituen eremuaren izena';
$string['auth_dbfielduser'] = 'Erabiltzaile-izenak dituen eremuaren izena';
$string['auth_dbhost'] = 'Datu-base zerbitzaria ostatatzen duen ordenagailua.';
$string['auth_dbname'] = 'Datu-basearen izena';
$string['auth_dbpass'] = 'Goiko erabiltzaile izenarekin bat datorren pasahitza';
$string['auth_dbpasstype'] = 'Pasahitza eremuak erabiltzen duen formatua zehaztu.  MD5 enkiptazioa beste web aplikazio orokorrekin konektatzeko erabilgarria da, PostNuke adibidez';
$string['auth_dbtable'] = 'Datu-baseko taulak duen izena';
$string['auth_dbtitle'] = 'Kanpoko datu-base bat erabili';
$string['auth_dbtype'] = 'Datu-base mota (Ikus <A HREF=../lib/adodb/readme.htm#drivers>ADOdb documentation</A> xehetasun gahiagorako)';
$string['auth_dbuser'] = 'Datu-basean irakurtzeko baimena duen erabiltzaile izena';
$string['auth_emaildescription'] = 'ePostaz berrestea da lehenetsitako autentifikazio metodoa.  Erabiltzaileak izena ematen duenean, bere izen eta pasahitza aukeratuz, konfirmazio ePosta mezu bat bidaltzen zaio erabiltzailearen ePostara.  ePosta Mezu honek, erabiltzaileei kontua berresteko aukera emango dien orri batera lotura zihur bat du. Hurrengoetan saioa hasteko izen eta pasahitza Moodle datu-basean dauden balioekin konparatzea nahikoa da.';
$string['auth_emailtitle'] = 'E-posta bitarteko autentifikazioa';
$string['auth_imapdescription'] = 'Metodo honek IMAP zerbitzari bat erabiltzen du emandako erabiltzaile izen eta pasahitza baliozkoak diren egiaztatzeko.';
$string['auth_imaphost'] = 'IMAP zerbitzariaren helbidea. IP helbidea erabili ezazu, ez DNS izena.';
$string['auth_imapport'] = 'IMAP zerbitzariaren kaia zenbakia. 143 edo 993 izan ohi da.';
$string['auth_imaptitle'] = 'IMAP zerbitzari bat erabili';
$string['auth_imaptype'] = 'IMAP zerbitzari mota.  IMAP zerbitzariak autentifikazio eta negoziazio mota ezberdinak izan ditzazkete.';
$string['auth_ldap_bind_dn'] = 'Erabiltzaileak bilatzeko bind-user erabili nahi baduzu, hemen zehaztu. Honen antzeko zerbait: \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'bind-user erabiltzaile pasahitza.';
$string['auth_ldap_contexts'] = 'Erabiltzaileak kokatzen diren testuinguru zerrenda. Testuinguruak \';\' erabiliz banatu. Adibidez: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_host_url'] = 'LDAP ostatua URL bidez zehaztu, adibidez \'ldap://ldap.zerbitzaria.com/\' edo \'ldaps://ldap.zerbitzaria.com/\' ';
$string['auth_ldap_search_sub'] = '<> 0 balioa jarri erabiltzaileak azpi-testuinguruneetan bilatu nahi badituzu.';
$string['auth_ldap_update_userinfo'] = 'Erabiltzaile informazioa (izena, abizena, helbidea..) LDAP-tik Moodle-ra eguneratu. /auth/ldap/attr_mappings.php fitxategian begira ezazu mapa informazioa';
$string['auth_ldap_user_attribute'] = 'Erbiltzaileak izendatzeko edo bilatzeko atributua. \'cn\' izan ohi da.';
$string['auth_ldapdescription'] = 'Metodo honek kanpo LDAP zerbitzari baten aurkako autentifikazioa eskeintzen du.
                                  Emandako erabiltzaile izen eta pasahitza baliozkoak badira, Moodlek erabiltzaile 
								  sarrera berri bat sortuko du bere datu-basean. Modulu honek erabiltzaile atributuak 
								  LDAP zerbitzaritik irakurri ditzazke eta eremuak Moodlen bete.  Hurrengo saio hasieretan
								  soilik izen eta pasahitza egiaztatuko dira.';
$string['auth_ldapextrafields'] = 'Eremu hauek aukerazkoak dira.  Moodle erabiltzaile eremu batzuk hemen zehaztutako <b>LDAP eremu</b>etako informazioz betetzea aukeratu dezakezu. <P>Zurian uzten badituzu, ez da ezer transferituko LDAP-tik eta Moodlek lehenetsitako balioak erabiliko dira ordez.<P>Edozein kasutan, erabiltzaileak eremu guzti hauek editatzeko gaitasuna izango du behin saioa hasita.';
$string['auth_ldaptitle'] = 'LDAP zerbitzari bat erabili';
$string['auth_nntpdescription'] = 'Metodo honek NNTP zerbitzari bat erabiltzen du emandako erabiltzaile izen eta pasahitza baliozkoak diren egiaztatzeko.';
$string['auth_nntphost'] = 'NNTP zerbitzariaren helbidea. IP zenbakia erabili ezazu, ez DNS izena.';
$string['auth_nntpport'] = 'Zerbitzari kaia (119 arruntena da)';
$string['auth_nntptitle'] = 'NNTP zerbitzari bat erabili';
$string['auth_nonedescription'] = 'Erabiltzaileen berehala eman dezakete izena eta baliozko kontuak sortu, kanpo zerbitzari baten aurkako autentifikaziorik gabe eta ePosta bidez berretsi gabe.  Aukera hau erabiltzeerekin kontuz - honek sor ditzazkeen segurtasun eta administrazio arazoak kontutan hartu.';
$string['auth_nonetitle'] = 'Autentifikaziorik ez';
$string['auth_pop3description'] = 'Metodo honek POP3 zerbitzari bat erabiltzen du emandako erabiltzaile izen eta pasahitza baliozkoak diren egiaztatzeko.';
$string['auth_pop3host'] = 'POP3 zerbitzariaren helbidea. IP zenbakia erabili, ez DNS izena.';
$string['auth_pop3port'] = 'Zerbitzari kaia (110 izan ohi da)';
$string['auth_pop3title'] = 'POP3 zerbitzari bat erabili';
$string['auth_pop3type'] = 'Zerbitzari mota. Zure zerbitzariak zertifikatu segurtasuna erabiltzen badu, pop3cert aukeratu ezazu.';
$string['authenticationoptions'] = 'Autentifikazio aukerak';
$string['authinstructions'] = 'Hemen zure erabiltzaileentzat argibideak eman ditzazkezu, erabili behar duten erabiltzaile eta pasahitza zeintzu diren jakin dezaten.  Hemen sartutako testua saio hasiera pantailan agertuko da.  Zurian uzten baduzu ez da argibiderik emango.';
$string['changepassword'] = 'Pasahitz URL-a aldatu';
$string['changepasswordhelp'] = 'Hemen zure erabiltzaileek bere izen edo pasahitza aldatzeko, edo ahaztekotan berreskuratzeko, erabili dezaketen helbide bat zehaztu dezakezu.  Hau erabiltzaileei saio hasiera pantailan eta erabiltzaile orrian botoi gisa aurkeztuko zaie.  Zurian uzten baduzu ez zaie botoirik aurkeztuko.';
$string['chooseauthmethod'] = 'Autentifikazio metodoa aukeratu: ';
$string['guestloginbutton'] = 'Bisitariek saioa hasteko botoia';
$string['instructions'] = 'Argibideak';
$string['md5'] = 'MD5 enkriptazioa';
$string['plaintext'] = 'Testu arrunta';
$string['showguestlogin'] = 'Bisitariek saioa hasteko botoia bistaratu edo ezkutatu dezakezu saio hasiera pantailan.';

?>
