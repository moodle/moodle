<?PHP // $Id$ 
      // auth.php - created with Moodle 1.5 ALPHA (2005051500)


$string['alternatelogin'] = 'Pokiaµ sem vlo¾íte nejaké URL, bude pou¾ité ako prihlasovacia stránka k tomuto systému. Táto Va¹a stránka by mala obsahova» formulár s vlastnos»ou \'action\' nastavenou na <strong>\'$a\'</strong>, ktorý vracia pole <strong>username</strong> a <strong>password</strong>.<br />Dbajte na to, aby ste vlo¾ili platné URL! V opaènom prípade by ste mohli komukoµvek vrátane seba zamedzi» prístup k týmto stránkam.<br />Ak chcete pou¾íva» ¹tandardnú prihlasovaciu stránku, nechajte toto pole prázdne.';
$string['alternateloginurl'] = 'Alternatívne URL pre prihlásenie';
$string['auth_cas_baseuri'] = 'URI serveru (alebo niè, pokiaµ nie je baseUri)<br />Ak je napr. CAS server dostupný na  host.domena.sk/CAS/ potom nastavte<br />cas_baseuri = CAS/';
$string['auth_cas_create_user'] = 'Pokiaµ chcete vlo¾i» CAS autentifikovaných pou¾ívateµov do Moodle databázy, musíte zapnú» túto voµbu. Pokiaµ ju nezapnete, budú sa môc» prihlási» len pou¾ívatelia ktorí u¾ existujú v databáze Moodle.';
$string['auth_cas_enabled'] = 'Pokiaµ chcete pou¾íva» CAS autentifikáciu, musíte zapnú» túto voµbu';
$string['auth_cas_hostname'] = 'Adresa CAS serveru<br />napr. server.domena.sk';
$string['auth_cas_invalidcaslogin'] = 'Prepáète, nepodarilo sa Vám prihlási» - nemohli ste by» autorizovaný';
$string['auth_cas_language'] = 'Vybraný jazyk';
$string['auth_cas_logincas'] = 'Zabezpeè spojenie';
$string['auth_cas_port'] = 'Port CAS serveru';
$string['auth_cas_server_settings'] = 'Konfigurácia CAS serveru';
$string['auth_cas_text'] = 'Zabezpeèené spojenie';
$string['auth_cas_version'] = 'Verzia CAS';
$string['auth_casdescription'] = 'Táto metóda pou¾íva CAS server (Central Authentication Service) pre autentifikáciu u¾ívateµov v prostredí jednotného systému prihlasovania (Single Sign On - SSO). Tie¾ mô¾ete pou¾i» jednoduchú LDAP autentifikáciu. Pokiaµ je zadané meno a heslo platné oproti CAS, Moodle vytvorí nového u¾ívateµa v databáze, prièom si potrebné u¾ívateµské údaje, vezme z databázy LDAP. Pri nasledujúcich prihláseniach sú u¾ kontrolované len prihlasovacie meno a heslo.';
$string['auth_castitle'] = 'Pou¾i» CAS server (SSO)';
$string['auth_common_settings'] = 'Be¾né nastavenia';
$string['auth_data_mapping'] = 'Zobrazenie údajov';
$string['auth_dbdescription'] = 'Táto metóda vyu¾íva externú databázovú tabuµku na kontrolu platnosti daného u¾ívateµského mena a hesla. Ak je to nové konto, mô¾u by» do prostredia Moodle prenesené informácie aj z iných políèok.';
$string['auth_dbextrafields'] = 'Tieto políèka sú nepovinné. Je tu mo¾nos», aby niektoré u¾ívateµské políèka v prostredí Moodle uvádzali informácie z <b>políèok externých databáz</b>, ktoré tu zadáte.<br />
Ak toto políèko necháte prázdne, bude tu uvádzané pôvodné nastavenie.<br />
V obidvoch prípadoch, bude môc» u¾ívateµ po prihlásení upravova» v¹etky tieto políèka.';
$string['auth_dbfieldpass'] = 'Názov políèka obsahujúceho heslá';
$string['auth_dbfielduser'] = 'Názov políèka obsahujúceho u¾ívateµské mená';
$string['auth_dbhost'] = 'Poèítaè hos»ujúci databázový server';
$string['auth_dbname'] = 'Vlastný názov databázy';
$string['auth_dbpass'] = 'Heslo pre uvedeného u¾ívateµa';
$string['auth_dbpasstype'] = '©pecifkujte formát, ktorý pou¾íva políèko pre heslo. MD5 ¹ifrovanie je vhodné pre pripojenie k ïal¹ím be¾ným web aplikáciám ako PostNuke';
$string['auth_dbtable'] = 'Názov tabuµky v databáze';
$string['auth_dbtitle'] = 'Pou¾i» externú databázu';
$string['auth_dbtype'] = 'Databázový typ (bli¾¹ie viï <a href=\"../lib/adodb/readme.htm#drivers\">ADOdb dokumentácia</a>)';
$string['auth_dbuser'] = 'U¾ívateµské meno s prístupom do databázy len na èítanie.';
$string['auth_editlock'] = 'Uzamknutá hodnota';
$string['auth_editlock_expl'] = '<p><b>Uzamknutá hodnota:</b>Ak je akívna, zabráni tomu, aby u¾ívatelia a administrátori Moodle priamo upravovali toto políèko. Túto voµbu pou¾ite, ak uchovávate údaje v externom auth systéme.</p>';
$string['auth_emaildescription'] = 'Emailové potvrdzovanie je prednastavený spôsob overovania. Keï sa u¾ívateµ prihlási, vyberie si vlastné nové u¾ívateµské meno a heslo a dostane potvrdzujúci email na svoju emailovú adresu. Tento email obsahuje bezpeènú linku na stránku, kde mô¾e u¾ívateµ potvrdi» svoje nastavenie. Pri ïal¹ích prihlasovaniach iba skontroluje u¾ívateµské meno a heslo v porovnaní s údajmi ulo¾enými v Moodle databáze.';
$string['auth_emailtitle'] = 'Emailové overovanie';
$string['auth_fccreators'] = 'Zoznam skupín, ktorých èlenovia majú oprávnenie na vytváranie nových kurzov. Ak ide o viaceré skupiny, oddeµte ich \';\'. Mená musia bz» napísané presne tak, ako na FirstClass serveri. Systém zohµadòuje písanie malých a veµkých písmen.';
$string['auth_fcdescription'] = 'Táto metóda pou¾íva FirstClass server na skontrolovanie správnosti pou¾ívateµského mena a hesla.';
$string['auth_fcfppport'] = 'Port servera (3333 je najbe¾nej¹í)';
$string['auth_fchost'] = 'Adresa FirstClass servera. Pou¾ite IP adresu alebo meno DNS.';
$string['auth_fcpasswd'] = 'Heslo pre hore uvedený u¾ívateµský úèet';
$string['auth_fctitle'] = 'Pou¾i» FirstClass server';
$string['auth_fcuserid'] = 'Odstránenie u¾ívateµského úètu z FirstClass servera s nastavením privilégia \'Vedµaj¹í administrátor\'.';
$string['auth_imapdescription'] = 'Na kontrolu správnosti daného u¾ívateµského mena a hesla pou¾íva táto metóda IMAP server.';
$string['auth_imaphost'] = 'Adresa IMAP serveru. Pou¾ívajte èíslo IP, nie názov DNS.';
$string['auth_imapport'] = 'Èíslo IMAP server portu. Zvyèajne je to 143 alebo 993.';
$string['auth_imaptitle'] = 'Pou¾i» IMAP server';
$string['auth_imaptype'] = 'Typ IMAP serveru.  IMAP servery mô¾u ma» rozlièné typy overovania.';
$string['auth_ldap_bind_dn'] = 'Ak chcete pou¾íva» spoluu¾ívateµov, aby ste mohli hµada» u¾ívateµov uveïte to tu. Napríklad: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_bind_pw'] = 'Heslo pre spoluu¾ívateµov.';
$string['auth_ldap_bind_settings'] = 'Spoloèné nastavenia ';
$string['auth_ldap_contexts'] = 'Zoznam prostredí, kde sa nachádzajú u¾ívatelia. Oddeµte rozlièné prostredia s \';\'. Napríklad: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Ak umo¾níte vytváranie u¾ívateµov s emailovým potvrdzovaním, ¹pecifikujte kontext, kde budú u¾ívatelia vytvorení. Tento kontext by mal by» iný, ako pre ostatných u¾ívateµov v záujme bezpeènosti. Nepotrebujete prida» tento kontext do premennej ldap-context, Moodle bude vyhµadáva» u¾ívateµov z tohto kontextu automaticky.<br />
<b>Pozor!</b> Musíte upravi» funkciu auth_user_create() v súbore auth/ldap/lib.php, aby mohli by» vytvorení noví u¾ívatelia.';
$string['auth_ldap_creators'] = 'Zoznam skupín, ktorých èlenovia majú dovolené vytvára» nové kurzy. Jednotlivé skupiny oddeµujte bodkoèiarkou. Obyèajne nieèo ako cn=ucitelia,ou=ostatni,o=univ\'';
$string['auth_ldap_expiration_desc'] = 'Vyberte si \"Nie\", aby sa deaktivovalo kontrolovanie neaktívneho hesla alebo LDAP na èítanie passwordexpiration èasu priamo z LDAP';
$string['auth_ldap_expiration_warning_desc'] = 'Poèet dní pred tým, ako sa objaví upozornenie o vypr¹aní platnosti hesla';
$string['auth_ldap_expireattr_desc'] = 'Nepovinné: potlaèí ldap-vlastnosti, ktoré uchovávajú  èas do vypr¹ania hesla  asswordAxpirationTime';
$string['auth_ldap_graceattr_desc'] = 'Nepovinné: Potlaèí vlastnos» gracelogin';
$string['auth_ldap_gracelogins_desc'] = 'Umo¾ni» podporu LDAP gracelogin. Po tom, ako vypr¹í platnos» hesla, u¾ívateµ sa mô¾e prihlási», pokým nie je hodnota gracelogin 0. Aktiváciou tohto nastavenia zobrazíte správu o grace login, ak vypr¹í platnos» hesla.';
$string['auth_ldap_host_url'] = '©pecifikujte hostiteµa LDAP v podobe URL tj. \'ldap://ldap.myorg.com/\' alebo \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_login_settings'] = 'Nastavenia prihlasovania';
$string['auth_ldap_memberattribute'] = '©pecifikujte èlenský atribút u¾ívateµa, keï u¾ívatelia patria do skupín; obyèajne je to \'member\'';
$string['auth_ldap_objectclass'] = 'Nepovinné: potlaèí funkciu objectClass pou¾ívanú na hµadanie u¾ívateµov na ldap_user_type. Zvyèajne túto voµbu nepotrebujete meni».';
$string['auth_ldap_opt_deref'] = 'Táto voµba urèuje, ako sa zaobchádza s aliasmi pri hµadaní. Vyberte jednu z nasledujúcich hodnôt: \"Nie\"(LDAP_DEREF_NEVER) alebo \"Áno\"(LDAP_DEREF_ALWAYS)';
$string['auth_ldap_passwdexpire_settings'] = 'LDAP nastavenia pri vypr¹aní platnosti hesla';
$string['auth_ldap_search_sub'] = 'Uveïte hodnotu <> 0 ak chcete hµada» u¾ívateµov v subkontextoch.';
$string['auth_ldap_server_settings'] = 'LDAP nastavenia servera';
$string['auth_ldap_update_userinfo'] = 'Aktualizova» informácie o u¾ívateµovi (krstné meno, priezvisko, adresa...) z LDAP do Moodle. Hµada» v /auth/ldap/attr_mappings.php pre priraïujúce informácie.';
$string['auth_ldap_user_attribute'] = 'Vlastnos» pou¾ívaná na hµadanie mien u¾ívateµov. Zvyèajne \'cn\'.';
$string['auth_ldap_user_settings'] = 'Nastavenia vzhµadu u¾ívateµa';
$string['auth_ldap_user_type'] = 'Vyberte si, ako budú u¾ívatelia uchovávaní v LDAP. Toto nastavenie tie¾ ¹pecifikuje, ako bude fungova» vytváranie nových u¾ívateµov, grace logins a vypr¹anie platnosti hesla.';
$string['auth_ldap_version'] = 'Verzia LDAP protokolu ';
$string['auth_ldapdescription'] = 'Táto metóda poskytuje overovanie s LDAP serverom. 

