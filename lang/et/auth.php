<?PHP // $Id$ 
      // auth.php - created with Moodle 1.2 (2004032000)


$string['auth_dbdescription'] = 'See meetod kasutab välise andmebaasi tabelit, et kontrollida, kas antud kasutajanimi ja salasõna kehtivad. Kui tegemist on uue kontoga, siis võib Moodle\'isse kopeerida infot ka mujalt.';
$string['auth_dbextrafields'] = 'Need väljad on valikulised. Otsustage, kas soovite eeltäita mõned Moodle\'i väljad infoga <B>välisandmebaasidest väljadelt</B> mida täpsustate siin. <P>Kui jätate need tühjaks, kasutatatkse vaikeseadeid.<P>Mõlemal juhul on kasutajal võimalus redigeerida kõiki välju, kui ta on sisse loginud.';
$string['auth_dbfieldpass'] = 'Salasõna sisaldava välja nimi';
$string['auth_dbfielduser'] = 'Kasutajanime sisaldava välja nimi';
$string['auth_dbhost'] = 'Andmebaasi serveri arvuti.';
$string['auth_dbname'] = 'Andmebaasi enese nimi';
$string['auth_dbpass'] = 'Antud kasutajanimega sobiv salasõna.';
$string['auth_dbpasstype'] = 'Täpsusta formaati, mida salasõna väli kasutab.  MD5 krüpteerimine on kasulik, et ühendada teiste tavaliste veebirakendustega nagu PostNuke';
$string['auth_dbtable'] = 'Tabeli nimi andmebaasis';
$string['auth_dbtitle'] = 'Välise andmebaasi kasutamine';
$string['auth_dbtype'] = 'Andmebaasi tüüp(Vaata <A HREF=../lib/adodb/readme.htm#drivers>ADOdb dokumentatsiooni</A> et detaile täpsustada)';
$string['auth_dbuser'] = 'Kasutajanimi andmebaasile lugemiseks juurdepääsu tarvis ';
$string['auth_emaildescription'] = 'Emaili kinnitus on vaikimisi autentsuse kontrolli meetod. Kui kasutaja registreerub, valides omale uue kasutajanime ja salasõna, saadetakse tema emaili aadressile kinnituskiri. See email sisaldab turvalist linki lehele, kus kasutaja saab oma konto kinnitada. Edasipidised logimised üknnes kontrollivad kasutajanime ja salasõna, võrreldes neid Moodle\'I andmebaasis säilitatavatega.';
$string['auth_emailtitle'] = 'Emailil põhinev autentsuse kontroll';
$string['auth_imapdescription'] = 'See meetod kasutab IMAP serverit kontrollimaks, kas antud kasutajanimi ja salasõna kehtivad..';
$string['auth_imaphost'] = 'IMAP serveri aadress. Kasuta IP numbrit, mitte DNS nime.';
$string['auth_imapport'] = 'IMAP serveri pordi number. Tavaliselt on see 143 või  993.';
$string['auth_imaptitle'] = 'Kasuta IMAP serverit';
$string['auth_imaptype'] = 'IMAP serveri tüüp.  IMAP serveritel võib olla erinevat tüüpi autentsuse kontrolli ja loovutamist.';
$string['auth_ldap_bind_dn'] = 'Kui soovid kasutada bind-user kasutajate otsimiseks,täpsusta see siin. Näiteks \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Salasõna bind-user tarvis.';
$string['auth_ldap_contexts'] = 'Kontekstide loend, kus kasutajad paiknevad. Eralda erinevad kontekstid nii \';\'. Näiteks: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Kui võimaldad kasutajate tekitamist emaili konfiguratsioonis, täpsusta kontekst, milles kasutajaid tekitatakse.See kontekst peaks erinema teiste kasutajate omast,et ei tekiks turvaprobleeme.seda konteksti pole vaja lisada ldap_context-muutujale, Moodle otsib automaatselt sellest kontekstist kasutajaid.';
$string['auth_ldap_creators'] = 'Gruppide loend, kelle liikmetel on luba tekitada uusi kursusi. Eralda multi-grupid nii \';\'. Enamasti midagi sellist \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_host_url'] = 'Täpsusta LDAP host URL-formaadis nagu \'ldap://ldap.myorg.com/\' või \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_memberattribute'] = 'Täpsusta kasutaja liikme atribuut, kui kasutajad kuuluvad gruppi. Enamasti \'member\'';
$string['auth_ldap_search_sub'] = 'Määra väärtus &lt;&gt; 0 kui soovid kasutajaid alakontekstidest otsida.';
$string['auth_ldap_update_userinfo'] = 'Värskenda kasutajanifot (eesnimi, perekonnanimi, aadress,..) alates LDAP-ist kuni Moodle\'ni. Vaata /auth/ldap/attr_mappings.php kaardistamisinfo saamiseks';
$string['auth_ldap_user_attribute'] = 'Atribuut kasutajate nimetamiseks / otsimiseks. Enamasti \'cn\'.';
$string['auth_ldapdescription'] = 'See meetod tagab autentsuse kontrolli võrreldes välise LDAP serveriga.
Kui antud kasutajanimi ja salasõna kehtivad, tekitab Moodle uue kasutajakande oma andmebaasi.See moodul oskab lugeda kasutaja atribuute LDAP-ist ja eeltäita soovitud väljad Moodle\'is.  Logied jälgimiseks kontrollitakse üksnes kasutajanime ja salasõna.';
$string['auth_ldapextrafields'] = 'Need väljad pole kohustuslikud. Võid otsustada eeltäita mõned Moodle\'I kasutajaväljad infoga <B>LDAP väljadelt</B> mille täpsustad siin. <P> Kui jätad need väljad tühjaks, ei kanta LDAP\'ist midagi üle ja selle asemel kasutatakse Moodle\'I vaikeseadeid. <P> Mõlemil puhul tohib kasutaja redigeerida kõiki neid välju, kui ta on sisse loginud.';
$string['auth_ldaptitle'] = 'Kasuta LDAP serverit';
$string['auth_manualdescription'] = 'See meetod võtab kasutajatelt igasuguse võimaluse endale kontosid tekitada. Kõik kontod tuleb tekitada käsitsi admin. kasutaja poolt.';
$string['auth_manualtitle'] = 'Kontod ainult käsitsi';
$string['auth_multiplehosts'] = 'Mitu hosti saad kirjeldada lihtsalt (näiteks host1.ee;host2.ee;host3.ee)';
$string['auth_nntpdescription'] = 'See meetod kasutab NNTP serverit, et kontrollida, kas antud kasutajanimi ja salasõna kehtivad.';
$string['auth_nntphost'] = 'NNTP serveri aadress. Kasuta IP numbrit, mitte DNS nime.';
$string['auth_nntpport'] = 'Serveri port (119 on kõige tavalisem)';
$string['auth_nntptitle'] = 'Kasuta NNTP serverit';
$string['auth_nonedescription'] = 'Kasutaja võib end sisse kirjutada ja tekitada kehiva konto otsekohe, ilma autentsuse kontrollita välisserveri suhtes ja ilma emaili teel kinnitamata. Selle võimaluse kasutamisel ole ettevaatlik - mõtle turvalisusele ja haldamisprobleemidele, mida see võib tekitada.';
$string['auth_nonetitle'] = 'Ilma autentsuse kontrollita';
$string['auth_pop3description'] = 'See meetod kasutab POP3 serverit kontrollimaks, kas antud kasutajanimi ja salasõna kehtivad.';
$string['auth_pop3host'] = 'POP3 serveri aadress. Kasuta IP numbrit, mitte DNS nime.';
$string['auth_pop3port'] = 'Serveri port (110 on kõige tavalisem)';
$string['auth_pop3title'] = 'Kasuta POP3 serverit';
$string['auth_pop3type'] = 'Serveri tüüp. Kui sinu server kasutab turvasertifikaati, vali pop3cert.';
$string['auth_user_create'] = 'Luba tekitada kasutajaid';
$string['auth_user_creation'] = 'Uued(anonüümsed) kasutajad võivad luua kasutajakontosid välise autentsuse kontrolli allika kaudu ja saada kinnituse emaili teel. Kui seda lubad, ära unusta konfigureerida moodulspetsiifilisi valikuid kasutaja loomiseks.';
$string['auth_usernameexists'] = 'Valitud kasutajanimi on juba olemas. Palun vali uus.';
$string['authenticationoptions'] = 'Autentsuse kontrooli valikud';
$string['authinstructions'] = 'Siin võid instrueerida kasutajaid, et nad teaksid, millist kasutajanime ja salasõna nad peaksid kasutama. Siia sisestatud tekst ilmub logimislehel. Kui jätad selle välja tühjaks, ei trükita mingeid instruktsioone.';
$string['changepassword'] = 'Muuda salasõna URL';
$string['changepasswordhelp'] = 'Siin võid täpsustada asukohta, kus kasutajad saavad oma kasutajanime / salasõna taastada, kui see on ununenud. See antakse kasutajale klahvi kujul logimislehel ja tema kasutajalehel. Kui jätad selle tühjaks, siis klahvi ei trükita.';
$string['chooseauthmethod'] = 'Vali autentsuse kontrooli meetod: ';
$string['guestloginbutton'] = 'Külalise logimisklahv';
$string['instructions'] = 'Instruktsioonid';
$string['md5'] = 'MD5 krüpteering';
$string['plaintext'] = 'Lihttekst';
$string['showguestlogin'] = 'Võid peita või näidata külalisele logimisklahvi logimislehel.';

?>
