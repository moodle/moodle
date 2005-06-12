<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.5 + (2005060201)


$string['description'] = '<p>K øízení zápisù do kurzù mù¾ete pou¾ív rovnì¾ vá¹ LDAP server. Pøedpokládá se, ¾e vá¹ LDAP strom (tree) obsahuje skupiny (groups) odpovídající va¹im kurzùm a ¾e ka¾dá z tìchto skupin má polo¾ky èlenství odpovídající studentùm.</p>

<p>Ka¾dý kurz by tedy mìl mít nadefinován jako LDAP skupina a ka¾dá z tìchto skupin bude mít nìkolik polí èlenství (<em>member</em> or <em>memberUid</em>), které obsahují unikátní identifikaci u¾ivatele.</p>

<p>Chcete-li tento re¾im zápisù do kurzù pou¾ít, <strong>musí</strong> mít va¹i u¾ivatelé ve svých profilech vyplnìno platné pole idnumber. LDAP skupiny, které odpovídají kurzùm, uvedou toto idnumber v polích svých èlenù. Tento zpùsob by mìl bez problémù fungovat, pokud u¾ pou¾íváte ovìøování u¾ivatelù pomocí LDAP.</p>

<p>Zápisy v kurzech (tzv. enrolments) budou aktualizovány poka¾dé, co se u¾ivatel pøihlásí. Pro  synchronizaci mù¾ete rovnì¾ spou¹tìt skript <em>enrol/ldap/enrol_ldap_sync.php</em> (viz zdrojový kód pro více informací).</p>

<p>Tento doplnìk mù¾e být rovnì¾ pou¾it pro automatické vytváøení nových kurzù, jakmile se odpovídající skupiny objeví ve va¹em LDAP serveru.</p>';
$string['enrol_ldap_autocreate'] = 'Kurzy mohou být vytváøeny automaticky, pokud se objeví zápis do kurzu, který v Moodlu je¹tì neexistuje.';
$string['enrol_ldap_autocreation_settings'] = 'Nastavení automatického vytváøení kurzù';
$string['enrol_ldap_bind_dn'] = 'Chcete-li v vyhledání u¾ivatelù pou¾ít bind-user, uveïte zde plný název. Nìco jako  \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Heslo pro bind-user.';
$string['enrol_ldap_category'] = 'Kategorie automaticky vytváøených kurzù.';
$string['enrol_ldap_course_fullname'] = 'Volitelné: LDAP pole, odkud se pøevezme celý název.';
$string['enrol_ldap_course_idnumber'] = 'Na který unikátní identifikátor v LDAP mapovat id kurzu. Vìt¹inou <em>cn</em> nebo <em>uid</em>. Doporuèuje se tuto hodnotu uzamknout, pokud pou¾íváte automatické vytváøení kurzù.';
$string['enrol_ldap_course_settings'] = 'Nastavení zápisù do kurzù';
$string['enrol_ldap_course_shortname'] = 'Volitelné: LDAP pole, odkud se pøevezme krátký název.';
$string['enrol_ldap_course_summary'] = 'Volitelné: LDAP pole, odkud se pøevezme souhrn kurzu.';
$string['enrol_ldap_editlock'] = 'Uzamknout hodnotu';
$string['enrol_ldap_general_options'] = 'Obecná nastavení';
$string['enrol_ldap_host_url'] = 'Urèete LDAP hostitele ve formì URL - napø. ldap://ldap.naseskola.cz/\' nebo ldaps://ldap.naseskola.cz/\'';
$string['enrol_ldap_objectclass'] = 'objectClass pou¾itá pøi vyhledávání kurzù. Vìt¹inou \'posixGroup\'.';
$string['enrol_ldap_search_sub'] = 'Hledej èlenství ve skupinách v subkontextech';
$string['enrol_ldap_server_settings'] = 'Nastavení serveru LDAP';
$string['enrol_ldap_student_contexts'] = 'Seznam kontextù, ve kterých jsou umístìny skupiny se zápisy studentù v kurzech. Kontexty oddìlujte støedníkem \';\'. Napø.: \'ou=kurzy,o=org; ou=dalsi,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Atribut skupiny, pokud je u¾ivatel jejím èlenem (tj. student je zapsán do pøíslu¹ného kurzu). Vìt¹inou \'member\' nebo \'memberUid\'.';
$string['enrol_ldap_student_settings'] = 'Nastavení zápisù do kurzù';
$string['enrol_ldap_teacher_contexts'] = 'Seznam kontextù, ve kterých jsou umístìny skupiny se zápisy vyuèujících v kurzech. Kontexty oddìlujte støedníkem \';\'. Napø.: \'ou=kurzy,o=org; ou=dalsi,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Atribut skupiny, pokud je u¾ivatel jejím èlenem (tj. u¾ivatel je vyuèujícím v pøíslu¹ném kurzu). Vìt¹inou \'member\' nebo \'memberUid\'.';
$string['enrol_ldap_teacher_settings'] = 'Nastavení vyuèujících v kurzech';
$string['enrol_ldap_template'] = 'Volitelné: automaticky vytváøené kurzy mohou pøevzít nastavení z nìjaké ¹ablony (vzorového kurzu).';
$string['enrol_ldap_updatelocal'] = 'Aktualizovat lokální data';
$string['enrol_ldap_version'] = 'Verze protokolu LDAP, který pou¾ívá vá¹ server';
$string['enrolname'] = 'LDAP';

?>