Ak je u¾ívateµské meno a heslo správne, Moodle vytvorí nového u¾ívateµa v svojej databáze. 	  Tento modul doká¾e èíta» u¾ívateµské vlastnosti z LDAP a vyplni» ¾elané políèka v Moodle. 

Pre nasledujúce prihlasovania sa kontrolujú iba u¾ívateµské meno a heslo.';
$string['auth_ldapextrafields'] = 'Tieto políèka sú nepovinné. Je taká mo¾nos», ¾e Moodle u¾ívateµské políèka budú uvádza» informácie z <b>LDAP políèok</b> ,ktoré tu udáte.<br />
<p>Ak tu niè neuvediete, informácie z LDAP nebudú prevedené, a namiesto toho bude uvádzané Moodle nastavenie.</p>
<p>V obidvoch prípadoch bude môc» u¾ívateµ po prihlásení korigova» v¹etky tieto políèka.</p>';
$string['auth_ldaptitle'] = 'Pou¾i» LDAP server';
$string['auth_manualdescription'] = 'Táto metóda neumo¾òuje u¾ívateµom vytvára» vlastné kontá. V¹etky kontá musí manuálne vytvori» administrátor.';
$string['auth_manualtitle'] = 'Len manuálne kontá';
$string['auth_multiplehosts'] = 'Tu mô¾u by» ¹pecifikované viaceré host OR adresy (napr. host1.com;host2.com;host3.com)alebo (napr.xxx.xxx.xxx.xxx;xxx.xxx.xxx.xxx)';
$string['auth_nntpdescription'] = 'Tento postup pou¾íva na kontrolu správnosti u¾ívateµského mena a hesla NNTP server.';
$string['auth_nntphost'] = 'Adresa NNTP servera. Pou¾ite èíslo IP, nie názov DNS.';
$string['auth_nntpport'] = 'Server port (119 je najbe¾nej¹í)';
$string['auth_nntptitle'] = 'Pou¾i» NNTP server';
$string['auth_nonedescription'] = 'U¾ívatelia sa mô¾u prihlási» a vytvori» kontá bez overovania s externým serverom a bez potvrdzovania prostredníctvom emailu. Buïte opatrní pri tejto voµbe - myslite na bezpeènos» a problémy pri administrácii, ktoré tým mô¾u vzniknú».';
$string['auth_nonetitle'] = '®iadne overenie';
$string['auth_pamdescription'] = 'Táto metóda pou¾íva PAM na prístup do u¾ívateµských mien na tomto serveri. Musíte si nain¹talova» <a href=\"http://www.math.ohio-state.edu/~ccunning/pam_auth/\">PHP4 PAM Authentication</a>, aby ste mohli pou¾íva» tento modul.';
$string['auth_pamtitle'] = 'PAM (Pluggable Authentication Modules)';
$string['auth_passwordisexpired'] = 'Platnos» Vá¹ho hesla vypr¹ala. Chcete si zmeni» Va¹e heslo teraz?';
$string['auth_passwordwillexpire'] = 'Platnos» Vá¹ho hesla vypr¹í o $a dní. Chcete si zmeni» Va¹e heslo teraz?';
$string['auth_pop3description'] = 'Tento postup pou¾íva  na kontrolu správnosti u¾ívateµského mena a hesla POP3 server.';
$string['auth_pop3host'] = 'Adresa POP3 servera. Pou¾ite èíslo IP , nie názov DNS.';
$string['auth_pop3mailbox'] = 'Meno po¹tovej schránky, s ktorou by mohol by» nadviazaný kontakt (väè¹inou prieèinok doruèenej po¹ty)';
$string['auth_pop3port'] = 'Server port (110 je najbe¾nej¹í)';
$string['auth_pop3title'] = 'Pou¾íva» POP3 server';
$string['auth_pop3type'] = 'Typ servera. Ak vá¹ server pou¾íva certifikované zabezpeèenie, vyberte si pop3cert.';
$string['auth_shib_convert_data'] = 'API pre úpravu dát';
$string['auth_shib_convert_data_description'] = 'Toto API (aplikaèné rozhranie) Vám umo¾òuje ïalej upravova» dáta, ktoré máte k dispozícii zo systému Shibboleth. Viac infomácií <a href=\"../auth/shibboleth/README.txt\" target=\"_blank\">nájdete tu</a>.';
$string['auth_shib_instructions'] = 'Pou¾ite <a href=\"$a\">prihlásenie cez Shibboleth</a>, pokiaµ Va¹a in¹titúcia tento systém podporuje.<br />V opaènom prípade pou¾ite normálny formulár pre prihlásenie.';
$string['auth_shib_instructions_help'] = 'Tu mô¾ete vlo¾i» vlastné informácie o Va¹om systéme Shibboleth. Budú se zobrazova» na prihlasovacej stránke. Vlo¾ené informácie by maly obsahova» odkaz na zdroj chránený systémom Shibboleth, ktorý presmeruje pou¾ívateµov na &quot;<b>$a</b>&quot;, tak¾e sa pou¾ívatelia systému Shibboleth budú môc» prihlási» do Moodle. Ak ponecháte toto pole prázdne, budú se na prihlasovacej stránke zobrazova» v¹eobecné pokyny.';
$string['auth_shib_only'] = 'Len pre Shibboleth';
$string['auth_shib_only_description'] = 'Za¹krtnite túto voµbu, pokiaµ si chcete vynúti» prihlásenie za pomoci systému Shibboleth';
$string['auth_shib_username_description'] = 'Názov premennej prostredia webserveru Shibboleth, ktorá má by» pou¾itá ako u¾ívateµské meno Moodle ';
$string['auth_shibboleth_login'] = 'Prihlásenie cez Shibboleth';
$string['auth_shibboleth_manual_login'] = 'Ruèné prihlásenie';
$string['auth_shibbolethdescription'] = 'Táto metóda umo¾òuje vytvára» a overova» pou¾ívatelov pomocou systému <a href=\"http://shibboleth.internet2.edu/\" target=\"_blank\">Shibboleth</a>.<br />
Uistite sa, ¾e ste si preèítali súbor <a href=\"../auth/shibboleth/README.txt\" target=\"_blank\">README</a> obsahujúci informácie o tom, ako nastavi» vá¹ Moodle pre podporu systému Shibboleth.';
$string['auth_shibbolethtitle'] = 'Shibboleth';
$string['auth_updatelocal'] = 'Aktualizova» miestne údaje';
$string['auth_updatelocal_expl'] = '<p><b>Aktualizova» miestne údaje:</b> Ak je táto voµba aktívna, políèko bude aktualizované (z externej autentifikácie) zaka¾dým, keï sa u¾ívateµ prihlási, alebo je tu synchronizácia u¾ívateµa. Políèka, ktoré by sa mali miestne aktualizova», by mali by» uzamknuté.</p>';
$string['auth_updateremote'] = 'Aktualizova» externé údaje';
$string['auth_updateremote_expl'] = '<p><b>Aktualizova» externé údaje:</b> Ak je táto voµba aktívna, externá autentifikácia bude aktualizovaná, keï sa aktualizuje záznam o u¾ívateµovi. Políèka by nemali by» uzamknuté, aby sa mohli upravova».</p>';
$string['auth_updateremote_ldap'] = '<p><b>Poznámka:</b> Aktualizácia externých LDAP údajov si vy¾aduje nastavenie binddn a bindpw spoluu¾ívateµom s právom úpravy v¹etkých záznamov o u¾ívateµoch. Momentálne sa tu neuchovávajú vlastnosti viacerých hodnôt a pri aktualizácii sa odstránia nadbytoèné hodnoty.</p>';
$string['auth_user_create'] = 'Umo¾ni» vytváranie u¾ívateµov';
$string['auth_user_creation'] = 'Noví (anonymní) u¾ívatelia mô¾u vytvára» u¾ívateµské kontá v externom prostredí a overova» ich cez email. Ak to umo¾níte, nezabudnite tie¾ konfigurova» ¹pecifické voµby pre jednotlivé moduly.';
$string['auth_usernameexists'] = 'Vybrané u¾ívateµské meno u¾ existuje. Prosím, vyberte si iné.';
$string['authenticationoptions'] = 'Mo¾nosti overovania';
$string['authinstructions'] = 'Tu mô¾ete uvies» pokyny pre u¾ívateµov, aby vedeli, aké u¾ívateµské meno a heslo majú pou¾íva». Text, ktorý tu vlo¾íte sa objaví na prihlasovacej stránke. Ak to tu neuvediete, nebudú zobrazené ¾iadne pokyny.';
$string['changepassword'] = 'Zmeni» heslo URL';
$string['changepasswordhelp'] = 'Tu mô¾ete uvies» miesto, na ktorom si Va¹i u¾ívatelia mô¾u obnovi» alebo zmeni» u¾ívateµské meno/heslo, ak ho zabudli. Pre u¾ívateµov to bude zobrazené ako tlaèidlo na prihlasovacej stránke ich u¾ívateµskej stránky. Ak to tu neuvediete, tlaèidlo sa nezobrazí.';
$string['chooseauthmethod'] = 'Vyberte si postup overovania: ';
$string['forcechangepassword'] = 'Vy¾adova» zmenu hesla';
$string['forcechangepassword_help'] = 'Vy¾adova» od u¾ívateµov zmenu hesla pri ich ïal¹om prihlásení do Moodle';
$string['forcechangepasswordfirst_help'] = 'Vy¾adova» od u¾ívateµov zmenu hesla pri ich prvom prihlásení do Moodle';
$string['guestloginbutton'] = 'Prihlasovacie tlaèidlo pre hos»a';
$string['instructions'] = 'In¹trukcie';
$string['md5'] = 'MD5 ¹ifrovanie';
$string['plaintext'] = 'Èistý text';
$string['showguestlogin'] = 'Mô¾ete skry», alebo zobrazi» prihlasovacie tlaèidlo pre hos»a na prihlasovacej stránke.';
$string['stdchangepassword'] = 'Pou¾i» ¹tandardnú stránku pre zmenu hesla';
$string['stdchangepassword_expl'] = 'Ak externý autentifikaèný systém povoµuje zmeny hesla v prostredí Moodle, prepnite túto voµbu na \"Áno\". Toto nastavenie potlaèí funkciu \"Zmeni» heslo URL\".';
$string['stdchangepassword_explldap'] = 'Poznámka: Odporúèa sa pou¾ívanie LDAP cez SSL ¹ifrovací tunel (ldaps://), ak je LDAP server vzdialený.';

?>
