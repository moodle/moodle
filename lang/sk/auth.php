<?PHP // $Id$ Radik Kalakay - 2003-09-05 12:00 ver 1.1.a
      // auth.php - created with Moodle 1.1 (2003082800)


$string['auth_dbdescription'] = "Táto metóda vyu¾íva externú databázovú tabuµku na kontrolu platnosti daného u¾ívateµského mena a hesla. Ak je to nové konto mô¾u by» do prostredia Moodle prenesené informácie aj z inýcho políèok.";
$string['auth_dbextrafields'] = "Tieto políèka sú nepovinné. Je tu mo¾nos», aby niektoré u¾ívateµské políèka v prostredí Moodle uvádzali informácie z <B>políèok externých databáz</B>, ktoré tu udáte. <P>Ak tu niè neuvediete, bude uvádzané pôvodné nastavenie.<P>V obidvoch prípadoch bude môc» u¾ívateµ po prihlásení upravova» v¹etky tieto políèka.";
$string['auth_dbfieldpass'] = "Názov políèka obsahujúceho heslá";
$string['auth_dbfielduser'] = "Názov políèka obsahujúceho u¾ívateµské mená";
$string['auth_dbhost'] = "Poèítaè hos»ujúci databázový server";
$string['auth_dbname'] = "Vlastný názov databázy";
$string['auth_dbpass'] = "Heslo pre uvedeného u¾ívateµa";
$string['auth_dbpasstype'] = "©pecifkujte formát, ktorý pou¾íva políèko pre heslo. MD5 ¹ifrovanie je vhodné pre pripojenie k ïal¹ím be¾ným web aplikáciám ako PostNuke";
$string['auth_dbtable'] = "Názov tabuµky v databáze";
$string['auth_dbtitle'] = "Pou¾i» externú databázu";
$string['auth_dbtype'] = "Databázový typ (bli¾¹ie viï<A HREF=../lib/adodb/readme.htm#drivers>ADOdb dokumentácia</A> )";
$string['auth_dbuser'] = "U¾ívateµské meno s prístupom do databázy len na èítanie.";
$string['auth_emaildescription'] = "Emailové potvrdzovanie je prednastavený spôsob overovania. Keï sa u¾ívateµ prihlási, vyberie si vlastné nové u¾ívateµské meno a heslo a dostane potvrdzovací email na svoju emailovu adresu. Tento email obsahuje bezpeènostnú linku na stránku, kde mô¾e u¾ívateµ potvrdi» svoje nastavenie. Pri ïal¹ích prihlasovaniach iba skontroluje u¾ívateµské meno a heslo v porovnaní s údajmi ulo¾enými v Moodle databáze.";
$string['auth_emailtitle'] = "Emailové overovanie";
$string['auth_imapdescription'] = "Na kontrolu správnosti daného u¾ívateµského mena a hesla pou¾íva táto metóda IMAP server.";
$string['auth_imaphost'] = "Adresa IMAP serveru. Pou¾ívajte èíslo IP, nie názov DNS.";
$string['auth_imapport'] = "Èíslo IMAP server portu. Zvyèajne je to 143 alebo 993.";
$string['auth_imaptitle'] = "Pou¾i» IMAP server";
$string['auth_imaptype'] = "Typ IMAP serveru.  IMAP servery mô¾u ma» rozlièné typy overovania.";
$string['auth_ldap_bind_dn'] = "Ak chcete pou¾íva» spoluu¾ívateµov, aby ste mohli hµada» u¾ívateµov uveïte to tu. Napríklad: 'ou=users,o=org; ou=others,o=org'";
$string['auth_ldap_bind_pw'] = "Heslo pre spoluu¾ívateµov.";
$string['auth_ldap_contexts'] = "Zoznam prostredí, kde sa machádzajú u¾ívatelia. Oddeµte rozlièné prostredia s ';'. Napríklad: 'ou=users,o=org; ou=others,o=org'";
$string['auth_ldap_create_context'] = "Ak umo¾níte vytváranie u¾ívateµov s eailovým potvrdzovaním, ¹pecifikujte kontext, kde budú u¾ívatelia vytvorení. Tento kontext by mal by» iný, ako pre ostatných u¾ívateµov v záujme bezpeènosti. Nepotrebujete prida» tento kontext fo premennej ldap-context, Moodle bude vyhµadáva» u¾ívateµov z tohto kontextu automaticky.";
$string['auth_ldap_creators'] = "Zoznam skupín, ktorých èlenovia majú dovolené vytvára» nové kurzy. Jednotlivé skupiny oddeµujte bodkoèiarkou. Obyèajne nieèo ako cn=ucitelia,ou=ostatni,o=univ'";
$string['auth_ldap_host_url'] = "©pecifikujte hostiteµa LDAP v podobe URL tj. 'ldap://ldap.myorg.com/' alebo 'ldaps://ldap.myorg.com/' ";
$string['auth_ldap_memberattribute'] = "©pecifikujte èlenský atribút u¾ívateµa, keï u¾ívatelia patria do skupín; obyèajne je to 'member'";
$string['auth_ldap_search_sub'] = "Uveïte hodnotu &lt;&gt; 0 ak chcete hµada» u¾ívateµov v subkontextoch.";
$string['auth_ldap_update_userinfo'] = "Aktualizova» informácie o u¾ívateµovi (krstné meno, priezvisko, adresa,...) z LDAP do Moodle. Hµada» v /auth/ldap/attr_mappings.php pre priraïujúce informácie.";
$string['auth_ldap_user_attribute'] = "Vlastnos» pou¾ívaná na hµadanie mien u¾ívateµov. Zvyèajne 'cn'.";
$string['auth_ldapdescription'] = "Táto metóda poskytuje overovanie s LDAP serverom. 

