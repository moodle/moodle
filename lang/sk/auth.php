<?PHP // $Id$ 
      // auth.php - created with Moodle 1.6 development (2005060201)


$string['alternatelogin'] = 'Pokiaµ sem vlo¾íte nejaké URL, bude pou¾ité ako prihlasovacia stránka k tomuto systému. Táto Va¹a stránka by mala obsahova» formulár s vlastnos»ou \'action\' nastavenou na <strong>\'$a\'</strong>, ktorá vracia pole <strong>username</strong> a <strong>password</strong>.<br />Dbajte na to, aby ste vlo¾ili platné URL! V opaènom prípade by ste mohli komukoµvek vrátane seba zamedzi» prístup k týmto stránkam.<br />Ak chcete pou¾íva» ¹tandardnú prihlasovaciu stránku, nechajte toto pole prázdne.';
$string['alternateloginurl'] = 'Alternatívne URL pre prihlásenie';
$string['auth_cas_baseuri'] = 'URI serveru (alebo niè, pokiaµ nie je baseUri)<br />Ak je napr. CAS server dostupný na  host.domena.sk/CAS/ potom nastavte<br />cas_baseuri = CAS/';
$string['auth_cas_create_user'] = 'Pokiaµ chcete vlo¾i» do Moodle databázy pou¾ívateµov overených pomocou CAS, musíte zapnú» túto voµbu. Pokiaµ ju nezapnete, budú sa môc» prihlási» len tí pou¾ívatelia, ktorí u¾ existujú v databáze Moodle.';
$string['auth_cas_enabled'] = 'Pokiaµ chcete pou¾íva» overovanie pomocou CAS, musíte zapnú» túto voµbu.';
$string['auth_cas_hostname'] = 'Hostiteµské meno (hostname) CAS serveru<br />napr. host.domena.sk';
$string['auth_cas_invalidcaslogin'] = 'Prepáète, nepodarilo sa Vám prihlási» - nebolo mo¾né Vás overi»';
$string['auth_cas_language'] = 'Zvolený  jazyk';
$string['auth_cas_logincas'] = 'Prístup cez zabezpeèené spojenie';
$string['auth_cas_port'] = 'Port CAS serveru';
$string['auth_cas_server_settings'] = 'Konfigurácia CAS serveru';
$string['auth_cas_text'] = 'Zabezpeèené spojenie';
$string['auth_cas_version'] = 'Verzia CAS';
$string['auth_casdescription'] = 'Táto metóda pou¾íva CAS server (Central Authentication Service) pre overovanie pou¾ívateµov v prostredí jednotného systému prihlasovania (Single Sign On - SSO). Tie¾ mô¾ete pou¾i» jednoduché LDAP overovanie. Pokiaµ je zadané meno a heslo platné na serveri CAS, Moodle vytvorí záznam pre nového pou¾ívateµa v databáze, prièom si potrebné pou¾ívateµské údaje vezme z databázy LDAP. Pri nasledujúcich prihláseniach sú u¾ kontrolované len prihlasovacie meno a heslo.';
$string['auth_castitle'] = 'Pou¾i» CAS server (SSO)';
$string['auth_common_settings'] = 'Be¾né nastavenia';
$string['auth_data_mapping'] = 'Mapovanie údajov';
$string['auth_dbdescription'] = 'Táto metóda vyu¾íva tabuµku v externej databáze na kontrolu platnosti daného pou¾ívateµského mena a hesla. Ak je to nové konto, mô¾u by» do prostredia Moodle skopírované informácie aj z iných políèok.';
$string['auth_dbextrafields'] = 'Tieto políèka sú nepovinné. Je tu mo¾nos», aby niektoré pou¾ívateµské políèka v prostredí Moodle uvádzali informácie z <b>políèok externých databáz</b>, ktoré tu zadáte.<br />
Ak toto políèko necháte prázdne, bude tu uvádzané pôvodné nastavenie.<br /><p>
V obidvoch prípadoch, bude môc» pou¾ívateµ po prihlásení upravova» v¹etky tieto políèka.</p>';
$string['auth_dbfieldpass'] = 'Názov políèka obsahujúceho heslá';
$string['auth_dbfielduser'] = 'Názov políèka obsahujúceho pou¾ívateµské mená';
$string['auth_dbhost'] = 'Poèítaè hos»ujúci databázový server.';
$string['auth_dbname'] = 'Názov databázy';
$string['auth_dbpass'] = 'Heslo pre uvedeného pou¾ívateµa';
$string['auth_dbpasstype'] = '©pecifkujte formát, ktorý pou¾íva políèko s heslom. MD5 ¹ifrovanie je vhodné pre pripojenie k ïal¹ím be¾ným web aplikáciám ako PostNuke';
$string['auth_dbtable'] = 'Názov tabuµky v databáze';
$string['auth_dbtitle'] = 'Pou¾i» externú databázu';
$string['auth_dbtype'] = 'Databázový typ (bli¾¹ie viï <a href=\"../lib/adodb/readme.htm#drivers\">ADOdb dokumentácia</a>)';
$string['auth_dbuser'] = 'Pou¾ívateµské meno s prístupom do databázy len na èítanie.';
$string['auth_emaildescription'] = 'Emailové potvrdzovanie je prednastavený spôsob overovania. Keï sa pou¾ívateµ prihlási, vyberie si vlastné nové pou¾ívateµské meno a heslo. Následne dostane potvrdzujúci email na svoju emailovú adresu. Tento email obsahuje bezpeèný odkaz na stránku, kde mô¾e pou¾ívateµ potvrdi» svoje nastavené údaje. Pri ïal¹ích prihlasovaniach sa u¾ iba skontroluje pou¾ívateµské meno a heslo v porovnaní s údajmi ulo¾enými v Moodle databáze.';
$string['auth_emailtitle'] = 'Emailové overovanie';
$string['auth_fccreators'] = 'Zoznam skupín, ktorých èlenovia majú oprávnenie na vytváranie nových kurzov. Ak ide o viaceré skupiny, oddeµte ich bodkoèiarkou. Mená musia by» napísané presne tak, ako na FirstClass serveri. Systém zohµadòuje písanie malých a veµkých písmen.';
$string['auth_fcdescription'] = 'Táto metóda pou¾íva FirstClass server na skontrolovanie správnosti pou¾ívateµského mena a hesla.';
$string['auth_fcfppport'] = 'Port servera (3333 je najbe¾nej¹í)';
$string['auth_fchost'] = 'Adresa FirstClass servera. Pou¾ite IP adresu alebo meno DNS.';
$string['auth_fcpasswd'] = 'Heslo pre hore uvedený pou¾ívateµský úèet.';
$string['auth_fctitle'] = 'Pou¾i» FirstClass server';
$string['auth_fcuserid'] = 'ID pou¾ívateµa pre úèet na FirstClass serveri s nastavením privilégia \'Vedµaj¹í administrátor\'.';
$string['auth_fieldlock'] = 'Zamknú» hodnotu';
$string['auth_fieldlock_expl'] = '<p><b>Zamknú» hodnotu:</b>Ak je voµba aktivovaná, bude zabraòova» priamemu upravovaniu políèok pou¾ívateµmi a administrátormi Moodle. Pou¾ite túto voµbu, ak spravujete údaje v externom overovacom systéme.</p>';
$string['auth_fieldlocks'] = 'Zamknú» políèka pou¾ívateµov';
$string['auth_fieldlocks_help'] = '	<p>Mô¾ete zamknú» údaje v políèkach pou¾ívateµov. Toto je u¾itoèné najmä na tých stránkach , kde sú údaje pou¾ívateµov spravované administrátormi ruène, prostredníctvom upravovania ich záznamov alebo ich prenesenia cez voµbu \'Prenies» pou¾ívateµov\'. Ak zamknete políèka, ktoré sú vy¾adované Moodle, uistite sa, ¾e pri vytváraní pou¾ívateµských úètov a &emdash, potom poskytnete v¹etky potrebné údaje; v opaènom prípade budú úèty nepou¾iteµné.</p><p>Odporúèame zvá¾i» mo¾nos» nastavenia re¾imu zamkýnania na \'Odomknuté, ak prázdne\', aby ste sa vyhli tomuto problému.</p>';
$string['auth_imapdescription'] = 'Na kontrolu správnosti daného pou¾ívateµského mena a hesla pou¾íva táto metóda IMAP server.';
$string['auth_imaphost'] = 'Adresa IMAP serveru. Pou¾ívajte èíslo IP, nie názov DNS.';
$string['auth_imapport'] = 'Èíslo portu IMAP serveru . Zvyèajne je to 143 alebo 993.';
$string['auth_imaptitle'] = 'Pou¾i» IMAP server';
$string['auth_imaptype'] = 'Typ IMAP serveru. IMAP servery mô¾u pou¾íva» rozlièné typy overovania.';
$string['auth_ldap_bind_dn'] = 'Ak chcete pou¾íva» spoluu¾ívateµov na vyhµadávanie pou¾ívateµov, uveïte to tu. Napríklad: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_bind_pw'] = 'Heslo pre spoluu¾ívateµa.';
$string['auth_ldap_bind_settings'] = 'Spoloèné nastavenia ';
$string['auth_ldap_contexts'] = 'Zoznam kontextov, v ktorých sa nachádzajú pou¾ívatelia. Oddeµte rozlièné kontexty bodkoèiarkou. Napríklad: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Ak umo¾níte vytváranie pou¾ívateµov s emailovým potvrdzovaním, ¹pecifikujte kontext, kde budú pou¾ívatelia vytvorení. Tento kontext by mal by» iný, ako pre ostatných pou¾ívateµov, v záujme bezpeènosti. Nepotrebujete prida» tento kontext do premennej ldap-context, Moodle bude vyhµadáva» pou¾ívateµov z tohto kontextu automaticky.<br />
<b>Pozor!</b> Musíte upravi» funkciu auth_user_create() v súbore auth/ldap/lib.php, aby mohli by» takýmto spôsobom vytváraní noví pou¾ívatelia.';
$string['auth_ldap_creators'] = 'Zoznam skupín, ktorých èlenovia majú povolené vytvára» nové kurzy. Jednotlivé skupiny oddeµujte bodkoèiarkou. Obyèajne nieèo ako cn=ucitelia,ou=ostatni,o=univ\'';
$string['auth_ldap_expiration_desc'] = 'Vyberte si \'Nie\', aby sa deaktivovalo kontrolovanie neaktívneho hesla alebo LDAP na èítanie \'passwordexpiration time\' priamo z LDAP';
$string['auth_ldap_expiration_warning_desc'] = 'Poèet dní pred tým, ako sa objaví upozornenie o vypr¹aní platnosti hesla.';
$string['auth_ldap_expireattr_desc'] = 'Nepovinné: toto potlaèí ldap-vlastnosti, ktoré uchovávajú  èas do vypr¹ania hesla  passwordExpirationTime';
$string['auth_ldap_graceattr_desc'] = 'Nepovinné: Potlaèí vlastnos» gracelogin';
$string['auth_ldap_gracelogins_desc'] = 'Umo¾ni» podporu LDAP gracelogin (tzv. prihlásenie z milosti). Po tom, ako vypr¹í platnos» hesla, pou¾ívateµ sa mô¾e prihlási», pokým nie je hodnota gracelogin 0. Ak povolíte toto nastavenie, pou¾ívatelia budú informovaní, v prípade, ¾e im vypr¹í platnos» hesla.';
$string['auth_ldap_host_url'] = '©pecifikujte hostiteµa LDAP v podobe URL, napr. \'ldap://ldap.myorg.com/\' alebo \'ldaps://ldap.myorg.com/\'. Jednotlivé servery oddeµte bodkoèiarkou.';
$string['auth_ldap_login_settings'] = 'Nastavenia prihlasovania';
$string['auth_ldap_memberattribute'] = 'Nepovinné: voµba potlaèí názov atribútu èlena skupiny, ak pou¾ívateµ patrí do skupiny. Obyèajne je to \'member\'';
$string['auth_ldap_objectclass'] = 'Nepovinné: voµba potlaèí funkciu objectClass pou¾ívanú na vyhµadávanie pou¾ívateµov na ldap_user_type. Zvyèajne túto voµbu nepotrebujete meni».';
$string['auth_ldap_opt_deref'] = 'Táto voµba urèuje, ako sa zaobchádza s aliasmi pri vyhµadávaní. Vyberte jednu z nasledujúcich hodnôt: \"Nie\"(LDAP_DEREF_NEVER) alebo \"Áno\"(LDAP_DEREF_ALWAYS)';
$string['auth_ldap_passwdexpire_settings'] = 'LDAP nastavenia pri vypr¹aní platnosti hesla.';
$string['auth_ldap_search_sub'] = 'Uveïte hodnotu <> 0, ak chcete hµada» pou¾ívateµov v subkontextoch.';
$string['auth_ldap_server_settings'] = 'LDAP nastavenia servera';
$string['auth_ldap_update_userinfo'] = 'Aktualizova» informácie o pou¾ívateµovi (krstné meno, priezvisko, adresa...) z LDAP do Moodle. Hµada» v /auth/ldap/attr_mappings.php pre priraïujúce informácie. Ak potrebujete, definujte nastavenia pre \"Mapovanie údajov\". ';
$string['auth_ldap_user_attribute'] = 'Nepovinné: voµba potlaèí vlastnos» pou¾ívanú na hµadanie mien pou¾ívateµov. Zvyèajne \'cn\'.';
$string['auth_ldap_user_settings'] = 'Nastavenia prehµadávania pou¾ívateµov';
$string['auth_ldap_user_type'] = 'Vyberte si, ako budú pou¾ívatelia uchovávaní v LDAP. Toto nastavenie tie¾ ¹pecifikuje, ako bude fungova» vytváranie nových pou¾ívateµov, grace loginy a vypr¹anie platnosti hesla.';
$string['auth_ldap_version'] = 'Verzia LDAP protokolu, ktorú pou¾íva vá¹ server';
$string['auth_ldapdescription'] = 'Táto metóda poskytuje overovanie pou¾ívateµov proti  LDAP serveru. 

