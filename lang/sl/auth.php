<?PHP // $Id$ 
      // auth.php - created with Moodle 1.3.1 (2004052501)


$string['auth_dbdescription'] = 'S to izbiro doloèite eksterno bazo podatkov za preverjanje veljavnosti uporabniškega imena in gesla. Pri novem raèunu za portal (moodle) je možno obstojeèe podatke iz baze prenesti (prekopirati).';
$string['auth_dbextrafields'] = 'Ta polja so izbirna. Vrednosti polj v portalu lahko vnaprej zapolnite <B>z vrednostmi polj v eksterni bazi</B>. Èe pustite polja prazna, bodo vpisane privzete vrednosti. Po prijavi sme uporabnik spreminjati vsa polja.';
$string['auth_dbfieldpass'] = 'Stolpec, ki vsebuje geslo';
$string['auth_dbfielduser'] = 'Stolpec, ki vsebuje uporabniško ime';
$string['auth_dbhost'] = 'Naziv raèunalnika (podatkovnega strežnika)';
$string['auth_dbname'] = 'Naziv (ime) baze podatkov';
$string['auth_dbpass'] = 'Geslo za navedeno uporabniško ime';
$string['auth_dbpasstype'] = 'Doloèite format uporabljenega gesla. Enkripcija po algoritmu MD5 je koristna za povezovanje z drugimi spletnimi rešitvami kot je npr. PostNuke';
$string['auth_dbtable'] = 'Naziv tabele v bazi ';
$string['auth_dbtitle'] = 'Uporaba eksterne baze';
$string['auth_dbtype'] = 'Tip baze podatkov (glej podrobnosti: <a href=\"../lib/adodb/readme.htm#drivers\">ADOdb documentation</a>)';
$string['auth_dbuser'] = 'Uporabniško ime s pravico branja baze';
$string['auth_emaildescription'] = 'Potrjevanje z epošto je privzeti naèin avtentikacije.  Ko uporabnik vnese novi ime in geslo, portal pošlje epošto na navedeni naslov. Sporoèilo vsebuje vareno povezavo na stran, kjer uporabnik potrdi svoj novi raèun. Pri vseh naslednjih prijavah portal preverja ime in geslo v svoji bazi.';
$string['auth_emailtitle'] = 'Na epošti temeljeèa avtentikacija';
$string['auth_imapdescription'] = 'Veljavnost uporabniškega imena in gesla preveri strežnik IMAP.';
$string['auth_imaphost'] = 'Naslov strežnika IMAP. Uporabite številko IP - ne DNS.';
$string['auth_imapport'] = 'Številka vrat strežnika IMAP (obièajno 143 ali 993).';
$string['auth_imaptitle'] = 'Uporabi strežnik IMAP';
$string['auth_imaptype'] = 'Tip strežnika IMAP.Strežniki IMAP lahko uporabljajo razliène naèine avtentikacije in dogovarjanja.';
$string['auth_ldap_bind_dn'] = 'Èe boste uporabljali LDAP, doloèite uporabnika npr. \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Geslo za LDAP.';
$string['auth_ldap_contexts'] = 'Doloèite sezname uporabnikov. Sezname loèite s \';\'. Primer: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Èe ste izbrali potrjevanje raèunov preko epošte, doloèite še mesto kreiranja. To mesto mora biti razlièno od drugih uporabnikov (iz varnostnih razlogov). Tega niza ni potrebno dodajati v spremenljivko ldap_context-variable, saj bo portal samodejno iskal uporabnike na tem mestu.';
$string['auth_ldap_creators'] = 'Seznam skupin uporabnikov, ki smejo kreirati nove predmete (npr.\'cn=teachers,ou=staff,o=myorg\'). Veè seznamov loèite s \';\'';
$string['auth_ldap_host_url'] = 'Doloèite URL LDAP strežnika (npr. \'ldap://ldap.myorg.com/\' or \'ldaps://ldap.myorg.com/\'';
$string['auth_ldap_memberattribute'] = 'Doloèite atribute èlana skupine (obièajno \'member\')';
$string['auth_ldap_search_sub'] = 'Doloèite vrednost <> 0 kadar išèete uporabnika v podkontekstu.';
$string['auth_ldap_update_userinfo'] = 'Spremeni podatke o uporabniku (ime, priimek, naslov..) na portalu iz strežnika LDAP (podrobnosti preslikave na  /auth/ldap/attr_mappings.php)';
$string['auth_ldap_user_attribute'] = 'Atribut za imenovanje/iskanje uporabnika (obièajno \'cn\').';
$string['auth_ldap_version'] = 'Verzija protokola LDAP na strežniku.';
$string['auth_ldapdescription'] = 'Ta naèin omogoèa avtentikacijo preko strežnika LDAP.
                                  Èe je vpisano uporabniško ime in geslo veljavno, portal kreira novega uporabnika 
                                  v svoji bazi. Ta modul bere uporabnikove atribute v LDAP in jih napolni v polja portala. 
                                  Pri naslednjih prijavah se preverjajo l ime in geslo.';
