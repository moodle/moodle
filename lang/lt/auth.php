<?PHP // $Id$ 
      // auth.php - created with Moodle 1.1.1 (2003091111)


$string['auth_dbdescription'] = "Ðis bûdu naudojama iðorinë duomenø bazës lentelë patikrinti ar duoti vartotojo vardas ir slaptaþodis yra teisingi. Jei tai naujas vartotojas, tai informacija ið kitø laukø irgi bûti nukopijuota tiesiai á Moodle.";
$string['auth_dbextrafields'] = "Ðie laukai nëra privalomi. Jûs galite pasirinkti, kad kai kurie Moodle vartotojo laukai bûtø uþpildyti informacija ið iðorinës duomenø bazës laukø, kurios èia nurodysite. Jei paliksite tuðèius laukus, tada bus pasirinkta informacija pagal nutylëjimà. Kitu atveju vartotojas savo informacijà papildyti kai prisijunks vëliau.";
$string['auth_dbfieldpass'] = "Lauko vardas, kuriame yra slaptaþodis";
$string['auth_dbfielduser'] = "Lauko vardas, kuriame yra vartotojo vardas";
$string['auth_dbhost'] = "Kompiuterio adresas, kuris laiko duomenø bazæ.";
$string['auth_dbname'] = "Duomenø bazës vardas";
$string['auth_dbpass'] = "Password matching the above username";
$string['auth_dbpasstype'] = "Nurodykite kokio formato slaptaþodis yra slaptaþodþio lauke. MD5 kodavimas yra naudingas jungentis prie kitø interneto programø (tokiø kaip PostNuke)";
$string['auth_dbtable'] = "Lentelës pavadinimas duomenø bazëje";
$string['auth_dbtitle'] = "Naudoti iðorinæ duomenø bazæ";
$string['auth_dbtype'] = "Duomenø bazës tipas (Þiûrëk smulkiau<A HREF=../lib/adodb/readme.htm#drivers>ADOdb dokumentacija</A>)";
$string['auth_dbuser'] = "Vartotojo vardas su skaitymo teise duomenø bazei";
$string['auth_emaildescription'] = "El. paðtu patvirtinama autentiðkumas yra áprastas bûdas. Kai vartotojas uþsiregistruoja, pasirinkdamas vartotojo vardà ir slaptaþodá, patvirtinimo laiðkas nusiunèiamas vartotojo el. paðto adresu. Laiðke bûna saugi nuoroda á puslapá, kuriame vartotojas gali patvirtinti savo uþsiregistravimà. Kiti prisijungimai reikalauja tik vartotojo vardo ir jo slaptaþodþio, kurie yra saugomi Moodle duomenø bazëje.";
$string['auth_emailtitle'] = "El. paðtu pagrásta autentifikacija";
$string['auth_imapdescription'] = "Ðiuo bûdu duoti vartotojo vardas ir slaptaþodis yra tikrinami IMAP serveryje ir nustatoma ar jie teisingi.";
$string['auth_imaphost'] = "IMAP serverio adresas. Naudokite IP adresa, o ne DNS vardà.";
$string['auth_imapport'] = "IMAP servario port'as. Daþniausiai tai yra 143 arba 993.";
$string['auth_imaptitle'] = "Naudoti IMAP serverá";
$string['auth_imaptype'] = "IMAP serverio tipas. IMAP serveriai gali turëti skirtingus autentikavimo bûdus.";
$string['auth_ldap_bind_dn'] = "If you want to use bind-user to search users, specify it here. Someting like 'cn=ldapuser,ou=public,o=org'";
$string['auth_ldap_bind_pw'] = "Password for bind-user.";
$string['auth_ldap_contexts'] = "Sàraðas, ið kurios vietos kilæ vartotojai. Atskirkite skirtingus sàraðus su kabletaðkiu ';'. Pvz.: 'ou=user,o=org; ou=other,o=org'";
$string['auth_ldap_create_context'] = "Jei pasirinkote vartotojø kûrimà su el. paðto patvirtinimu, nurodykite sàraðà, kur vartotojas yra sukurtas. Jis turëtø bûti kitoks nei kitø vartotojø, saugumo sumetimais. Nebûtina nurodyti sàraðo ldap_context kintamajam, Moodle automatiðkai tai padarys uþ jus.";
$string['auth_ldap_creators'] = "Grupiø sàraðas kuriø nariai gali kurti naujus kursus.";
$string['auth_ldap_host_url'] = "Nurodykite LDAP adresà URL forma.";
$string['auth_ldap_memberattribute'] = "Nurodykite vartotojo nario atributà, kai jis priklauso grupei.";
$string['auth_ldap_search_sub'] = "Put value &lt;&gt; 0 if  you like to search users from subcontexts.";
$string['auth_ldap_update_userinfo'] = "Atnaujinti vartotojo informacijà (vardas, pavardë, adresas..) ið LDAP á Moodle.";
$string['auth_ldap_user_attribute'] = "Atributas naudojamas rasti vartotojui. Daþniausiai 'cn'.";
$string['auth_ldapdescription'] = "Ðis bûdas leidþia atlikti autentifikacijà naudojant iðoriná LDAP serverá. Jei duotas vartotojo vardas ir slaptaþodis yra teisingi, Moodle sukurs naujà vartotojà savo duomenø bazëje. Ðis modulis gali skaityti vartotojo atributus ið LDAP ir pildyti norimus Moodle laukus. Kitiems prisijungimams tik vartotojo vardas ir slaptaþodis yra tikrinami.";
$string['auth_ldapextrafields'] = "Ðie laukai yra nebûtini. Jûs galite pasirinkti, kad Moodle pati uþpildytø juos informacija ið <B>LDAP laukø</B>, kuriuos èia nurodysite. <P>Jei laukus paliksite tuðèius, tai jokia informacija nebus atsiøsta ið LDAP ir Moodle pati uþpildys áprastinëmis vertëmis. <P>Abiem atvejais, vartotojai galës keisti pateiktà informacijà, kai tik prisijunks.";
$string['auth_ldaptitle'] = "Naudoti LDAP serverá";
$string['auth_manualdescription'] = "Ðis bûdas neleidþia jokiems vartotojamas registruotis. Visi registravimai turi bûti atlikti administratoriaus.";
$string['auth_manualtitle'] = "Tiktai rankinis vartotojø registravimas";
$string['auth_nntpdescription'] = "Ðis bûdas naudoja NNTP serverá patikrinti ar vartotojo vardas ir slaptaþodis teisingi.";
$string['auth_nntphost'] = "NNTP serverio adresas. Naudokite IP adresa, o ne DNS vardà.";
$string['auth_nntpport'] = "NNTP servario port'as. Daþniausiai tai yra 119.";
$string['auth_nntptitle'] = "Naudoti NNTP serverá";
$string['auth_nonedescription'] = "Vartotojai gali registruotis ir kurti vartotojø vardus, nenaudojaunt jokios autentikavimo sistemos. Bukite atsargûs - pagalvokite apie saugumà.";
$string['auth_nonetitle'] = "Neautentikuojamas";
$string['auth_pop3description'] = "Ðis bûdas naudoja POP3 serverá patikrinti ar vartotojo vardas ir slaptaþodis teisingi.";
$string['auth_pop3host'] = "POP3 serverio adresas. Naudokite IP adresa, o ne DNS vardà.";
$string['auth_pop3port'] = "POP3 servario port'as. Daþniausiai tai yra 110.";
$string['auth_pop3title'] = "Naudoti POP3 serverá";
$string['auth_pop3type'] = "Serverio tipas. Jei jûsø serveris naudoja sertifikavimo saugumo sistemà, pasirinkite pop3cert.";
$string['auth_user_create'] = "Aktyvuoti vartotojø kûrimà";
$string['auth_user_creation'] = "Nauji (anoniminiai) vartotojai gali kûrti vartotojø vardus su iðoriniu autentifikavimo ðalitiniu ir el. paðto patvirtinimu. Jei aktyvuosite, nepamirðkite konfiguruoti modulá 'specifinës nuostatos vartotojø kûrimui'.";
$string['auth_usernameexists'] = "Pasirinktas vartotojo vardas jau uþregistruotas. Pasirinkite kità.";
$string['authenticationoptions'] = "Autentifikavimo nuostatos";
$string['authinstructions'] = "Èia galite raðyti nurodymus savo vartotojams, kad jie þinotø koká vartotojà ir koká slaptaþodá jiems naudoti. Ðis tekstas matysis prisijungimo lange. Jei nieko neraðysite, nurodymai nebus spaustdinami.";
$string['changepassword'] = "Pakeisti slaptaþodþio URL";
$string['changepasswordhelp'] = "Èia galite nurodyti vietà, kur vartotojai galëtø atstatyti ar pakeisti vartotojo vardà ir slaptaþodá, jei juos pamirðo.";
$string['chooseauthmethod'] = "Pasirinkite autentifikavimo bûdà:";
$string['guestloginbutton'] = "Sveèio prisijungimo mygtukas";
$string['instructions'] = "Nurodymai";
$string['md5'] = "MD5 kodavimas";
$string['plaintext'] = "Paprastas tekstas";
$string['showguestlogin'] = "Jûs galite paslëpti ir rodyti sveèio prisijungimo mygtuka prisijungimo puslapyje.";

?>