Ak je pou¾ívateµské meno a heslo správne, Moodle vytvorí nového pou¾ívateµa vo svojej databáze. 	  

Tento modul doká¾e naèíta» pou¾ívateµské vlastnosti z LDAP a predvyplni» po¾adované políèka v Moodle. 

Pre nasledujúce prihlasovania sa kontrolujú u¾ iba pou¾ívateµské meno a heslo.';
$string['auth_ldapextrafields'] = 'Tieto políèka sú nepovinné. Mô¾ete predvyplni» niektoré políèka v profile pou¾ívateµa v Moodle s informáciami z <b>LDAP políèok</b>, ktoré tu uvediete.
<p>Ak tu niè neuvediete, informácie z LDAP nebudú prenesené a namiesto toho bude uvádzané ¹tandardné Moodle nastavenie.</p>
<p>V obidvoch prípadoch bude môc» pou¾ívateµ po prihlásení upravova» v¹etky tieto políèka.</p>';
$string['auth_ldaptitle'] = 'Pou¾i» LDAP server';
$string['auth_manualdescription'] = 'Táto metóda neumo¾òuje pou¾ívateµom vytvára» vlastné kontá. V¹etky kontá musí ruène vytvori» administrátor.';
$string['auth_manualtitle'] = 'Len ruène vytvorené kontá';
$string['auth_multiplehosts'] = 'Tu mô¾u by» ¹pecifikovaní viacerí hostitelia ALEBO ich adresy (napr. host1.com;host2.com;host3.com)alebo (napr.xxx.xxx.xxx.xxx;xxx.xxx.xxx.xxx)';
$string['auth_nntpdescription'] = 'Táto metóda pou¾íva na kontrolu správnosti pou¾ívateµského mena a hesla NNTP server.';
$string['auth_nntphost'] = 'Adresa NNTP serveru. Pou¾ite èíslo IP, nie názov DNS.';
$string['auth_nntpport'] = 'Port serveru (119 je najbe¾nej¹í)';
$string['auth_nntptitle'] = 'Pou¾i» NNTP server';
$string['auth_nonedescription'] = 'Pou¾ívatelia sa mô¾u prihlási» a okam¾ite si vytvori» kontá bez nutnosti overovania proti externým serverom a bez potvrdzovania prostredníctvom emailu. Buïte opatrní pri tejto voµbe - myslite na bezpeènos» a problémy pri administrácii, ktoré tým mô¾u vzniknú».';
$string['auth_nonetitle'] = 'Bez overenia';
$string['auth_pamdescription'] = 'Táto metóda pou¾íva PAM na prístup do pou¾ívateµských mien na tomto serveri. Musíte si nain¹talova» <a href=\"http://www.math.ohio-state.edu/~ccunning/pam_auth/\">PHP4 PAM Authentication</a>, aby ste mohli pou¾íva» tento modul.';
$string['auth_pamtitle'] = 'PAM (Pluggable Authentication Modules)';
$string['auth_passwordisexpired'] = 'Platnos» Vá¹ho hesla vypr¹ala. Chcete si zmeni» Va¹e heslo teraz?';
$string['auth_passwordwillexpire'] = 'Platnos» Vá¹ho hesla vypr¹í o $a dní. Chcete si zmeni» Va¹e heslo teraz?';
$string['auth_pop3description'] = 'Táto metóda pou¾íva na kontrolu správnosti pou¾ívateµského mena a hesla POP3 server.';
$string['auth_pop3host'] = 'Adresa POP3 serveru. Pou¾ite èíslo IP, nie názov DNS.';
$string['auth_pop3mailbox'] = 'Názov po¹tovej schránky, s ktorou by mohol by» nadviazaný kontakt. (väè¹inou prieèinok doruèenej po¹ty INBOX)';
$string['auth_pop3port'] = 'Port serveru  (110 je najbe¾nej¹í)';
$string['auth_pop3title'] = 'Pou¾íva» POP3 server';
$string['auth_pop3type'] = 'Typ serveru. Ak Vá¹ server pou¾íva zabezpeèenie pomocou certifikátu, vyberte si pop3cert.';
$string['auth_shib_convert_data'] = 'API pre úpravu údajov';
$string['auth_shib_convert_data_description'] = 'Toto API (aplikaèné rozhranie) Vám umo¾òuje ïalej upravova» údaje, ktoré máte k dispozícii zo systému Shibboleth. Viac infomácií <a href=\"../auth/shibboleth/README.txt\" target=\"_blank\">nájdete tu</a>.';
$string['auth_shib_convert_data_warning'] = 'Súbor neexistuje alebo k nemu proces web serveru nemá prístup na èítanie!';
$string['auth_shib_instructions'] = 'Pou¾ite <a href=\"$a\">prihlásenie cez Shibboleth</a>, pokiaµ Va¹a in¹titúcia tento systém podporuje.<br />V opaènom prípade pou¾ite normálny formulár pre prihlásenie.';
$string['auth_shib_instructions_help'] = 'Tu mô¾ete vlo¾i» vlastné informácie o Va¹om systéme Shibboleth. Budú sa zobrazova» na prihlasovacej stránke. Vlo¾ené informácie by mali obsahova» odkaz na zdroj chránený systémom Shibboleth, ktorý presmeruje pou¾ívateµov na \"<b>$a</b>\", tak¾e sa pou¾ívatelia systému Shibboleth budú môc» prihlási» do Moodle. Ak necháte toto pole prázdne, budú sa na prihlasovacej stránke zobrazova» v¹eobecné pokyny.';
$string['auth_shib_only'] = 'Len Shibboleth';
$string['auth_shib_only_description'] = 'Za¹krtnite túto voµbu, pokiaµ si chcete nastavi» prihlásenie za pomoci systému Shibboleth';
$string['auth_shib_username_description'] = 'Názov premennej prostredia webserveru Shibboleth, ktorá má by» pou¾itá ako pou¾ívateµské meno Moodle ';
$string['auth_shibboleth_login'] = 'Prihlásenie cez Shibboleth';
$string['auth_shibboleth_manual_login'] = 'Ruèné prihlásenie';
$string['auth_shibbolethdescription'] = 'Táto metóda umo¾òuje vytvára» a overova» pou¾ívatelov pomocou systému <a href=\"http://shibboleth.internet2.edu/\" target=\"_blank\">Shibboleth</a>.<br />
Uistite sa, ¾e ste si preèítali súbor <a href=\"../auth/shibboleth/README.txt\" target=\"_blank\">README</a> obsahujúci informácie o tom, ako nastavi» Vá¹ Moodle pre podporu systému Shibboleth.';
$string['auth_shibbolethtitle'] = 'Shibboleth';
$string['auth_updatelocal'] = 'Aktualizova» miestne údaje';
$string['auth_updatelocal_expl'] = '<p><b>Aktualizova» miestne údaje:</b> Ak je táto voµba aktívna, políèko bude aktualizované (z externého overovacieho zdroja) zaka¾dým, keï sa pou¾ívateµ prihlási, alebo pri synchronizácii pou¾ívateµov. Políèka urèené na miestnu aktualizáciu by mali by» uzamknuté.</p>';
$string['auth_updateremote'] = 'Aktualizova» externé údaje';
$string['auth_updateremote_expl'] = '<p><b>Aktualizova» externé údaje:</b> Ak je táto voµba aktívna, externý overovací zdroj bude aktualizovaný zaka¾dým, keï dôjde k aktualizácii profilu pou¾ívateµa. Políèka urèené na miestnu aktualizáciu by nemali by» uzamknuté, aby sa mohli upravova».</p>';
$string['auth_updateremote_ldap'] = '<p><b>Poznámka:</b> Aktualizácia externých LDAP údajov si vy¾aduje nastavenie binddn a bindpw spoluu¾ívateµom s právom úpravy v¹etkých záznamov o pou¾ívateµoch. Momentálne systém nepodporuje vlastnosti s viacerými hodnotami a pri aktualizácii sa preto odstránia nadbytoèné hodnoty.</p>';
$string['auth_user_create'] = 'Umo¾ni» vytváranie pou¾ívateµov';
$string['auth_user_creation'] = 'Noví (anonymní) pou¾ívatelia mô¾u vytvára» pou¾ívateµské kontá v externom zdroji a overova» ich cez email. Ak to umo¾níte, nezabudnite tie¾ konfigurova» ¹pecifické voµby pre vytváranie pou¾ívateµských úètov v danom externom zdroji.';
$string['auth_usernameexists'] = 'Zvolené pou¾ívateµské meno u¾ existuje. Prosím, vyberte si iné.';
$string['authenticationoptions'] = 'Mo¾nosti overovania';
$string['authinstructions'] = 'Tu mô¾ete uvies» pokyny pre pou¾ívateµov, aby vedeli, aké pou¾ívateµské meno a heslo majú pou¾íva». Text, ktorý tu vlo¾íte sa objaví na prihlasovacej stránke. Ak to tu neuvediete, nebudú zobrazené ¾iadne pokyny.';
$string['changepassword'] = 'URL na zmenu hesla ';
$string['changepasswordhelp'] = 'Tu mô¾ete uvies» URL, na ktorom si Va¹i pou¾ívatelia mô¾u obnovi» alebo zmeni» pou¾ívateµské meno/heslo, ak ho zabudli. Pre pou¾ívateµov to bude zobrazené ako tlaèidlo na prihlasovacej stránke ich pou¾ívateµskej stránky. Ak to tu neuvediete, tlaèidlo sa nezobrazí.';
$string['chooseauthmethod'] = 'Vyberte si spôsob overovania pou¾ívateµov: ';
$string['createchangepassword'] = 'Vytvori», ak chýba - je nutné zmeni»';
$string['createpassword'] = 'Vytvori», ak chýba';
$string['forcechangepassword'] = 'Vy¾adova» zmenu hesla';
$string['forcechangepassword_help'] = 'Vy¾adova» od pou¾ívateµov zmenu hesla pri ich ïal¹om prihlásení do Moodle.';
$string['forcechangepasswordfirst_help'] = 'Vy¾adova» od pou¾ívateµov zmenu hesla pri ich prvom prihlásení do Moodle.';
$string['guestloginbutton'] = 'Prihlasovacie tlaèidlo pre hos»a';
$string['infilefield'] = 'Políèko vy¾adované v súbore';
$string['instructions'] = 'In¹trukcie';
$string['locked'] = 'Zamknutý/Zamknuté';
$string['md5'] = 'MD5 ¹ifrovanie';
$string['passwordhandling'] = 'Zaobchádzanie s políèkom s heslom';
$string['plaintext'] = 'Èistý text';
$string['showguestlogin'] = 'Mô¾ete skry», alebo zobrazi», prihlasovacie tlaèidlo pre hos»a na prihlasovacej stránke.';
$string['stdchangepassword'] = 'Pou¾i» ¹tandardnú stránku pre zmenu hesla';
$string['stdchangepassword_expl'] = 'Ak Vá¹ externý overovací systém povoµuje zmeny hesla v prostredí Moodle, prepnite túto voµbu na \"Áno\". Toto nastavenie potlaèí funkciu \"URL na zmenu hesla\".';
$string['stdchangepassword_explldap'] = 'Poznámka: Ak pou¾ívate vzdialený LDAP server, odporúèame Vám komunikova» cez ¹ifrované SSL spojenie (ldaps://).';
$string['unlocked'] = 'Odomknutý/Odomknuté';
$string['unlockedifempty'] = 'Odomknuté, ak prázdne';
$string['update_never'] = 'Nikdy';
$string['update_oncreate'] = 'Pri vytváraní';
$string['update_onlogin'] = 'Pri ka¾dom prihlásení';
$string['update_onupdate'] = 'Pri aktualizácii';

?>