$string['auth_ldapextrafields'] = 'Ta polja so izbirna.  Vnaprej prenesene vrednosti iz <B>LDAP polj</B> doloèite tukajt. <P>Èe pustite polja prazna, se niè ne prenese. Vpisane bodo privzete vrednosti.<P>V vsakem primeru bodo uporabniki smeli spreminjati svoje podatke ob naslednjih prijavah.';
$string['auth_ldaptitle'] = 'Uporabi strežnik LDAP';
$string['auth_manualdescription'] = 'S tem pristopom uporabniki ne morejo kreirali lastnih raèunov. Vse raèune mora roèno kreirati administrator.';
$string['auth_manualtitle'] = 'Le roèni uporabniški raèuni';
$string['auth_multiplehosts'] = 'Doloèite lahko veè strežnikov(npr. host1.com;host2.com;host3.com)';
$string['auth_nntpdescription'] = 'Veljavnost uporabniškega imena in gesla preveri strežnik NNTP.';
$string['auth_nntphost'] = 'Naslov strežnika NNTP. Uporabite številko IP - ne DNS.';
$string['auth_nntpport'] = 'Številka vrat strežnika NNTP (obièajno 119).';
$string['auth_nntptitle'] = 'Uporabi strežnik  NNTP.';
$string['auth_nonedescription'] = 'Uporabniki takoj pridobijo uporabniški raèun - brez avtentikacije v eksterni bazi in brez potrjevanja z epošto.  Ta pristop ni priporoèljiv iz varnostnih in administrativnih razlogov.';
$string['auth_nonetitle'] = 'Brez avtentikacije';
$string['auth_pop3description'] = 'Veljavnost uporabniškega imena in gesla preveri strežnik POP3';
$string['auth_pop3host'] = 'Naslov strežnika POP3. Uporabite številko IP - ne DNS.';
$string['auth_pop3port'] = 'Številka vrat strežnika POP3 (obièajno 110).';
$string['auth_pop3title'] = 'Uporabi strežnik POP3';
$string['auth_pop3type'] = 'Tip strežnika. Èe strežnik uporablja varnostni certifikat, izberite pop3cert.';
$string['auth_user_create'] = 'Kreiranje uporabnikov omogoèeno';
$string['auth_user_creation'] = 'Novi (anonimni) uporabniki smejo kreirati raèune na ekstrenih avtentikacijskih virih in jih potrjevati z epošto. Èe to dovolite, doloèite še posebne opcije za kreiranje uporabnikov';
$string['auth_usernameexists'] = 'Izbrano ime že obstaja. Doloèite novega.';
$string['authenticationoptions'] = 'Izbire avtentikacije';
$string['authinstructions'] = 'Na tem mestu napišite navodila za kreiranje uporabniških raèunov. To besedilo se prikaže na strani za prijavo.  Èe ne napišete navodil, potem bo stran prazna.';
$string['changepassword'] = 'Spremeni geslo URL';
$string['changepasswordhelp'] = 'Doloèite mesto za obnovo ali spremembo imena/gesla, èe ga/ju uporabnik pozabi. Na strani za prijavo se izpiše gumb. Èe pustite prazno, se gumb ne bo izpisal.';
$string['chooseauthmethod'] = 'Izberite naèin avtentikacije: ';
$string['guestloginbutton'] = 'Gumb za prijavo gosta';
$string['instructions'] = 'Navodila';
$string['md5'] = 'Enkripcija MD5';
$string['plaintext'] = 'tekst';
$string['showguestlogin'] = 'Gumb za prijavo gosta lahko skrijete ali prikažete na vstopni strani.';

?>
