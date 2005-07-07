<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.4 (2004083100)


$string['description'] = '<p>Beiratkozásait kezelheti egy LDAP-szerver segítségével. Feltételezés szerint az Ön LDAP-fája olyan csoportokat tartalmaz, amelyek kurzusoknak vannak megfeleltetve, az egyes kurzusok/csoportok pedig tagjegyzékkel rendelkeznek a tanulók megfeleltetéséhez.</p>
<p>A kurzusok csoportokként szerepelnek az  
LDAP-ben, mindegyik csoport több olyan tagsági mezõvel  
(<em>tag</em> vagy <em>tagazonosító</em>) rendelkezik, amely a felhasználó egyedi azoönosítóját tartalmazza.</p>
<p>Az LDAP-beiratkozás használatához felhasználóinak érvényes azonosítószámot tartalmazó mezõkkel <strong>kell</strong> 
rendelkezni. Az LDAP-csoportoknak ezzel az azonosítószámmal kell rendelkezni ahhoz, hogy egy felhasználó felvehesse a kurzust.
Ez általában akkor mûködik megfelelõen, ha már használ LDAP-hitelesítést.</p>
<p>A beiratkozások frissítése a felhasználó bejelentkezésekor történik. A beiratkozások naprakészen tartásához lefuttathat egy programkódot is. Lásd:  
<em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
<p>Ezt a kódrészletet beállíthatja úgy, hogy automatikusan új kurzusokat hozzon létre, ha új csoportok jelennek meg az LDAP-ben.</p>';
$string['enrol_ldap_autocreate'] = 'Automatikusan létrehozhatók kurzusok, ha a Moodle-ban még nem létezõ kurzusra iratkoznak fel.';
$string['enrol_ldap_autocreation_settings'] = 'Automatikus kurzus-létrehozási beállítások';
$string['enrol_ldap_bind_dn'] = 'Ha felhasználók kereséséhez a bind-user opciót kívánja használni, adja meg itt. Például:
\'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'A  bind-user jelszava.';
$string['enrol_ldap_category'] = 'Automatikusan létrehozott kurzusok kategóriája.';
$string['enrol_ldap_course_fullname'] = 'Opcionális: LDAP-mezõ a teljes név eléréséhez';
$string['enrol_ldap_course_idnumber'] = 'Egyeztesse az LDAP egyedi azonosítójával, ez általában <em>cn</em> vagy <em>uid</em>. Automatikusan létrehozott kurzusok esetén célszerû az értéket zárolni.';
$string['enrol_ldap_course_settings'] = 'Beállítások a kurzusbeiratkozáshoz';
$string['enrol_ldap_course_shortname'] = 'Opcionális: LDAP-mezõ a rövid név eléréséhez';
$string['enrol_ldap_course_summary'] = 'Opcionális: LDAP-mezõ az összegõ forma eléréséhez';
$string['enrol_ldap_editlock'] = 'Érték zárolása';
$string['enrol_ldap_general_options'] = 'Általános opciók';
$string['enrol_ldap_host_url'] = 'Az LDAP-gazdagépet URL-formában adja meg: 
\'ldap://ldap.myorg.com/\' vagy \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_objectclass'] = 'Kurzusok kjeresésére használt objektumosztály. Általában \'posixGroup\'.';
$string['enrol_ldap_search_sub'] = 'Csoporttagság kikeresése résztartalom alapján';
$string['enrol_ldap_server_settings'] = 'LDAP-szerver beállításai';
$string['enrol_ldap_student_contexts'] = 'Azon környezetek felsorolása, ahol a tanulói beiratkozások csoportjai találhatók. A környezeteket válassza el \';\'-vel. Például: 
\'ou=kurzusok,o=org; ou=egyebek,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Tag jellemzõje, ha a felhasználó egy csoporthoz tartozik (iratkozott be). Általában \'tag\'
vagy \'tagazonosító\'.';
$string['enrol_ldap_student_settings'] = 'Tanulók beiratkozásának beállításai';
$string['enrol_ldap_teacher_contexts'] = 'Azon környezetek felsorolása, ahol a tanári beiratkozások csoportjai találhatók. A környezeteket válassza el \';\'-vel. Például: 
\'ou=kurzusok,o=org; ou=egyebek,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Tag jellemzõje, ha a felhasználó egy csoporthoz tartozik (iratkozott be). Általában \'tag\'
vagy \'tagazonosító\'.';
$string['enrol_ldap_teacher_settings'] = 'Tanári beiratkozások beállításai';
$string['enrol_ldap_template'] = 'Opcionális: az automatikusan létrehozott kurzusok a sablonkurzusból átámásolhatják beállításaikat.';
$string['enrol_ldap_updatelocal'] = 'Helyi adatok frissítése';
$string['enrol_ldap_version'] = 'A szervere által használt LDAP-protokoll verziója';
$string['enrolname'] = 'LDAP';

?>
