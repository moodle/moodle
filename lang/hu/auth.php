<?PHP // $Id$ 
      // auth.php - created with Moodle 1.1.1 (2003091111)


$string['auth_dbdescription'] = "Ez a módszer egy külsõ adatbázistáblát használ a felhasználó nevének és jelszavának ellenõrzésére.  Új felhasználó esetén az egyéb mezõkben tárolt információk is átkerülnek a Moodle-ba.";
$string['auth_dbextrafields'] = "Ezek választható mezõk. Választhatja azt is, hogy a Moodle a mezõk egy részét egy itt megadott  <B>külsõ adatbázisból</B> elõre feltöltse. <P>A mezõket üresen hagyva az alapértelmezett értékek lesznek használva.<P>Bármely esetben a felhsználó belépés után változtathatja ezeket a mezõket.";
$string['auth_dbfieldpass'] = "A jelszót tartalmazó mezõ neve";
$string['auth_dbfielduser'] = "A felhasználónevet tartalmazó mezõ neve";
$string['auth_dbhost'] = "Az adatbázisszervert futtató számítógép.";
$string['auth_dbname'] = "Az adatbázis neve";
$string['auth_dbpass'] = "Jelszó összehasonlitás a fenti felhasználónév alapján";
$string['auth_dbpasstype'] = "A jelszó mezõ formátumát határozza meg. Az MD5 titkosítás hasznos olyan népszerû web-alkalmazások esetén, mint pl. a PostNuke";
$string['auth_dbtable'] = "A tábla neve az adatbázisban";
$string['auth_dbtitle'] = "Külsõ adatbázis használata";
$string['auth_dbtype'] = "Az adatbázis típusa (Lásd a <A HREF=../lib/adodb/readme.htm#drivers>ADOdb dokumentációt</A> a részletekért)";
$string['auth_dbuser'] = "Az adatbázishoz olvasási joggal rendelkezõ felhasználónév";
$string['auth_emaildescription'] = "Az Email visszaigazolás az alapértelmezett hitelesítési eljárás. Amikor a felhasználó feliratkozik, és új felhasználónevet ill. jelszót választ, egy visszaigazoló email lesz elküldve a megadott email címre.  Az email egy biztonságos linket tartalmaz arra az oldalra, ahol a felhasználó igazolhatja a feliratkozást. A következõ bejelentkezések csak a nevet és a jelszót ellenõrzik a Moodle adatbázisból.";
$string['auth_emailtitle'] = "Email-alapú hitelesités";
$string['auth_imapdescription'] = "Ez az eljárás egy IMAP servert használ annak ellenõrzésére, hogy a megadott felhasználónév és jelszó érvényes-e.";
$string['auth_imaphost'] = "Az IMAP szerver címe. Használjon IP címet, ne DNS nevet.";
$string['auth_imapport'] = "Az IMAP szerver portszáma. Ez általában 143 vagy 993.";
$string['auth_imaptitle'] = "IMAP szerver használata";
$string['auth_imaptype'] = "Az IMAP szerver típusa. Az IMAP szervereknek különbözõ típusú hitelesítése és dialektusa lehet.";
$string['auth_ldap_bind_dn'] = "Ha bind-usert kíván felhasználók keresésére használni, állítsa be itt. Pl.:'cn=ldapuser,ou=public,o=org'";
$string['auth_ldap_bind_pw'] = "A bind-user jelszava";
$string['auth_ldap_contexts'] = "Kontextusok listája, melyekbne a felhasználó található. Különbözõ kontextusokat ';' -vel válasszon el. Pl.: 'ou=users,o=org; ou=others,o=org'";
$string['auth_ldap_create_context'] = "Ha engedélyezte felhasználók létrehozását e-mail visszaigazolással, adja meg itt a kontextust, amelyben a felhasználók létrejönnek. Ennek - biztonsági okokból - különböznie kell más felhasználókétól. Ezt nem kell hozzáadni az ldap_context változóhoz, a szoftver automatikusan ebben a kontextusban keresi a felhasználókat.";
$string['auth_ldap_creators'] = "Azon csoportok listája, melyek tagjai létrehozhatnak kurzusokat. A csoportokat ';' választja el. Általában valami ilyesmi: 'cn=teachers,ou=staff,o=myorg'";
$string['auth_ldap_host_url'] = "LDAP gép megadása URL-szerûen pl.'ldap://ldap.myorg.com/' or 'ldaps://ldap.myorg.com/'";
$string['auth_ldap_memberattribute'] = "Adja meg a felhasználók csoporthoz tartozást jellemzõ attribútumát. Általában 'member'";
$string['auth_ldap_search_sub'] = "Írja be az &lt;&gt; 0 értékeket ha az alkontxtusokban is keresni kíván felhasználót.";
$string['auth_ldap_update_userinfo'] = "Felhasználói adatok (keresztnév, vezetéknév, cím..) frissítése LDAP-ból a Moodle-ba. Lásd a /auth/ldap/attr_mappings.php -t fájlt mapping információért";
$string['auth_ldap_user_attribute'] = "Attribútum felhasználók elnevezéséhez/kereséséhez. Általában 'cn'.";
$string['auth_ldapdescription'] = "Ez a módszer lehetõséget ad egy külsõ LDAP szerverrel történõ jogosultság-ellenõrzésre.
Ha a megadott név és jelszó érvényes, a Moodle egy új felhasználó bejegyzést hoz létre a saját                                 adatbázisában. Ez a modul képes kiolvasni a felhasználó adatait az LDAP-ból, és kitölti a kötelezõ mezõket a Moodle-ban. Következõ bejelentkezéskor csak a felhasználónév és a jelszó        lesz ellenõrizve.";
$string['auth_ldapextrafields'] = "Ezek a mezõk nem kötelezõek. Néhány Moodle felhasználói adatmezõt elõre kitölthet az itt megadott <B>LDAP mezõk</B> adataival. <P>Ha ezeket a mezõket üresen hagyja, semmilyen adat nem kerül át az LDAP-ból és a Moodle alapértelmezett értékek lesznek használva.<P>Mindkét esetben a afelhasználónak lehetõsége lesz változtatni a mezõk értékén bejelentkezés után.";
$string['auth_ldaptitle'] = "LDAP szerver használata";
$string['auth_manualdescription'] = "Ez a módzser minden lehetõséget elvesz a felhasználóktól saját account létrehozására. Minden account-ot manuálisan hoz létre az admin felhasználó.";
$string['auth_manualtitle'] = "Csak amnuális account-ok";
$string['auth_nntpdescription'] = "Ez a módszer egy NNTP szerverrel ellenõrzi a felhasználónév és jelszó érvényeeségét.";
$string['auth_nntphost'] = "Az NNTP szerver címe. Használjon IP címet, ne DNS nevet.";
$string['auth_nntpport'] = "Szerver port (általában 119)";
$string['auth_nntptitle'] = "NNTP szerver használata";
$string['auth_nonedescription'] = "A felhasználók azonnal feliratkozhatnak és egy érvényes hozzáférést hozhatnak létre, külsõ jogosultság-ellenõrzés és emailen történõ megerõsítés nélkül. Óvatosan használja ezt a lehetõséget - gondoljon a lehetséges biztonsági és adminisztrációs problémákra.";
$string['auth_nonetitle'] = "nincs hitelesités";
$string['auth_pop3description'] = "Ez a módszer egy POP3 szerverrel ellenõrzi a felhasználónév és jelszó érvényeeségét.";
$string['auth_pop3host'] = "Az POP3 szerver címe. Használjon IP címet, ne DNS nevet.";
$string['auth_pop3port'] = "Szerver port (általában 110)";
$string['auth_pop3title'] = "POP3 szerver használata";
$string['auth_pop3type'] = "Szervertípus. Ha a szerver certifikációs biztonsági modellt használ, válaszsza a pop3cert -t.";
$string['auth_user_create'] = "Felhasználó létrehozás engedélyezése";
$string['auth_user_creation'] = "Új (anonymus) felhasználó létrehozhat új account-ot a külsõ autentikációs forráson, email megerõsítéssel.Ha ezt engedélyezi, ne feledje megadni a felhasználó létrehozás modul-specifikus tulajdonságait sem .";
$string['auth_usernameexists'] = "A választott felhasználónév már létezik. Válasszon másikat.";
$string['authenticationoptions'] = "Felhasználó-azonosítási lehetõségek";
$string['authinstructions'] = "Itt instrukciókat adhat a felhasználók számára, hogy tudják, milyen nevet és jelszavat kell használni. Az itt megadott szöveg megjelenik a bejelentkezõ oldalon. Ha üresen hagyja, nem jelenik meg semmilyen instrukció.";
$string['changepassword'] = "Jelszó URL cseréje";
$string['changepasswordhelp'] = "Itt megadhat egy helyet, ahol a felhasználók visszakereshetik vagy megváltoztathatják nevüket/jelszavukat ha elfelejtették. Ez gombként jelenik meg a bejelentkezõ oldalon és a felhasználói oldalon. Ha üresen hagyja, nem jelenik meg ilyen gomb.";
$string['chooseauthmethod'] = "Válasszon egy azonosítási eljárást:";
$string['guestloginbutton'] = "Vendég belépése gomb";
$string['instructions'] = "Teendõk";
$string['md5'] = "MD5 titkosítás";
$string['plaintext'] = "Egyszerû szöveg";
$string['showguestlogin'] = "Megjelenítheti vagy elrejtheti a vendég belépése gombot a bejelentkezõ oldalon.";

?>
