<?PHP // $Id$ 
      // auth.php - created with Moodle 1.3 - (2004052400)


$string['auth_dbdescription'] = 'Metoda ta wykorzystuje tabelê zewnêtrznej bazy danych dla sprawdzenia czy podana nazwa u¿ytkownika i has³o s± poprawne. W przypadku nowego konta, informacje z innych pól równie¿ mog± zostaæ skopiowane do Moodle.';
$string['auth_dbextrafields'] = 'Te pola s± opcjonalne. Mo¿esz wstêpnie wype³niæ niektóre pola dotycz±ce u¿ytkownika informacj± z <B>pól zewnêtrznej bazy danych</B>, które tutaj okre¶lasz. <P>Je¿eli nic  w tym miejscu nie wpiszesz, u¿yte zostan± warto¶ci domy¶lne. <P> W obu przypadkach, u¿ytkownik bêdzie móg³ dokonaæ edycji tych pól po zalogowaniu';
$string['auth_dbfieldpass'] = 'Nazwa pola zawieraj±cego has³a';
$string['auth_dbfielduser'] = 'Nazwa pola zawieraj±cego nazwy u¿ytkowników';
$string['auth_dbhost'] = 'Komputer bêd±cy hostem serwera bazy danych.';
$string['auth_dbname'] = 'Nazwa bazy danych';
$string['auth_dbpass'] = 'Has³o dla powy¿szej nazwy u¿ytkownika';
$string['auth_dbpasstype'] = 'Okre¶l format stosowany przez pole has³a. Kodowanie MD5 przydatne jest przy ³±czeniu siê z innymi popularnymi aplikacjami sieci WWW, takimi jak PostNuke';
$string['auth_dbtable'] = 'Nazwa tabeli w bazie danych';
$string['auth_dbtitle'] = 'Korzystaj z zewnêtrznej bazy danych';
$string['auth_dbtype'] = 'Rodzaj bazy danych (szczegó³owe informacje: <A HREF=../lib/adodb/readme.htm#drivers>ADOdb documentation</A>';
$string['auth_dbuser'] = 'Nazwa u¿ytkownika maj±cego prawo dostêpu do odczytu z bazy';
$string['auth_emaildescription'] = 'Potwierdzenie e-mailem jest domy¶ln± metod± uwierzytelniania. U¿ytkownik rejestruje siê wybieraj±c w³asn±, now± nazwê u¿ytkownika oraz has³o, a nastêpnie wysy³ane jest potwierdzenie na adres jego konta pocztowego. E-mail ten zawiera bezpieczny odno¶nik do strony, na której u¿ytkownik mo¿e potwierdziæ zarejestrowanie swojego konta. Przy kolejnych logowaniach dokonywane jest tylko porównanie nazwy u¿ytkownika i has³a z warto¶ciami zapisanymi w bazie danych Moodle.';
$string['auth_emailtitle'] = 'Uwierzytelnienie z wykorzystaniem poczty elektronicznej';
$string['auth_imapdescription'] = 'Metoda ta korzysta z serwera IMAP w celu sprawdzenia czy podana nazwa u¿ytkownika i has³o s± poprawne.';
$string['auth_imaphost'] = 'Adres serwera IMAP. Nale¿y stosowaæ adres IP, a nie nazwê DNS.';
$string['auth_imapport'] = 'Numer portu serwera IMAP, zwykle 142 lub 993.';
$string['auth_imaptitle'] = 'U¿yj serwera IMAP';
$string['auth_imaptype'] = 'Typ serwera IMAP. Serwery IMAP mog± stosowaæ ró¿ne rodzaje uwierzytelniania i negocjacji.';
$string['auth_ldap_bind_dn'] = 'Okre¶l tutaj czy chcesz skorzystaæ z funkcji bind-user do szukania u¿ytkowników, np. \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Has³o dla funkcji bind-user';
$string['auth_ldap_contexts'] = 'Lista kontekstów, w których znajduj± siê u¿ytkownicy. Oddzielaj ró¿ne konteksty symbolem \';\', np. \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Je¿eli w³±czysz opcjê tworzenia u¿ytkowników z potwierdzeniem poczt± elektroniczn±, zdefiniuj kontekst, w którym tworzeni s± tacy u¿ytkownicy. Powinien byæ ró¿niæ siê od kontekstu innych u¿ytkowników w celu unikniêcia problemów zwi±zanych z bezpieczeñstwem. Nie musisz dodawaæ tego kontekstu do zmiennej ldap_context-variable - Moodle automatycznie wyszuka u¿ytkowników w tym kontek¶cie.';
$string['auth_ldap_creators'] = 'Lista grup, których cz³onkowie mog± tworzyæ nowe kursy. Oddziel kolejne grupy symbolem \';\'. Przyk³adowa lista: \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_host_url'] = 'Okre¶l hosta LDAP za pomoc± adresu, np. URL\'ldap://ldap.myorg.com/\' lub \'ldaps://ldap.myorg.com/\'';
$string['auth_ldap_memberattribute'] = 'Okre¶l atrybut cz³onkostwa u¿ytkownika je¿eli u¿ytkownik nale¿y do grupy. Zazwyczaj jest to \'member\'';
$string['auth_ldap_search_sub'] = 'Wpisz warto¶æ <> 0 je¿eli chcesz szukaæ u¿ytkowników z podkontekstów';
$string['auth_ldap_update_userinfo'] = 'Uaktualnij informacje o u¿ytkowniku (imiê, nazwisko, adres...) z LDAP do Moodle. Informacje na temat mapowania: /auth/ldap/attr_mappings.php';
$string['auth_ldap_user_attribute'] = 'Atrybut u¿ywany do nazywania/szukania u¿ytkowników, zwykle \'cn\'.';
$string['auth_ldap_version'] = 'Wersja protoko³u LDAP u¿ywana przez serwer.';
$string['auth_ldapdescription'] = 'Metoda ta zapewnia uwierzytelnienie wzglêdem zewnêtrznego serwera LDAP.<br> Je¿eli podana nazwa u¿ytkownika i has³o s± poprawne, Moodle dokonuje wpisu nowego u¿ytkownika do swojej bazy danych. Modu³ ten mo¿e odczytywaæ atrybuty u¿ytkownika z LDAP i wstêpnie wype³niæ odpowiednie pola w Moodle. Przy kolejnych logowaniach sprawdzane s± tylko nazwa u¿ytkownika i has³o.';
$string['auth_ldapextrafields'] = 'Te pola s± opcjonalne. Mo¿esz wstêpnie wype³niæ niektóre pola dotycz±ce u¿ytkowników Moodle informacjami z okre¶lonych tutaj <B>pól LDAP.<B> <P> Je¿eli pola te pozostawisz puste, ¿adne informacje nie zostan± przeniesione z LDAP i wykorzystane zostan± warto¶ci domy¶lne Moodle. <P> W obu przypadkach, u¿ytkownik bêdzie móg³ dokonaæ edycji tych pól po zalogowaniu.';
$string['auth_ldaptitle'] = 'U¿yj serwera LDAP';
$string['auth_manualdescription'] = 'Metoda ta uniemo¿liwia u¿ytkownikom tworzenie w³asnych kont. Wszystkie konta musz± byæ rêcznie utworzone przez administratora (Admin User).';
$string['auth_manualtitle'] = 'Tylko konta utworzone rêcznie';
$string['auth_multiplehosts'] = 'Mo¿na wskazaæ wiêcej komputerów-hostów np. host1.com; host2.com; host3.com';
$string['auth_nntpdescription'] = 'Metoda ta wykorzystuje serwer NNTP w celu sprawdzenia czy podana nazwa u¿ytkownika i has³o s± poprawne.';
$string['auth_nntphost'] = 'Adres serwera NNTP. Nale¿y stosowaæ adres IP, a nie nazwê DNS.';
$string['auth_nntpport'] = 'Port serwera (najczê¶ciej 119)';
$string['auth_nntptitle'] = 'U¿yj serwera NNTP';
$string['auth_nonedescription'] = 'U¿ytkownicy mog± siê zarejestrowaæ i niezw³ocznie utworzyæ dzia³aj±ce konta, bez uwierzytelniania wzglêdem zewnêtrznego serwera i potwierdzenia e-mailem. Korzystaj z tej opcji ostro¿nie pamiêtaj±c o mo¿liwych problemach z bezpieczeñstwem i administracj±.';
$string['auth_nonetitle'] = 'Brak uwierzytelniania';
$string['auth_pop3description'] = 'Metoda ta wykorzystuje serwer POP3 w celu sprawdzenia czy podana nazwa u¿ytkownika i has³o s± poprawne.';
$string['auth_pop3host'] = 'Adres serwera POP3. Nale¿y stosowaæ adres IP, a nie nazwê DNS.';
$string['auth_pop3port'] = 'Port serwera (najczê¶ciej 110)';
$string['auth_pop3title'] = 'U¿yj serwera POP3';
$string['auth_pop3type'] = 'Typ serwera. Je¿eli Twój serwer wykorzystuje certyfikaty bezpieczeñstwa, wybierz pop3cert.';
$string['auth_user_create'] = 'W³±cz opcjê tworzenia u¿ytkowników';
$string['auth_user_creation'] = 'Nowi (anonimowi) u¿ytkownicy mog± tworzyæ konta u¿ytkownika u¿ywaj±c zewnêtrznego ¼ród³a uwierzytelniania z potwierdzeniem poczt± elektroniczn±. Je¿eli w³±czysz tê opcjê, pamiêtaj równie¿ o skonfigurowaniu zwi±zanych z modu³ami opcji tworzenia u¿ytkowników.';
$string['auth_usernameexists'] = 'Wybrana nazwa u¿ytkownika ju¿ istnieje - proszê wybraæ inn±.';
$string['authenticationoptions'] = 'Opcje uwierzytelniania';
$string['authinstructions'] = 'Mo¿esz tutaj wprowadziæ instrukcje dla Twoich u¿ytkowników dotycz±ce nazwy u¿ytkownika i has³a, których powinni u¿ywaæ. Tekst wpisany w tym miejscu pojawi siê na stronie logowania. Je¿eli nic nie wpiszesz, nie zostan± wy¶wietlone ¿adne instrukcje.';
$string['changepassword'] = 'Zmieñ adres URL has³a';
$string['changepasswordhelp'] = 'Mo¿esz tutaj okre¶liæ miejsce, w którym Twoi u¿ytkownicy mog± odzyskaæ lub zmieniæ swoja nazwê u¿ytkownika/has³o, je¿eli ich zapomn±. Wybranie tej opcji spowoduje wy¶wietlenie przycisku na stronie logowania i stronach u¿ytkownika. Je¿eli nic nie wpiszesz, przycisk nie zostanie wy¶wietlony.';
$string['chooseauthmethod'] = 'Wybierz sposób uwierzytelniania';
$string['guestloginbutton'] = 'Przycisk logowania jako go¶æ';
$string['instructions'] = 'Instrukcje';
$string['md5'] = 'Kodowanie MD5';
$string['plaintext'] = 'Zwyk³y tekst';
$string['showguestlogin'] = 'Mo¿esz ukryæ b±d¼ pokazaæ przycisk logowania jako go¶æ';

?>
