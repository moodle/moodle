<?PHP // $Id$ 
      // auth.php - created with Moodle 1.3 (2004052500)


$string['auth_dbdescription'] = 'Tato metoda pou¾ívá tabulku v externí databázi ke kontrole, zda zadané u¾ivatelské jméno a heslo je platné.  Pøi vytváøení nového úètu mohou být informace z dal¹ích polí zkopírovány do databáze Moodle.';
$string['auth_dbextrafields'] = 'Tato pole jsou volitelná. Mù¾ete si zvolit pøednastavení nìkterých informací o u¾ivateli na základì hodnot v <B>polích externí databáze</B>, která urèíte zde.<P>Necháte-li tato pole prázdná, budou pou¾ity implicitní hodnoty.<P>Tak jako tak si u¾ivatel po pøihlá¹ení mù¾e tato pole mìnit.';
$string['auth_dbfieldpass'] = 'Název pole, které obsahuje hesla';
$string['auth_dbfielduser'] = 'Název pole, které obsahuje u¾ivatelské jména';
$string['auth_dbhost'] = 'Poèítaè hostující databázi';
$string['auth_dbname'] = 'Název databáze';
$string['auth_dbpass'] = 'Heslo k tomuto u¾ivatelskému jménu';
$string['auth_dbpasstype'] = 'Urèete pou¾itý formát pole s heslem. ©ifrování MD5 je u¾iteèné pøi pøipojování k dal¹ím webovým aplikacím, jako je napøíklad PostNuke';
$string['auth_dbtable'] = 'Název tabulky v databázi';
$string['auth_dbtitle'] = 'Pou¾ití externí databáze';
$string['auth_dbtype'] = 'Typ databáze (Viz <A HREF=../lib/adodb/readme.htm#drivers>ADOdb dokumentaci</A>)';
$string['auth_dbuser'] = 'U¾ivatelské jméno s právy èíst externí databázi';
$string['auth_emaildescription'] = 'Potvrzení emailem je pøednastavená metoda ovìøování. Pøi registraci si u¾ivatel vybere vlastní u¾ivatelské jméno a heslo. Poté je na jeho adresu odeslán email obsahující zabezpeèený odkaz na stránku, kde potvrdí zadané údaje. Pøi dal¹ím pøihlá¹ení se ji¾ ovìøuje pouze zadané u¾ivatelské jméno a heslo proti hodnotì ulo¾ené v databázi Moodle.';
$string['auth_emailtitle'] = 'Ovìøení na základì emailu';
$string['auth_imapdescription'] = 'Tato metoda pou¾ívá IMAP server ke kontrole, zda zadané u¾ivatelské jméno a heslo je platné.';
$string['auth_imaphost'] = 'Adresa serveru IMAP. Zadejte IP adresu, nikoliv DNS jméno serveru!';
$string['auth_imapport'] = 'Èíslo portu IMAP servere. Vìt¹inou bývá 143 nebo 993.';
$string['auth_imaptitle'] = 'Pou¾ití IMAP serveru';
$string['auth_imaptype'] = 'Typ IMAP serveru.  IMAP servery mohou mít rùzné typy ovìøování a vyjednávání (IMAP authentication and negotiation).';
$string['auth_ldap_bind_dn'] = 'Chcete-li pou¾ívat metodu bind-user k vyhledání u¾ivatelù, specifikujte ji zde. Pøíklad: \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Heslo pro bind-user';
$string['auth_ldap_contexts'] = 'Seznam kontextù, ve kterých se nacházejí u¾ivatelé. Jednotlivé kontexty oddìlujte støedníkem. Pøíklad: \'ou=uzivatele,o=naseskola; ou=dalsi,o=naseskola\'';
$string['auth_ldap_create_context'] = 'Povolíte-li vytváøení u¾ivatelù (po ovìøení emailem), urèete kontext, ve kterém budou noví u¾ivatelé vytváøeni. Tento kontext by mìl být z bezpeènostních dùvodù odli¹ný od kontextu ostatních u¾ivatelù. Není tøeba pøidávat tento kontext do promìnné auth_ldap_contexts, Moodle automaticky hledá u¾ivatele i v tomto kontextu.';
$string['auth_ldap_creators'] = 'Seznam skupin, jejich¾ èlenové jsou oprávnìni vytváøet nové kurzy. Jednotlivé skupiny oddìlujte støedníkem. Pøíklad: \'cn=ucitele,ou=zamestnanci,o=naseskola\'';
$string['auth_ldap_host_url'] = 'Zadejte URL serveru LDAP. Napøíklad \'ldap://ldap.naseskola.cz/\' nebo \'ldaps://ldap.naseskola.cz/\' ';
$string['auth_ldap_memberattribute'] = 'Urèete atribut èlena skupiny (user member attribute), pokud u¾ivatel patøí do skupiny. Vìt¹inou \'member\'';
$string['auth_ldap_search_sub'] = 'Zadejte hodnotu <> 0 pokud chcete prohledávat u¾ivatele v subkontextech.';
$string['auth_ldap_update_userinfo'] = 'Aktualizovat informace o u¾ivateli (pøíjmení, køestní jméno, adresa...) z LDAP serveru do Moodle. Pro mapování viz /auth/ldap/attr_mappings.php';
$string['auth_ldap_user_attribute'] = 'Atribut pou¾itý pro pojmenování a vyhledávání u¾ivatelù. Vìt¹inou \'cn\'.';
$string['auth_ldap_version'] = 'Verze protokolu LDAP, kterou pou¾ívá vá¹ server.';
$string['auth_ldapdescription'] = 'Tato metoda poskytuje ovìøení u¾ivatele proti LDAP serveru. Je-li zadané jméno a heslo platné, Moodle si vytvoøí nový záznam o u¾ivateli ve své vlastní databázi. Tento modul dále umí naèíst informace z LDAP serveru a pøednastavit po¾adované pole v Moodle. Pøi dal¹ím pøihla¹ování se ji¾ pouze ovìøuje u¾ivatelské jméno a heslo.';
$string['auth_ldapextrafields'] = 'Tato pole jsou volitelná. Mù¾ete vybrat, která pole s informacemi z <B>LDAP serveru</B> budou pou¾ita jako pøednastavená v Moodle. <P>Necháte-li pole prázdná, nepøevezmou se ¾ádné údaje z LDAP a Moodle pou¾ije vlastní pøednastavené hodnoty. <P>V ka¾dém pøípadì si u¾ivatel mù¾e tyto hodnoty mìnit po pøihlá¹ení sám.';
$string['auth_ldaptitle'] = 'Pou¾ití LDAP serveru';
$string['auth_manualdescription'] = 'Tato metoda neumo¾òuje u¾ivatelùm zakládat si vlastní úèty. V¹echny úèty musí být ruènì vytvoøeny správcem Moodle (admin).';
$string['auth_manualtitle'] = 'Pouze ruènì vytváøené úèty';
$string['auth_multiplehosts'] = 'Mù¾ete vlo¾it i více hostitelù (napø. server1.cz;server2.cz;server3.com)';
$string['auth_nntpdescription'] = 'Tato metoda pou¾ívá NNTP server ke kontrole, zda zadané u¾ivatelské jméno a heslo je platné.';
$string['auth_nntphost'] = 'Adresa NNTP serveru. Zadejte IP adresu, nikoliv DNS název!';
$string['auth_nntpport'] = 'Èíslo portu NNTP serveru (vìt¹inou 119)';
$string['auth_nntptitle'] = 'Pou¾ití NNTP serveru';
$string['auth_nonedescription'] = 'U¾ivatelé si mohou vytváøet nová konta pøímo bez ovìøení vùèi externímu serveru nebo potvrzení pøes email. S touto volbou buïte opatrní - zva¾te mo¾né problémy se zabezpeèením a správou u¾ivatelù, které vám tato volba mù¾e zpùsobit.';
$string['auth_nonetitle'] = 'Bez ovìøení';
$string['auth_pop3description'] = 'Tato metoda pou¾ívá POP3 server ke kontrole, zda zadané u¾ivatelské jméno a heslo je platné.';
$string['auth_pop3host'] = 'Adresa POP3 serveru. Zadejte IP adresu, nikoliv DNS název!';
$string['auth_pop3port'] = 'Èíslo portu POP3 serveru (vìt¹inou 110)';
$string['auth_pop3title'] = 'Pou¾ití POP3 serveru';
$string['auth_pop3type'] = 'Typ serveru. Pokud vá¹ server pou¾ívá zabezpeèení pomocí certifikátù, zvolte pop3cert.';
$string['auth_user_create'] = 'Povolit vytváøení u¾ivatelù';
$string['auth_user_creation'] = 'Noví (anonymní!) u¾ivatelé si mohou zakládat u¾ivatelský úèet v externím zdroji a potvrdit jej pøes email. Pokud toto povolíte, nezapomeòte nakonfigurovat nastavení pro daný externí zdroj týkající se vytváøení nových u¾ivatelù.';
$string['auth_usernameexists'] = 'Zvolené u¾ivatelské jméno ji¾ existuje. Prosím, vyberte si jiné.';
$string['authenticationoptions'] = 'Mo¾nosti ovìøení';
$string['authinstructions'] = 'Zde mù¾ete zadat instrukce pro va¹e u¾ivatele, aby vìdìli, které u¾ivatelské jméno a heslo mají pou¾ít. Tento text se objeví na pøihla¹ovací stránce. Necháte-li toto pole prázdné, nebudou zobrazeny ¾ádné instrukce.';
$string['changepassword'] = 'URL ke zmìnì hesla';
$string['changepasswordhelp'] = 'Zde mù¾ete urèit URL, na kterém si va¹i u¾ivatelé mohou obnovit heslo nebo zmìnit své u¾ivatelské jméno, pokud jej zapomnìli. URL bude u¾ivatelùm poskytnuto jako tlaèitko na pøihla¹ovací a osobní stránce. Necháte-li toto pole prázdné, nebude toto tlaèítko zobrazováno.';
$string['chooseauthmethod'] = 'Vyberte si zpùsob ovìøení u¾ivatelù: ';
$string['guestloginbutton'] = 'Tlaèítko pro hosta';
$string['instructions'] = 'Pokyny';
$string['md5'] = 'MD5 ¹ifrování';
$string['plaintext'] = 'Èistý text';
$string['showguestlogin'] = 'Na pøihla¹ovací stránce mù¾ete skrýt nebo ukázat tlaèítko pro pøihlá¹ení se jako host.';

?>