Ak je u¾ívateµské meno a heslo správne, Moodle vytvorí nového u¾ívateµa v svojej databáze. 	  Tento modul doká¾e èíta» u¾ívateµské vlastnosti z LDAP a vyplni» ¾elané políèka v Moodle. 

Pre nasledujúce prihlasovania sa kontrolujú iba u¾ívateµské meno a heslo.";
$string['auth_ldapextrafields'] = "Tieto políèka sú nepovinné. Je taká mo¾nos», ¾e Moodle u¾ívateµské políèka budú uvádza» informácie z <B>LDAP políèok</B> ,ktoré tu udáte. <P>Ak tu niè neuvediete, informácie z LDAP nebudú prevedené, a namiesto toho bude uvádzané Moodle nastavenie. <P>V obidvoch prípadoch bude môc» u¾ívateµ po prihlásení korigova» v¹etky tieto políèka.";
$string['auth_ldaptitle'] = "Pou¾i» LDAP server";
$string['auth_manualdescription'] = "Táto metóda neumo¾òuje u¾ívateµom vytvára» vlastné kontá. V¹etky kontá musí manuálne vytvori» administrátor.";
$string['auth_manualtitle'] = "Len manuálne kontá";
$string['auth_nntpdescription'] = "Tento postup pou¾íva na kontrolu správnosti u¾ívateµského mena a hesla NNTP server.";
$string['auth_nntphost'] = "Adresa NNTP servera. Pou¾ite èíslo IP, nie názov DNS.";
$string['auth_nntpport'] = "Server port (119 je najbe¾nej¹í)";
$string['auth_nntptitle'] = "Pou¾i» NNTP server";
$string['auth_nonedescription'] = "U¾ívatelia sa mô¾u prihlási» a vytvori» kontá bez overovania s externým serverom a bez potvrdzovania prostredníctvom emailu. Buïte opatrní pri tejto voµbe - myslite na bezpeènos» a problémy pri administrácii, ktoré tým mô¾u vzniknú».";
$string['auth_nonetitle'] = "®iadne overenie";
$string['auth_pop3description'] = "Tento postup pou¾íva  na kontrolu správnosti u¾ívateµského mena a hesla POP3 server.";
$string['auth_pop3host'] = "Adresa POP3 servera. Pou¾ite èíslo IP , nie názov DNS.";
$string['auth_pop3port'] = "Server port (110 je najbe¾nej¹í)";
$string['auth_pop3title'] = "Pou¾íva» POP3 server";
$string['auth_pop3type'] = "Typ servera. Ak vá¹ server pou¾íva certifikované zabezpeèenie, vyberte si pop3cert.";
$string['auth_user_create'] = "Umo¾ni» vytváranie u¾ívateµov";
$string['auth_user_creation'] = "Noví (anonymní) u¾ívatelia mô¾u vytvára» u¾ívateµské kontá v externom prostredí a overova» ich cez e=mail. Ak to umo¾níte, nezabudnite tie¾ skonfigurova» ¹pecifické voµby pre jednotlivé moduly.";
$string['auth_usernameexists'] = "Vybrané u¾ívateµské meno u¾ existuje. Prosím, vyberte si iné.";
$string['authenticationoptions'] = "Mo¾nosti overovania";
$string['authinstructions'] = "Tu mô¾ete uvies» pokyny pre u¾ívateµov, aby vedeli, aké u¾ívateµské meno a heslo majú pou¾íva». Text, ktorý tu vlo¾íte sa objaví na prihlasovacej stránke. Ak to tu neuvediete, nebudú zobrazené ¾iadne pokyny.";
$string['changepassword'] = "Zmeni» heslo URL";
$string['changepasswordhelp'] = "Tu mô¾ete uvies» miesto, na ktorom si va¹i u¾ívatelia mô¾u pripomenú», alebo zmeni» u¾ívateµské meno/heslo, ak ho zabudli. Pre u¾ívateµov to bude zobrazené ako tlaèidlo na prihlasovacej stránke ich u¾ívateµskej stránky. Ak to tu neuvediete, tlaèidlo sa nezobrazí.";
$string['chooseauthmethod'] = "Vyberte si postup overovania: ";
$string['guestloginbutton'] = "Prihlasovacie tlaèidlo pre hos»a";
$string['instructions'] = "In¹trukcie";
$string['md5'] = "MD5 ¹ifrovanie";
$string['plaintext'] = "Èistý text";
$string['showguestlogin'] = "Mô¾ete skry», alebo zobrazi» prihlasovacie tlaèidlo pre hos»a na prihlasovacej stránke.";

?>
