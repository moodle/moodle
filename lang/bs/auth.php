<?PHP // $Id$ 
      // auth.php - created with Moodle 1.4.3 + (2004083131)


$string['auth_common_settings'] = 'Opšta podešavanja';
$string['auth_data_mapping'] = 'Mapiranje podataka';
$string['auth_dbdescription'] = 'Ovaj metod koristi tabelu vanjske baze podataka za provjeru da li su dodijeljeno korisnièko ime i lozinka ispravni. Ako je nalog nov, onda informacija iz ostalih polja može biti kopirana u Moodle.';
$string['auth_dbextrafields'] = 'Ova polja su po izboru. Možete izabrati da prednapunite neka Moodle korisnièka polja sa informacijom iz <b>vanjska polja baze podataka</b> koja naznaèite ovdje.  <br />Ako ih ostavite prazne, onda æe biti korištene podrazumjevane.<br /> U svakom sluèaju, korisnik æe biti u moguænosti da ureðuje sva polja nakon prijavljivanja.';
$string['auth_dbfieldpass'] = 'Ime polja koje sadrži lozinke';
$string['auth_dbfielduser'] = 'Ime polja koje sadrži korisnièka imena';
$string['auth_dbhost'] = 'Raèunar koji hostuje server baze podataka.';
$string['auth_dbname'] = 'Ime baze podataka';
$string['auth_dbpass'] = 'Lozinka koja odgovara gore navedenom korisnièkom imenu';
$string['auth_dbpasstype'] = 'Zadajte format koje koristi polje za lozinku. MD5 šifrovanje je korisno prilikom povezivanja na ostale opšte mrežne aplikacije kao što je PostNuke.';
$string['auth_dbtable'] = 'Ime tabele u bazi podataka';
$string['auth_dbtitle'] = 'Upotrijebite vanjsku bazu podataka';
$string['auth_dbtype'] = 'Tip baze podataka (Pogledajte <a href=\"../lib/adodb/readme.htm#drivers\">ADOdb dokumentacija</a> za detalje)';
$string['auth_dbuser'] = 'Korisnièko ime sa pristupom èitanju korisnièke baze';
$string['auth_editlock'] = 'Pogledaj vrijednosti';
$string['auth_editlock_expl'] = '<p><b>Obuhvatanje vrijednosti:</b> Ako je omoguæena, korisnici i administratori Moodla æe se zaštititi od direktnih prepravki polja. Upotrijebi ovu opciju ako održavate ove podatake izvan sistema. </p>';
$string['auth_emaildescription'] = 'Potvrðivanje putem elektronske pošte je uobièajen naèin provjere. Nakon što se korisnik prijavi i izabere svoje novo ime i lozinku, elektronska pošta se šalje na adresu tog korisnika. U elektronskoj pošti se nalazi sigurnosni link prema stranici gdje novi korisnik potvrðuje svoj nalog. Svi buduæi upisi se samo provjeravaju u postojeæoj Moodle bazi podataka.';
$string['auth_emailtitle'] = 'Provjera putem elektronske pošte';
$string['auth_fccreators'] = 'Lista grupa èijim saradnicima je dozvoljeno da kreiraju nove kurseve. Odvojite spojene grupe sa \';\'. Imena moraju biti sroèena upravo onako kao ona na FirstClass serveru. Sistem je osjetljiv na promjene.';
$string['auth_fcdescription'] = 'Ova metoda koristi FisrtClass server za provjeru ako je dato korisnièko ime i lozinka validno.';
$string['auth_fcfppport'] = 'Serverski port (3333 je najèešæi)';
$string['auth_fchost'] = 'Adresa FirstClass servera. Koristi IP broj ili DSN ime.';
$string['auth_fcpasswd'] = 'Lozinka za otvoreni nalog.';
$string['auth_fctitle'] = 'Koristi  FirstClass server';
$string['auth_fcuserid'] = 'Korisnik FirstClass naloga sa kompletnom privilegijom \'podadministrator\'';
$string['auth_imapdescription'] = 'Ovaj metod koristi IMAP server da provjeri da li su dodijeljeno korisnièko ime i lozinka ispravni.';
$string['auth_imaphost'] = 'IMAP adresa servera. Koristi IP broj, a ne DNS nazive.';
$string['auth_imapport'] = 'Broj porta IMAP servera. Obièno je 143 ili 993.';
$string['auth_imaptitle'] = 'IMAP server';
$string['auth_imaptype'] = 'Tip IMAP servera. IMAP serveri mogu imati razlièite tipove provjere.';
$string['auth_ldap_bind_dn'] = 'Ako želite koristiti bind-korisnika za pretragu korisnika, odredite to ovdje. Nešto nalik  na \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Lozinka za bind-korisnika.';
$string['auth_ldap_bind_settings'] = 'Bind podešavanja';
$string['auth_ldap_contexts'] = 'Lista sadržaja gdje su korisnici locirani. Razdvojite razlièite sadržaje sa \';\'. Primjer : \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Ako omoguæite kreiranje korisnika sa email potvrdom, naznaèite sadržaj gdje su korisnici kreirani. Ovaj sadržaj bi trebao biti drugaèiji od ostalih korisnika da bi se sprijeèili sigurnosni problemi. Nema potrebe dodavati ovaj sadržaj u ldap_context-variable, jer æe Moodle potražiti korisnike iz tog sadržaja automatski.';
$string['auth_ldap_creators'] = 'Lista grupa èijim èlanovima je dozvoljeno kreiranje novih kurseva. Razdvojite višestruke grupe sa \';\'. Obièno nešto slièno \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_expiration_desc'] = 'Izaberite NO da onemoguæite provjeru isteka lozinke ili LDAP da uèitate vrijeme isteka lozinke direktno sa LDAP-a.';
$string['auth_ldap_expiration_warning_desc'] = 'Dodijeljeno je upozorenje brojem dana prije isteka lozinke.';
$string['auth_ldap_expireattr_desc'] = 'Mimoiðite LDAP atribute koji su potrebni za vrijeme isteka lozinke';
$string['auth_ldap_graceattr_desc'] = 'Mimoiðite prièek unosa atributa';
$string['auth_ldap_gracelogins_desc'] = 'Omoguæite LDAP podršku za prièek unosa. Nakom isteka lozinke korisnik se može logirati sve dok je iznos prièeka unosa 0. Ako je lozinka istekla omoguæite ova podešavanja za prikazivanje poruke prièeka unosa.';
$string['auth_ldap_host_url'] = 'Naznaèi LDAP host u URL-formi kao što je \'ldap://ldap.myorg.com/\' ili \'ldaps://ldap.myorg.com/\'';
$string['auth_ldap_login_settings'] = 'Podešavanje unošenja naziva korisnika';
$string['auth_ldap_memberattribute'] = 'Odreðuje korisnikov èlanski pridjev, kad korisnik pripada grupi. Obièno \'member\'';
$string['auth_ldap_objectclass'] = 'Odreðuje objectClass koristeæi ime/tražeæi korisnika na ldap_user_type. Obièno nemate potrebu da ovo mijenjate.';
$string['auth_ldap_opt_deref'] = 'Ustanovite kako drugaèije upravljati u toku traženja. Izaberite jednu od prateæih vrijednosti: 
\"No\" (LDAP_DEREF_NEVER) ili
\"Yes\" (LDAP_DEREF_ALWAYS)';
$string['auth_ldap_passwdexpire_settings'] = 'Podešavanje LDAP isteka lozinke';
$string['auth_ldap_search_sub'] = 'Stavite vrijednost <> 0 ako želite tražiti korisnike u podkontekstu';
$string['auth_ldap_server_settings'] = 'Podešavanje LDAP servera';
$string['auth_ldap_update_userinfo'] = 'Ažurirajte korisnièke informacije (ime, prezime, adrese..) iz LDAP u Moodle. Za informacije, pogledajte /auth/ldap/attr_mappings.php\'';
$string['auth_ldap_user_attribute'] = 'Atribut koji se koristi za ime/pretraga korisnika. Uglavnom je \'cn\'.';
$string['auth_ldap_user_settings'] = 'Izgled korisnièkog podešavanja';
$string['auth_ldap_user_type'] = 'Izaberite kako se korisnik snabdjeva u LDAP. Ovo podešavanje je takoðer odreðeno istekom upisa, prièekom unosa i da li æe korisnièko djelo raditi.';
$string['auth_ldap_version'] = 'Verzija LDAP protokola koji Vaš server koristi.';
$string['auth_ldapdescription'] = 'Ovaj metod služi za provjeru od strane spoljnog LDAP servera.
Ako su dodijeljeno korisnièko ime i lozinka ispravni, Moodle kreira novi korisnièki ulaz u svoju bazu korisnika. Ovaj modul može èitati korisnièke atribute sa LDAP-a i postaviti
tražena polja u Moodle. Za sljedeæe upise samo se provjeravaju korisnièko ime i lozinka.';
$string['auth_ldapextrafields'] = 'Ova polja nisu obavezna. Možete izabrati da ispunite neka Moodle korisnièka polja sa informacijama iz <b>LDAP fields</b> koja ovdje odredite. <br />Ako polja ostavite prazna, onda se ništa neæe prebaciti sa LDAP, tako da æe podrazumijevane opcije na Moodle æe biti korištene.<br />U svakom sluèaju, korisnici mogu da ureðuju ova polja nakon upisivanja.';
$string['auth_ldaptitle'] = 'LDAP server';
$string['auth_manualdescription'] = 'Ovaj metod uklanja korisnicima sve naèine pravljenja njihovih vlastitih naloga. Svi nalozi moraju biti ruèno napravljeni od administratora.';
$string['auth_manualtitle'] = 'Samo za ruèno pravljenje naloga';
$string['auth_multiplehosts'] = 'Odreðivanje više host-ova (npr. host1.com;host2.com;host3.com)';
$string['auth_nntpdescription'] = 'Ovaj metod koristi NNTP server za provjeru ispravnosti korisnièkih imena i lozinki.';
$string['auth_nntphost'] = 'NNTP adrese servera. Koristite IP broj, a ne DNS nazive.';
$string['auth_nntpport'] = 'Serverski port (119 je najèešæi)';
$string['auth_nntptitle'] = 'NNTP server';
$string['auth_nonedescription'] = 'Korisnici se mogu upisati i odmah napraviti važeæe naloge, bez provjere od strane spoljnog servera i bez potvrde putem elektronske pošte.
Budite oprezni kad koristite ovu opciju - mislite na sigurnost i administrativne probleme koji mogu biti prouzrokovani!';
$string['auth_nonetitle'] = 'Nema provjere';
$string['auth_pamdescription'] = 'Ovaj metod koristi PAM za pristup korisnièkom imenu na domaæem serveru. Imate za instalaciju <a href=\"http://www.math.ohio-state.edu/~ccunning/pam_auth/\" target=\"_blank\">PHP4 PAM Authentication</a> kopiju da koristite ovaj modul. ';
$string['auth_pamtitle'] = 'PAM (Pluggable Authentication Modules)';
$string['auth_passwordisexpired'] = 'Vaša šifra je istekla. Da li želite sada promjeniti Vašu lozinku?';
$string['auth_passwordwillexpire'] = 'Vaša šifra istièe za $a dana. Da li želite sada promjeniti Vašu lozinku?';
$string['auth_pop3description'] = 'Ovaj metod koristi POP3 server za provjeru ispravnosti korisnièkih imena i lozinki.';
$string['auth_pop3host'] = 'POP3 adrese servera. Koristite IP broj, a ne DNS ime.';
$string['auth_pop3mailbox'] = 'Koristi ime u mailboxu za pokušaj pristupa sa. (obièno INBOX)';
$string['auth_pop3port'] = 'Serverski port (110 je uobièajen)';
$string['auth_pop3title'] = 'POP3 server';
$string['auth_pop3type'] = 'Tip servera. Ako vaš server koristi sigurnosne certifikate, izaberite pop3cert.';
$string['auth_updatelocal'] = 'Nadogradi lokalne podatke';
$string['auth_updatelocal_expl'] = '<p><b>Nadogradite lokalne podatke:</b> Ako su dostupni polje æe biti nadograðeno (iz unutrašnjeg auth-a) svaki puta kada se korisnik prijavi a tamo je korisnièka sinhronizacija. Polje namješteno na lokalnu nadogradnju æe biti zakljuèano.</p>';
$string['auth_updateremote'] = 'Nadogradi vanjske podatke';
$string['auth_updateremote_expl'] = '<p><b>Nadogradite vanjske podatke:</b> Ako su dostupni, unutrašnji auth æe biti nadograðen kada je i korisnièki dokument nadograðen. Polja æe biti otkljuèana za dozvolu korigovanja.</p>';
$string['auth_updateremote_ldap'] = '<p><b>Bilješka:</b> Nadogradite vanjske LDAP podatke zahtijevajuæi povezivanje dn i pw za povezivanje korisnika sa privilegijama korigovanja svih korisnièkih dokumenata. Ovo trenutno nije zaštiæeno multivrijednosnim atributima i ukloniti æe ekstra vrijednosti sa nadogradnje.</p>';
$string['auth_user_create'] = 'Dozvola kreiranja korisnicima';
$string['auth_user_creation'] = 'Novi (anonimni) korisnici mogu napraviti korisnièke naloge na spoljnom izvoru za provjeru i izvršiti potvrdu putem elektronske pošte. Ako omoguæite ovu opciju, pazite da takoðe uredite opcije za module koji dozvoljavaju kreiranje korisnicima.';
$string['auth_usernameexists'] = 'Ovo korisnièko ime veæ postoji. Molimo Vas da izaberite drugo.';
$string['authenticationoptions'] = 'Opcije za provjeru';
$string['authinstructions'] = 'Ovdje možete dati instrukcije vašim korisnicima, tako da znaju koje korisnièko ime i lozinku trebaju koristiti. Tekst koji ovdje napišete biæe prikazan na stranici za upis. Ako polje ostavite prazno, onda instrukcije neæe biti prikazane.';
$string['changepassword'] = 'Promjeni lozinku URL-a';
$string['changepasswordhelp'] = 'Ovde možete zadati lokaciju na kojoj Vaši korisnici mogu obnoviti ili promijeniti svoje korisnièko ime/lozinku, u sluèaju da se zaboravi.
Ova opcija se može pružiti korisnicima u vidu dugmeta na stranici za upis i njihovoj korisnièkoj strani. Ako ostavite prazno polje, dugme neæe biti prikazano.';
$string['chooseauthmethod'] = 'Izaberite metod provjere';
$string['forcechangepassword'] = 'Primoraj na promjenu lozinke';
$string['forcechangepassword_help'] = 'Primoraj korisnike da promijene lozinku na sljedeæem pristupu Moodl-u';
$string['forcechangepasswordfirst_help'] = 'Primoraj korisnike da promijene lozinku na prvom pristupu Moodl-u';
$string['guestloginbutton'] = 'Dugme za prijavu gostiju';
$string['instructions'] = 'Uputstva';
$string['md5'] = 'MD5 kodiranje';
$string['plaintext'] = 'Èisti (Plain) tekst';
$string['showguestlogin'] = 'Možete sakriti ili prikazati dugme za prijavu gostiju na prijavnoj stranici.';
$string['stdchangepassword'] = 'Upotrijebi standardnu stranicu za promjenu lozinke';
$string['stdchangepassword_expl'] = 'Ako vanjski autentièni sistem dozvoljava promjenu lozinke iz Moodla, ukljuèi ga na YES. Ovo podešavanje obilazi \'Change Password URL\'.';
$string['stdchangepassword_explldap'] = 'Bilješka: Ovo je preporuèeno da možete upotrijebiti LDAP iznad SSL šifrovanjem tunela (ldaps://) ako je LDAP server udaljen.';

?>
