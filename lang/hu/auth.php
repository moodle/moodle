<?PHP // $Id$ 
      // auth.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2005012800)


$string['auth_dbdescription'] = 'Ez a módszer egy külsõ adatbázistáblát használ a felhasználó nevének és jelszavának ellenõrzésére.  Új felhasználó esetén az egyéb mezõkben tárolt információk is átmásolhatók a Moodle-ba.';
$string['auth_dbextrafields'] = 'Ezek választható mezõk. Választhatja azt is, hogy a Moodle a mezõk egy részét az itt megadott <b>külsõ adatbázismezõkbõl</b> elõre feltöltse. <p>Ha a mezõket üresen hagyja, a rendszer az alapbeállításokat fogja használni.</p><p>Mindkét esetben a felhasználó belépés után változtathatja ezeket a mezõket.</p>';
$string['auth_dbfieldpass'] = 'A jelszót tartalmazó mezõ neve';
$string['auth_dbfielduser'] = 'A felhasználónevet tartalmazó mezõ neve';
$string['auth_dbhost'] = 'Az adatbázisszervert futtató számítógép';
$string['auth_dbname'] = 'Az adatbázis neve';
$string['auth_dbpass'] = 'A fenti felhasználónévnek megfelelõ jelszó';
$string['auth_dbpasstype'] = 'Adja meg a jelszómezõ formátumát. Az MD5 titkosítás hasznos olyan elterjedt webalkalmazások esetén, mint a PostNuke';
$string['auth_dbtable'] = 'A tábla neve az adatbázisban';
$string['auth_dbtitle'] = 'Külsõ adatbázis használata';
$string['auth_dbtype'] = 'Az adatbázis típusával kapcsolatos részletek (lásd a <a href=\"../lib/adodb/readme.htm#drivers\">ADOdb dokumentációt</a>.)';
$string['auth_dbuser'] = 'Az adatbázishoz olvasási joggal rendelkezõ felhasználónév';
$string['auth_emaildescription'] = 'Az e-mail visszaigazolása az alapértelmezett hitelesítési eljárás. Amikor a felhasználó felíratkozik és új felhasználónevet, ill. jelszót választ, egy visszaigazoló e-mailt kap a megadott e-mail címre. Az e-mail egy biztonságos ugrópontot tartalmaz arra az oldalra, ahol a felhasználó visszaigazolhatja a felíratkozást. Ezután a bejelentkezések csak a nevet és a jelszót ellenõrzik a Moodle adatbázisa alapján.';
$string['auth_emailtitle'] = 'Hitelesítés e-mail alapján';
$string['auth_imapdescription'] = 'Ez az eljárás egy IMAP-szervert használ annak ellenõrzésére, hogy a megadott felhasználónév és jelszó érvényes-e.';
$string['auth_imaphost'] = 'Az IMAP-szerver címe. Az IP-címet használja, ne a DNS-nevet.';
$string['auth_imapport'] = 'Az IMAP-szerver portszáma. Ez általában 143 vagy 993.';
$string['auth_imaptitle'] = 'Használjon IMAP-szervert.';
$string['auth_imaptype'] = 'Az IMAP-szerver típusa. Az IMAP-szervereknek különbözõ típusú hitelesítése és felülhitelesítése lehet.';
$string['auth_ldap_bind_dn'] = 'Ha bind-user-t kíván felhasználók keresésére használni, állítsa be itt. Pl.:\'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'A bind-user jelszava';
$string['auth_ldap_contexts'] = 'Kontextusok listája, melyekben a felhasználók találhatók. A különbözõ kontextusokat válassza el pontosvesszõvel. Pl.: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Ha engedélyezte felhasználók létrehozását e-maillel való visszaigazolással, adja meg itt a kontextust, amelyben a felhasználók létrejönnek. Ennek - biztonsági okokból - különböznie kell más felhasználókétól. Ezt nem kell hozzáadni az ldap_context változóhoz, a szoftver automatikusan ebben a kontextusban keresi a felhasználókat.';
$string['auth_ldap_creators'] = 'Azon csoportok listája, melyek tagjai létrehozhatnak kurzusokat. A csoportokat válassza el pontosvesszõvel egymástól. Általában például: \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_host_url'] = 'LDAP-gazdagép megadása URL-szerûen, pl.\'ldap://ldap.myorg.com/\' vagy \'ldaps://ldap.myorg.com/\'';
$string['auth_ldap_memberattribute'] = 'Adja meg a felhasználók adott csoporthoz tartozást jellemzõ attribútumát. Ez általában a \'member\' [tag]';
$string['auth_ldap_search_sub'] = 'Írja be az <> 0 értékeket, ha az alkontextusokban is keresni kíván felhasználót.';
$string['auth_ldap_update_userinfo'] = 'Felhasználói adatok (keresztnév, vezetéknév, cím...) frissítése LDAP-ból a Moodle-ba. Az információk szerkezetét lásd az /auth/ldap/attr_mappings.php állományban.';
$string['auth_ldap_user_attribute'] = 'Attribútum a felhasználók elnevezéséhez/kereséséhez. Általában \'cn\'.';
$string['auth_ldap_version'] = 'A szerver által használt LDAP-protokoll verziója.';
$string['auth_ldapdescription'] = 'Ez a módszer lehetõséget ad a jogosultság külsõ LDAP-szerverrel történõ ellenõrzésére. 
Ha a megadott név és jelszó érvényes, a Moodle egy új felhasználót hoz létre az adatbázisában. Ez a modul képes kiolvasni a felhasználó adatait az LDAP-ból, és kitölti a kötelezõ mezõket a Moodle-ban. Következõ bejelentkezéskor már csak a felhasználónév és a jelszó ellenõrzésére kerül sor.';
$string['auth_ldapextrafields'] = 'Ezek a mezõk nem kötelezõek. Néhány felhasználói adatmezõt elõre kitölthet a Moodle az itt megadott <B>LDAP-mezõk</B> adataival. <P>Ha ezeket a mezõket üresen hagyja, semmilyen adat nem kerül át az LDAP-ból és a Moodle az alapértelmezett értékeket fogja használni.<P>Mindkét esetben bejelentkezés után a felhasználónak lehetõsége lesz a mezõk értékeit módosítani.';
$string['auth_ldaptitle'] = 'LDAP-szerver használata';
$string['auth_manualdescription'] = 'Ez a módszer nem teszi lehetõvé felhasználók számára azonosítók létrehozását. Minden felhasználói azonosítót az adminisztrátornak kell kézzel létrehozni.';
$string['auth_manualtitle'] = 'Csak kézzel létrehozott felhasználói azonosítók';
$string['auth_nntpdescription'] = 'Ez a módszer egy NNTP-szerverrel ellenõrzi a felhasználónév és a jelszó érvényességét.';
$string['auth_nntphost'] = 'Az NNTP-szerver címe. Az IP-címet használja, ne a DNS-nevet.';
$string['auth_nntpport'] = 'Szerverport (általában a 119-es)';
$string['auth_nntptitle'] = 'NNTP-szerver használata';
$string['auth_nonedescription'] = 'A felhasználók azonnal felíratkozhatnak és érvényes felhasználói azonosítót hozhatnak létre, külsõ jogosultság-ellenõrzés és e-mailen történõ megerõsítés nélkül. Óvatosan használja ezt a lehetõséget - gondoljon a lehetséges biztonsági és adminisztrációs problémákra.';
$string['auth_nonetitle'] = 'Nincs hitelesítés';
$string['auth_pop3description'] = 'Ez a módszer egy POP3-szerverrel ellenõrzi a felhasználónév és jelszó érvényességét.';
$string['auth_pop3host'] = 'A POP3-szerver címe. Az IP-címet használja, ne a DNS-nevet.';
$string['auth_pop3port'] = 'Szerverport (általában a 110-es)';
$string['auth_pop3title'] = 'POP3-szerver használata';
$string['auth_pop3type'] = 'Szervertípus. Ha a szerver tanúsítványos biztonsági modellt használ, válassza a pop3cert-et.';
$string['auth_user_create'] = 'Felhasználó létrehozásának engedélyezése';
$string['auth_user_creation'] = 'Új (névtelen) felhasználók is létrehozhatnak új felhasználói azonosítót a külsõ hitelesítési forráson, e-mailes megerõsítéssel. Ha ezt engedélyezi, ne feledje megadni a felhasználó létrehozásához a modul-specifikus adatokat.';
$string['auth_usernameexists'] = 'A választott felhasználónév már létezik. Válasszon másikat.';
$string['authenticationoptions'] = 'Felhasználó-azonosítási lehetõségek';
$string['authinstructions'] = 'Itt tájékoztathatja a felhasználókat arról, hogy milyen felhasználóneveket és jelszavakat használhatnak. Az itt megadott szöveg megjelenik a bejelentkezõ oldalon. Ha üresen hagyja, nem jelenik meg semmilyen tájékoztatás.';
$string['changepassword'] = 'Jelszó cseréje';
$string['changepasswordhelp'] = 'Itt megadhat egy helyet, ahol a felhasználók visszakereshetik vagy megváltoztathatják felhasználói nevüket/jelszavukat, ha elfelejtették. Ez gombként jelenik meg a bejelentkezõ oldalon és az adott felhasználó oldalán. Ha üresen hagyja, nem jelenik meg ilyen gomb.';
$string['chooseauthmethod'] = 'Válasszon azonosítási eljárást:';
$string['guestloginbutton'] = 'Vendég belépése gomb';
$string['instructions'] = 'Tájékoztatás';
$string['md5'] = 'MD5-titkosítás';
$string['plaintext'] = 'Egyszerû szöveg';
$string['showguestlogin'] = 'Megjelenítheti vagy elrejtheti a vendégként való belépésre szolgáló gombot a bejelentkezõ oldalon.';

?>
